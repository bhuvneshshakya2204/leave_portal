<?php
include('includes/config.php');
include('includes/send_email.php');
session_start();
if(isset($_POST['resend'])){
	if(isset($_SESSION['empid'])){
		send_otp($dbh);
		echo "sent";
	}else{
		echo "expired";
	}
}else{
$empid=$_POST['empid'];
$email=$_POST['emailid'];
$sql ="SELECT id FROM tblemployees WHERE EmailId=:email and EmpId=:empid";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':empid', $empid, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
	foreach ($results as $result) {
		$_SESSION['empid']=$result->id;
	  }
	  $_SESSION['email_otp']= $email;
	  send_otp($dbh);
	  echo "valid";
}else{
	echo "invalid";
}
}

//////////////////////////Function for sending otp//////////////////////////////////////////////////////////

function send_otp($dbh){
		$empid= $_SESSION['empid'];
		$otp= mt_rand(10, 10000);
		$_SESSION['otp_window']= "set";
		date_default_timezone_set("Asia/Kolkata");
		$created_time= date("Y-m-d H:i:s");
		$sql="INSERT INTO tblotp(empid,otp,created_time) VALUES(:empid,:otp,:created_time)";
		$query = $dbh->prepare($sql);
		$query->bindParam(':empid',$empid,PDO::PARAM_STR);
		$query->bindParam(':otp',$otp,PDO::PARAM_STR);
		$query->bindParam(':created_time',$created_time,PDO::PARAM_STR);
		$query->execute();
		$email_body= "<table><tr><td><strong>Hello,</strong><br><br>\r\n";
		$email_body.= "$otp is your OTP for changing your password. Do not share with anyone.</td></tr>\r\n";
		//$email_body.= "<tr><td align='left'>Have a good day!</td></tr>\r\n";
		$email_body.= "<tr><td><br><font face='verdana' size='-1'><strong>Thanks</strong></font></td></tr></table>\r\n";
		$to= $_SESSION['email_otp'];
		//$to= "dipak.kumar@netsutra.com";
		$from= "noreply@netsutra.com";
		$bcc= "";
		$cc= "";
		$subject= "Netsutra OTP";
		send_email($to, $from, $bcc, $cc, $subject, $email_body);
}
?>