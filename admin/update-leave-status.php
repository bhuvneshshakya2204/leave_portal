<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../shared/email-template.php');
include('../includes/custom_functions.php');
$output = array('msg' => 'Unauthorized Access','status' => 'ERROR' ); 
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	echo json_encode($output);
	die();
}

date_default_timezone_set('Asia/Kolkata');
$userId= $_SESSION['eid'];

$action = $_POST['action'];
$leaveId= base64_decode(base64_decode($_POST['leaveId']));
$leave = getLeave($leaveId);


if($action == 'update-date'){
	$validate = validateDateUpdation($leave, $_POST);
	if($validate['status'] == 'ERROR'){
		echo json_encode($validate);
		die();
	}
	$success = updateLeaveDate($leave, $_POST);
	if($success){
		echo json_encode(array('msg' => 'Leave updated successfully','status' => 'SUCCESS' ));
		die();
	}
	echo json_encode(array('msg' => 'Updating leave failed... Try again later.','status' => 'ERROR' ));
	die();
}

if($action == 'cancel'){
	$validate = validateLeaveCancelation($leave);
	if($validate['status'] == 'ERROR'){
		echo json_encode($validate);
		die();
	}
	$success = cancelLeave($leave);
	if($success){
		echo json_encode(array('msg' => 'Leave canceled successfully','status' => 'SUCCESS' ));
		die();
	}
	echo json_encode(array('msg' => 'Canceling leave failed... Try again later.','status' => 'ERROR' ));
	die();
}











function validateLeaveCancelation($leave){
	if($leave->Status == '3')
		return array('msg' => 'Leave is already canceled','status' => 'ERROR' );

	if(strlen($_SESSION['hrlogin'])==0)
		return array('msg' => 'Unauthorized Access','status' => 'ERROR' );

	return array('msg' => 'ok','status' => 'SUCCESS' );
	
}

function validateDateUpdation($leave, $params){
	$leaveDetail = getLatestLeaveDetail($leave->id);
	$l_type = $params['leaveType'];
	$l_from = $params['fDate'];
	$l_to = $params['toDate'];
	$today= date("d-m-Y");
	
	$isLALE = (strtolower($l_type) =='leaving early (le)' || strtolower($l_type) =='late arrival (la)') ? true : false;

	if($l_type == $leaveDetail->leave_type && $l_from == $leaveDetail->from_date && $l_to == $leaveDetail->to_date)
		return array('msg' => 'Sorry!! You can\'t sent edit request for same leave','status' => 'ERROR' );

	if(isLeaveExistInDateRange($params['fDate'],$params['toDate'],$leave))
		return array('msg' => 'Sorry!! you already have a leave in this range','status' => 'ERROR' );

	$diff =  dateDiff($l_from, $l_to);
	$diffcurrentdate =  dateDiff($today, $l_from);
	if($diff->format("%R%a") < 0)
        return array('msg' => 'Sorry!! `To Date` should be greater than `From Date`','status' => 'ERROR' );

     if($l_from == "" || $l_to == "")
        return array('msg' => 'Sorry!! Please fill out all the fields properly','status' => 'ERROR' );

    if( $isLALE && $diffcurrentdate->format("%R%a")< -3)
        return array('msg' => 'Sorry!! more than three back days are not allowed for LA/LE','status' => 'ERROR' );
    
    if( !$isLALE && $diffcurrentdate->format("%R%a") > 90)
        return array('msg' => 'Sorry!! you can not apply leave in too much advance 90 days','status' => 'ERROR' );

    if(!$isLALE){
    	$start_date = new DateTime(date('d-m-Y',strtotime($l_from)));
        $since_start = $start_date->diff(new DateTime());
        $start_date1 = new DateTime(date('d-m-Y',strtotime($l_to)));
        $since_start1 = $start_date1->diff(new DateTime());
        $total_diff_days= $since_start->days;
        $total_diff_days1= $since_start1->days;
        // if($total_diff_days > 15 || $total_diff_days1 > 15)
        // 	return array('msg' => 'Sorry!! more than fifteen back days are not allowed for leave','status' => 'ERROR' );
    }

    if(check_official_off(date('d-m-Y',strtotime($l_from))))
    	return array('msg' => 'Sorry!! you can not apply leave on official off','status' => 'ERROR' );

    return array('msg' => 'OK', 'status' => 'SUCCESS' );
	
}

