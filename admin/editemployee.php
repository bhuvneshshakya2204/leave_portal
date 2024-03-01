<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['hrlogin'])==0)
    {   
header('location:../index.php');
}
else{
$eid=intval($_GET['empid']);
if(isset($_POST['update']))
{
$fname=$_POST['firstName'];
$fname=ucwords($fname);
$mname=$_POST['middle_name'];
$mname= ucwords($mname);
$lname=$_POST['lastName'];
$lname= ucwords($lname);
$joining= $_POST['joining'];
$shift= $_POST['shift'];
$pers_email= $_POST['pers_email'];
$gender=$_POST['gender']; 
$dob=$_POST['dob']; 
$designation=$_POST['designation']; 
$address=$_POST['address']; 
//$city=$_POST['city']; 
$anniversary=$_POST['anniversary'];  
$mobileno=$_POST['mobileno'];
$e_mobileno=$_POST['e_mobileno'];
$p_address=$_POST['p_address'];
$reporting_manager=$_POST['reporting_manager'];
$experience=$_POST['experience'];
$skill=$_POST['skill'];
$blood_group=$_POST['blood_group'];
$pan=$_POST['pan_text'];
$aadhaar=$_POST['aadhaar_text'];
$relieving= "00-00-0000";
$active_status= 1;
if($_POST['relieving']!= "NA"){
	$relieving=$_POST['relieving'];
	$active_status= 0;
}
$il_text=$_POST['il_text'];
$cl_text=$_POST['cl_text'];
$co_text=$_POST['co_text'];
    
//Code for if manager changed all leaves tranfer to new manager
$sql = "SELECT reporting_manager from  tblemployees where id=:eid";
$query = $dbh -> prepare($sql);
$query->bindParam(':eid',$eid,PDO::PARAM_STR);
$query->execute();
$results_emp=$query->fetchAll(PDO::FETCH_OBJ);
//$cnt=1;
foreach($results_emp as $result_emp){}
if($result_emp->reporting_manager != $reporting_manager) {
    $sql="update tblleaves set ManagerID=:reporting_manager WHERE empid=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid',$eid,PDO::PARAM_STR);
    $query->bindParam(':reporting_manager',$reporting_manager,PDO::PARAM_STR);
    $query->execute();
    
    $sql="update tblleaves_history set manager_id=:reporting_manager WHERE emp_id=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid',$eid,PDO::PARAM_STR);
    $query->bindParam(':reporting_manager',$reporting_manager,PDO::PARAM_STR);
    $query->execute();
}
//End code for if manager changed
    
$sql="update tblemployees set FirstName=:fname,MiddleName=:mname,LastName=:lname,Gender=:gender,Dob=:dob,designation=:designation,Address=:address,p_address=:p_address,AnniversaryDate=:anniversary,JoiningDate=:joining,shift=:shift,email_pers=:pers_email,Phonenumber=:mobileno,informed_leave=:il_text,casual_leave=:cl_text,co=:co_text,reporting_manager=:reporting_manager,previous_exp=:experience,skill_set=:skill,emergency_contact=:e_mobileno,releiving_date=:relieving,Status=:active_status,blood_group=:blood_group,pan=:pan,aadhaar=:aadhaar where id=:eid";
$query = $dbh->prepare($sql);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':mname',$mname,PDO::PARAM_STR);
$query->bindParam(':joining',$joining,PDO::PARAM_STR);
$query->bindParam(':shift',$shift,PDO::PARAM_STR);
$query->bindParam(':pers_email',$pers_email,PDO::PARAM_STR);
$query->bindParam(':lname',$lname,PDO::PARAM_STR);
$query->bindParam(':gender',$gender,PDO::PARAM_STR);
$query->bindParam(':dob',$dob,PDO::PARAM_STR);
$query->bindParam(':designation',$designation,PDO::PARAM_STR);
$query->bindParam(':address',$address,PDO::PARAM_STR);
//$query->bindParam(':city',$city,PDO::PARAM_STR);
$query->bindParam(':anniversary',$anniversary,PDO::PARAM_STR);
$query->bindParam(':mobileno',$mobileno,PDO::PARAM_STR);
$query->bindParam(':eid',$eid,PDO::PARAM_STR);
$query->bindParam(':p_address',$p_address,PDO::PARAM_STR);
$query->bindParam(':il_text',$il_text,PDO::PARAM_STR);
$query->bindParam(':cl_text',$cl_text,PDO::PARAM_STR);
$query->bindParam(':co_text',$co_text,PDO::PARAM_STR);
$query->bindParam(':reporting_manager',$reporting_manager,PDO::PARAM_STR);
$query->bindParam(':experience',$experience,PDO::PARAM_STR);
$query->bindParam(':skill',$skill,PDO::PARAM_STR);
$query->bindParam(':e_mobileno',$e_mobileno,PDO::PARAM_STR);
$query->bindParam(':relieving',$relieving,PDO::PARAM_STR);
$query->bindParam(':active_status',$active_status,PDO::PARAM_STR);
$query->bindParam(':blood_group',$blood_group,PDO::PARAM_STR);
$query->bindParam(':pan',$pan,PDO::PARAM_STR);
$query->bindParam(':aadhaar',$aadhaar,PDO::PARAM_STR);
$query->execute();
$msg="Employee record updated Successfully";
echo "<script type='text/javascript'>alert('$msg');window.location='dashboard.php';</script>";
}

    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Update Employee</title>
        
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
                                <form id="example-form" method="post" name="updatemp" onSubmit="return validateBloodGroup()">
                                    <div>
                                        <h3>Update Employee</h3><br><br>
                                           <?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
                else if($msg){?><div class="succWrap"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
                                        <section>
                                            <div class="wizard-content">
                                                <div class="row">
                                                    <!--<div class="col m6">-->
                                                        <!--<div class="row">-->
<?php 
$eid=intval($_GET['empid']);
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
<label for="empcode">Employee Code(Must be unique)</label>
<input  name="empcode" id="empcode" value="<?php echo htmlentities($result->EmpId);?>" type="text" autocomplete="off" readonly required>
<span id="empid-availability" style="font-size:12px;"></span> 
</div>
<div class="input-field col m3 s12">
<label for="firstName">First name</label>
<input id="firstName" name="firstName" value="<?php echo htmlentities($result->FirstName);?>"  type="text" required>
</div>
<div class="input-field col m3 s12">
<label for="firstName">Middle name</label>
<input id="middle_name" name="middle_name" value="<?php echo $result->MiddleName == NULL ? 'NA' : $result->MiddleName; ?>"  type="text">
</div>

<div class="input-field col m3 s12">
<label for="lastName">Last name </label>
<input id="lastName" name="lastName" value="<?php echo htmlentities($result->LastName);?>" type="text" autocomplete="off" required>
</div>

<div class="input-field col m3 s12">
<label for="gender" class="label_up">Gender</label>
<select  name="gender" autocomplete="off"  id="gender">
<option value="<?php echo htmlentities($result->Gender);?>"><?php echo htmlentities($result->Gender);?></option>                                          
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Other">Other</option>
</select>
</div>

<div class="input-field col m3 s12">
<label for="birthdate" class="label_up">Date of Birth</label>
<input id="birthdate" name="dob"  class="datepicker" value="<?php echo htmlentities($result->Dob);?>" >
</div>
<div class="input-field col m3 s12">
<label for="anniversary" class="label_up">Anniversary</label>
<input id="anniversary" name="anniversary"  class="datepicker" value="<?php echo htmlentities($result->AnniversaryDate);?>" >
</div>                                               

<div class="input-field col m3 s12">
<label for="designation" class="label_up">Designation</label>
<select  name="designation" autocomplete="off" id="designation">
<?php $sql = "SELECT DesignationType FROM tbldesignationtype";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $results_designation=$query->fetchAll(PDO::FETCH_OBJ);
    $cnt=1;
    if($query->rowCount() > 0)
    {
    foreach($results_designation as $rs_designation)
    {   
        $selected = ($rs_designation->DesignationType == $result->designation) ? 'selected' : '';
    ?>    
    <option value="<?php echo htmlentities($rs_designation->DesignationType); ?>" <?php echo $selected;?>><?php echo htmlentities($rs_designation->DesignationType);?></option>
    
<?php }} ?>
</select>
</div>

<div class="input-field col m6 s12">
<label for="email">Official Email</label>
<input  name="email" type="email" id="email" value="<?php echo htmlentities($result->EmailId);?>" readonly autocomplete="off" required>
<span id="emailid-availability" style="font-size:12px;"></span> 
</div>
<div class="input-field col m6 s12">
<label for="pers_email">Personal Email</label>
<input  name="pers_email" type="email" id="pers_email" value="<?php echo htmlentities($result->email_pers);?>" autocomplete="off" required>
<span id="emailid-availability" style="font-size:12px;"></span> 
</div>
<div class="input-field col m3 s12">
<label for="phone">Mobile Number</label>
<input id="phone" name="mobileno" type="tel" value="<?php echo htmlentities($result->Phonenumber);?>" maxlength="15" autocomplete="off" required>
 </div>
 <div class="input-field col m3 s12">
<label for="e_mobileno">Emergency Contact Name and Number</label>
<input id="e_mobileno" name="e_mobileno" type="text" value="<?php echo $result->emergency_contact; ?>" autocomplete="off" required>
 </div>
<div class="input-field col m3 s12">
<label for="joining" class="label_up">Joining Date</label>
<input id="joining" name="joining"  class="datepicker" value="<?php echo htmlentities($result->JoiningDate);?>" >
</div>
<div class="input-field col m3 s12">
<label class="label_up">Shift Timing</label>
<select  name="shift" autocomplete="off">
	<!--<option value="">Shift timing</option>-->
	<option value="9:00 AM to 6:00 PM" <?php if($result->shift== "9:00 AM to 6:00 PM"){ echo "selected"; } ?> >9:00 AM to 6:00 PM</option>
	<option value="9:30 AM to 6:30 PM" <?php if($result->shift== "9:30 AM to 6:30 PM"){ echo "selected"; } ?> >9:30 AM to 6:30 PM</option>
	<option value="10:00 AM to 7:00 PM" <?php if($result->shift== "10:00 AM to 7:00 PM"){ echo "selected"; } ?> >10:00 AM to 7:00 PM</option>
	<option value="10:30 AM to 7:30 PM" <?php if($result->shift== "10:30 AM to 7:30 PM"){ echo "selected"; } ?> >10:30 AM to 7:30 PM</option>
	<option value="11:00 AM to 8:00 PM" <?php if($result->shift== "11:00 AM to 8:00 PM"){ echo "selected"; } ?> >11:00 AM to 8:00 PM</option>
	<option value="11:30 AM to 8:30 PM" <?php if($result->shift== "11:30 AM to 8:30 PM"){ echo "selected"; } ?> >11:30 AM to 8:30 PM</option>
	<option value="12:00 AM to 9:00 PM" <?php if($result->shift== "12:00 AM to 9:00 PM"){ echo "selected"; } ?> >12:00 AM to 9:00 PM</option>
	<option value="not applicable" <?php if($result->shift== "not applicable"){ echo "selected"; } ?>>Not Applicable</option>
</select>
</div>  
<div class="input-field col m6 s12">
<label for="address">Current Address</label>
<input id="address" name="address" type="text"  value="<?php echo htmlentities($result->Address);?>" autocomplete="off" required>
</div>
<div class="input-field col m6 s12">
<label for="p_address">Permanent Address</label>
<input id="p_address" name="p_address" type="text"  value="<?php echo $result->p_address; ?>" autocomplete="off" required>
</div>

<div class="input-field col m3 s12">
<label for="reporting_manager" class="label_up">Reporting Manager</label>
<select name="reporting_manager" id="reporting_manager" required>
<option value="">Select Manager</option>
	<?php $sql = "SELECT  FirstName, LastName, id from tblemployees where UserType= 'project manager' or UserType= 'director' order by FirstName";
$query = $dbh -> prepare($sql);
$query->execute();
$results_manager=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results_manager as $rs_manager)
{   ?>                                            
<option value="<?php echo htmlentities($rs_manager->id);?>" <?php if($result->reporting_manager== $rs_manager->id){ echo "selected"; } ?>><?php echo htmlentities($rs_manager->FirstName)." ".htmlentities($rs_manager->LastName);?></option>
<?php }} ?>
</select>
</div> 
<div class="input-field col m3 s12">
<label for="experience">Previous Experience</label>
<input id="experience" name="experience" type="text"  value="<?php echo $result->previous_exp; ?>" autocomplete="off" required>
</div>

