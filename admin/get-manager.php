<?php
session_start();
error_reporting(0);
include('includes/config.php');
$output = array('msg' => 'Unauthorized Access','status' => 'ERROR' ); 
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	echo json_encode($output);
	die();
}

date_default_timezone_set('Asia/Kolkata');

$empid= $_POST['empid'];
$data = getManagerDetail($empid);
	if($data){
		echo json_encode(array('msg' => 'Record Found','status' => 'SUCCESS','manager'=>$data));
		die();
	}
	echo json_encode(array('msg' => 'Record Not Found','status' => 'ERROR' ));
	die();


function getManagerDetail($empId){
	global $dbh;
	$sql = "select e1.FirstName as mFirstName,e1.LastName as mLastName,
			e1.EmailId as ManagerEmail,e1.id as managerid from tblemployees e 
			LEFT JOIN tblemployees e1 ON e.reporting_manager = e1.id 
			where e.id=$empId";
	$query = $dbh -> prepare($sql);
	$query->execute();
	return $query->fetch(PDO::FETCH_OBJ);
}


?>