function getLeave($leaveId){
	global $dbh;
	$sql = "SELECT * from tblleaves WHERE id = ".$leaveId;
	$query = $dbh -> prepare($sql);	
	$query->execute();
	return $query->fetch(PDO::FETCH_OBJ);
}

function getLatestLeaveDetail($leaveId){
	global $dbh;
	$sql = "select * from tblleaves_history where leave_id=$leaveId AND id IN (
			    SELECT MAX(id)
			    FROM tblleaves_history where leave_id=$leaveId
			)";
	$query = $dbh -> prepare($sql);
	$query->execute();
	return $query->fetch(PDO::FETCH_OBJ);
}

function getEmployeeDetail($empId){
	global $dbh;
	$sql = "select e.*,e1.FirstName as mFirstName,e1.LastName as mLastName,
			e1.EmailId as ManagerEmail from tblemployees e 
			LEFT JOIN tblemployees e1 ON e.reporting_manager = e1.id 
			where e.id=$empId";
	$query = $dbh -> prepare($sql);
	$query->execute();
	return $query->fetch(PDO::FETCH_OBJ);
}

function isLeaveExistInDateRange($fromdate,$todate,$leave){
	global $dbh;
	$sql= "select * from tblleaves where (FromDate='$fromdate' or ToDate='$fromdate' or FromDate='$todate') and 
		  empid=$leave->empid and  ManagerID=$leave->ManagerID and Status not in (3) and id not in ($leave->id)";
    $query = $dbh -> prepare($sql);
    $query->execute();
    if($query->rowCount() > 0)
        return true;
    else
    	return false;
}

function cancelLeave($leave){
	global $dbh;
	$created_at= date("d-m-Y H:i:s");
	$lid = $leave->id;
	$leaveDetail = getLatestLeaveDetail($lid);
	$l_type = $leaveDetail->leave_type;
	$l_from = $leaveDetail->from_date;
	$l_to = $leaveDetail->to_date;
	$status_updated= "Cancelled by HR";
	$emp_id= $leaveDetail->emp_id;
	$manager_id= $leaveDetail->manager_id;

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
	$lastInsertId = $dbh->lastInsertId();
	if($lastInsertId){
		$sql= "update tblleaves set Status=3, leave_order=6 where empid=$emp_id and ManagerID=$manager_id and id=$lid";
		$query = $dbh->prepare($sql);
		$query->execute();
		emailLeaveCancellation($leave,$leaveDetail);
		return true;
	}
	return false;
}

function updateLeaveDate($leave, $param){
	global $dbh;
	$preLeaveDetail = getLatestLeaveDetail($leave->id);
	$status = 'request for edit by HR';
	$sql="INSERT INTO tblleaves_history(leave_id,leave_type,from_date,to_date,created_at,status,emp_id,manager_id) VALUES(:lid,:l_type,:l_from,:l_to,:created_at,:status,:eid,:manager_id)";
	$query = $dbh->prepare($sql);
	$query->bindParam(':lid',$leave->id,PDO::PARAM_STR);
	$query->bindParam(':l_type',$param['leaveType'],PDO::PARAM_STR);
	$query->bindParam(':l_from',$param['fDate'],PDO::PARAM_STR);
	$query->bindParam(':l_to',$param['toDate'],PDO::PARAM_STR);
	$query->bindParam(':created_at',date("d-m-Y H:i:s"),PDO::PARAM_STR);
	$query->bindParam(':status',$status,PDO::PARAM_STR);
	$query->bindParam(':eid',$leave->empid,PDO::PARAM_STR);
	$query->bindParam(':manager_id',$leave->ManagerID,PDO::PARAM_STR);
	$query->execute();
	$lastInsertId = $dbh->lastInsertId();
	if($lastInsertId){
		$sql="update tblleaves set Status= 4, leave_order=2 where id=$leave->id";
		$query = $dbh->prepare($sql);
		$query->execute();
		emailLeaveDateUpdation($leave,$preLeaveDetail,$param);
		return true;		
	}else{
		return false;
	}
}

