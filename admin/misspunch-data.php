<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../shared/pagination.php');
$paginationOption = array('recordPerPage' => 3 , 'pageNumber' => 1 );
$condition = "";
$order = "";
$limit = "";
$param = $_REQUEST;

if($param['action']=='name'){    
    echo json_encode(getNames($param['term']));
    exit();
}

if($param['action']=='leaveDetail'){    
    echo json_encode(getleaveDetail($param['leaveId']));
    exit();
}
//print_r($gbl_statusArr);

$userAutoId= $_SESSION['eid'];
$columns = array(
                ['name'=>'Employee Name','db'=>'concat(e.FirstName," ",e.LastName)','visible'=>true,'seq'=>1],
                ['name'=>'Date','db'=>'m.misspunch_date','visible'=>true,'seq'=>2],
                ['name'=>'type','db'=>'m.type','visible'=>true,'seq'=>3],
                ['name'=>'status','db'=>'m.Status','visible'=>true,'seq'=>4],
                ['name'=>'Action','db'=>'','visible'=>true,'seq'=>5],
                ['name'=>'empid','db'=>'m.empid','visible'=>false,'seq'=>6],
                ['name'=>'id','db'=>'m.id','visible'=>false,'seq'=>7]
           );

$colStr = getColumns($columns);
$sql = "SELECT $colStr from tblmisspunch m INNER JOIN tblemployees e on m.empid=e.id where 1=1 ";
$sqlCount = "SELECT count(m.id) as total from tblmisspunch m INNER JOIN tblemployees e on m.empid=e.id where 1=1 ";

if(strlen($_SESSION['mlogin'])!=0){
    $condition = "AND m.managerid= $userAutoId ";
}

if(isset($param['filter'])){
    $condition .= getFilterString($param['filter']);
}

if(isset($param['order'])){
    $order = getOrderString($param['order'],$columns);
}

$sqlCount .= $condition;
$queryCount = $dbh -> prepare($sqlCount); 
$queryCount->execute();
$resultCount=$queryCount->fetch(PDO::FETCH_OBJ);
$total = intval($resultCount->total);

$start = $param['start'];
$length = $param['length'];
$limit = "LIMIT $start,$length ";

$sql .= $condition.$order.$limit;
$query = $dbh -> prepare($sql); 
$query->execute();
$results=$query->fetchAll(PDO::FETCH_ASSOC);

$processedData = array();
foreach ($results as $key => $row) {
    $row['Employee Name'] = "<span class='gridLink' onclick='getUserDetail(".$row['empid'].")'>".$row['Employee Name']."</span>";
    $row['status'] = "<span class='".$gbl_statColorArr[$row['status']]."'>".$gbl_statusArr[$row['status']]."</span>";
    $row['type'] = $row['type'] == 1 ? 'In Time':'Out Time';
    $arr = array();

    $cnt = 0;
    foreach ($columns as $i => $value) {
        if(strlen($value['db']) <= 0){
            $arr[$cnt++] = getAction($row);
            continue;
        }
        if($value['visible'] == true){
            $arr[$cnt++] = $row[$value['name']];
        }

    }
    array_push($processedData, $arr);
}
$output = array('recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $processedData, 'params' => $param, 'sql'=>$sql );
echo json_encode($output);



















/* -------------------------------------------------CUSTOM FUNCTION FOR LEAVE DATA----------------------------------------------*/

function getNames($str){
    global $dbh;
    $sql = "select concat(FirstName,' ',LastName) as name,id from tblemployees where concat(FirstName,' ',LastName) like '%$str%'";
    $query = $dbh -> prepare($sql); 
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_ASSOC);
    $output = array();
    foreach ($results as $key => $row) {
        $output[] = array("value"=>$row['name'],"label"=>$row['name'],"id"=>$row['id']);
    }
    return $output;
}

