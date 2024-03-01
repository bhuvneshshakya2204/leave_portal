<?php
include('includes/config.php');
include('includes/send_email.php');
session_start();
$otp= trim($_POST['otp']);
if(isset($_SESSION['empid'])){
	$id= $_SESSION['empid'];
	$sql= "select created_time,otp from tblotp where empid=:id order by created_time desc limit 1";
	$query= $dbh -> prepare($sql);
	$query-> bindParam(':id', $id, PDO::PARAM_STR);
	$query-> execute();
	$results=$query->fetchAll(PDO::FETCH_OBJ);
	foreach ($results as $result){}
	date_default_timezone_set("Asia/Kolkata");
	$otp_db= $result->otp;
	$created_time= $result->created_time;
	$current_time= date("Y-m-d H:i:s");
	//code start for getting difference in minutes
		$start_date = new DateTime($created_time);
		$diff_minutes = $start_date->diff(new DateTime($current_time));
	$minutes = $diff_minutes->days * 24 * 60;
	$minutes += $diff_minutes->h * 60;
	$minutes += $diff_minutes->i;
	//end of minutes diff
	if($minutes > 5){
		unset($_SESSION['empid']);
		unset($_SESSION['otp_window']);
		$sql= "delete from tblotp where empid=:id";
		$query= $dbh -> prepare($sql);
		$query-> bindParam(':id', $id, PDO::PARAM_STR);
		$query-> execute();
		echo "otp_expired";
	}else{
		if($otp===$otp_db){
			$sql= "delete from tblotp where empid=:id";
			$query= $dbh -> prepare($sql);
			$query-> bindParam(':id', $id, PDO::PARAM_STR);
			$query-> execute();
			$_SESSION['auth']= "set";
			echo "correct";
		}else{
			echo "incorrect";
		}
	}
}else{
	echo "s_expired";
}
?>