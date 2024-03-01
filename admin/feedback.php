<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../includes/send_email.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0 && strlen($_SESSION['emplogin'])==0)
{   
	header('location: ../index.php');
}
else{
	$email_flag= 0;
	$email_error= 0;
// Code for sending feedback
if(isset($_POST['submit']))
{
	$eid=$_SESSION['eid'];
	$feedback= $_POST['feedback_textarea'];
	$sql = "SELECT FirstName,LastName,EmailId from tblemployees where id=:eid";
	$query = $dbh -> prepare($sql);
	$query->bindParam(':eid',$eid,PDO::PARAM_STR);
	$query->execute();
	$results_feedback= $query->fetchAll(PDO::FETCH_OBJ);
	foreach($results_feedback as $val_feedback){
		$name= htmlentities($val_feedback->FirstName." ".$val_feedback->LastName);
		$from= $val_feedback->EmailId;
		$to= "gurjeet.singh@netsutra.com,dipak.kumar@netsutra.com,kriti@netsutra.com";
		$bcc= "";
		$cc= "";
		$subject= "Feedback from ".$name;
		$msg= "<strong>Hi,</strong><br /><br />\r\n";
		$msg.= $feedback."\r\n";
		$msg.= "<br /><br /><span style='font-weight: bold;'>Regards,</span><br />\r\n";
		$msg.= $name."\r\n";
		if(send_email($to, $from, $bcc, $cc, $subject, $msg))
			$email_flag= " Feedback sent successfully";
		else
			$email_error= " Something went wrong, please try again";
	}
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Feedback</title>
        
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
		.red_border{
			border: 2px solid red;
		}
		</style>
    </head>
    <body>
  <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
            <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">Give your feedback</div>
                    </div>
                    <div class="col s12 m12 l9">
                        <div class="card">
                            <div class="card-content">
                              <h3>Give your feedback</h3><br><br>
                                <div class="row">
								 <?php if($email_error){?><div class="errorWrap"><strong>ERROR </strong>:<?php echo htmlentities($email_error); ?> </div><?php } 
								 else if($email_flag){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($email_flag); ?> </div><?php }?>
                                    <form class="col s12" name="feedback_form" method="post" action="" onSubmit="return check_feedback()">
                                        <div class="row">
                                            <div class="input-field col s12">
<!--<input id="password" type="password"  class="validate" autocomplete="off" name="password"  required>-->
<textarea style="height: 200px!important; margin-top: 3px;" name="feedback_textarea" id="feedback_textarea"></textarea>
                                                <label for="password" style="margin-left: 10px;">Feedback</label>
                                            </div>


<div class="input-field col s12">
<button type="submit" name="submit" class="waves-effect waves-light btn indigo m-b-xs" onclick="return valid();">Submit</button>

</div>




                                        </div>
                                       
                                    </form>
                                </div>
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
		<script>
		$("#feedback_textarea").focus(function(){
			$("#feedback_textarea").removeClass("red_border");
		});
			function check_feedback(){
				if($("#feedback_textarea").val()== ""){
					$("#feedback_textarea").addClass("red_border");
					return false;
				}
				return true;
			}
		</script>
        
    </body>
</html>
<?php } ?> 