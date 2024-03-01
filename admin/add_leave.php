<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../includes/send_email.php');
$error= 0;
$msg= 0;
if(strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
}
else{
	date_default_timezone_set('Asia/Kolkata');
	$eid=$_SESSION['eid'];
	$sql = "SELECT id,FirstName,LastName,EmpId,EmailId from  tblemployees";
	$query = $dbh -> prepare($sql);
	//$query->bindParam(':eid',$eid,PDO::PARAM_STR);
	$query->execute();
	$results_emp=$query->fetchAll(PDO::FETCH_OBJ);
	//$cnt=1;
	//foreach($results_emp as $result_emp){}
	
if(isset($_POST['apply']))
{
	//$empid=$_SESSION['eid'];
	$empid= $_POST['employee_id'];
	//codes start to count total leave/LA/LE
	$this_year_print= date("Y");
	$this_month= date("m");
	$this_long_month= date("M");
	$current_date= date("Y-m-d");
	$sql= "SELECT LeaveType, COUNT(LeaveType) AS LeaveCount FROM `tblleaves` WHERE YEAR(str_to_date(ToDate, '%d-%m-%Y')) = '".$this_year_print."' and LeaveType = 'Leave' and Status= 1 and str_to_date(ToDate, '%d-%m-%Y') < '$current_date' and empid= '".$empid."' GROUP BY(LeaveType)
	UNION
	SELECT LeaveType, COUNT(LeaveType) AS LeaveCount FROM `tblleaves` WHERE MONTH(str_to_date(ToDate, '%d-%m-%Y')) = '".$this_month."' and (LeaveType = 'Late Arrival (LA)' or LeaveType = 'Leaving Early (LE)') and Status= 1 and str_to_date(ToDate, '%d-%m-%Y') < '$current_date' and empid= '".$empid."' GROUP BY(LeaveType)";
	$query = $dbh -> prepare($sql);
	$query->execute();
	$rs_taken= $query->fetchAll(PDO::FETCH_OBJ);
	//$cnt=1;
	$total_leave= 0;
	$total_short_leave= 0;
	foreach($rs_taken as $leave_taken){
			if($leave_taken->LeaveType== "Leave"){
				$total_leave= $leave_taken->LeaveCount;
			}else{
				$total_short_leave+= $leave_taken->LeaveCount;
			}
	}
	//End of codes to count leave
	$leavetype=$_POST['leavetype'];
	$manager_id= $_POST['manager'];
	$fromdate=$_POST['fromdate'];  
	$todate=$_POST['todate'];
	
	$fromdate= str_replace("/", "-", $fromdate);
	$fromdate = date('d-m-Y',strtotime($fromdate));
    $fromdate1 =  date_create($fromdate);
	$todate= str_replace("/", "-", $todate);
	$todate = date('d-m-Y',strtotime($todate));
    $todate1 =  date_create($todate);
	//code to check the leaves for already exists
	$found= 0;
		$sql= "select * from tblleaves where LeaveType='$leavetype' and FromDate='$fromdate' and ToDate='$todate' and empid=$empid and ManagerID=$manager_id and Status not in (3)";
		$query = $dbh -> prepare($sql);
		$query->execute();
		if($query->rowCount() > 0)
			$found= 1;
	//end of codes for already exists
	//$description=$_POST['description'];  
	
	//date_default_timezone_set('Asia/Kolkata');
	$posting_date= date("d-m-Y H:i:s");
	$posting_date_main= date("Y-m-d H:i:s");

	if(isset($_POST['posting_date']) && $_POST['posting_date'] != ""){
		$posting_date = str_replace("/", "-", $_POST['posting_date']);
		$posting_date= $posting_date . date(" H:i:s");
		$postingDateArr = explode("/",$_POST['posting_date']);
		$posting_date_main = $postingDateArr[2]."-".$postingDateArr[1]."-".$postingDateArr[0].date(" H:i:s");
	}

	// $fromdate= str_replace("/", "-", $fromdate);
	// $fromdate = date('d-m-Y',strtotime($fromdate));
    // $fromdate1 =  date_create($fromdate);
	// $todate= str_replace("/", "-", $todate);
	// $todate = date('d-m-Y',strtotime($todate));
    // $todate1 =  date_create($todate);
	$diff =  date_diff($fromdate1, $todate1);
	//back dates allowing codes start
	$today= date("d-m-Y");
	//$expire= "23-08-2017";
	$today_dt = date_create($today);
	$expire_dt_from = date_create($fromdate);
	$expire_dt_to = date_create($todate);
	$total_diff_days= 0;
	$total_diff_days1= 0;
	$back_dates= 0;
	if($expire_dt_from < $today_dt || $expire_dt_to < $today_dt){
		if($leavetype== "Leave" || $leavetype== "Informed Leave (IL)" || $leavetype== "Casual Leave (CL)"){
			$start_date = new DateTime($fromdate);
			$since_start = $start_date->diff(new DateTime());
			$start_date1 = new DateTime($todate);
			$since_start1 = $start_date1->diff(new DateTime());
			$total_diff_days= $since_start->days;
			$total_diff_days1= $since_start1->days;
		}else{
			$back_dates= 1;
		}
	}
	/////end of back days codes
	$fromdate_array= explode("-", $fromdate);
	$todate_array= explode("-", $todate);
	$this_year= date("Y")+1;
	if($diff->format("%R%a") < 0){
		$error=" ToDate should be greater than FromDate";
	 }else if($_POST['leavetype']== "" || $_POST['manager']== "" || $_POST['fromdate']== "" || $_POST['todate']== ""){
		 $error=" Please fill out all the fields properly";
	 }else if(false){
		 $error=" Sorry!! more than three back dates are not allowed for leave";
	 }else if(false){
		 $error=" Sorry!! back dates are not allowed for LA/LE";
	 }else if(false){
		 $error=" Sorry!! you can not apply leave in too much advance";
	 }else if($found){
		 $error=" Sorry!! this leave already exists";
	 }else{
	 	if($leavetype== "Leave"){
	 		$status=0;
			$isread=0;
			$isread_manager=0;
		 	$leave_order= 1;
	 	}else{
	 		$status=(isset($_POST['isApprovalRequired']) && $_POST['isApprovalRequired'] == '1') ? 0 : 1; 
			$isread=(isset($_POST['isApprovalRequired']) && $_POST['isApprovalRequired'] == '1') ? 0 : 1;
			$isread_manager=(isset($_POST['isApprovalRequired']) && $_POST['isApprovalRequired'] == '1') ?  0 : 1;
			$leave_order= (isset($_POST['isApprovalRequired']) && $_POST['isApprovalRequired'] == '1') ? 1 : 4.5;
	 	}
		if($leavetype== "Leave")
			$sql="INSERT INTO tblleaves(LeaveType,ToDate,FromDate,PostingDate,Status,leave_order,IsRead,IsRead_Manager,empid,ManagerID) VALUES(:leavetype,:todate,:fromdate,:posting_date_main,:status,:leave_order,:isread,:isread_manager,:empid,:manager_id)";
		else
			$sql="INSERT INTO tblleaves(LeaveType,ToDate,FromDate,PostingDate,AdminRemarkDate,Status,leave_order,IsRead,IsRead_Manager,empid,ManagerID) VALUES(:leavetype,:todate,:fromdate,:posting_date_main,:posting_date_main,:status,:leave_order,:isread,:isread_manager,:empid,:manager_id)";
		$query = $dbh->prepare($sql);
		$query->bindParam(':leavetype',$leavetype,PDO::PARAM_STR);
		$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
		$query->bindParam(':todate',$todate,PDO::PARAM_STR);
		$query->bindParam(':posting_date_main',$posting_date_main,PDO::PARAM_STR);
		$query->bindParam(':status',$status,PDO::PARAM_STR);
		$query->bindParam(':leave_order',$leave_order,PDO::PARAM_STR);
		$query->bindParam(':isread',$isread,PDO::PARAM_STR);
		$query->bindParam(':isread_manager',$isread_manager,PDO::PARAM_STR);
		$query->bindParam(':empid',$empid,PDO::PARAM_STR);
		$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
		$query->execute();
		$lastInsertId = $dbh->lastInsertId(); 
		if($lastInsertId)
		{
			//codes to maintain history
			$status_history= "leave added by hr";
			$sql="INSERT INTO tblleaves_history(leave_id,leave_type,from_date,to_date,created_at,status,emp_id,manager_id) VALUES(:lastInsertId,:leavetype,:fromdate,:todate,:posting_date,:status_history,:empid,:manager_id)";
			$query = $dbh->prepare($sql);
			$query->bindParam(':lastInsertId',$lastInsertId,PDO::PARAM_STR);
			$query->bindParam(':leavetype',$leavetype,PDO::PARAM_STR);
			$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
			$query->bindParam(':todate',$todate,PDO::PARAM_STR);
			$query->bindParam(':posting_date',$posting_date,PDO::PARAM_STR);
			$query->bindParam(':status_history',$status_history,PDO::PARAM_STR);
			$query->bindParam(':empid',$empid,PDO::PARAM_STR);
			$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
			$query->execute();
			$lastInsertId_history = $dbh->lastInsertId();
			// end of history
			if($lastInsertId_history){
				$lastInsertId= base64_encode(base64_encode(base64_encode($lastInsertId)));
				$m_approve= base64_encode(base64_encode(base64_encode("approve_1")));
				$m_disapprove= base64_encode(base64_encode(base64_encode("approve_0")));

				$sql = "SELECT id,FirstName,LastName,EmpId,EmailId from tblemployees where id= '".$empid."'";
				$query = $dbh -> prepare($sql);
				$query->execute();
				$results_emp1=$query->fetchAll(PDO::FETCH_OBJ);
				foreach($results_emp1 as $emp1){}
				$domain= explode("/", $_SERVER['REQUEST_URI']);
				if($domain[1]== "leave"){
					$action_path= "leave";
				}else{
					$action_path= "test_leave";
				}
				$emp_name= htmlentities($emp1->FirstName." ".$emp1->LastName);
				$email_body= "<table><tr><td colspan='2'><strong>Dear Sir/Madam,</strong><br><br>\r\n";
				$email_body.= "Please find my leave details:</td></tr>\r\n";
				$email_body.= "<tr><th align='left'>Name:</th><td align='left'>$emp_name</td></tr>\r\n";
				$email_body.= "<tr><th align='left'>Leave Type:</th><td align='left'>$leavetype</td></tr>\r\n";
				$email_body.= "<tr><th align='left'>From Date:</th><td align='left'>$fromdate</td></tr>\r\n";
				$email_body.= "<tr><th align='left'>To Date:</th><td align='left'>$todate</td></tr>\r\n";
				$email_body.= "<tr><th align='left'><br>Total Leave Taken in $this_year_print:</th><td align='left'><br>$total_leave</td></tr>\r\n";
				$email_body.= "<tr><th align='left'>Total LA/LE Taken in $this_long_month $this_year_print:</th><td align='left'>$total_short_leave/4</td></tr>\r\n";
				$email_body.= "<tr><td><br><a href='http://www.netsutra.com/$action_path/m_action/action.php?lid=$lastInsertId&act=$m_approve' style='height: 30px; padding: 3px 7px 0 7px; text-decoration: none; font-weight: bold; color: #ffffff; border: 1px solid #D9D9D9; background: #2C5499;'>Approve</a></td><td><br><a href='http://www.netsutra.com/$action_path/m_action/action.php?lid=$lastInsertId&act=$m_disapprove' style='height: 30px; padding: 3px 7px 0 7px; text-decoration: none; font-weight: bold; color: #ffffff; border: 1px solid #D9D9D9; background: #2C5499;'>Disapprove</a></td></tr>\r\n";
				$email_body.= "<tr><td colspan='2'><br><font face='verdana' size='-1'><strong>Thanks and Regards,</strong><br>$emp_name</font></td></tr></table>\r\n";
				/* manager email id starts*/
				$sql = "SELECT EmailId from  tblemployees where id=:manager_id";
				$query = $dbh -> prepare($sql);
				$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
				$query->execute();
				$results=$query->fetchAll(PDO::FETCH_OBJ);
				foreach($results as $result_manager){}
				/* manager email id ends */
				$to= $result_manager->EmailId;
				$from= $emp1->EmailId;
				$bcc= "";
				$cc= "";
				$subject= "Application for $leavetype";
				if($leavetype== "Leave" || (isset($_POST['isApprovalRequired']) && $_POST['isApprovalRequired'] == '1'))
					send_email($to, $from, $bcc, $cc, $subject, $email_body);
				$msg=" ".$leavetype." added successfully";
			}else{
				$error=" Opps!! Something went wrong, please try again";
			}
		}
		else 
		{
			$error=" Opps!! Something went wrong, please try again";
		}
	}
}

    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Add Leave/LA/LE</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet"> 
        <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
  <style>
        .errorWrap {
	color: red!important;
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.lable_style{
	font-size: 0.8rem;
    -webkit-transform: translateY(-140%);
    transform: translateY(-140%);
}
.label_up{
	color: #9e9e9e!important;
    position: absolute!important;
    top: -10px!important;
    font-size: 12px!important;
    cursor: text!important;
    transition: .2s ease-out!important;
}
.red_border{
	border-bottom: 2px solid red!important;
}
        </style>
 


    </head>
    <body>
  <?php include('includes/header.php');?> 
       <?php include('includes/sidebar.php');?>
   <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">Add Leave/LA/LE</div>
                    </div>
                    <div class="col s12 m12 l8">
                        <div class="card">
                            <div class="card-content">
                                <form id="leave_form" method="post" name="addemp" onSubmit="return checkValidation()">
                                    <div>
                                        <h3>Add Leave/LA/LE</h3>
                                        <section>
                                            <div class="wizard-content">
                                                <div class="row">
                                                    <div class="col m12">
                                                        <div class="row">
     <?php if($error){?><div class="errorWrap"><strong>ERROR </strong>:<?php echo htmlentities($error); ?> </div><?php } 
                else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
<!--code for total leave display-->
<?php
if(!$error && !$msg){ ?>
	<!--<div class="col m6 s12" style="position: relative; top: -20px;">
<span style="color: #000000; font-family: Roboto, sans-serif; font-size: 14px; font-weight: bold;">Total Leave Taken in <?php echo $this_year_print; ?>:</span>&nbsp;&nbsp;<span style="color: #000000; font-weight: normal;"><?php echo $total_leave; ?></span>
</div>
<div class="col m6 s12" style="position: relative; top: -20px;">
<span style="color: #000000; font-family: Roboto, sans-serif; font-size: 14px; font-weight: bold;">Total LA/LE Taken in <?php echo $this_long_month." ".$this_year_print; ?>:</span>&nbsp;&nbsp;<span style="color: #000000; font-weight: normal;"><?php echo $total_short_leave."/4"; ?></span>
</div>-->
<?php } ?>
<!--end of codes of toal leave display-->
<div class="input-field col  s12">
	<span class="label_up">Employee Name</span>
	<input id="nameFilter" name="nameFilter" value="<?php echo $_POST['nameFilter'] ?>">
	<input type="hidden" name="employee_id" id="empid" value="<?php echo $_POST['employee_id'] ?>">
</div>

 <div class="input-field col  s12">
<select  name="leavetype" id="leavetype" autocomplete="off" onChange="hideToDate(this.value)">
<option value="" selected>Select Leave Type...</option>
<?php $sql = "SELECT  LeaveType from tblleavetype where status=1";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{   ?>                                            
<option value="<?php echo htmlentities($result->LeaveType);?>"><?php echo htmlentities($result->LeaveType);?></option>
<?php }} ?>
</select>
</div>
<div class="input-field col  s12">
<select  name="manager" id="manager" style="border: 2px solid red!important;">
<option value="" selected>Select Manager...</option>
<?php $sql = "SELECT  FirstName, LastName, id from tblemployees where UserType= 'project manager' or UserType= 'director'";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{   ?>                                            
<option value="<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->FirstName)." ".htmlentities($result->LastName);?></option>
<?php }} ?>
</select>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <!--<link rel="stylesheet" href="/resources/demos/style.css">-->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
	  $( function() {
		$( ".datepicker_apply_leave" ).datepicker();
		$( ".datepicker_apply_leave" ).datepicker("option", "dateFormat", "dd/mm/yy");
		//$( "#format" ).on( "change", function() {
		 // $( "#datepicker" ).datepicker( "option", "dateFormat", $( this ).val() );
		//});
	  } );