function getleaveDetail($leaveId){
    global $dbh;
    $lid= base64_decode(base64_decode($leaveId));
    $sql = "select * from tblleaves where id=$lid";
    $query = $dbh -> prepare($sql); 
    $query->execute();
    $leaveDetail=$query->fetch(PDO::FETCH_ASSOC);
    if(sizeof($leaveDetail)>0){
        $sql="select * from tblleaves_history where leave_id='$lid' order by id desc";
        $query = $dbh -> prepare($sql); 
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_ASSOC);
        $leaveDetail['leaveHistory'] = $results;
    }
    return $leaveDetail;
}

function getAction($row){
    $action = "";
    if(strlen($_SESSION['mlogin'])!=0){
        $action .= '<i class="material-icons icon_hover txt-blue edit_icon btnAction" action="approval" title="Approve / Disapprove">edit</i>';
        $action .= '<i class="material-icons icon_hover check_icon check_action btnAction" action="approve" title="Approve">check</i>&nbsp;
                    <i class="material-icons icon_hover clear_icon clear_action btnAction" action="disapprove" title="Disapprove">clear</i>';
        $action .= '<i data-id="'.base64_encode(base64_encode($row['id'])).'" ></i>';
    }
    return $action;
}

function getFilterString($filter){
    $str = "";
    $from_date = strlen(trim($filter['fDateFilter'])) > 0 ? date('Y-m-d',strtotime($filter['fDateFilter'])) : "";
    $to_date = strlen(trim($filter['toDateFilter'])) > 0 ? date('Y-m-d',strtotime($filter['toDateFilter'])) : "";

    if(strlen(trim($filter['nameFilter']))>0){
        $str .= "AND concat(e.FirstName,' ',e.LastName) LIKE '%".$filter['nameFilter']."%' ";
    }
    
    if(strlen(trim($filter['fDateFilter'])) > 0 && strlen(trim($filter['toDateFilter'])) > 0){
        $str .= "AND ((str_to_date(m.misspunch_date, '%d-%m-%Y') >= '".$from_date."' 
                 AND str_to_date( m.misspunch_date,'%d-%m-%Y') <= '".$to_date."') 
                 OR (str_to_date(m.misspunch_date, '%d-%m-%Y') >= '".$from_date."' 
                 AND str_to_date(m.misspunch_date, '%d-%m-%Y') <= '".$to_date."'))";
    }else if(strlen(trim($filter['fDateFilter']))>0 && strlen(trim($filter['toDateFilter'])) == 0){
        $str .= "AND str_to_date(m.misspunch_date, '%d-%m-%Y') >= '".$from_date."' ";
    }else if(strlen(trim($filter['fDateFilter'])) == 0 && strlen(trim($filter['toDateFilter'])) > 0){
        $str .= "AND str_to_date(m.misspunch_date, '%d-%m-%Y') <= '".$to_date."' ";
    }

    if(gettype($filter['typeFilter']) == 'array'){
        if (!in_array("All", $filter['typeFilter'])){
            $str .= "AND m.type in ('".implode("','",$filter['typeFilter'])."') ";
        }
    }

    if(gettype($filter['statusFilter']) == 'array'){
        $str .= "AND m.Status in ('".implode("','",$filter['statusFilter'])."') ";
    }

    return $str;
}

function getOrderString($orderArr, $columns){
    $oDirection = $orderArr[0]['dir'];
    $colIndex = $orderArr[0]['column'];
    
    $colCnt = -1;
    foreach ($columns as $key => $column) {
        $colCnt = ($column['visible'] == true ) ? $colCnt + 1 : $colCnt ;
        if($column['visible'] == true && $colCnt == $colIndex ){
            $oColumn = $columns[$key]['db'];
            if($columns[$key]['name'] == 'Date'){
                $oColumn = "STR_TO_DATE($oColumn, '%d-%m-%Y')";
            }
            if($columns[$key]['name'] == 'Status'){
                $oColumn = "l.leave_order";
            }
        }
    }
    return "ORDER BY $oColumn $oDirection ";
}

/* -------------------------------------------------END CUSTOM FUNCTION FOR LEAVE DATA-------------------------------------*/