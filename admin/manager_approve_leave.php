<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../includes/send_email.php');
include('../includes/custom_functions.php');
//echo $_SESSION['mlogin'];die;
if(strlen($_SESSION['mlogin'])==0)
{   
	//header('location:../index.php');
	$msg= "Sorry!! You are logged out";
}
else{
	date_default_timezone_set('Asia/Kolkata');
	$admremarkdate=date('Y-m-d G:i:s ', strtotime("now"));
	$lid= base64_decode(base64_decode($_POST['lid']));
	$isread_manager= 1;
	$sql="update tblleaves set IsRead_Manager=:isread_manager where id=:lid";
	$query = $dbh->prepare($sql);
	$query->bindParam(':isread_manager',$isread_manager,PDO::PARAM_STR);
	$query->bindParam(':lid',$lid,PDO::PARAM_STR);
	$query->execute();
	
	////////////Approving leave////////////////
	$description= "";
	if(!empty($_POST['description']))
	$description= $_POST['description'];
$status=$_POST['status'];
//codes to maintain history
	$sql = "SELECT tl.empid, tl.ManagerID, tl.Status, tl.FromDate, tl.ToDate, tl.LeaveType, tl.PostingDate, te1.FirstName, 
			te1.LastName, te1.EmailId, te2.FirstName AS ManagerName, te2.LastName AS ManagerLastName, 
			te2.EmailId AS ManagerEmail
			FROM tblleaves AS tl
			INNER JOIN tblemployees AS te1 ON tl.empid = te1.id
			INNER JOIN tblemployees AS te2 ON tl.ManagerID = te2.id
			WHERE tl.id =:lid";
		$query = $dbh -> prepare($sql);
		$query->bindParam(':lid',$lid,PDO::PARAM_STR);
		$query->execute();
		$results_history= $query->fetchAll();
		if($query->rowCount() > 0)
		{
			foreach($results_history as $result_both){}
			if($result_both['Status']== 5){
				include("cancel_request_action.php");
			}else if($result_both['Status']== 4){
				include("edit_request_action.php");
			}else if($result_both['Status']== 1 && $status== 1){
				$msg= "Leave is already approved";
			}else if($result_both['Status']== 2 && $status== 2){
				$msg= "Leave is already not approved";
			}else if($result_both['Status']== 3){
				$msg= "This leave is cancelled";
			}else{
				$l_type= $result_both['LeaveType'];
				$l_from= $result_both['FromDate'];
				$l_to= $result_both['ToDate'];
				$emp_id= $result_both['empid'];
				$manager_id= $result_both['ManagerID'];
				$created_at= date("d-m-Y H:i:s");
                $modify_posting_date= new DateTime($result_both['PostingDate']);
				$modify_posting_date= $modify_posting_date->format("d-m-Y H:i:s");
				if($status== 1){
						$status_inword= "Approved";
						$status_history= "leave approved";
						$leave_order= 4;
				}else{
						$status_inword= "Not Approved";
						$status_history= "leave not approved";
						$leave_order= 5;
					}
				$sql="INSERT INTO tblleaves_history(leave_id,leave_type,from_date,to_date,created_at,status,emp_id,manager_id) VALUES(:lid,:l_type,:l_from,:l_to,:created_at,:status_history,:emp_id,:manager_id)";
				$query = $dbh->prepare($sql);
				$query->bindParam(':lid',$lid,PDO::PARAM_STR);
				$query->bindParam(':l_type',$l_type,PDO::PARAM_STR);
				$query->bindParam(':l_from',$l_from,PDO::PARAM_STR);
				$query->bindParam(':l_to',$l_to,PDO::PARAM_STR);
				$query->bindParam(':created_at',$created_at,PDO::PARAM_STR);
				$query->bindParam(':status_history',$status_history,PDO::PARAM_STR);
				$query->bindParam(':emp_id',$emp_id,PDO::PARAM_STR);
				$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
				$query->execute();
				$lastInsertId_history = $dbh->lastInsertId();
				if($lastInsertId_history){
					//date_default_timezone_set('Asia/Kolkata');
					//$admremarkdate=date('Y-m-d G:i:s ', strtotime("now"));
					$admremarkdate= date('d-m-Y H:i:s');
					$sql="update tblleaves set AdminRemark=:description,Status=:status,leave_order=:leave_order,AdminRemarkDate=:admremarkdate where id=:lid";
					$query = $dbh->prepare($sql);
					$query->bindParam(':description',$description,PDO::PARAM_STR);
					$query->bindParam(':status',$status,PDO::PARAM_STR);
					$query->bindParam(':leave_order',$leave_order,PDO::PARAM_STR);
					$query->bindParam(':admremarkdate',$admremarkdate,PDO::PARAM_STR);
					$query->bindParam(':lid',$lid,PDO::PARAM_STR);
					$query->execute();
					//Code to send an email to employee
					$sql="select FirstName, EmailId, ManagerID, FromDate, ToDate, LeaveType, l.empid from tblemployees as e
						  join tblleaves as l on e.id= l.empid
						  where l.id=:lid";
					$query = $dbh->prepare($sql);
					$query->bindParam(':lid',$lid,PDO::PARAM_STR);
					$query->execute();
					$results=$query->fetchAll();
					foreach($results as $result){}
					$fname= $result['FirstName'];
					$to= $result['EmailId'];
					$from_date= $result['FromDate'];
					$to_date= $result['ToDate'];
					$managerId= $result['ManagerID'];
					$leave_type= $result['LeaveType'];
					$userType= "manager";
					$empid= $result['empid'];
                    //Code for display count total taken leave and LA/LE
                    $fromdate_array= explode("-", $from_date);
                    $this_year_print= $fromdate_array[2];
                    $this_month= $fromdate_array[1];
                    $this_long_month= date("M",strtotime($from_date));                      
                    $total_leave = total_leave_taken_current_month($dbh, $empid, $this_year_print, $this_month);
                    $total_short_leave = total_shortleave_taken_current_month($dbh, $empid, $this_year_print, $this_month);
                    //End code for display count total taken leave and LA/LE                    
					$sql= "select FirstName, LastName, EmailId from tblemployees where id=:managerId";
					$query = $dbh->prepare($sql);
					$query->bindParam(':managerId',$managerId,PDO::PARAM_STR);
					//$query->bindParam(':userType',$userType,PDO::PARAM_STR);
					$query->execute();
					$results=$query->fetchAll();
					foreach($results as $result){}
					$m_f_name= $result['FirstName'];
					$m_l_name= $result['LastName'];
					$from= $result['EmailId'];
					$bcc= "";
					$cc= "kriti@netsutra.com";
					$subject= "Re: Application for ".$leave_type;
					/*if($status== 1)
						$status_inword= "Approved";
					else
						$status_inword= "Not Approved";*/
					$manager_name= htmlentities($m_f_name." ".$m_l_name);	
					$email_body= "<table><tr><td colspan='2'><strong>Dear $fname</strong><br><br></td></tr>\r\n";
								$email_body.= "<tr><td colspan='2'>Your $leave_type from $from_date to $to_date is $status_inword.</td></tr>\r\n";
								if($description!= "")
									$email_body.= "<tr><td colspan='2'><strong>Description:</strong> $description</td></tr>\r\n";  
                                $email_body.= "<tr><td colspan='2'><br><strong>Total Leave Taken in $this_long_month $this_year_print:</strong>  $total_leave</td></tr>\r\n";
				                $email_body.= "<tr><td colspan='2'><strong>Total LA/LE Taken in $this_long_month $this_year_print: </strong> $total_short_leave/4</td></tr>\r\n";
                                $email_body.= "<tr><td colspan='2'><strong>Posting Date: </strong>$modify_posting_date</td></tr>\r\n";
								$email_body.= "<tr><td colspan='2'><br><font face='verdana' size='-1'><strong>Thanks,</strong><br>$manager_name</font></td></tr></table>\r\n";
								send_email($to, $from, $bcc, $cc, $subject, $email_body);
								if($status== 1)
									$msg= "Leave approved successfully";
								else
									$msg= "Leave disapproved successfully";
				}else{
					$msg= "Opps!! Something went wrong, please try again";
				}
			}
		}else{
			$msg= "Opps!! Something went wrong, please try again";
		}
		//echo $msg;
		//if($msg != "Leave approved successfully" || $msg != "Leave disapproved successfully" || $msg != "Opps!! Something went wrong, please try again" || $msg != "Leave is already not approved" || $msg != "Leave is already approved" || $msg != "This leave is cancelled" || $msg != "Sorry!! You are logged out"){
			if($msg == "Leave approved successfully" || $msg == "Leave disapproved successfully"){
			if($_POST['pending']== 1)
				include("ui_approval_disapproval.php");
			else
				include("all_leaves_ajax.php");
}else{
			echo $msg;
		}
	//////end of leave approval////////////////
}
?>