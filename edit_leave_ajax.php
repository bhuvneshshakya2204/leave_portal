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
$lid= $_POST['l_id'];
$updated_By_HR= $_POST['updated_HR'];
if($updated_By_HR){
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
$l_type= $_POST['l_type'];
$l_from= $_POST['l_from'];
$l_from= str_replace("/", "-", $l_from);
if($l_type == "Late Arrival (LA)" || $l_type == "Leaving Early (LE)") {
    $l_to= $_POST['l_from'];
} else {    
    $l_to= $_POST['l_to'];
}
$l_to= str_replace("/", "-", $l_to);
$fromdate = date('d-m-Y',strtotime($l_from));
$fromdate1 =  date_create($fromdate);
$todate = date('d-m-Y',strtotime($l_to));
$todate1 =  date_create($todate);
$diff =  date_diff($fromdate1, $todate1);
//back dates allowing codes start
$today= date("d-m-Y");
$today_dt = date_create($today);
$expire_dt_from = date_create($fromdate);
$expire_dt_to = date_create($todate);
$total_diff_days= 0;
$total_diff_days1= 0;
$back_dates= 0;
//code to check the leaves for already exists
$found= 0;
$diffcurrentdate =  date_diff($today_dt, $fromdate1);
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
			foreach($results as $result_both){}
			$db_leave_type= $result_both['LeaveType'];
			$from_date= $result_both['FromDate'];
			$to_date= $result_both['ToDate'];
			$emp_name= $result_both['FirstName']." ".$result_both['LastName'];
			$emp_email= $result_both['EmailId'];
			$m_name= $result_both['ManagerName']." ".$result_both['ManagerLastName'];
			$manager_id= $result_both['ManagerID'];
			$m_email= $result_both['ManagerEmail'];
			//$m_email= "dipak.kumar@netsutra.com";
			//code for cheking same leave application
				if($l_type==$db_leave_type && $l_from==$from_date && $l_to==$to_date){
					echo "same_leave";
					die;
				}
			//end of same application            
            //validation code to check the leaves for already exists
            $sql= "select * from tblleaves where (FromDate='$fromdate' or ToDate='$fromdate' or FromDate='$todate') and empid=$eid and ManagerID=$manager_id and Status not in (3) and id not in ('$lid')";
            $query = $dbh -> prepare($sql);
            $query->execute();
            if($query->rowCount() > 0)
                $found= 1;
            //end of codes for already exists
            if($expire_dt_from < $today_dt || $expire_dt_to < $today_dt){
                if($l_type== "Leave" || $l_type== "Informed Leave (IL)" || $l_type== "Casual Leave (CL)"){
                    $start_date = new DateTime($fromdate);
                    $since_start = $start_date->diff(new DateTime());
                    $start_date1 = new DateTime($todate);
                    $since_start1 = $start_date1->diff(new DateTime());
                    $total_diff_days= $since_start->days;
                    $total_diff_days1= $since_start1->days;
                }else{
                    if($diffcurrentdate->format("%R%a")< -3){
                       $back_dates= 1;
                    }
                }
            }
            /////end of back days codes          
            if($diff->format("%R%a") < 0){
                echo "wrong_date_range";
				die;
             }else if($l_from== "" || $l_to== ""){
                echo "fields_blank";
				die;
             }else if($total_diff_days > 15 || $total_diff_days1 > 15){
                echo "more_than_fifteen";
				die;
             }else if($back_dates){
                echo "back_days_LA_LE";
				die;
             }else if($diffcurrentdate->format("%R%a") > 90){
                echo "too_much_advance";
				die;
             }else if($found){
                echo "already_range";
				die;
             }/*else if(check_official_off($fromdate)){
                echo "official_off";
				die;
             }*/
            // end validation code 
            //Code for display count total taken leave and LA/LE               
            $fromdate_array= explode("-", $fromdate);
            $this_year_print= $fromdate_array[2];
            $this_month= $fromdate_array[1];
            $this_long_month= date("M",strtotime($fromdate));             
            $total_leave = total_leave_taken_current_month($dbh, $eid, $this_year_print, $this_month);
            $total_short_leave = total_shortleave_taken_current_month($dbh, $eid, $this_year_print, $this_month);
            //End code for display count total taken leave and LA/LE	
			$subject= "Application for updating ".$db_leave_type;
			$bcc= "";
			$cc= "";
			$lid_encoded= base64_encode(base64_encode(base64_encode($lid)));
			$m_approve= base64_encode(base64_encode(base64_encode("approved_ok")));
			$m_disapprove= base64_encode(base64_encode(base64_encode("disapproved_not_ok")));
			$m_disapprove_action_page= base64_encode(base64_encode(base64_encode("approve_0")));
			$email_body= "<table><tr><td colspan='3'><strong>Dear Sir/Madam,</strong><br><br>\r\n";
						$email_body.= "<strong>Kindly review my application regarding modification in leave.</strong><br><br>\r\n";
						$email_body.= "<strong>Earlier leave:</strong></td></tr>\r\n";
						$email_body.= "<tr><td align='left'>Leave Type:</td><td>$db_leave_type</td><td>&nbsp</td></tr>\r\n";
						$email_body.= "<tr><td align='left'>From:</td><td>$from_date</td><td>&nbsp</td></tr>\r\n";
						$email_body.= "<tr><td align='left'>To:</td><td>$to_date</td><td>&nbsp</td></tr>\r\n";
						$email_body.= "<tr><td colspan='2'><br><strong>Modified leave:</strong></td><td>&nbsp</td></tr>\r\n";
						$email_body.= "<tr><td align='left'>Leave Type:</td><td>$l_type</td><td>&nbsp</td></tr>\r\n";
						$email_body.= "<tr><td align='left'>From:</td><td>$l_from</td><td>&nbsp</td></tr>\r\n";
						$email_body.= "<tr><td align='left'>To:</td><td>$l_to</td><td>&nbsp</td></tr>\r\n";
            
                        $email_body.= "<tr><td align='left'><br>Total Leave Taken in $this_long_month $this_year_print:</td><td align='left'><br>$total_leave</td><td>&nbsp</td></tr>\r\n";
				        $email_body.= "<tr><td align='left'>Total LA/LE Taken in $this_long_month $this_year_print:</td><td align='left'>$total_short_leave/4</td><td>&nbsp</td></tr>\r\n";
            
						$email_body.= "<tr><td><br><a href='http://www.netsutra.com/leave/m_action/edit_action.php?lid=$lid_encoded&act=$m_approve' style='height: 30px; padding: 3px 7px 0 7px; text-decoration: none; font-weight: bold; color: #ffffff; border: 1px solid #D9D9D9; background: #2C5499;'>Approve Edit & Leave</a></td><td><br>&nbsp</td><td><br><a href='http://www.netsutra.com/leave/m_action/edit_action.php?lid=$lid_encoded&act=$m_disapprove' style='height: 30px; padding: 3px 7px 0 7px; text-decoration: none; font-weight: bold; color: #ffffff; border: 1px solid #D9D9D9; background: #2C5499;'>Disapprove Edit & Leave</a></td></tr>\r\n";
						$email_body.= "<tr><td colspan='2'><br><font face='verdana' size='-1'><strong>Thanks and Regards,</strong><br>$emp_name</font></td><td>&nbsp</td></tr></table>\r\n";
						//code to record histry
						date_default_timezone_set('Asia/Kolkata');
						$created_at= date("d-m-Y H:i:s");
                        if($updated_By_HR){
                            $status= "request for edit by HR";
                        } else {                            
				            $status= "request for edit";
                        }
						$sql="INSERT INTO tblleaves_history(leave_id,leave_type,from_date,to_date,created_at,status,emp_id,manager_id) VALUES(:lid,:l_type,:l_from,:l_to,:created_at,:status,:eid,:manager_id)";
						$query = $dbh->prepare($sql);
						$query->bindParam(':lid',$lid,PDO::PARAM_STR);
						$query->bindParam(':l_type',$l_type,PDO::PARAM_STR);
						$query->bindParam(':l_from',$l_from,PDO::PARAM_STR);
						$query->bindParam(':l_to',$l_to,PDO::PARAM_STR);
						$query->bindParam(':created_at',$created_at,PDO::PARAM_STR);
						$query->bindParam(':status',$status,PDO::PARAM_STR);
						$query->bindParam(':eid',$eid,PDO::PARAM_STR);
						$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
						$query->execute();
						$lastInsertId = $dbh->lastInsertId();
						if($lastInsertId){
						//end of history
						//code to update main table status
							$sql="update tblleaves set Status= 4, leave_order=2 where id=:lid";
							$query = $dbh->prepare($sql);
							$query->bindParam(':lid',$lid,PDO::PARAM_STR);
							$query->execute();
						//end of updateing main table
							if(send_email($m_email, $emp_email, $bcc, $cc, $subject, $email_body))
								echo "sent";
							else
								echo "not_sent";
						}else{
							echo "error";
						}
		}else{
			echo "error";
		}
?>