<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../includes/send_email.php');
if(strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
}
else{
if(isset($_POST['add']))
{
$empid=$_POST['empcode'];
$joining=$_POST['joining'];
$fname=$_POST['firstName'];
$fname= ucwords($fname);
$lname=$_POST['lastName'];
$lname= ucwords($lname);  
$email=$_POST['email']; 
//$password=md5($_POST['password']);
$password_email= $fname.rand();
$password= md5($password_email);  
$gender=$_POST['gender']; 
$dob=$_POST['dob']; 
$designation=$_POST['designation']; 
$address=$_POST['address']; 
$city=$_POST['city']; 
$anniversary=$_POST['anniversary']; 
$mobileno=$_POST['mobileno']; 
$status=1;

$sql="INSERT INTO tblemployees(EmpId,FirstName,LastName,EmailId,Password,Gender,Dob,Address,City,Phonenumber,Status,JoiningDate,AnniversaryDate,UserType) VALUES(:empid,:fname,:lname,:email,:password,:gender,:dob,:address,:city,:mobileno,:status,:joining,:anniversary,:designation)";
$query = $dbh->prepare($sql);
$query->bindParam(':empid',$empid,PDO::PARAM_STR);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':lname',$lname,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':password',$password,PDO::PARAM_STR);
$query->bindParam(':gender',$gender,PDO::PARAM_STR);
$query->bindParam(':dob',$dob,PDO::PARAM_STR);
$query->bindParam(':designation',$designation,PDO::PARAM_STR);
$query->bindParam(':address',$address,PDO::PARAM_STR);
$query->bindParam(':city',$city,PDO::PARAM_STR);
$query->bindParam(':anniversary',$anniversary,PDO::PARAM_STR);
$query->bindParam(':joining',$joining,PDO::PARAM_STR);
$query->bindParam(':mobileno',$mobileno,PDO::PARAM_STR);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$msg=" Employee record added Successfully";
$to= $email;
$from= "kriti@netsutra.com";
$subject= "Welcome ".$fname;
$bcc= "gurjeet.singh@netsutra.com,dipak.kumar@netsutra.com";
$cc= "";
$msg_email= "<table><tr><td>Dear $fname,</td></tr>\r\n";
$msg_email.= "<tr><td><br><br>Congratulations!!<br><br>Your profile has been created with the company.</td></tr>\r\n";
$msg_email.= "<tr><td>Following are the login credentials for your profile.</td></tr>\r\n";
$msg_email.= "<tr><td><strong>URL: </strong><a href='http://www.netsutra.com/leave'>http://www.netsutra.com/leave</a> <br><strong>Password:</strong> $password_email</td></tr>\r\n";
$msg_email.= "<tr><td><br /></br /><strong style='color: navy;'>Regards,</strong><br>Manager- HR<br>Netsutra</td></tr>\r\n";
$msg_email.= "</table>";
send_email($to, $from, $bcc, $cc, $subject, $msg_email);
}
else 
{
$error=" Something went wrong. Please try again";
}

}

    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Add Employee</title>
        
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
        </style>
    <script type="text/javascript">
/*function valid()
{
if(document.addemp.password.value!= document.addemp.confirmpassword.value)
{
alert("New Password and Confirm Password Field do not match  !!");
document.addemp.confirmpassword.focus();
return false;
}
return true;
}*/
</script>

<script>
function checkAvailabilityEmpid() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'empcode='+$("#empcode").val(),
type: "POST",
success:function(data){
$("#empid-availability").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
</script>

<script>
function checkAvailabilityEmailid() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'emailid='+$("#email").val(),
type: "POST",
success:function(data){
$("#emailid-availability").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
</script>



    </head>
    <body>
  <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
   <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">Add Employee</div>
                    </div>
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                                <form id="example-form" method="post" name="addemp">
                                    <div>
                                        <h3>Add Employee</h3><br><br>
                                        <section>
                                            <div class="wizard-content">
                                                <div class="row">
                                                    <div class="col m6">
                                                        <div class="row">
     <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
                else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>


 <div class="input-field col  s12">
<label for="empcode">Employee Code(Must be unique)</label>
<input  name="empcode" id="empcode" onBlur="checkAvailabilityEmpid()" type="text" autocomplete="off" required>
<span id="empid-availability" style="font-size:12px;"></span> 
</div>


<div class="input-field col m6 s12">
<label for="firstName">First name</label>
<input id="firstName" name="firstName" type="text" required>
</div>

<div class="input-field col m6 s12">
<label for="lastName">Last name</label>
<input id="lastName" name="lastName" type="text" autocomplete="off" required>
</div>

<div class="input-field col s12">
<label for="email">Email</label>
<input  name="email" type="email" id="email" onBlur="checkAvailabilityEmailid()" autocomplete="off" required>
<span id="emailid-availability" style="font-size:12px;"></span> 
</div>

<!--<div class="input-field col s12">
<label for="password">Password</label>
<input id="password" name="password" type="password" autocomplete="off" required>
</div>-->
<div class="input-field col s12">
<label for="phone">Mobile number</label>
<input id="phone" name="mobileno" type="tel" maxlength="10" autocomplete="off" required>
 </div>
<!--<div class="input-field col s12">
<label for="confirm">Confirm password</label>
<input id="confirm" name="confirmpassword" type="password" autocomplete="off" required>
</div>-->
</div>
</div>
                                                    
<div class="col m6">
<div class="row">
<div class="input-field col m6 s12">
<select  name="gender" autocomplete="off">
<option value="">Gender...</option>                                          
<option value="Male">Male</option>
<option value="Female">Female</option>
</select>
</div>

<div class="input-field col m6 s12">
<label for="birthdate">Birthdate</label>
<input id="birthdate" name="dob" type="date" class="datepicker" autocomplete="off" >
</div>

                                                    

<div class="input-field col m6 s12">
<select  name="designation" autocomplete="off">
	<option value="">Designation</option>
	<option value="software engineer">Software Engineer</option>
	<option value="manager">Manager</option>
	<option value="team leader">Team Leader</option>
	<option value="network admin">Network Admin</option>
	<option value="account executive">Account Executive</option>
	<option value="manager-hr">Manager- HR</option>
</select>
</div>

<div class="input-field col m6 s12">
<label for="address">Address</label>
<input id="address" name="address" type="text" autocomplete="off" required>
</div>

<div class="input-field col m6 s12">
<label for="city">City/Town</label>
<input id="city" name="city" type="text" autocomplete="off" required>
 </div>
   
<div class="input-field col m6 s12">
<label for="birthdate">Anniversary</label>
<input id="anniversary" name="anniversary" type="date" class="datepicker" autocomplete="off" >
</div>

                                                            
<!--<div class="input-field col s12">
<label for="phone">Mobile number</label>
<input id="phone" name="mobileno" type="tel" maxlength="10" autocomplete="off" required>
 </div>-->
<div class="input-field col m6 s12">
<label for="joining">Joining Date</label>
<input id="joining" name="joining" type="date" class="datepicker" autocomplete="off" >
</div>
<div class="input-field col m6 s12">
<button type="submit" name="add" onclick="return valid();" id="add" class="waves-effect waves-light btn indigo m-b-xs">ADD</button>
</div>

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
        <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
        <script src="../assets/js/pages/form_elements.js"></script>
        
    </body>
</html>
<?php } ?> 