<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0 && strlen($_SESSION['emplogin'])==0)
{   
	echo "error";
}else{
	$id= $_POST['id'];
	$sql= "select * from tblemployees where id= '".$id."'";
	$query = $dbh -> prepare($sql);
	$query->execute();
	$results=$query->fetchAll(PDO::FETCH_OBJ);
	if($query->rowCount() > 0){
		foreach($results as $result){}
		$reporting_manager= $result->reporting_manager;
		$sql= "select FirstName, LastName from tblemployees where id= $reporting_manager";
		$query = $dbh -> prepare($sql);
		$query->execute();
		$results_manager=$query->fetchAll(PDO::FETCH_OBJ);
		foreach($results_manager as $val){}
		$manager= $val->FirstName." ".$val->LastName;
		$response= array($result->FirstName." ".$result->LastName, $result->Phonenumber, $result->Address, $result->blood_group, $result->skill_set, $result->designation, $result->EmailId, $result->email_pers, $manager, $result->previous_exp);
		echo json_encode($response);
	}else{
		echo "error";
	}
}
?>