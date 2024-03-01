<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../includes/send_email.php');
$error= 0;
$msg= 0;
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
}
else{
	$eid=$_SESSION['eid'];
	$sql = "SELECT FirstName,LastName,EmpId,EmailId from  tblemployees where id=:eid";
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
	$manager_id= $_POST['manager'];
	$fromdate=$_POST['fromdate'];  
	$todate=$_POST['todate'];
	//$description=$_POST['description'];  
	$status=0;
	$isread=0;
	$fromdate= str_replace("/", "-", $fromdate);
	$fromdate = date('d-m-Y',strtotime($fromdate));
    $fromdate1 =  date_create($fromdate);
	$todate= str_replace("/", "-", $todate);
	$todate = date('d-m-Y',strtotime($todate));
    $todate1 =  date_create($todate);
	$diff =  date_diff($fromdate1, $todate1);
	if($diff->format("%R%a") < 0){
		$error=" ToDate should be greater than FromDate";
	 }else if($_POST['leavetype']== "" || $_POST['manager']== "" || $_POST['fromdate']== "" || $_POST['todate']== ""){
		 $error=" Please fill out all the fields properly";
	 }else{
		$sql="INSERT INTO tblleaves(LeaveType,ToDate,FromDate,Status,IsRead,empid,ManagerID) VALUES(:leavetype,:todate,:fromdate,:status,:isread,:empid,:manager_id)";
		$query = $dbh->prepare($sql);
		$query->bindParam(':leavetype',$leavetype,PDO::PARAM_STR);
		$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
		$query->bindParam(':todate',$todate,PDO::PARAM_STR);
		//$query->bindParam(':description',$description,PDO::PARAM_STR);
		$query->bindParam(':status',$status,PDO::PARAM_STR);
		$query->bindParam(':isread',$isread,PDO::PARAM_STR);
		$query->bindParam(':empid',$empid,PDO::PARAM_STR);
		$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
		$query->execute();
		$lastInsertId = $dbh->lastInsertId();
		if($lastInsertId)
		{
			$emp_name= htmlentities($result->FirstName." ".$result->LastName);
			$email_body= "<table><tr><td colspan='2'><strong>Dear Sir/Madam,</strong><br><br>\r\n";
			$email_body.= "Please find my leave details:</td></tr>\r\n";
			$email_body.= "<tr><th align='left'>Name of the Employee:</th><td align='left'>$emp_name</td></tr>\r\n";
			$email_body.= "<tr><th align='left'>Leave Type:</th><td align='left'>$leavetype</td></tr>\r\n";
			$email_body.= "<tr><th align='left'>From Date:</th><td align='left'>$fromdate</td></tr>\r\n";
			$email_body.= "<tr><th align='left'>To Date:</th><td align='left'>$todate</td></tr>\r\n";
			$email_body.= "<tr><td colspan='2'><br><font face='verdana' size='-1' color='navy'><strong>Thanks and Regards,</strong><br>$emp_name</font></td></tr></table>\r\n";
			$to= "gurjeet.singh@netsutra.com,dipak.kumar@netsutra.com,dipak123@yopmail.com";
			$from= $result->EmailId;
			$bcc= "";
			$cc= "";
			$subject= "Application for Leave/LA/LE";
			send_email($to, $from, $bcc, $cc, $subject, $email_body);
			$msg=" Leave applied successfully";
		}
		else 
		{
			$error=" Something went wrong. Please try again";
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
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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

<div class="input-field col s12">
	<span style="color: #7F9FAF; font-family: Roboto, sans-serif; font-size: 11px;">Name of the Employee</span>
	<input type="text" name="employee_name_text" id="employee_name_text" value="<?php echo htmlentities($result_emp->FirstName." ".$result_emp->LastName);?>" readonly="readonly" style="color: #7F9FAF; font-family: Roboto, sans-serif; font-weight: 500; font-size: 14px; border-bottom: 1px solid #BCBCBC;" />
</div>
 <div class="input-field col  s12">
<select  name="leavetype" autocomplete="off">
<option value="" selected>Select Leave Type...</option>
<?php $sql = "SELECT  LeaveType from tblleavetype";
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
<?php $sql = "SELECT  FirstName, LastName, id from tblemployees where UserType= 'manager'";
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

<div class="input-field col m6 s12">
<label for="fromdate" id="from_date">From  Date</label>
<input placeholder="" id="mask1" name="fromdate" class="masked masked1 from_date" type="text" data-inputmask="'alias': 'date'">
</div>
<div class="input-field col m6 s12">
<label for="todate" id="to_date">To Date</label>
<input placeholder="" id="mask1" name="todate" class="masked to_date" type="text" data-inputmask="'alias': 'date'">
</div>
<!--<div class="input-field col m12 s12">
<label for="birthdate">Description</label>    

<textarea id="textarea1" name="description" class="materialize-textarea" length="500" required></textarea>
</div>-->
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
        <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
        <script src="../assets/js/pages/form_elements.js"></script>
          <script src="../assets/js/pages/form-input-mask.js"></script>
                <script src="../assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
				<script>
				$('#leave_form').find('input,select,.select-dropdown').on('focus', function(){
					$(this).removeClass('red_border');
				});
				$('#leave_form').find('input,select,.select-dropdown').on('blur',function(){
					$(this).closest('input, select, .select-dropdown').each(function(){
					var inputval = $(this).val();
					if(inputval== null || inputval == "")
					{
						$(this).addClass('red_border');	
					}
				//});	
			});
	});	
					$(".from_date").hover(function(){
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
					});
					function checkValidation(){
						var flag= true;
						from_date= $(".from_date").val();
						from_date_array= from_date.split("/");
						var y_from= from_date_array[2];
						var d = new Date();
						var y_current = d.getFullYear();
						if(from_date.length < 10 || from_date.includes("m") || from_date.includes("y") || y_from < y_current || y_from > y_current+1){
							$(".from_date").addClass("red_border");
							flag= false;
						}else{
							$(".from_date").removeClass("red_border");
						}
						to_date= $(".to_date").val();
						to_date_array= to_date.split("/");
						var y_to= to_date_array[2];
						if(to_date.length < 10 || to_date.includes("m") || to_date.includes("y") || y_to < y_current || y_to > y_current+1){
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
						if(flag)
							return true;
						else
							return false;
					}
				</script>
    </body>
</html>
<?php } ?> 