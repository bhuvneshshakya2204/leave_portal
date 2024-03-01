<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../includes/send_email.php');
include('../includes/custom_functions.php');
$error= 0;
$msg= 0;
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
}
else{
	date_default_timezone_set('Asia/Kolkata');
	$eid=$_SESSION['eid'];
	$sql = "SELECT FirstName,LastName,EmpId,EmailId,reporting_manager from  tblemployees where id=:eid";
	$query = $dbh -> prepare($sql);
	$query->bindParam(':eid',$eid,PDO::PARAM_STR);
	$query->execute();
	$results=$query->fetchAll(PDO::FETCH_OBJ);
	//$cnt=1;
	foreach($results as $result_emp){}
	
if(isset($_POST['apply']))
{
	$empid=$_SESSION['eid'];
	$leavetype=$_POST['leavetype'];
	//$manager_id= $_POST['manager'];
	$manager_id= $result_emp->reporting_manager;
	$fromdate=$_POST['fromdate'];  
	$todate=$_POST['todate'];
	//$description=$_POST['description'];  
	$status=0;
	$isread=0;
	//date_default_timezone_set('Asia/Kolkata');
	$posting_date= date("d-m-Y H:i:s");
	$posting_date_main= date("Y-m-d H:i:s");
	$fromdate= str_replace("/", "-", $fromdate);
	$fromdate = date('d-m-Y',strtotime($fromdate));
    $fromdate1 =  date_create($fromdate);
	$todate= str_replace("/", "-", $todate);
	$todate = date('d-m-Y',strtotime($todate));
    $todate1 =  date_create($todate);
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
    $diffcurrentdate =  date_diff($today_dt, $fromdate1);
	//code to check the leaves for already exists
	$found= 0;
		/*$sql= "select * from tblleaves where LeaveType='$leavetype' and FromDate='$fromdate' and ToDate='$todate' and empid=$empid and ManagerID=$manager_id";*/
		$sql= "select * from tblleaves where (FromDate='$fromdate' or ToDate='$fromdate' or FromDate='$todate') and empid=$empid and ManagerID=$manager_id and Status not in (3)";
		$query = $dbh -> prepare($sql);
		$query->execute();
		if($query->rowCount() > 0)
			$found= 1;
	//end of codes for already exists
	if($expire_dt_from < $today_dt || $expire_dt_to < $today_dt){
		if($leavetype== "Leave" || $leavetype== "Informed Leave (IL)" || $leavetype== "Casual Leave (CL)"){
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
	//Code for display count total taken leave and LA/LE
	$fromdate_array= explode("-", $fromdate);
    $this_year_print= $fromdate_array[2];
    $this_month= $fromdate_array[1];
    $this_long_month= date("M",strtotime($fromdate));  
    $total_leave = total_leave_taken_current_month($dbh, $eid, $this_year_print, $this_month);
    $total_short_leave = total_shortleave_taken_current_month($dbh, $eid, $this_year_print, $this_month);
    //End code for display count total taken leave and LA/LE	
    
	if($diff->format("%R%a") < 0){
		$error=" ToDate should be greater than FromDate";
	 }else if($_POST['leavetype']== "" || $_POST['fromdate']== "" || $_POST['todate']== ""){
		 $error=" Please fill out all the fields properly";
	 }else if($total_diff_days > 15 || $total_diff_days1 > 15){
		 $error=" Sorry!! more than fifteen back days are not allowed for leave";
	 }else if($back_dates){
		 $error=" Sorry!! more than three back days are not allowed for LA/LE";
	 }else if($diffcurrentdate->format("%R%a") > 90){
		 $error=" Sorry!! you can not apply leave in too much advance 90 days";
	 }else if($found){
		 $error=" Sorry!! you already have a leave in this range";
	 }/*else if(check_official_off($fromdate)){
		 $error=" Sorry!! you can not apply leave on official off";
	 }*/else{
		$sql="INSERT INTO tblleaves(LeaveType,ToDate,FromDate,PostingDate,Status,IsRead,empid,ManagerID) VALUES(:leavetype,:todate,:fromdate,:posting_date_main,:status,:isread,:empid,:manager_id)";
		$query = $dbh->prepare($sql);
		$query->bindParam(':leavetype',$leavetype,PDO::PARAM_STR);
		$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
		$query->bindParam(':todate',$todate,PDO::PARAM_STR);
		$query->bindParam(':posting_date_main',$posting_date_main,PDO::PARAM_STR);
		$query->bindParam(':status',$status,PDO::PARAM_STR);
		$query->bindParam(':isread',$isread,PDO::PARAM_STR);
		$query->bindParam(':empid',$empid,PDO::PARAM_STR);
		$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
		$query->execute();
		$lastInsertId = $dbh->lastInsertId(); 
		if($lastInsertId)
		{
			//codes to maintain history
			$status_history= "leave applied";
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
				$emp_name= htmlentities($result_emp->FirstName." ".$result_emp->LastName);
				$email_body= "<table><tr><td colspan='2'><strong>Dear Sir/Madam,</strong><br><br>\r\n";
				$email_body.= "Please find my leave details:</td></tr>\r\n";
				$email_body.= "<tr><th align='left'>Name:</th><td align='left'>$emp_name</td></tr>\r\n";
				$email_body.= "<tr><th align='left'>Leave Type:</th><td align='left'>$leavetype</td></tr>\r\n";
				$email_body.= "<tr><th align='left'>From Date:</th><td align='left'>$fromdate</td></tr>\r\n";
				$email_body.= "<tr><th align='left'>To Date:</th><td align='left'>$todate</td></tr>\r\n";
				$email_body.= "<tr><th align='left'><br>Total Leave Taken in $this_long_month $this_year_print:</th><td align='left'><br>$total_leave</td></tr>\r\n";
				$email_body.= "<tr><th align='left'>Total LA/LE Taken in $this_long_month $this_year_print:</th><td align='left'>$total_short_leave/4</td></tr>\r\n";
				$email_body.= "<tr><td><br><a href='http://www.netsutra.com/leave/m_action/action.php?lid=$lastInsertId&act=$m_approve' style='height: 30px; padding: 3px 7px 0 7px; text-decoration: none; font-weight: bold; color: #ffffff; border: 1px solid #D9D9D9; background: #2C5499;'>Approve</a></td><td><br><a href='http://www.netsutra.com/leave/m_action/action.php?lid=$lastInsertId&act=$m_disapprove' style='height: 30px; padding: 3px 7px 0 7px; text-decoration: none; font-weight: bold; color: #ffffff; border: 1px solid #D9D9D9; background: #2C5499;'>Disapprove</a></td></tr>\r\n";
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
				$from= $result_emp->EmailId;
				$bcc= "";
				$cc= "";
				$subject= "Application for $leavetype";
				send_email($to, $from, $bcc, $cc, $subject, $email_body);
				$msg=" ".$leavetype." applied successfully";
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
        <title>Netsutra - Apply Leave</title>
        
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
                        <div class="page-title">Apply for Leave</div>
                    </div>
                    <div class="col s12 m12 l8">
                        <div class="card">
                            <div class="card-content">
                                <form id="leave_form" method="post" name="addemp" onSubmit="return checkValidation()">
                                    <div>
                                        <h3>Apply for Leave</h3>
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

<div class="input-field col s12">
	<span style="color: #7F9FAF; font-family: Roboto, sans-serif; font-size: 11px;">Name of the Employee</span>
	<input type="text" name="employee_name_text" id="employee_name_text" value="<?php echo htmlentities($result_emp->FirstName." ".$result_emp->LastName);?>" readonly="readonly" style="color: #7F9FAF; font-family: Roboto, sans-serif; font-weight: 500; font-size: 14px; border-bottom: 1px solid #BCBCBC;" />
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
<!-- <div class="input-field col  s12">
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
</div> -->
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <!--<link rel="stylesheet" href="/resources/demos/style.css">-->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
	  $( function() {
            var date = new Date();
            var currentMonth = date.getMonth();
            var currentDate = date.getDate();
            var currentYear = date.getFullYear();          
            $(".datepicker_apply_leave").datepicker({
                //minDate: new Date(currentYear, currentMonth-1, currentDate),
                maxDate: new Date(currentYear, currentMonth+3, currentDate)
            });
		   $( ".datepicker_apply_leave" ).datepicker();
		   $( ".datepicker_apply_leave" ).datepicker("option", "dateFormat", "dd/mm/yy");
	  });
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
<span id="displayLeaveCount"></span>
</div>
      <button type="submit" name="apply" id="apply" class="waves-effect waves-light btn indigo m-b-xs">Apply</button>                                             

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
          <script src="../assets/js/pages/form-input-mask.js"></script>
                <script src="../assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
				<script>
				jQuery.fn.preventDoubleSubmission = function() {

				var last_clicked, time_since_clicked;

				$(this).bind('submit', function(event){

				if(last_clicked) 
				  time_since_clicked = event.timeStamp - last_clicked;

				last_clicked = event.timeStamp;

				if(time_since_clicked < 5000)
				  return false;

				return true;
			  });   
			};
				$('#leave_form').preventDoubleSubmission();
				$('#leave_form').find('select,.select-dropdown').on('focus', function(){
					$(this).removeClass('red_border');
				});
				$('#leave_form').find('select,.select-dropdown').on('blur',function(){
					$(this).closest('select, .select-dropdown').each(function(){
					var inputval = $(this).val();
					if(inputval== null || inputval == "")
					{
						$(this).addClass('red_border');	
					}
				//});	
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
						}else{
							$("#changable_grid").removeClass("m12");
							$("#changable_grid").addClass("m6");
							$("#label_changable").html("From Date");
							$("#to_date_hidable").show();
							$(".to_date").val("");
						}
                        if($(".from_date").val() && $("#leavetype").val()) {                            
                            showCountFromDateMonth($("#leavetype").val(), $(".from_date").val());
                        }else{
                            $("#displayLeaveCount").html("");
                        }
					}
					function populateToDate(value){
						if($("#leavetype").val()== "Late Arrival (LA)" || $("#leavetype").val()== "Leaving Early (LE)"){
							$(".to_date").val(value);
						}else{
							$(".to_date").val("");
						}
						if($(".from_date").val() && $("#leavetype").val()) {                            
                            showCountFromDateMonth($("#leavetype").val(), $(".from_date").val());
                        }else{
                            $("#displayLeaveCount").html("");
                        }
					}                    
                    function showCountFromDateMonth(leaveType, fromDate){                     
                        
                        from_date_array= fromDate.split("/");
						var y_from= from_date_array[2];
						var m_from= from_date_array[1];
                        var month_array = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct", "Nov", "Dec"];
                    
                        $.ajax({
                            type: "post",
                            url: "../show_leave_count.php",
                            data: {y_from: y_from, m_from: m_from, leave_type: leaveType},
                            success: function(data){
                                //alert(data);
                                if(data!= "invalid"){                                  
                                    if(m_from < '10') {
                                        m_from = m_from.replace('0', '');
                                    }                                    
                                    if(leaveType== "Late Arrival (LA)" || leaveType== "Leaving Early (LE)"){ 
                                        var displayMessage = "Total LA/LE Taken in "+ month_array[m_from-1]+" "+ y_from+ ": "+data+"/4";
                                        
                                    } else {
                                        var displayMessage = "Total Leave Taken in "+ month_array[m_from-1]+" "+ y_from+ ": "+data; 
                                    }
                                    $("#displayLeaveCount").html(displayMessage);
                                }else{
                                    alert("Invalid data received, please try after some time.");
                                }
                            }
                        });
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
				</script>
    </body>
</html>
<?php } ?> 