</script>
<div class="input-field col m6 s12" id="changable_grid">
<span style="color: #7F9FAF; font-family: Roboto, sans-serif; font-size: 11px;" id="label_changable">From  Date</span>
<input type="text" id="" name="fromdate" class="datepicker_apply_leave from_date" size="30" readonly="readonly" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd/mm/yyyy" onChange="populateToDate(this.value)" />
</div>
<div class="input-field col m6 s12" id="to_date_hidable">
<span style="color: #7F9FAF; font-family: Roboto, sans-serif; font-size: 11px;">To Date</span>
<input type="text" id="" name="todate" class="datepicker_apply_leave to_date" size="30" readonly="readonly" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd/mm/yyyy" />
</div>
<!--<div class="input-field col m12 s12">
<label for="birthdate">Description</label>    

<textarea id="textarea1" name="description" class="materialize-textarea" length="500" required></textarea>
</div>-->

<div class="input-field col m6 s12">
<span style="color: #7F9FAF; font-family: Roboto, sans-serif; font-size: 11px;" >Posting  Date</span>
<input type="text" id="posting_date" name="posting_date" class="datepicker_apply_leave" size="30" readonly="readonly" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd/mm/yyyy" value="<?php echo date('d/m/Y')?>"/>
</div>

		<div class="input-field col m12 s12">
			<div id="divApprovalRequired" style="display: none;">
				<input id="isApprovalRequired" name="isApprovalRequired" type="checkbox" value="1">
				<label for="isApprovalRequired">Approval Required</label>
			</div>
			<button type="submit" name="apply" id="apply" class="waves-effect waves-light btn indigo m-b-xs" style="float: right;">Add</button>
		</div>
