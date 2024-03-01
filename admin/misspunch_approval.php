<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../shared/email-template.php');
$output = array('msg' => 'Unauthorized Access','status' => 'ERROR' );
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	echo json_encode($output);
	die();
}
// 
 
date_default_timezone_set('Asia/Kolkata');
$admremarkdate=date('Y-m-d G:i:s ', strtotime("now"));

// code for action taken on leave
if(isset($_POST['update']))
{ 
	$id= base64_decode(base64_decode($_POST['id']));
	$status=$_POST['status'];
	//codes to maintain history
	$sql = "SELECT m.empid, m.managerid, m.status, m.misspunch_date, m.misspunch_time, te1.FirstName, 
			te1.LastName, te1.EmailId, te2.FirstName AS ManagerName, te2.LastName AS ManagerLastName, 
			te2.EmailId AS ManagerEmail
			FROM tblmisspunch AS m
			INNER JOIN tblemployees AS te1 ON m.empid = te1.id
			INNER JOIN tblemployees AS te2 ON m.managerid = te2.id
			WHERE m.id =:id";
	$query = $dbh -> prepare($sql);
	$query->bindParam(':id',$id,PDO::PARAM_STR);
	$query->execute();
	$row = $query->fetch(PDO::FETCH_OBJ);
	// print_r($row);
	// exit;
	if($row){
		if($row->status == 1 && $status== 1){
			$output = array('msg' => 'Miss Punch is already approved','status' => 'INFO' );
		}else if($row->status == 2 && $status== 2){
			$output = array('msg' => 'Miss Punch  is already disapproved','status' => 'INFO' );
		}else{
			$sql="update tblmisspunch set status=:status where id=:id";
			$query = $dbh->prepare($sql);
			$query->bindParam(':status',$status,PDO::PARAM_STR);
			$query->bindParam(':id',$id,PDO::PARAM_STR);
			$query->execute();

			$arr = array(
	            'HR_NAME'           => 'Kriti',
	            'EMP_NAME'          => $row->FirstName." ".$row->LastName,
	            'STATUS'            => ($status == 1) ? 'approved':'disapproved',
	            'DATE'              => $row->misspunch_date,
				'TIME'    			=> $row->misspunch_time,
	            'MISS_PUNCH_TYPE'   => ($row->type ==1)?'In-Time':'Out-Time', 
	            'SIGNATURE_NAME'    => $row->ManagerName." ".$row->ManagerLastName
	        );

			// exit;
	        $email_body = getEmailContent($arr,EMAIL_MISS_PUNCH_APPROVAL);

	        $subject= "Re: Application for miss punch approval";
	        $bcc= "";
	        $cc= $row->EmailId;
	        send_email('kriti@netsutra.com', $row->ManagerEmail, $bcc, $cc, $subject, $email_body);
	        $msg= "Miss punch approval request updated successfully";
	        $output = array('msg' => $msg,'status' => 'SUCCESS','data'=>$sql );
		}
	}else{
		$output = array('msg' => 'Opps!! Something went wrong, please try again','status' => 'ERROR','data'=>$id );
	}
}
echo json_encode($output);
?>
