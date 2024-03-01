<?php
session_start();
error_reporting(1);
include('includes/config.php');
include('../includes/send_email.php');
if (strlen($_SESSION['hrlogin']) == 0) {
    header('location:../index.php');
} else {
    if (isset($_POST['add'])) {
        // print_r($_POST);
        // echo '<br>';
                   
        // exit;
        $empid = $_POST['empcode'];
        $joining = $_POST['joining'];
        $fname = trim($_POST['firstName']);
        $fname = ucwords($fname);
        $lname = trim($_POST['lastName']);
        $lname = ucwords($lname);
        $mname = trim($_POST['middle_name']);
        $mname = ucwords($mname);
        $p_address = trim($_POST['p_address']);
        $skill = trim($_POST['skill']);
        $blood_group = trim($_POST['blood_group']);
        $pan = trim($_POST['pan_text']);
        $aadhaar = trim($_POST['aadhaar_text']);
        $co = '0';
        
        $cstm_time=strtotime($joining);
        $joining_day=date("d",$cstm_time);
        $joining_month=date("m",$cstm_time);
        $joining_year=date("Y",$cstm_time);
        $experience_number = trim($_POST['experience']);
        $experience_text_tmp = $_POST['experience_label'];
        $total_il=$total_cl=$leave_per_month= '0';
        if((int)$joining_day <= '10'){
            if($experience_text_tmp == '1'){
                if($experience_number >= 2){
                    $total_il = '1';
                    $total_cl = '1';
                    $leave_per_month= '2';
                }else{
                    $total_il = '1';
                    $total_cl = '0';
                    $leave_per_month= '1';
                }

            }else if($experience_text_tmp == '2'){
                if($experience_number >= 24){
                    $total_il = '1';
                    $total_cl = '1';
                    $leave_per_month= '2';
                }else{
                    $total_il = '1';
                    $total_cl = '0';
                    $leave_per_month= '1';
                }
            }
        }else if((int)$joining_day > '10' && (int)$joining_day <= '20'){
            if($experience_text_tmp == '1'){
                if($experience_number >= 2){
                    $total_il = '1';
                    $total_cl = '0';
                    $leave_per_month= '1';
                }else{
                    $total_il = '0';
                    $total_cl = '0';
                    $leave_per_month= '0';
                }

            }else if($experience_text_tmp == '2'){
                if($experience_number >= 24){
                    $total_il = '1';
                    $total_cl = '0';
                    $leave_per_month= '1';
                }else{
                    $total_il = '0';
                    $total_cl = '0';
                    $leave_per_month= '0';
                }
            }
        }else if((int)$joining_day > '20' && (int)$joining_day <= '30'){
            $total_il = '0';
            $total_cl = '0';
            $leave_per_month= '0';
        }
        $experience_text = '';
        if($experience_text_tmp == 1){
            $experience_text = 'Years';
        }else if($experience_text_tmp == 2){
            $experience_text = 'Months';
        }
        $experience = $experience_number.' '.$experience_text;
        $reporting_manager = trim($_POST['reporting_manager']);
        $e_mobileno = trim($_POST['e_mobileno']);
        $email = trim($_POST['email']);
        //$password=md5($_POST['password']);
        $password_email = $fname . rand();
        $password = md5($password_email);
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $designation = $_POST['designation'];
        $address = $_POST['address'];
        //$city=$_POST['city']; 
        $anniversary = $_POST['anniversary'];
        $mobileno = $_POST['mobileno'];
        $shift = $_POST['shift'];
        $email_pers = $_POST['email_pers'];
        $status = 1;
        
        $co_text = '0';
        //code to check allready exixt or not
        $sql = "select * from tblemployees where EmpId=:empid or EmailId=:email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':empid', $empid, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $result_allready_exist = $query->fetchAll(PDO::FETCH_OBJ);
        if ($query->rowCount() > 0) {
            $msg = " Employee record already exists";
        } else {
            //end allready exist
            $sql = "INSERT INTO tblemployees(EmpId,FirstName,MiddleName,LastName,EmailId,Password,Gender,Dob,Address,p_address,Phonenumber,Status,JoiningDate,AnniversaryDate,UserType,shift,email_pers,leave_per_month,informed_leave,casual_leave,co,reporting_manager,previous_exp,skill_set,emergency_contact,blood_group,pan,aadhaar,designation) VALUES(:empid,:fname,:mname,:lname,:email,:password,:gender,:dob,:address,:p_address,:mobileno,:status,:joining,:anniversary,:designation,:shift,:email_pers,:leave_per_month,:il_text,:cl_text,:co_text,:reporting_manager,:experience,:skill,:e_mobileno,:blood_group,:pan,:aadhaar,:emp_designation)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':empid', $empid, PDO::PARAM_STR);
            $query->bindParam(':fname', $fname, PDO::PARAM_STR);
            $query->bindParam(':mname', $mname, PDO::PARAM_STR);
            $query->bindParam(':lname', $lname, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':password', $password, PDO::PARAM_STR);
            $query->bindParam(':gender', $gender, PDO::PARAM_STR);
            $query->bindParam(':dob', $dob, PDO::PARAM_STR);
            $query->bindParam(':designation', $designation, PDO::PARAM_STR);
            $query->bindParam(':address', $address, PDO::PARAM_STR);
            $query->bindParam(':p_address', $p_address, PDO::PARAM_STR);
            //$query->bindParam(':city',$city,PDO::PARAM_STR);
            $query->bindParam(':anniversary', $anniversary, PDO::PARAM_STR);
            $query->bindParam(':joining', $joining, PDO::PARAM_STR);
            $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_STR);
            $query->bindParam(':shift', $shift, PDO::PARAM_STR);
            $query->bindParam(':email_pers', $email_pers, PDO::PARAM_STR);
            $query->bindParam(':leave_per_month', $leave_per_month, PDO::PARAM_STR);
            $query->bindParam(':il_text', $total_il, PDO::PARAM_STR);
            $query->bindParam(':cl_text', $total_cl, PDO::PARAM_STR);
            $query->bindParam(':co_text', $co_text, PDO::PARAM_STR);
            $query->bindParam(':reporting_manager', $reporting_manager, PDO::PARAM_STR);
            $query->bindParam(':experience', $experience, PDO::PARAM_STR);
            $query->bindParam(':skill', $skill, PDO::PARAM_STR);
            $query->bindParam(':e_mobileno', $e_mobileno, PDO::PARAM_STR);
            $query->bindParam(':blood_group', $blood_group, PDO::PARAM_STR);
            $query->bindParam(':pan', $pan, PDO::PARAM_STR);
            $query->bindParam(':aadhaar', $aadhaar, PDO::PARAM_STR);
            $query->bindParam(':emp_designation', ucwords(htmlentities($designation)), PDO::PARAM_STR);
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();
            if ($lastInsertId) {
                //Code For Available Leaves Updated 
                // =========================================================
                    $updation_type = '2';
                    
                    $leave_update_sql = "INSERT INTO leave_counts(emp_id,total_il,total_cl,month,year,updation_type) VALUES(:empid,:total_il,:total_cl,:month,:year,:updation_type)";

                    $leave_update_query = $dbh->prepare($leave_update_sql);
                    $leave_update_query->bindParam(':empid', $lastInsertId, PDO::PARAM_STR);
                    $leave_update_query->bindParam(':total_il', $total_il, PDO::PARAM_STR);
                    $leave_update_query->bindParam(':total_cl', $total_cl, PDO::PARAM_STR);
                    $leave_update_query->bindParam(':month', $joining_month, PDO::PARAM_STR);
                    $leave_update_query->bindParam(':year', $joining_year, PDO::PARAM_STR);
                    $leave_update_query->bindParam(':updation_type', $updation_type, PDO::PARAM_STR);
                    // $leave_update_query->execute();

                    try {
                        $leave_update_query->execute();
                        $lastleaveId = $dbh->lastInsertId();
                    } catch(PDOException $e) {
                        // print_r($leave_update_query);
                        echo "<br>" . $e->getMessage();
                    }
                    if ($lastleaveId) {
                        echo '<script type="text/javascript">alert("Leaves Updated Successfully");</script>';

                        $msg = " Employee record added Successfully";
                        $to = $email;
                        $from = "kriti@netsutra.com";
                        $subject = "Welcome " . $fname;
                        $bcc = "";
                        $cc = "";
                        $msg_email = "<table><tr><td>Dear $fname,</td></tr>\r\n";
                        $msg_email .= "<tr><td><br><br>Congratulations!!<br><br>Your profile has been created with the company.</td></tr>\r\n";
                        $msg_email .= "<tr><td>Your Employee id is $empid.</td></tr>\r\n";
                        $msg_email .= "<tr><td>Your login credentials for Leave portal are as follows:- </td></tr>\r\n";
                        $msg_email .= "<tr><td><strong>URL: </strong><a href='http://www.netsutra.com/leave'>http://www.netsutra.com/leave</a>  <br> <strong> Login id: </strong> $email <br> <strong> Password: </strong> $password_email</td></tr>\r\n";
                        $msg_email .= "<tr> <td> <br /> <br /><strong>Regards,</strong><br>Manager- HR<br>Netsutra</td></tr>\r\n";
                        $msg_email .= "</table>";
                        send_email($to, $from, $bcc, $cc, $subject, $msg_email);

                        //Code for sent the Welcome mail to all 
                        $sql = "SELECT EmailId from  tblemployees WHERE Status=1";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $to_all = "";
                        foreach ($results as $result) {
                            $to_all = $to_all . $result->EmailId . ",";
                        }
                        $to_all = trim($to_all, ",");
                        if ($gender == 'Female') {
                            $gender_type = "Miss.";
                            $type = "She";
                            $type1 = "she";
                            $type2 = "her";
                        } else {
                            $gender_type = "Mr.";
                            $type = "He";
                            $type1 = "he";
                            $type2 = "him";
                        }
                        $fullname = $gender_type . " " . $fname . " " . $mname . " " . $lname;
                        $subject_all = "Let Us All Welcome " . $fname . "!!";
                        $msg_email_all = "<table><tr><td>Dear All,</td></tr>\r\n";
                        $msg_email_all .= "<tr><td><br>Greetings of the day!</td></tr>\r\n";
                        $msg_email_all .= "<tr><td><br>You will be glad to know that <strong>" . $fullname . "</strong> has joined our team on " . $joining . "<br>who brings a new lease of energy, experience, and enthusiasm along.</td></tr>\r\n";
                        $msg_email_all .= "<tr><td><br>" . $type . " has joined as <strong>" . ucwords(htmlentities($designation)) . ".</strong></td></tr>\r\n";
                        $msg_email_all .= "<tr><td><br>Let us make " . $type2 . " most comfortable and take every measure that " . $type1 . " is able to embrace our incredible work culture in the most natural way.</td></tr>\r\n";
                        $msg_email_all .= "<tr><td><br>Let us all welcome " . $type2 . " on board!</td></tr>\r\n";
                        $msg_email_all .= "<tr> <td> <br /> <br /><strong>Regards,</strong><br>Manager- HR<br>Netsutra</td></tr>\r\n";
                        $msg_email_all .= "</table>";
                        send_email($to_all, $from, $bcc, $cc, $subject_all, $msg_email_all);
                        //End code for sent the Welcome mail to all 

                    }else{
                        echo '<script type="text/javascript">alert("Leaves Updation Failed");</script>';

                        $error = " Something went wrong. Please try again";
                    }


                // ============================================================

                
            } else {
                $error = " Something went wrong. Please try again";
                //$error=$sql;
            }
        }
    }