<div class="input-field col m6 s12">
<label for="blood_group">Blood Group</label>
<input id="blood_group" name="blood_group" type="text"  value="<?php echo $result->blood_group== NULL ? 'NA':$result->blood_group; ?>" autocomplete="off">
</div>
<div class="input-field col s12">
<label for="skill">Skill Set</label>
<input id="skill" name="skill" type="text"  value="<?php echo $result->skill_set; ?>" autocomplete="off" required>
</div>
<div class="input-field col m4 s12">
<label for="relieving" class="label_up">Relieving Date</label>
<input id="relieving" name="relieving"  class="datepicker" value="<?php if($result->releiving_date== "00-00-0000"){ echo "NA"; }else{ echo $result->releiving_date; } ?>" >
</div>
<div class="input-field col m4 s12">
<label for="il_text">Total IL</label>
<input type="text" name="il_text" id="il_text" value="<?php echo $result->informed_leave; ?>" />
</div>
<div class="input-field col m4 s12">
<label for="cl_text">Total CL</label>
<input type="text" name="cl_text" id="cl_text" value="<?php echo $result->casual_leave; ?>" />
</div>
<!-- <div class="input-field col m3 s12">
<label for="co_text">Total CO</label>
<input type="text" name="co_text" id="co_text" value="<?php echo $result->co; ?>" />
</div> -->
<div class="input-field col m6 s12">
<label for="pan_text">Pan Card</label>
<input type="text" name="pan_text" id="pan_text" value="<?php echo $result->pan== NULL ? 'NA':$result->pan; ?>" />
</div>
<div class="input-field col m6 s12">
<label for="aadhaar_text">Aadhaar Card</label>
<input type="text" name="aadhaar_text" id="aadhaar_text" value="<?php echo $result->aadhaar== NULL ? 'NA':$result->aadhaar; ?>" />
</div>
<div class="input-field col m6 s12">
<button type="submit" name="update"  id="update" class="waves-effect waves-light btn indigo m-b-xs">UPDATE</button>
</div>

<!--</div>-->
<!--</div>-->
                                                    
<!--<div class="col m6">
<div class="row">-->
 
<!--<div class="input-field col m6 s12">
<label for="city">City/Town</label>
<input id="city" name="city" type="text"  value="<?php echo htmlentities($result->City);?>" autocomplete="off" required>
 </div>
<div class="input-field col m6 s12">
<label for="joining" class="label_up">Joining Date</label>
<input id="joining" name="joining"  class="datepicker" value="<?php echo htmlentities($result->JoiningDate);?>" >
</div>-->

<?php }}?>
                                                        
<!--<div class="input-field col s12">
<button type="submit" name="update"  id="update" class="waves-effect waves-light btn indigo m-b-xs">UPDATE</button>

</div>-->

                                                        </div>
                                                    <!--</div>-->
                                                <!--</div>-->
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
		<script>
			function validateBloodGroup(){
				blood_group= document.getElementById("blood_group").value;
				if(blood_group== "NA" || blood_group== ""){
					response= confirm("Blood group is empty, would you like to continue?");
					if(!response){
						return false;
					}
				}
			}
		</script>
        <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
        <script src="../assets/js/pages/form_elements.js"></script>
        
    </body>
</html>
<?php } ?> 