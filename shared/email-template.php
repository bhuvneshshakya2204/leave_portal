<?php
require_once("../includes/config.php");

$urlArray = explode('/', $_SERVER['PHP_SELF']);
$rootFolder = array_search("test_leave",$urlArray) > 0 ? "test_leave" : "leave";
$siteURL = $_SERVER['HTTP_HOST'].'/'.$rootFolder."/";
$emailCancelLeave 		=  	'<p><strong>Dear #EMP_NAME#</strong></p><br>
							<p>Your #LEAVE_TYPE# from #DATE_FROM# to #DATE_TO# is #DISPLAY_STATUS#.</p><br>
							<p><strong>Total Leave Taken in #LBL_MNT_YR#: </strong> #LEAVE_COUNT#</p>
							<p><strong>Total LA/LE Taken in #LBL_MNT_YR#: </strong> #LA_LE_COUNT#</p>
							<p><strong>Posting Date: </strong>#POSTING_DATE#</p><br>
							<p><font face="verdana" size="-1"><strong>Thanks,</strong><br>#SIGNATURE_NAME#</font></p>';

$emailLeaveDateUpdation =	'<p><strong>Dear Sir/Madam,</strong></p>
							<p><strong>Kindly review my application regarding modification in leave.</strong></p>
							<table>
							<tr><td align="left"><strong>Earlier leave:</strong></td><td>&nbsp;</td></tr>
							<tr><td align="left">Leave Type:</td><td>#PRE_LEAVE_TYPE#</td></tr>
							<tr><td align="left">From:</td><td>#PRE_DATE_FROM#</td></tr>
							<tr><td align="left">To:</td><td>#PRE_DATE_TO#</td></tr>
							</table><br/>
							<table>
							<tr><td align="left"><strong>Modified leave:</strong></td><td>&nbsp;</td></tr>
							<tr><td align="left">Leave Type:</td><td>#LEAVE_TYPE#</td></tr>
							<tr><td align="left">From:</td><td>#DATE_FROM#</td></tr>
							<tr><td align="left">To:</td><td>#DATE_TO#</td></tr>
							</table><br/>
							<p><strong>Total Leave Taken in #LBL_MNT_YR#: </strong> #LEAVE_COUNT#</p>
							<p><strong>Total LA/LE Taken in #LBL_MNT_YR#: </strong> #LA_LE_COUNT#</p>
							<table>
							<tr><td><a href="#LINK_APPROVE#" style="padding: 10px; text-decoration: none; color: #fff; background: #2C5499;">Approve Edit & Leave</a></td><td><br>&nbsp</td><td><a href="#LINK_DISAPPROVE#" style="padding: 10px; text-decoration: none; color: #fff; background: #2C5499;">Disapprove Edit & Leave</a></td></tr>
							</table>
							<p><font face="verdana" size="-1"><strong>Thanks and Regards,</strong><br>#SIGNATURE_NAME#</font></p>';	

$emailMissPunchMgr 		=	'<p><strong>Dear Sir/Madam,</strong></p>
							<p>Please approve #EMP_NAME#\'s #MISS_PUNCH_TYPE# miss punch dated #DATE# Punch Time was #TIME#.</p>
							<table>
							<tr><td><a href="#LINK_APPROVE#" style="padding: 10px; text-decoration: none; color: #fff; background: #2C5499;">Approve Miss Punch</a></td><td><br>&nbsp</td><td><a href="#LINK_DISAPPROVE#" style="padding: 10px; text-decoration: none; color: #fff; background: #2C5499;">Disapprove Miss Punch</a></td></tr>
							</table>
							<p><font face="verdana" size="-1"><strong>Thanks and Regards,</strong><br>#SIGNATURE_NAME#</font></p>';	

$emailMissPunchEmp 		=	'<p><strong>Dear #EMP_NAME#,</strong></p>
							<p>Your Miss Punch #MISS_PUNCH_TYPE# Approval Request for #DATE# #TIME# has been sent to your Project Manager. You will receive the response shortly.</p>
							<p><font face="verdana" size="-1"><strong>Thanks and Regards,</strong><br>#SIGNATURE_NAME#</font></p>';								

