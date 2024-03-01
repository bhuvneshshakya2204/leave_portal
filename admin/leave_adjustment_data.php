<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../shared/pagination.php');
$paginationOption = array('recordPerPage' => 10 , 'pageNumber' => 3 );
$condition = "";
$order = "";
$limit = "";
$param = $_REQUEST;

$userAutoId= $_SESSION['eid'];

$columns = array(
				['name'=>'Emp Id','db'=>'e.EmpId','visible'=>true,'seq'=>1],
                ['name'=>'Employee Name','db'=>'concat(e.FirstName," ",e.LastName)','visible'=>true,'seq'=>2],
                ['name'=>'Mobile Number','db'=>'e.PhoneNumber','visible'=>true,'seq'=>3],
                ['name'=>'IL','db'=>'lc.total_il','visible'=>true,'seq'=>4],
                ['name'=>'CL','db'=>'lc.total_cl','visible'=>true,'seq'=>5],
                // ['name'=>'CO','db'=>'lc.total_co','visible'=>true,'seq'=>6],
                ['name'=>'Action','db'=>'','visible'=>true,'seq'=>6],
                ['name'=>'id','db'=>'e.id','visible'=>true,'seq'=>7]
           );

$colStr = getColumns($columns);
$filter = $param['filter'];
$sql = "SELECT $colStr from tblemployees e LEFT JOIN leave_counts lc ON e.id=lc.emp_id AND lc.month = ".$filter['lc.month']." AND year = ".date('Y')." where e.status = 1 ";

if(isset($param['filter'])){
    $condition .= getFilterString($param['filter']);
}

if(isset($param['order'])){
    $order = getOrderString($param['order'],$columns);
}

$sqlCount .= "select count(e.id) as total from tblemployees e where 1=1 ";
$sqlCount.=$condition;
$queryCount = $dbh -> prepare($sqlCount); 
$queryCount->execute();
$resultCount=$queryCount->fetch(PDO::FETCH_OBJ);
$total = intval($resultCount->total);

$start = $param['start'];
$length = $param['length'];
$limit = "LIMIT $start,$length ";

$sql .= $condition;
$finalSql = $sql.$order.$limit;
$query = $dbh -> prepare($finalSql); 
$query->execute();
$results=$query->fetchAll(PDO::FETCH_ASSOC);

$processedData = array();
foreach ($results as $key => $row) {
    $cloneRow = $row;
    $row['Employee Name'] = "<span class='gridLink' onclick='getUserDetail(".$row['empid'].")'>".$row['Employee Name']."</span>";
    $arr = array();

    $cnt = 0;
    foreach ($columns as $i => $value) {
        if(strlen($value['db']) <= 0){
            $arr[$cnt++] = getAction($cloneRow);
            continue;
        }
        if($value['visible'] == true){
            $arr[$cnt++] = $row[$value['name']];
        }

    }
    array_push($processedData, $arr);
}
$output = array('recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $processedData, 'params' => $finalSql );
echo json_encode($output);



















/* -------------------------------------------------CUSTOM FUNCTION FOR LEAVE DATA----------------------------------------------*/

function getAction($row){
        $action = '<i class="material-icons btnAction txt-blue" action="adjustLeave" title="Click to Adjest Leave">edit</i>';
        $action .= '<i data-id="'.base64_encode(base64_encode($row['id'])).'" ></i>';
    	return $action;
}

function getFilterString($filter){
    $str = "";
    foreach ($filter as $key => $value) {
    	if($key != "lc.month" && $key != "lc.year")
    		$str .= "AND $key = '".$value."' ";
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
            $oColumn = "`".$columns[$key]['name']."`";
            if($columns[$key]['name'] == 'From Date' || $columns[$key]['name'] == 'To Date'){
                $oColumn = "STR_TO_DATE($oColumn, '%d-%m-%Y')";
            }
            if($columns[$key]['name'] == 'Status'){
                $oColumn = "leave_order";
            }
        }
    }
    return "ORDER BY $oColumn $oDirection ";
}

/* -------------------------------------------------END CUSTOM FUNCTION FOR LEAVE DATA-------------------------------------*/