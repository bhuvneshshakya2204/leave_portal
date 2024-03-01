<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('includes/send_email.php');
include('includes/custom_functions.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0 && strlen($_SESSION['emplogin'])==0){
	echo "error";
	die;
}
$lid= $_POST['id'];
// $lid= base64_decode(base64_decode(base64_decode($lid)));
// $lid= explode("-", $lid);
// $lid= $lid[0];
//$sql= "select * from tblleaves where id=:lid";
$sql = "SELECT tl.empid, tl.ManagerID, tl.Status, tl.FromDate, tl.ToDate, tl.LeaveType, te1.FirstName, 
		te1.LastName, te1.EmailId, te2.FirstName AS ManagerName, te2.LastName AS ManagerLastName, 
		te2.EmailId AS ManagerEmail
		FROM tblleaves AS tl
		INNER JOIN tblemployees AS te1 ON tl.empid = te1.id
		INNER JOIN tblemployees AS te2 ON tl.ManagerID = te2.id
		WHERE tl.id =:lid";
$query = $dbh -> prepare($sql);
$query->bindParam(':lid',$lid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll();
if($query->rowCount() > 0){
	foreach($results as $result){}
	$emp_id= $result['empid'];
	$manager_id= $result['ManagerID'];
	$emp_name= $result['FirstName']." ".$result['LastName'];
	$emp_email= $result['EmailId'];
	$manager_name= $result['ManagerName']." ".$result['ManagerLastName'];
	$manager_email= $result['ManagerEmail'];
	$l_type= $result['LeaveType'];
	$l_from= $result['FromDate'];
	$l_to= $result['ToDate'];
    //Code for display count total taken leave and LA/LE
	$fromdate_array= explode("-", $l_from);
    $this_year_print= $fromdate_array[2];
    $this_month= $fromdate_array[1];
    $this_long_month= date("M",strtotime($l_from));    
    $total_leave = total_leave_taken_current_month($dbh, $emp_id, $this_year_print, $this_month);
    $total_short_leave = total_shortleave_taken_current_month($dbh, $emp_id, $this_year_print, $this_month);
    //End code for display count total taken leave and LA/LE	
	if($result['Status']== 0){
                    
        $email_body= "<table><tr><td colspan='2'><strong>Dear Sir/Madam,</strong><br><br>\r\n";
        $email_body.= "<strong>Kindly be informed my leave cancellation application.</strong><br><br>\r\n";
        $email_body.= "<tr><td align='left'>Leave Type:</td><td>$l_type</td></tr>\r\n";
        $email_body.= "<tr><td align='left'>From:</td><td>$l_from</td></tr>\r\n";
        $email_body.= "<tr><td align='left'>To:</td><td>$l_to</td></tr>\r\n";        
        $email_body.= "<tr><td align='left'><br>Total Leave Taken in $this_long_month $this_year_print:</td><td align='left'><br>$total_leave</td></tr>\r\n";
        $email_body.= "<tr><td align='left'>Total LA/LE Taken in $this_long_month $this_year_print:</td><td align='left'>$total_short_leave/4</td></tr>\r\n";        
        $email_body.= "<tr><td colspan='2'><br><font face='verdana' size='-1'><strong>Thanks and Regards,</strong>         <br>$emp_name</font></td></tr></table>\r\n";
        $subject= $l_type." has been cancelled";
        $bcc= "";
        $cc= "";        
		//codes to maintain history
		$status_updated= "self cancelled";
		$lastInsertId= maintain_history($dbh, $emp_id, $manager_id, $emp_name, $emp_email, $manager_name, $manager_email, $l_type, $l_from, $l_to, $status_updated, $lid);
		//end of history
		if($lastInsertId){
			$sql= "update tblleaves set Status= 3 where id=:lid";
			$query = $dbh->prepare($sql);
			$query->bindParam(':lid',$lid,PDO::PARAM_STR);
			$query->execute();
            send_email($manager_email, $emp_email, $bcc, $cc, $subject, $email_body);
            
			echo "canceled";
		}else{
			echo "error";
		}
	}else{
		// $emp_name= $result['FirstName']." ".$result['LastName'];
		// $emp_email= $result['EmailId'];
		// $manager_name= $result['ManagerName']." ".$result['ManagerLastName'];
		// $manager_email= $result['ManagerEmail'];
		// $l_type= $result['LeaveType'];
		// $l_from= $result['FromDate'];
		// $l_to= $result['ToDate'];
		$email_body= "<table><tr><td colspan='2'><strong>Dear Sir/Madam,</strong><br><br>\r\n";
		$email_body.= "<strong>Kindly accept my leave cancellation application.</strong><br><br>\r\n";
		$email_body.= "<tr><td align='left'>Leave Type:</td><td>$l_type</td></tr>\r\n";
		$email_body.= "<tr><td align='left'>From:</td><td>$l_from</td></tr>\r\n";
		$email_body.= "<tr><td align='left'>To:</td><td>$l_to</td></tr>\r\n";
        
        $email_body.= "<tr><td align='left'><br>Total Leave Taken in $this_long_month $this_year_print:</td><td align='left'><br>$total_leave</td></tr>\r\n";
        $email_body.= "<tr><td align='left'>Total LA/LE Taken in $this_long_month $this_year_print:</td><td align='left'>$total_short_leave/4</td></tr>\r\n";  
        
		$lid_encoded= base64_encode(base64_encode(base64_encode($lid)));
		$m_approve= base64_encode(base64_encode(base64_encode("approved_ok")));
		$m_disapprove= base64_encode(base64_encode(base64_encode("disapproved_not_ok")));
		$email_body.= "<tr><td><br><a href='http://www.netsutra.com/leave/m_action/cancel_action.php?lid=$lid_encoded&act=$m_approve' style='height: 30px; padding: 3px 7px 0 7px; text-decoration: none; font-weight: bold; color: #ffffff; border: 1px solid #D9D9D9; background: #2C5499;'>Approve</a></td><td><br><a href='http://www.netsutra.com/leave/m_action/cancel_action.php?lid=$lid_encoded&act=$m_disapprove' style='height: 30px; padding: 3px 7px 0 7px; text-decoration: none; font-weight: bold; color: #ffffff; border: 1px solid #D9D9D9; background: #2C5499;'>Disapprove</a></td></tr>\r\n";
		$email_body.= "<tr><td colspan='2'><br><font face='verdana' size='-1'><strong>Thanks and Regards,</strong><br>$emp_name</font></td></tr></table>\r\n";
		$subject= "Application for ".$l_type." cancellation";
		$bcc= "";
		$cc= "";
		//codes to maintain history
		$status_updated= "request for cancellation";
		$lastInsertId= maintain_history($dbh, $emp_id, $manager_id, $emp_name, $emp_email, $manager_name, $manager_email, $l_type, $l_from, $l_to, $status_updated, $lid);
		//end of history
		if($lastInsertId){
			//code to update main table status
			$sql="update tblleaves set Status= 5 where id=:lid";
			$query = $dbh->prepare($sql);
			$query->bindParam(':lid',$lid,PDO::PARAM_STR);
			$query->execute();
			//end of updateing main table
			//$manager_email= "dipak.kumar@netsutra.com";
			send_email($manager_email, $emp_email, $bcc, $cc, $subject, $email_body);
			echo "sent";
		}else{
			echo "error";
		}
	}
}else{
	echo "error";
}
function maintain_history($dbh, $emp_id, $manager_id, $emp_name, $emp_email, $manager_name, $manager_email, $l_type, $l_from, $l_to, $status_updated, $lid){
	date_default_timezone_set('Asia/Kolkata');
	$created_at= date("d-m-Y H:i:s");
	//$status_updated= "self cancelled";
	$sql="INSERT INTO tblleaves_history(leave_id,leave_type,from_date,to_date,created_at,status,emp_id,manager_id) VALUES(:lid,:l_type,:l_from,:l_to,:created_at,:status_updated,:emp_id,:manager_id)";
	$query = $dbh->prepare($sql);
	$query->bindParam(':lid',$lid,PDO::PARAM_STR);
	$query->bindParam(':l_type',$l_type,PDO::PARAM_STR);
	$query->bindParam(':l_from',$l_from,PDO::PARAM_STR);
	$query->bindParam(':l_to',$l_to,PDO::PARAM_STR);
	$query->bindParam(':created_at',$created_at,PDO::PARAM_STR);
	$query->bindParam(':status_updated',$status_updated,PDO::PARAM_STR);
	$query->bindParam(':emp_id',$emp_id,PDO::PARAM_STR);
	$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
	$query->execute();
	return $lastInsertId = $dbh->lastInsertId();
}
?>