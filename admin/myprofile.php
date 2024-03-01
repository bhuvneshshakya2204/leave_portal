<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
    {   
header('location:../index.php');
}
else{
if(isset($_SESSION['hrlogin']))
	$eid=$_SESSION['hrlogin'];
else
	$eid=$_SESSION['mlogin'];
if(isset($_POST['update']))
{

// $fname=$_POST['firstName'];
// $lname=$_POST['lastName'];   
// $gender=$_POST['gender']; 
// $dob=$_POST['dob']; 
//$department=$_POST['department']; 
$address=$_POST['address']; 
// $city=$_POST['city']; 
$anniversary=$_POST['anniversary']; 
$mobileno=$_POST['mobileno'];
$emergency=$_POST['emergency'];
$skill=$_POST['skill'];
$pan=$_POST['pan'];
$aadhaar=$_POST['aadhaar'];
$blood_group=$_POST['blood_group'];
$experience= $_POST['experience'];
$p_address=$_POST['p_address'];
$sql="update tblemployees set Address=:address,p_address=:p_address,Phonenumber=:mobileno,emergency_contact=:emergency,AnniversaryDate=:anniversary,skill_set=:skill,pan=:pan,aadhaar=:aadhaar,blood_group=:blood_group,previous_exp=:experience where EmailId=:eid";
$query = $dbh->prepare($sql);
$query->bindParam(':emergency',$emergency,PDO::PARAM_STR);
$query->bindParam(':skill',$skill,PDO::PARAM_STR);
$query->bindParam(':pan',$pan,PDO::PARAM_STR);
$query->bindParam(':aadhaar',$aadhaar,PDO::PARAM_STR);
$query->bindParam(':blood_group',$blood_group,PDO::PARAM_STR);
$query->bindParam(':experience',$experience,PDO::PARAM_STR);
$query->bindParam(':address',$address,PDO::PARAM_STR);
$query->bindParam(':p_address',$p_address,PDO::PARAM_STR);
$query->bindParam(':anniversary',$anniversary,PDO::PARAM_STR);
$query->bindParam(':mobileno',$mobileno,PDO::PARAM_STR);
$query->bindParam(':eid',$eid,PDO::PARAM_STR);
$query->execute();
$msg="Employee record updated Successfully";
echo "<script type='text/javascript'>alert('$msg');window.location='emp-dashboard.php';</script>";
}

    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - My Profile</title>
        
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
        </style>





    </head>
    <body>
  <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
   <main class="mn-inner">
                <div class="row">
                    <!--<div class="col s12">
                        <div class="page-title">My Info</div>
                    </div>-->
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                                <form id="example-form" method="post" name="updatemp">
                                    <div>
                                        <h3>My Info</h3><br><br>
                                           <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
                else if($msg){?><div class="succWrap"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
                                        <section>
                                            <div class="wizard-content">
                                                <div class="row">
                                                    <!--<div class="col m6">
                                                        <div class="row">-->
<?php
if(isset($_SESSION['hrlogin']))
	$eid=$_SESSION['hrlogin'];
else
	$eid=$_SESSION['mlogin'];
$sql = "SELECT * from  tblemployees where EmailId=:eid";
$query = $dbh -> prepare($sql);
$query -> bindParam(':eid',$eid, PDO::PARAM_STR);
$query->execute();
$results_profile=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results_profile as $result_profile)
{               ?> 
 <div class="input-field col m3 s12">
<label for="empcode">Employee code</label>
<input  name="empcode" id="empcode" value="<?php echo htmlentities($result_profile->EmpId);?>" type="text" autocomplete="off" readonly required>
<span id="empid-availability" style="font-size:12px;"></span> 
</div>
<div class="input-field col m3 s12">
<label for="firstName">First Name</label>
<input id="firstName" name="firstName" value="<?php echo htmlentities($result_profile->FirstName);?>"  type="text" readonly required>
</div>

<div class="input-field col m3 s12">
<label for="middle_name">Middle Name</label>
<input id="middle_name" name="MiddleName" value="<?php echo htmlentities($result_profile->MiddleName);?>"  type="text" readonly>
</div>

<div class="input-field col m3 s12">
<label for="lastName">Last Name </label>
<input id="lastName" name="lastName" value="<?php echo htmlentities($result_profile->LastName);?>" type="text" autocomplete="off" readonly required>
</div>


<div class="input-field col m3 s12">
<label for="gender" id="gender_label" class="label_up">Gender</label>
<select  name="gender" id="gender" autocomplete="off" disabled>
<option value="<?php echo htmlentities($result_profile->Gender);?>"><?php echo htmlentities($result_profile->Gender);?></option>                                          
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Other">Other</option>
</select>
</div>

<div class="input-field col m3 s12">
<label for="birthdate" class="label_up">Date of Birth</label>
<input id="birthdate" name="dob"  class="datepicker1" value="<?php echo htmlentities($result_profile->Dob);?>" readonly>
</div>

<div class="input-field col m3 s12">
	<label for="anniversary" id="anniversary_label" class="label_up">Anniversary</label>
<input id="anniversary" name="anniversary"  class="datepicker1 anniversary_style" value="<?php echo htmlentities($result_profile->AnniversaryDate)=='00-00-0000' ? 'NA':htmlentities($result_profile->AnniversaryDate);?>" <?php if(htmlentities($result_profile->AnniversaryDate)!= "00-00-0000"){ echo "readonly"; } ?>>
</div>
<div class="input-field col m3 s12">
	<label for="designation" id="designation_label" class="label_up">Designation</label>
	<input id="designation" name="designation" value="<?php echo ucwords(htmlentities($result_profile->designation));?>" readonly>
</div>
<div class="input-field col m6 s12">
<label for="email">Official Email</label>
<input  name="email" type="email" id="email" value="<?php echo htmlentities($result_profile->EmailId);?>" readonly autocomplete="off" required>
<span id="emailid-availability" style="font-size:12px;"></span> 
</div>
<div class="input-field col m6 s12">
	<label for="email_pers" id="email_pers_label" class="label_up">Personal Email</label>
<input id="email_pers" name="email_pers" value="<?php echo htmlentities($result_profile->email_pers);?>" readonly>
</div>
<div class="input-field col m3 s12">
<label for="phone">Mobile number</label>
<input id="phone" name="mobileno" type="tel" value="<?php echo htmlentities($result_profile->Phonenumber);?>" maxlength="15" autocomplete="off" required>
 </div>
 <div class="input-field col m3 s12">
<label for="emergency">Emergency Contact Name and Number</label>
<input id="emergency" name="emergency" type="tel" value="<?php echo htmlentities($result_profile->emergency_contact);?>" autocomplete="off" required>
 </div>
<div class="input-field col m3 s12">
<label for="joining">Joining Date</label>
<input id="joining" name="joining" type="text" value="<?php echo htmlentities($result_profile->JoiningDate);?>" autocomplete="off" readonly required>
 </div>
 <div class="input-field col m3 s12">
	<label for="shift" id="shift" class="label_up">Shift Timing</label>
<input id="shift" name="shift" value="<?php echo htmlentities($result_profile->shift);?>" readonly>
</div>
<div class="input-field col m6 s12">
<label for="address">Current Address</label>
<input id="address" name="address" type="text"  value="<?php echo htmlentities($result_profile->Address);?>" autocomplete="off" required>
</div>
<div class="input-field col m6 s12">
<label for="p_address">Permanent Address</label>
<input id="p_address" name="p_address" type="text"  value="<?php echo htmlentities($result_profile->p_address);?>" autocomplete="off" readonly>
</div>
<div class="input-field col s12">
<label for="skill">Skill Set</label>
<input id="skill" name="skill" type="text"  value="<?php echo htmlentities($result_profile->skill_set);?>" autocomplete="off">
 </div>
  <div class="input-field col m3 s12">
<label for="r_manager">Reporting Manager</label>
<?php
$manager_id= $result_profile->reporting_manager;
$sql = "SELECT FirstName, LastName from  tblemployees where id=$manager_id";
$query = $dbh -> prepare($sql);
$query->execute();
$dtl_manager=$query->fetchAll(PDO::FETCH_OBJ);
foreach($dtl_manager as $val_manager){}
?>
<input id="r_manager" name="r_manager" type="text"  value="<?php echo $val_manager->FirstName." ".$val_manager->LastName; ?>" autocomplete="off" required readonly>
 </div>
 <div class="input-field col m2 s12">
<label for="experience">Prevoius Exp.</label>
<input id="experience" name="experience" type="text"  value="<?php echo htmlentities($result_profile->previous_exp); ?>" autocomplete="off" <?php if(htmlentities($result_profile->previous_exp)!= NULL && htmlentities($result_profile->previous_exp)!= "NA"){ echo "readonly"; } ?>>
 </div>
 <div class="input-field col m2 s12">
<label for="blood_group">Blood Group</label>
<input id="blood_group" name="blood_group" type="text"  value="<?php echo htmlentities($result_profile->blood_group);?>" autocomplete="off" <?php if(htmlentities($result_profile->blood_group)!= NULL && htmlentities($result_profile->blood_group)!= "NA"){ echo "readonly"; } ?>>
 </div>
  <div class="input-field col m2 s12">
<label for="pan">Pan Card</label>
<input id="pan" name="pan" type="text"  value="<?php echo htmlentities($result_profile->pan);?>" autocomplete="off" <?php if(htmlentities($result_profile->pan)!= NULL && htmlentities($result_profile->pan)!= "NA"){ echo "readonly"; } ?>>
 </div>
  <div class="input-field col m3 s12">
<label for="aadhaar">Aadhaar Card</label>
<input id="aadhaar" name="aadhaar" type="text"  value="<?php echo htmlentities($result_profile->aadhaar);?>" autocomplete="off" <?php if(htmlentities($result_profile->aadhaar)!= NULL && htmlentities($result_profile->aadhaar)!= "NA"){ echo "readonly"; } ?>>
 </div>



                                                            

<?php }}?>
                                                        
<div class="input-field col s12">
<button type="submit" name="update"  id="update" class="waves-effect waves-light btn indigo m-b-xs">UPDATE</button>

</div>

                                                       <!-- </div>
                                                    </div>-->
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
        <script>
			$(".anniversary_style").change(function(){
				$("#anniversary_label").addClass("label_up");
			});
		</script>
    </body>
</html>
<?php } ?> 