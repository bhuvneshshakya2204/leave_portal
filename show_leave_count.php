<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('includes/custom_functions.php');
$y_from= $_POST['y_from'];
$m_from= $_POST['m_from'];
$leave_type= $_POST['leave_type'];
$eid=$_SESSION['eid'];
if ($eid)
{   $countLeave = 0;
	if($leave_type== "Late Arrival (LA)" || $leave_type== "Leaving Early (LE)"){
        
        $countLeave = total_shortleave_taken_current_month($dbh, $eid, $y_from, $m_from);
            
    } else {
        
        $countLeave = total_leave_taken_current_month($dbh, $eid, $y_from, $m_from);
    }
 echo $countLeave;
    
} else {
    
	echo "invalid";
}
?>