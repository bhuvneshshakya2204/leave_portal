<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0)
{   
    header('location:../index.php');
}
else{
$eid=intval($_GET['id']);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Employee Details</title>
        
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
                   <!-- <div class="col s12">
                        <div class="page-title">Update Employee</div>
                    </div>-->
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                                <form id="example-form" name="empdetails">
                                    <div>
                                        <h3>Employee Details</h3><br><br>                                           
                                        <section>
                                            <div class="wizard-content">
                                                <div class="row">
                                                    <!--<div class="col m6">-->
                                                        <!--<div class="row">-->
<?php 
$eid=intval($_GET['id']);
$sql = "SELECT * from  tblemployees where id=:eid";
$query = $dbh -> prepare($sql);
$query -> bindParam(':eid',$eid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?> 
 <div class="input-field col m3 s12">
<label for="empcode">Employee Code</label>
<input  name="empcode" id="empcode" value="<?php echo htmlentities($result->EmpId);?>" type="text" autocomplete="off" readonly>
</div>
<div class="input-field col m3 s12">
<label for="firstName">First name</label>
<input id="firstName" name="firstName" value="<?php echo htmlentities($result->FirstName);?>"  type="text" readonly>
</div>
<div class="input-field col m3 s12">
<label for="firstName">Middle name</label>
<input id="middle_name" name="middle_name" value="<?php echo $result->MiddleName == NULL ? 'NA' : $result->MiddleName; ?>"  type="text" readonly>
</div>

<div class="input-field col m3 s12">
<label for="lastName">Last name </label>
<input id="lastName" name="lastName" value="<?php echo htmlentities($result->LastName);?>" type="text" readonly>
</div>

<div class="input-field col m3 s12">
<label for="gender" class="label_up">Gender</label>
<input id="gender" name="gender" value="<?php echo htmlentities($result->Gender);?>" type="text" readonly>
</div>

<div class="input-field col m3 s12">
<label for="birthdate" class="label_up">Date of Birth</label>
<input id="birthdate" name="dob" value="<?php echo htmlentities($result->Dob);?>" readonly>
</div>
<div class="input-field col m3 s12">
<label for="anniversary" class="label_up">Anniversary</label>
<input id="anniversary" name="anniversary"  value="<?php echo htmlentities($result->AnniversaryDate);?>" readonly>
</div>                                               

<div class="input-field col m3 s12">
<label for="designation" class="label_up">Designation</label>
<input id="designation" name="designation"  value="<?php echo htmlentities($result->designation);?>" readonly>
</div>

<div class="input-field col m6 s12">
<label for="email">Official Email</label>
<input  name="email" type="email" id="email" value="<?php echo htmlentities($result->EmailId);?>" readonly>
</div>
<div class="input-field col m6 s12">
<label for="pers_email">Personal Email</label>
<input  name="pers_email" type="email" id="pers_email" value="<?php echo htmlentities($result->email_pers);?>" required>
</div>
<div class="input-field col m3 s12">
<label for="phone">Mobile Number</label>
<input id="phone" name="mobileno" type="tel" value="<?php echo htmlentities($result->Phonenumber);?>" readonly>
 </div>
 <div class="input-field col m3 s12">
<label for="e_mobileno">Emergency Contact Name and Number</label>
<input id="e_mobileno" name="e_mobileno" type="text" value="<?php echo $result->emergency_contact; ?>" readonly>
 </div>
<div class="input-field col m3 s12">
<label for="joining" class="label_up">Joining Date</label>
<input id="joining" name="joining"  value="<?php echo htmlentities($result->JoiningDate);?>" readonly>
</div>
<div class="input-field col m3 s12">
<label class="label_up">Shift Timing</label>
<input id="shift" name="shift"  value="<?php echo htmlentities($result->shift);?>" readonly>
</div>  
<div class="input-field col m6 s12">
<label for="address">Current Address</label>
<input id="address" name="address" type="text"  value="<?php echo htmlentities($result->Address);?>" readonly>
</div>
<div class="input-field col m6 s12">
<label for="p_address">Permanent Address</label>
<input id="p_address" name="p_address" type="text"  value="<?php echo $result->p_address; ?>" readonly>
</div>

<div class="input-field col m3 s12">
<label for="reporting_manager">Reporting Manager</label>
<?php $sql = "SELECT  FirstName, LastName, id from tblemployees where UserType= 'project manager' or UserType= 'director'";
$query = $dbh -> prepare($sql);
$query->execute();
$results_manager=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results_manager as $rs_manager)
     {     ?>                                            
<?php if($result->reporting_manager== $rs_manager->id)
      { ?>
    <input id="reporting_manager" name="reporting_manager" type="text"  value="<?php echo htmlentities($rs_manager->FirstName)." ".htmlentities($rs_manager->LastName);?>" readonly>
<?php }         
     }
} ?>
</div> 
<div class="input-field col m3 s12">
<label for="experience">Previous Experience</label>
<input id="experience" name="experience" type="text"  value="<?php echo $result->previous_exp; ?>" readonly>
</div>

<div class="input-field col m6 s12">
<label for="blood_group">Blood Group</label>
<input id="blood_group" name="blood_group" type="text"  value="<?php echo $result->blood_group== NULL ? 'NA':$result->blood_group; ?>" readonly>
</div>
<div class="input-field col s12">
<label for="skill">Skill Set</label>
<input id="skill" name="skill" type="text"  value="<?php echo $result->skill_set; ?>" readonly>
</div>
<div class="input-field col m3 s12">
<label for="relieving" class="label_up">Relieving Date</label>
<input id="relieving" name="relieving"  value="<?php if($result->releiving_date== "00-00-0000"){ echo "NA"; }else{ echo $result->releiving_date; } ?>" readonly>
</div>
<div class="input-field col m3 s12">
<label for="il_text">Total IL</label>
<input type="text" name="il_text" id="il_text" value="<?php echo $result->informed_leave; ?>" readonly>
</div>
<div class="input-field col m3 s12">
<label for="cl_text">Total CL</label>
<input type="text" name="cl_text" id="cl_text" value="<?php echo $result->casual_leave; ?>" readonly>
</div>
<div class="input-field col m3 s12">
<label for="co_text">Total CO</label>
<input type="text" name="co_text" id="co_text" value="<?php echo $result->co; ?>" readonly>
</div>
<div class="input-field col m6 s12">
<label for="pan_text">Pan Card</label>
<input type="text" name="pan_text" id="pan_text" value="<?php echo $result->pan== NULL ? 'NA':$result->pan; ?>" readonly>
</div>
<div class="input-field col m6 s12">
<label for="aadhaar_text">Aadhaar Card</label>
<input type="text" name="aadhaar_text" id="aadhaar_text" value="<?php echo $result->aadhaar== NULL ? 'NA':$result->aadhaar; ?>" readonly>
</div>
<?php }}?>


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