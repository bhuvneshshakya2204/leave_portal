
<?php
session_start();
error_reporting(0);
include('includes/config.php');
//code for destroying session
if(isset($_SESSION['empid'])){
	$id= $_SESSION['empid'];
	$sql= "select created_time from tblotp where empid=:id order by created_time desc limit 1";
	$query= $dbh -> prepare($sql);
	$query-> bindParam(':id', $id, PDO::PARAM_STR);
	$query-> execute();
	$results=$query->fetchAll(PDO::FETCH_OBJ);
	foreach ($results as $result){}
	date_default_timezone_set("Asia/Kolkata");
	$created_time= $result->created_time;
	$current_time= date("Y-m-d H:i:s");
	//code start for getting difference in minutes
		$start_date = new DateTime($created_time);
		$diff_minutes = $start_date->diff(new DateTime($current_time));
	/*echo $diff_minutes->days.' days total<br>';
	echo $diff_minutes->y.' years<br>';
	echo $diff_minutes->m.' months<br>';
	echo $diff_minutes->d.' days<br>';
	echo $diff_minutes->h.' hours<br>';
	echo $diff_minutes->i.' minutes<br>';
	echo $diff_minutes->s.' seconds<br><br>';*/
	$minutes = $diff_minutes->days * 24 * 60;
	$minutes += $diff_minutes->h * 60;
	$minutes += $diff_minutes->i;
	//end of minutes diff
	if($minutes > 5){
		unset($_SESSION['empid']);
		unset($_SESSION['otp_window']);
		$sql= "delete from tblotp where empid=:id";
		$query= $dbh -> prepare($sql);
		$query-> bindParam(':id', $id, PDO::PARAM_STR);
		$query-> execute();
	}
}
//end of codes for session destroying

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

                                  <div class="card-content" id="dtl_container" <?php if(isset($_SESSION['otp_window'])){ echo "style='display: none'"; } ?>>
                                      <span class="card-title" style="font-size:20px;">Employee details</span>
                                         <?php if($msg){?><div class="succWrap"><strong>Success </strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
                                       <div class="row">
                                           <form class="col s12" name="signin_form" id="signin_form" method="post">
                                               <div class="input-field col s12">
                                                   <input id="empid" type="text" name="empid" class="validate" autocomplete="off">
                                                   <label for="email">Employee Code</label>
                                               </div>
                                               <div class="input-field col s12">
                                                   <input id="password" type="text" class="validate" name="emailid" autocomplete="off">
                                                   <label for="password">Email</label>
                                               </div>
                                               <div class="col s12 right-align m-t-sm">
                                                
                                                   <input type="button" name="submit" id="submit_dtl" value="Submit" class="waves-effect waves-light btn teal">
                                               </div>
											   <div class="col s12 right-align m-t-sm" style="height: 20px;" id="msg_before_sent">
													
                                               </div>
                                           </form>
                                      </div>
                                  </div>
								  <!--template starts for OTP-->
									<div class="card-content" id="otp_container" <?php if(isset($_SESSION['otp_window'])){ echo "style='display: block'"; }else{ echo "style='display: none'"; } ?>>
                                      <span class="card-title" style="font-size:20px;">OTP</span>
                                         <div style="color: green;" id="otp_msg"></div>
                                       <div class="row">
                                           <form class="col s12" name="otp_form" id="otp_form" method="post">
                                               <div class="input-field col s12">
                                                   <input id="otp" type="text" name="otp" class="validate" autocomplete="off">
                                                   <label for="otp">Enter OTP</label>
                                               </div>
                                               <!--<div class="input-field col s12">
                                                   <input id="password" type="text" class="validate" name="emailid" autocomplete="off" required>
                                                   <label for="password">Email</label>
                                               </div>-->
                                               <div class="col s12 right-align m-t-sm">
                                                
                                                   <input type="button" name="submit_otp" id="submit_otp" value="Submit" class="waves-effect waves-light btn teal">
												   <input type="button" name="resend_otp" id="resend_otp" value="Resend OTP" class="waves-effect waves-light btn" style="background: blue;">
                                               </div>
											   <div class="col s12 right-align m-t-sm" style="height: 20px; color: green;" id="otp_processing"></div>
                                           </form>
                                      </div>
                                  </div>
								  <!--End of OTP template-->
 






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
})
		$(document).ready(function(){
			$("#submit_otp").click(function(){
				if($("#otp").val()== ""){
					$("#otp").addClass("red_border");
				}else{
					otp= $("#otp").val();
					$("#otp_processing").text("Verifying...");
					$.ajax({
						type: "post",
						url: "verify_otp.php",
						data: {otp: otp},
						success: function(data){
							if(data== "correct"){
								window.location.href= "create-new-password.php";
							}else if(data== "otp_expired"){
								$("#otp_processing").text("");
								$("#otp_msg").css("color", "red");
								$("#otp_msg").text("OTP is expired...");
							}else if(data== "s_expired"){
								$("#otp_processing").text("");
								$("#otp_msg").css("color", "red");
								$("#otp_msg").text("Sorry!! your session has expired, please click password recovery option and provide details...");
							}else{
								$("#otp_processing").text("");
								$("#otp_msg").css("color", "red");
								$("#otp_msg").text("You have entered wrong OTP...");
							}
						}
					});
				}
			});
			$("#resend_otp").click(function(){
				$("#otp_processing").text("Resending...");
				$.ajax({
					type: "post",
					url: "forgot_password_ajax.php",
					data: {resend: "set"},
					success: function(data){
						if(data== "sent"){
							$("#otp_processing").text("");
							$("#otp_msg").css("color", "green");
							$("#otp_msg").text("An OTP has been sent to your official email, which will expire after 5 minutes...");
							setTimeout(function(){ $("#otp_msg").text(""); }, 6000);
						}else{
							$("#otp_processing").text("");
							$("#otp_msg").css("color", "red");
							$("#otp_msg").text("Sorry!! your session has expired, please click password recovery option and provide details...");
						}
					}
				});
			});
		$("#submit_dtl").click(function(){
			frm= $("#signin_form");
			flag= true;
			if($("#empid").val()== ""){
				flag= false;
				$("#empid").addClass("red_border");
			}
			if($("#password").val()== ""){
				flag= false;
				$("#password").addClass("red_border");
			}
			if(!flag){
				return false;
			}
			$("#msg_before_sent").css("color", "green");
			$("#msg_before_sent").text("Authenticating...");
			$.ajax({
				type: "post",
				url: "forgot_password_ajax.php",
				data: frm.serialize(),
				success: function(data){
					if(data== "valid"){
						$("#dtl_container").hide();
						$("#otp_container").show();
						$("#otp_msg").text("An OTP has been sent to your official email, which will expire after 5 minutes...");
						setTimeout(function(){ $("#otp_msg").text(""); }, 6000);
					}else{
						$("#msg_before_sent").css("color", "red");
						$("#msg_before_sent").text("Invalid credentials entered");
					}
				}
			});
		});
		});
		$('#change_pass_form, #otp_form, #signin_form').find('input').on('focus', function(){
					$(this).removeClass('red_border');
				});
				$('#change_pass_form, #otp_form, #signin_form').find('input').on('blur',function(){
					$(this).closest('input').each(function(){
					var inputval = $(this).val();
					if(inputval== null || inputval == "")
					{
						$(this).addClass('red_border');	
					}
				//});	
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
				if(!flag)
				return false;
			}
		</script>
    </body>
</html>