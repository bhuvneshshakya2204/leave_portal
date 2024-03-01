<?php
session_start();
error_reporting(0);
include('includes/config.php');
$lid= $_POST['lid'];
$eid=$_SESSION['eid'];
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
	$response= array($result->id,$result->LeaveType,$result->FromDate,$result->ToDate,$result->Status);
	echo json_encode($response);
}else{
	echo "invalid";
}
?>