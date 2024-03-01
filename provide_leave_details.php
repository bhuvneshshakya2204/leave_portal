<?php
session_start();
error_reporting(0);
include('includes/config.php');
$lid= $_POST['lid'];
if($_POST['updated_HR']){
    $sql = "SELECT empid from tblleaves where id=:lid";
    $query = $dbh -> prepare($sql);
    $query->bindParam(':lid',$lid,PDO::PARAM_STR);
    $query->execute();
    $results_empid=$query->fetchAll(PDO::FETCH_OBJ);
    foreach($results_empid as $result_emp){}
    $eid=$result_emp->empid;
} else {    
    $eid=$_SESSION['eid'];
}
$sql = "SELECT id,LeaveType,ToDate,FromDate,Description,PostingDate,AdminRemarkDate,AdminRemark,Status from tblleaves where empid=:eid and id=:lid";
$query = $dbh -> prepare($sql);
$query->bindParam(':eid',$eid,PDO::PARAM_STR);
$query->bindParam(':lid',$lid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
	foreach($results as $result){}
    $form_l_type_field .= '<select name="l_type" id="l_type" onChange="hideToDate(this.value)">';                            
    $sql = "SELECT  LeaveType from tblleavetype where status=1";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $results1=$query->fetchAll(PDO::FETCH_OBJ);
    $cnt=1;
    if($query->rowCount() > 0)
    {
        foreach($results1 as $result1)
        {                                    
            $selected = ($result1->LeaveType == $result->LeaveType) ? 'selected="selected"' : '';
            $form_l_type_field .= '<option value="'.htmlentities($result1->LeaveType).'" ' . $selected . '>'.htmlentities($result1->LeaveType).'</option>';
        }
    }
    $form_l_type_field .= '</select>';
	$response= array($result->id,$form_l_type_field,$result->FromDate,$result->ToDate,$result->Status,$result->LeaveType);
	echo json_encode($response);
}else{
	echo "invalid";
}
?>