?>


    <!DOCTYPE html>
    <html lang="en">

    <head>

        <!-- Title -->
        <title>Netsutra - Add Employee</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />

        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
        <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css" />
        <style>
            .errorWrap {
                color: red !important;
                padding: 10px;
                margin: 0 0 20px 0;
                background: #fff;
                border-left: 4px solid #dd3d36;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            }

            .succWrap {
                padding: 10px;
                margin: 0 0 20px 0;
                background: #fff;
                border-left: 4px solid #5cb85c;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
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
                    data: 'empcode=' + $("#empcode").val(),
                    type: "POST",
                    success: function(data) {
                        $("#empid-availability").html(data);
                        $("#loaderIcon").hide();
                    },
                    error: function() {}
                });
            }
        </script>

        <script>
            function checkAvailabilityEmailid() {
                $("#loaderIcon").show();
                jQuery.ajax({
                    url: "check_availability.php",
                    data: 'emailid=' + $("#email").val(),
                    type: "POST",
                    success: function(data) {
                        $("#emailid-availability").html(data);
                        $("#loaderIcon").hide();
                    },
                    error: function() {}
                });
            }

            function checkAvailabilityEmailidPers() {
                $("#loaderIcon").show();
                jQuery.ajax({
                    url: "check_availability.php",
                    data: 'emailid_pers=' + $("#email_pers").val(),
                    type: "POST",
                    success: function(data) {
                        $("#emailid-availability-pers").html(data);
                        $("#loaderIcon").hide();
                    },
                    error: function() {}
                });
            }
        </script>



    </head>

    <body>
        <?php include('includes/header.php'); ?>

        <?php include('includes/sidebar.php'); ?>
        <main class="mn-inner">
            <div class="row">
                <!-- <div class="col s12">
                        <div class="page-title">Add Employee</div>
                    </div>-->
                <div class="col s12 m12 l12">
                    <div class="card">
                        <div class="card-content">
                            <form id="example-form" method="post" name="addemp" onSubmit="return validateBloodGroup()">
                                <div>
                                    <h3>Add Employee</h3><br><br>
                                    <section>
                                        <div class="wizard-content">
                                            <div class="row">
                                                <!--<div class="col m6">-->
                                                <!--<div class="row">-->
                                                <?php if ($error) { ?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } else if ($msg) { ?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php } ?>


                                                <div class="input-field col m3 s12">
                                                    <span id="empid-availability" style="font-size:12px; display: none;"></span>
                                                    <label for="empcode">Employee Code(Must be unique)</label>
                                                    <input name="empcode" id="empcode" onBlur="checkAvailabilityEmpid()" type="text" autocomplete="off" required>
                                                </div>
                                                <div class="input-field col m3 s12">
                                                    <label for="firstName">First name</label>
                                                    <input id="firstName" name="firstName" type="text" required>
                                                </div>
                                                <div class="input-field col m3 s12">
                                                    <label for="middle_name">Middle name</label>
                                                    <input id="middle_name" name="middle_name" value="" type="text">
                                                </div>

                                                <div class="input-field col m3 s12">
                                                    <label for="lastName">Last name</label>
                                                    <input id="lastName" name="lastName" type="text" autocomplete="off" required>
                                                </div>
                                                <div class="input-field col m3 s12">
                                                    <label for="gender" class="label_up">Gender</label>
                                                    <select name="gender" autocomplete="off">
                                                        <option value="">Gender...</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                </div>

                                                <div class="input-field col m3 s12">
                                                    <label for="birthdate">Birthdate</label>
                                                    <input id="birthdate" name="dob" type="date" class="datepicker" autocomplete="off">
                                                </div>

                                                <div class="input-field col m3 s12">
                                                    <label for="anniversary">Anniversary</label>
                                                    <input id="anniversary" name="anniversary" type="date" class="datepicker" autocomplete="off">
                                                </div>

                                                <div class="input-field col m3 s12">
                                                    <select name="designation" autocomplete="off">
                                                        <option value="">Designation</option>
                                                        <?php $sql = "SELECT DesignationType FROM tbldesignationtype";
                                                        $query = $dbh->prepare($sql);
                                                        $query->execute();
                                                        $results_designation = $query->fetchAll(PDO::FETCH_OBJ);
                                                        $cnt = 1;
                                                        if ($query->rowCount() > 0) {
                                                            foreach ($results_designation as $rs_designation) {   ?>
                                                                <option value="<?php echo htmlentities(strtolower($rs_designation->DesignationType)); ?>"><?php echo htmlentities($rs_designation->DesignationType); ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </div>

                                                <div class="input-field col m6 s12">
                                                    <label for="email">Official Email</label>
                                                    <input name="email" type="email" id="email" onBlur="checkAvailabilityEmailid()" autocomplete="off" required>
                                                    <span id="emailid-availability" style="font-size:12px;"></span>
                                                </div>
                                                <div class="input-field col m6 s12">
                                                    <label for="email_pers">Personal Email</label>
                                                    <input name="email_pers" type="email" id="email_pers" onBlur="checkAvailabilityEmailidPers()" autocomplete="off" required>
                                                    <span id="emailid-availability-pers" style="font-size:12px;"></span>
                                                </div>

                                                <div class="input-field col m3 s12">
                                                    <label for="phone">Mobile number</label>
                                                    <input id="phone" name="mobileno" type="tel" maxlength="15" autocomplete="off" required>
                                                </div>
                                                <div class="input-field col m3 s12">
                                                    <label for="e_mobileno">Emergency Contact Name and Number</label>
                                                    <input id="e_mobileno" name="e_mobileno" type="text" value="" autocomplete="off" required>
                                                </div>
                                                <div class="input-field col m3 s12">
                                                    <label for="joining">Joining Date</label>
                                                    <input id="joining" name="joining" type="date" class="datepicker" autocomplete="off">
                                                </div>
                                                <div class="input-field col m3 s12">
                                                    <select name="shift" autocomplete="off">
                                                        <option value="">Shift timing</option>
                                                        <option value="9:00 AM to 6:00 PM">9:00 AM to 6:00 PM</option>
                                                        <option value="9:30 AM to 6:30 PM">9:30 AM to 6:30 PM</option>
                                                        <option value="10:00 AM to 7:00 PM">10:00 AM to 7:00 PM</option>
                                                        <option value="10:30 AM to 7:30 PM">10:30 AM to 7:30 PM</option>
                                                        <option value="11:00 AM to 8:00 PM">11:00 AM to 8:00 PM</option>
                                                        <option value="11:30 AM to 8:30 PM">11:30 AM to 8:30 PM</option>
                                                        <option value="12:00 AM to 9:00 PM">12:00 AM to 9:00 PM</option>
                                                        <option value="not applicable">Not Applicable</option>
                                                    </select>
                                                </div>

                                                <div class="input-field col m6 s12">
                                                    <label for="address">Current Address</label>
                                                    <input id="address" name="address" type="text" autocomplete="off" required>
                                                </div>
                                                <div class="input-field col m6 s12">
                                                    <label for="p_address">Permanent Address</label>
                                                    <input id="p_address" name="p_address" type="text" value="" autocomplete="off" required>
                                                </div>

                                                <div class="input-field col m3 s12">
                                                    <label for="reporting_manager" class="label_up">Reporting Manager</label>
                                                    <select name="reporting_manager" id="reporting_manager" required>
                                                        <?php $sql = "SELECT  FirstName, LastName, id from tblemployees where UserType= 'project manager' or UserType= 'director' order by FirstName";
                                                        $query = $dbh->prepare($sql);
                                                        $query->execute();
                                                        $results_manager = $query->fetchAll(PDO::FETCH_OBJ);
                                                        $cnt = 1;
                                                        if ($query->rowCount() > 0) {
                                                            foreach ($results_manager as $rs_manager) {   ?>
                                                                <option value="<?php echo htmlentities($rs_manager->id); ?>"><?php echo htmlentities($rs_manager->FirstName) . " " . htmlentities($rs_manager->LastName); ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                                <div class="input-field col m3 s12">
                                                    <label for="experience">Previous Experience</label>
                                                    <input id="experience" name="experience" type="text" class="isNumeric" value="" autocomplete="off">
                                                    
                                                </div>
                                                <div class="input-field col m3 s12">
                                                    <!-- <label for="experience1">Previous Experience</label> -->
                                                    <select id="experience_label" name="experience_label">
                                                        <option value="" selected hidden>Previous Experience</option>
                                                        <option value="1">Year(s)</option>
                                                        <option value="2">Month(s)</option>
                                                    </select>
                                                </div>
                                                <div class="input-field col m3 s12">
                                                    <label for="blood_group">Blood Group</label>
                                                    <input id="blood_group" name="blood_group" type="text" value="" autocomplete="off">
                                                </div>
                                                <div class="input-field col s12">
                                                    <label for="skill">Skill Set</label>
                                                    <input id="skill" name="skill" type="text" value="" autocomplete="off">
                                                </div>
                                                <!-- <div class="input-field col m6 s12">
                                                    <label for="il_text">Total IL</label>
                                                    <input type="text" name="il_text" id="il_text" value="0" />
                                                </div>
                                                <div class="input-field col m6 s12">
                                                    <label for="cl_text">Total CL</label>
                                                    <input type="text" name="cl_text" id="cl_text" value="0" />
                                                </div> -->
                                                <!-- <div class="input-field col m4 s12">
                                                    <label for="co_text">Total CO</label>
                                                    <input type="text" name="co_text" id="co_text" value="0" />
                                                </div> -->
                                                <div class="input-field col m6 s12">
                                                    <label for="pan_text">Pan Card</label>
                                                    <input type="text" name="pan_text" id="pan_text" value="" />
                                                </div>
                                                <div class="input-field col m6 s12">
                                                    <label for="aadhaar_text">Aadhaar Card</label>
                                                    <input type="text" name="aadhaar_text" id="aadhaar_text" value="" />
                                                </div>
                                                <div class="input-field col s12">
                                                    <button type="submit" name="add" onclick="return valid();" id="add" class="waves-effect waves-light btn indigo m-b-xs">ADD</button>
                                                </div>
                                                <!--</div>-->
                                                <!--</div>-->

                                                <!--<div class="col m6">-->
                                                <!--<div class="row">-->

                                                <!--<div class="input-field col m6 s12">
<label for="il_text">Total CL</label>
<input type="text" name="cl_text" id="cl_text" />
</div>-->
                                                <!--<div class="input-field col m6 s12">
<label for="il_text">Total CO</label>
<input type="text" name="co_text" id="co_text" />
</div>
<div class="input-field col m6 s12">
<button type="submit" name="add" onclick="return valid();" id="add" class="waves-effect waves-light btn indigo m-b-xs">ADD</button>
</div>-->

                                                <!--</div>-->

                                                <!-- </div>-->

                                            </div>

                                        </div>
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
            function validateBloodGroup() {
                blood_group = document.getElementById("blood_group").value;
                if (blood_group == "") {
                    response = confirm("Blood group is empty, would you like to continue?");
                    if (!response) {
                        return false;
                    }
                }
            }

            $( document ).ready(function() {
                $(document).on('input', '.isNumeric', function() {
                    this.value = this.value.replace(/[^0-9\.]/g, '');
                });
            });
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