$emailMissPunchApproval	=	'<p><strong>Hello #HR_NAME#,</strong></p>
							<p>Please find #EMP_NAME#\'s Miss Punch Request #STATUS#.</p>
							<p><strong>Date:</strong> #DATE#</p>
							<p><strong>Time:</strong> #TIME#</p>
							<p><strong>Miss Punch Type:</strong> #MISS_PUNCH_TYPE#</p>
							<p><font face="verdana" size="-1"><strong>Thanks and Regards,</strong><br>#SIGNATURE_NAME#</font></p>';	


$emailLeaveApproveDisApproveEmp 		=	'<p><strong>Dear #EMP_NAME#,</strong></p>
							<p>Your #LEAVE_TYPE# from #DATE_FROM# to #DATE_TO# is #DISPLAY_STATUS#.</p><br>
							<p><strong>Discription:</strong> #DISCRIPTION#</p><br>
							<p><strong>Total Leave Taken in #LBL_MNT_YR#: </strong> #LEAVE_COUNT#</p>
							<p><strong>Total LA/LE Taken in #LBL_MNT_YR#: </strong> #LA_LE_COUNT#</p>
							<p><strong>Posting Date: </strong>#POSTING_DATE#</p><br>
							<p><font face="verdana" size="-1"><strong>Thanks,</strong><br>#SIGNATURE_NAME#</font></p>';

define("EMAIL_CANCEL_LEAVE", $emailCancelLeave);
define("EMAIL_LEAVE_DATE_UPDATION", $emailLeaveDateUpdation);
define("EMAIL_MISS_PUNCH_MGR", $emailMissPunchMgr);
define("EMAIL_MISS_PUNCH_EMP", $emailMissPunchEmp);
define("EMAIL_MISS_PUNCH_APPROVAL", $emailMissPunchApproval);
define("EMAIL_LEAVE_APPROVAL_DISAPPROVAL", $emailLeaveApproveDisApproveEmp);

function getEmailContent($arr,$template){
	foreach ($arr as $key => $value) {
		$template = str_replace('#'.$key.'#', $value, $template);
	}
	return $template;
}

function send_email($to, $from, $bcc="", $cc="", $subject, $message){
	if($to == "" || $from == "")
		return(0);

	$headers = 'MIME-Version: 1.0'."\r\n";
	$headers.= 'Content-type: text/html; charset=UTF-8' . "\r\n";//charset=iso-8859-1
	$headers.= "From: $from \r\n" .  
	"Reply-To: $from \r\n" .  
	"X-Mailer: PHP/" . phpversion() . "\r\n";
		$headers.= "bcc:  $bcc" . "\r\n";
		$headers.= "cc:  $cc" . "\r\n";
	if(mail($to,$subject,$message,$headers))
		return(1);
	else
		return(0);
}

/*UNCOMMENT IF REQUIRED :: TEST DATA*/

// $lid_encoded= base64_encode(base64_encode(base64_encode(1)));
// $m_approve= base64_encode(base64_encode(base64_encode("approved_ok")));
// $m_disapprove= base64_encode(base64_encode(base64_encode("disapproved_not_ok")));

// $arr = array(
// 	'EMP_NAME' 		=> 'Rohit Kumar', 
// 	'LEAVE_TYPE'	=> 'Leave',
// 	'DATE_FROM'		=> '13-03-2019',
// 	'DATE_TO'		=> '14-03-2019',
// 	'DISPLAY_STATUS'=> 'cancelled by HR',
// 	'LBL_MNT_YR'	=> 'Mar 2019',
// 	'LEAVE_COUNT'	=> '7',
// 	'LA_LE_COUNT'	=> '2/4',
// 	'POSTING_DATE' 	=> '22-12-2018 14:06:00',
// 	'SIGNATURE_NAME'=> 'Avkash',
// 	'LINK_APPROVE'		=> '../m_action/edit_action.php?lid='.$lid_encoded.'&act='.$m_approve,
// 	'LINK_DISAPPROVE'	=> '../m_action/edit_action.php?lid='.$lid_encoded.'&act='.$m_disapprove,
// 	'MISS_PUNCH_TYPE'	=> 'in time',
// 	'DATE'			=>	'13-10-2018'
// 	);
// echo getEmailContent($arr,EMAIL_MISS_PUNCH);	
?>