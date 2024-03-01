
<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('includes/send_email.php');
if(!isset($_SESSION['auth'])){
	header("location: forgot-password.php");
	die;
}
// Code for change password
$msg= "";
if(isset($_POST['change']))
    {
$newpassword1= $_POST['newpassword'];
$newpassword=md5($newpassword1);
$empid=$_SESSION['empid'];

$con="update tblemployees set Password=:newpassword where id=:empid";
$chngpwd1 = $dbh->prepare($con);
$chngpwd1-> bindParam(':empid', $empid, PDO::PARAM_STR);
$chngpwd1-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
$chngpwd1->execute();

//codes strat for sending password change information

$email_body= "<table><tr><td><strong>Hello,</strong><br><br>\r\n";
$email_body.= "$newpassword1 is you new password.</td></tr>\r\n";
//$email_body.= "<tr><td align='left'>Have a good day!</td></tr>\r\n";
$email_body.= "<tr><td><br><font face='verdana' size='-1'><strong>Thanks</strong></font></td></tr></table>\r\n";
$to= $_SESSION['email_otp'];
//$to= "dipak.kumar@netsutra.com";
$from= "noreply@netsutra.com";
$bcc= "";
$cc= "";
$subject= "Netsutra password changed";
//send_email($to, $from, $bcc, $cc, $subject, $email_body);
//end of password change info
$msg=" Your Password has been changed succesfully";
session_destroy();
header("location: index.php");
die;
}

?><!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Password Recovery</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">        

        	
        <!-- Theme Styles -->
        <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/custom.css" rel="stylesheet" type="text/css"/>
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
.red_border{
	border-bottom: 2px solid red!important;
}
.error_color{
	color: red!important;
}
        </style>
        
    </head>
    <body oncontextmenu="return false">
        <div class="loader-bg"></div>
        <div class="loader">
            <div class="preloader-wrapper big active">
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-spinner-teal lighten-1">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mn-content fixed-sidebar">
            <header class="mn-header navbar-fixed">
                <nav class="cyan darken-1">
                    <div class="nav-wrapper row">
                        <section class="material-design-hamburger navigation-toggle">
                            <a href="#" data-activates="slide-out" class="button-collapse show-on-large material-design-hamburger__icon">
                                <span class="material-design-hamburger__layer"></span>
                            </a>
                        </section>
                        <div class="header-title col s3">      
                            <a href="http://www.netsutra.com"><img src="assets/images/Logo.png" alt="netsutra logo" style="margin-top: 3%;" /></a>
                        </div>
                      
                           
                        </form>
                     
                        
                    </div>
                </nav>
            </header>
            <?php include_once("includes/left_navigation.php"); ?>
            <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title"><h4>Password Recovery</h4></div>

                          <div class="col s12 m6 l8 offset-l2 offset-m3">
                              <div class="card white darken-1">
								  <!--template starts for change pass-->
									<div class="card-content">
									<div class="col s12 right-align m-t-sm" style="height: 20px; color: green;" id="msg_before_sent">
											
									</div>
                                      <span class="card-title" style="font-size:20px;">Change your password</span>
                                         <?php if($msg){?><div class="succWrap"><strong>Success </strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
                                       <div class="row">
                                           <form class="col s12" name="udatepwd" method="post" id="change_pass_form" onSubmit="return validate_pass();">
                                               <div class="input-field col s12">
                                                    <input id="password_new" type="password" name="newpassword" class="validate" autocomplete="off">
													<label for="password">New Password</label>
                                               </div>
                                               <div class="input-field col s12">
                                                   <input id="password_confirm" type="password" name="confirmpassword" class="validate" autocomplete="off">
													<label for="password">Confirm Password</label>
                                               </div>
                                               <div class="col s12 right-align m-t-sm">
                                                
                                                   <button type="submit" name="change" class="waves-effect waves-light btn indigo m-b-xs" onclick="return valid();">Change</button>
												   
                                               </div>
                                           </form>
                                      </div>
                                  </div>
								  <!--End of change pass template-->






                              </div>
                          </div>
                    </div>
                </div>
            </main>
            
        </div>
        <div class="left-sidebar-hover"></div>
        
        <!-- Javascripts -->
        <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="assets/js/alpha.min.js"></script>
        <script>
$(document).keydown(function (event) {
    if (event.keyCode == 123) { // Prevent F12
        return false;
    } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) { // Prevent Ctrl+Shift+I        
        return false;
    }
});
		$(document).ready(function(){
		$('#change_pass_form').find('input').on('focus', function(){
					$(this).removeClass('red_border');
				});
				$('#change_pass_form').find('input').on('blur',function(){
					$(this).closest('input').each(function(){
					var inputval = $(this).val();
					if(inputval== null || inputval == "")
					{
						$(this).addClass('red_border');	
					}
				//});	
			});
	});
	});
			function validate_pass(){
				new_pass= $("#password_new").val();
				confirm_pass= $("#password_confirm").val();
				flag= true;
				if(new_pass== ""){
					$("#password_new").addClass("red_border");
					flag= false;
				}
				if(confirm_pass== ""){
					$("#password_confirm").addClass("red_border");
					flag= false;
				}
				if(new_pass!= confirm_pass){
					$("#password_confirm").addClass("red_border");
					flag= false;
				}
				if(!flag){
					return false;
				}else{
					$("#msg_before_sent").html("Password changed successfully!!");
					setTimeout(function(){ return true; }, 1000);
				}
					
			}
		</script>
    </body>
</html>