</div>
		
      
                                                   

                                                </div>
                                            </div>
                                        </section>
                                     
                                    
                                        </section>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="left-sidebar-hover"></div>
        
        <!-- Javascripts -->
        <!--<script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>-->
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
        <script src="../assets/js/pages/form_elements.js"></script>
          <!-- <script src="../assets/js/pages/form-input-mask.js"></script>
                <script src="../assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script> -->
				<script>
				$(document).ready(function(){
					$( "#posting_date" ).datepicker( "setDate" , "<?php echo date('d/m/Y')?>" );
				});
				$('#leave_form').find('select,.select-dropdown').on('focus', function(){
					$(this).removeClass('red_border');
				});
				$('#leave_form').find('select,.select-dropdown').on('blur',function(){
					$(this).closest('select, .select-dropdown').each(function(){
						var inputval = $(this).val();
						if(inputval== null || inputval == ""){
							$(this).addClass('red_border');	
						}
					});
				});	
					/*$(".from_date").hover(function(){
						$("#from_date").addClass("label_up");
					});
					$(".from_date").mouseout(function(){
						$("#from_date").removeClass("label_up");
					});
					$(".to_date").hover(function(){
						$("#to_date").addClass("label_up");
					});
					$(".to_date").mouseout(function(){
						$("#to_date").removeClass("label_up");
					});*/
					function hideToDate(leave_type_hidable){
						if(leave_type_hidable== "Late Arrival (LA)" || leave_type_hidable== "Leaving Early (LE)"){
							$("#to_date_hidable").hide();
							$("#changable_grid").removeClass("m6");
							$("#changable_grid").addClass("m12");
							$("#label_changable").html("Select Date");
							$("#divApprovalRequired").show();
						}else{
							$("#changable_grid").removeClass("m12");
							$("#changable_grid").addClass("m6");
							$("#label_changable").html("From Date");
							$("#to_date_hidable").show();
							$(".to_date").val("");
							$("#divApprovalRequired").hide();
						}
					}
					function populateToDate(value){
						if($("#leavetype").val()== "Late Arrival (LA)" || $("#leavetype").val()== "Leaving Early (LE)"){
							$( ".to_date" ).datepicker( "setDate" , value );
						}else{
							$( ".to_date" ).datepicker( "setDate" , "" );						
						}
					}
					function checkValidation(){
						var flag= true;
						from_date= $(".from_date").val();
						from_date_array= from_date.split("/");
						var y_from= from_date_array[2];
						var m_from= from_date_array[1];
						var d_from= from_date_array[0];
						//alert(m_from); return false;
						var d = new Date();
						var y_current = d.getFullYear();
						var m_current = d.getMonth();
						 m_current++;
						 var d_current= d.getDate();
						if(from_date.length < 10 || from_date.includes("m") || from_date.includes("y") || y_from > y_current+1){
							$(".from_date").addClass("red_border");
							flag= false;
						}else{
							$(".from_date").removeClass("red_border");
						}
						to_date= $(".to_date").val();
						to_date_array= to_date.split("/");
						var y_to= to_date_array[2];
						var m_to= to_date_array[1];
						var d_to= to_date_array[0];
						if(to_date.length < 10 || to_date.includes("m") || to_date.includes("y") || y_to > y_current+1){
							$(".to_date").addClass("red_border");
							flag= false;
						}else{
							$(".to_date").removeClass("red_border");
						}
						if($("#manager").val()== ""){
							$(".select-dropdown").addClass("red_border");
							flag= false;
						}else{
							$(".select-dropdown").removeClass("red_border");
						}
						if($("#leavetype").val()== ""){
							$(".select-dropdown").addClass("red_border");
							flag= false;
						}else{
							$(".select-dropdown").removeClass("red_border");
						}
						if(flag)
							return true;
						else
							return false;
					}

					$('#nameFilter').autocomplete({
						type: "POST",
						minLength: 3,
						source : function (request, response) 
						{                         
						    var source_url = "leaves-data.php?action=name";

						    $.ajax({
						        url: source_url,
						        dataType: "json",
						        data: request,
						        success: function (data) { response(data); },
						        error : function (a,b,c) { HandleLookUpError(a); }
						    });
						},                
						select: function (event, ui) { $('#empid').val(ui.item.id);getManager(); }               
					});
					function getManager(){
						var empid =  $('#empid').val();
						$.ajax({
					        url: "get-manager.php",
					        dataType: "json",
					        data: {empid:empid},
					        method:"POST",
					        success: function (res) { 
					        	$("#manager").val(res.manager.managerid);
					        	$("#manager").material_select();
					        },
					        error : function (a,b,c) { HandleLookUpError(a); }
					    });
					}



				</script>
    </body>
</html>
<?php } ?> 