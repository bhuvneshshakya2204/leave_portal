<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../includes/send_email.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0 && strlen($_SESSION['emplogin'])==0){
    echo "expired";
	die;
}
if(isset($_SESSION['hrlogin']))
	$eid=$_SESSION['hrlogin'];
else if(isset($_SESSION['mlogin']))
	$eid=$_SESSION['mlogin'];
else if(isset($_SESSION['emplogin']))
    $eid=$_SESSION['emplogin'];
/*if(!isset($_SESSION['hrlogin'])){
	echo "expired";
	die;
}*/

$sql= "select concat(FirstName,' ', LastName) as fullname from tblemployees where Status= 1 and EmailId='".$eid."'";
$query = $dbh -> prepare($sql);
$query->execute();
$user=$query->fetch(PDO::FETCH_OBJ);


$id= $_POST['id'];
date_default_timezone_set('Asia/Kolkata');
$today_date= date("Y-m-d");
$date_object = new DateTime($today_date);
$today_date = $date_object->format('j F');
$sql = "SELECT id FROM tblemployees WHERE Status=1 AND (Dob like '".$today_date."%' OR AnniversaryDate like '".$today_date."%' OR JoiningDate like '".$today_date."%') AND id='".$id."'";
$query = $dbh -> prepare($sql);
$query->execute();
if($query->rowCount() > 0) {    
    $sql= "select FirstName, LastName, EmailId, JoiningDate from tblemployees where Status= 1 and id='".$id."'";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    $fname= $results[0]->FirstName;
    $lname= $results[0]->LastName;
    //codes start to send email
    $emp_name= $fname." ".$lname;
    
    $cst_today_date = date("Y-m-d");
    $joining_date_tmp = $results[0]->JoiningDate;
    $format = "j F, Y";
    $joining_date_tmp = date_create_from_format($format, $joining_date_tmp);
    $joining_date = date_format($joining_date_tmp, "Y-m-d");
    $date1=date_create($cst_today_date);
    $date2=date_create($joining_date);
    $diff=date_diff($date1,$date2);
    $work_anniversary_year =  $diff->format("%y");

    if(isset($_POST['anniversary'])){
        $email_body= "<table><tr><td align='left'><strong>Dear $emp_name,</strong><br><br>\r\n";
        $email_body.= "&#x275D;May the bond of love between you gets stronger every day and may your every<br><center>anniversary be more exciting. Happy anniversary to both of you.&#x275E;</center></td></tr>\r\n";
        $email_body.= "<tr><td align='left'><br><strong>From ".$user->fullname."</strong></td></tr>\r\n";
        $subject= "Wish you happy anniversary";
    }else if(isset($_POST['work_anniversary'])){
        $email_body= "<table><tr><td align='left'><strong>Dear $emp_name,</strong><br><br>\r\n";
        $email_body.= "&#x275D;On this ".$work_anniversary_year." work anniversary, we want you to know that you are our most valuable team member.<br>Thank you for your dedication and hard work over the years. Congratulations.&#x275E;</td></tr>\r\n";
        $email_body.= "<tr><td align='left'><br><strong>From ".$user->fullname."</strong></td></tr>\r\n";
        $subject= "Wish you happy work anniversary";
    }else{
        $email_body= "<table><tr><td align='left'><strong>Dear $emp_name,</strong><br><br>\r\n";
        $email_body.= "&#x275D;Happy birthday! I hope all your birthday wishes and dreams come true.<br>Count your life by smiles, not tears. Count your age by friends, not years. Happy birthday!&#x275E;</td></tr>\r\n";
        $email_body.= "<tr><td align='left'><br><strong>From ".$user->fullname."</strong></td></tr>\r\n";
        $subject= "Wish you happy birthday";
    }
    $to= $results[0]->EmailId;
    $from= $eid;
    $bcc= "";
    $cc= "";
    send_email($to, $from, $bcc, $cc, $subject, $email_body);
    echo "sent";
}
//end of email
?>