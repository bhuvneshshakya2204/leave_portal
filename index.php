<?php
session_start();
error_reporting(1);
include('includes/config.php');
if(isset($_SESSION['mlogin'])){
	echo "<script type='text/javascript'> document.location = 'admin/emp-dashboard.php'; </script>";
	die;
}
if(isset($_SESSION['emplogin'])){
	echo "<script type='text/javascript'> document.location = 'emp-dashboard.php'; </script>";
	die;
}
if(isset($_SESSION['hrlogin'])){
	echo "<script type='text/javascript'> document.location = 'admin/dashboard.php'; </script>";
	die;
}
if(isset($_POST['signin']))
{
$uname=$_POST['username'];
$password=md5($_POST['password']);
$sql ="SELECT EmailId,Password,Status,id,UserType FROM tblemployees WHERE EmailId=:uname and Password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':uname', $uname, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
	if(!empty($_POST['remember_me']))
	{
		setcookie("username",$uname,time()+60*60*24*30*12);
		setcookie("userpassword",$_POST['password'],time()+60*60*24*30*12);
	}else{
		setcookie("username", $uname, time()-1);
		setcookie("userpassword", $_POST['password'], time()-1);
	}
 foreach ($results as $result) {
    $status=$result->Status;
    $_SESSION['eid']=$result->id;
  }
if($status==0)
{
$msg="Your account is Inactive. Please contact HR";
} else{
	if($result->UserType== "project manager" || $result->UserType== "director"){
		$_SESSION['mlogin']=$_POST['username'];
		echo "<script type='text/javascript'> document.location = 'admin/emp-dashboard.php'; </script>";
	}else if($result->UserType== "manager - hr"){
			$_SESSION['hrlogin']=$_POST['username'];
			echo "<script type='text/javascript'> document.location = 'admin/dashboard.php'; </script>";
		}else{
			$_SESSION['emplogin']=$_POST['username'];
			echo "<script type='text/javascript'> document.location = 'emp-dashboard.php'; </script>";
		}
} }

else{

  //echo "<script>alert('Invalid Details');</script>";
	$msg= "Invalid Details";
}

}

?><!DOCTYPE html>
<html lang="en">
    <head>
<style>
.red_border{
	border-bottom: 2px solid red!important;
}
.errorWrap {
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
</style>
        <!-- Title -->
        <title>Netsutra - Home Page</title>

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


        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="http://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="http://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body oncontextmenu="return false">
	<!--<body>-->
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
                        <div class="page-title"><h4>Welcome to Leave Management System</h4></div>

                          <div class="col s12 m6 l8 offset-l2 offset-m3">
                              <div class="card white darken-1">

                                  <div class="card-content ">
                                      <span class="card-title" style="font-size:20px;">Login</span>
                                         <?php if($msg){?><div class="errorWrap" style="color: red;"><strong>ERROR</strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
                                       <div class="row">
                                           <form class="col s12" name="signin" method="post" id="login_form" onSubmit="return validate_login();">
                                               <div class="input-field col s12">
                                                   <input id="username" type="text" name="username" class="validate" autocomplete="off" value="<?php if(isset($_COOKIE['username'])){ echo $_COOKIE['username'];}?>">
                                                   <label for="email">Email</label>
                                               </div>
                                               <div class="input-field col s12">
                                                   <input id="password" type="password" class="validate" name="password" autocomplete="off" value="<?php if(isset($_COOKIE['userpassword'])){ echo $_COOKIE['userpassword'];}?>">
                                                   <label for="password">Password</label>
                                               </div>
											    <div class="input-field col s12">
                                                   <input id="remember_me" name="remember_me" type="checkbox" value="1" <?php if(isset($_COOKIE['username'])) { echo 'checked="checked"';} else { echo ''; } ?> >
													 <label for="remember_me">Remember me</label>
                                               </div>
                                               <div class="col s12 right-align m-t-sm">

                                                   <input type="submit" name="signin" value="Sign in" class="waves-effect waves-light btn teal">
                                               </div>
                                           </form>
                                      </div>
                                  </div>
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
		function validate_login(){
			flag= true;
			if($("#username").val()== ""){
				$("#username").addClass("red_border");
				flag= false;
			}
			if($("#password").val()== ""){
				$("#password").addClass("red_border");
				flag= false;
			}
			if(!flag)
				return false;
		}
			$('#login_form').find('input').on('focus', function(){
					$(this).removeClass('red_border');
				});
				$('#login_form').find('input').on('blur',function(){
					$(this).closest('input').each(function(){
					var inputval = $(this).val();
					if(inputval== null || inputval == "")
					{
						$(this).addClass('red_border');	
					}
				//});	
			});
	});	
		</script>
    </body>
</html>