/* Leave cancelation email goes to employee as well as reporting manager */
function emailLeaveCancellation($leave,$leaveDetail){
	global $dbh;
	$modify_posting_date= new DateTime($leave->PostingDate);
	$modify_posting_date= $modify_posting_date->format("d-m-Y H:i:s");
    //Code for display count total taken leave and LA/LE                            
    $fromdate_array= explode("-", $leaveDetail->from_date);
    $this_year_print= $fromdate_array[2];
    $this_month= $fromdate_array[1];
    $total_leave = total_leave_taken_current_month($dbh, $leaveDetail->emp_id, $this_year_print, $this_month);
    $total_short_leave = total_shortleave_taken_current_month($dbh, $leaveDetail->emp_id, $this_year_print, $this_month);
    //End code for display count total taken leave and LA/LE

	$empDetail = getEmployeeDetail($leaveDetail->emp_id);

	$arr = array(
		'EMP_NAME' 		=> $empDetail->FirstName." ".$empDetail->LastName, 
		'LEAVE_TYPE'	=> $leaveDetail->leave_type,
		'DATE_FROM'		=> $leaveDetail->from_date,
		'DATE_TO'		=> $leaveDetail->to_date,
		'DISPLAY_STATUS'=> 'cancelled by HR',
		'LBL_MNT_YR'	=> date("M",strtotime($leaveDetail->from_date))." ".$this_year_print,
		'LEAVE_COUNT'	=> $total_leave,
		'LA_LE_COUNT'	=> $total_short_leave.'/4',
		'POSTING_DATE' 	=> $modify_posting_date,
		'SIGNATURE_NAME'=> $_SESSION['hrlogin']
	);
	$email_body = getEmailContent($arr,EMAIL_CANCEL_LEAVE);

	$subject= "Re: $leaveDetail->leave_type from $leaveDetail->from_date to $leaveDetail->to_date is ".$arr['DISPLAY_STATUS'];
	$bcc= $empDetail->ManagerEmail;
	$cc= "";
	send_email($empDetail->EmailId, $_SESSION['hrlogin'], $bcc, $cc, $subject, $email_body);
}

function emailLeaveDateUpdation($leave,$preLeaveDetail,$param){
	global $dbh,$siteURL;
	$fromdate_array= explode("-", $param['fDate']);
    $this_year_print= $fromdate_array[2];
    $this_month= $fromdate_array[1];
    $total_leave = total_leave_taken_current_month($dbh, $preLeaveDetail->emp_id, $this_year_print, $this_month);
    $total_short_leave = total_shortleave_taken_current_month($dbh, $preLeaveDetail->emp_id, $this_year_print, $this_month);

    $empDetail = getEmployeeDetail($leave->empid);
    $lid_encoded= base64_encode(base64_encode(base64_encode($leave->id)));
	$m_approve= base64_encode(base64_encode(base64_encode("approved_ok")));
	$m_disapprove= base64_encode(base64_encode(base64_encode("disapproved_not_ok")));

    $arr = array(
		'EMP_EMAIL' 		=> $empDetail->EmailId, 
		'PRE_LEAVE_TYPE'	=> $preLeaveDetail->leave_type,
		'PRE_DATE_FROM'		=> $preLeaveDetail->from_date,
		'PRE_DATE_TO'		=> $preLeaveDetail->to_date,
		'LEAVE_TYPE'		=> $param['leaveType'],
		'DATE_FROM'			=> $param['fDate'],
		'DATE_TO'			=> $param['toDate'],
		'LBL_MNT_YR'		=> date("M",strtotime($param['fDate']))." ".$this_year_print,
		'LEAVE_COUNT'		=> $total_leave,
		'LA_LE_COUNT'		=> $total_short_leave.'/4',
		'LINK_APPROVE'		=> $siteURL.'m_action/edit_action.php?lid=$lid_encoded&act=$m_approve',
		'LINK_DISAPPROVE'	=> $siteURL.'m_action/edit_action.php?lid=$lid_encoded&act=$m_disapprove',
		'SIGNATURE_NAME'	=> $empDetail->FirstName." ".$empDetail->LastName
	);
	$email_body = getEmailContent($arr,EMAIL_LEAVE_DATE_UPDATION);

	$subject= "Application for updating Leave";
	$bcc= "";
	$cc= "";
	send_email($empDetail->ManagerEmail, $empDetail->EmailId, $bcc, $cc, $subject, $email_body);

}

function dateDiff($dtFrom, $dtTo){
	$fromdate = date_create(date('d-m-Y',strtotime($dtFrom)));
	$todate = date_create(date('d-m-Y',strtotime($dtTo)));
	return date_diff($fromdate, $todate);
}


?>
