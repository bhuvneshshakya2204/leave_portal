<?php
session_start();
error_reporting(1);
include('includes/config.php');
include('../shared/email-template.php');
$error= 0;
$msg= 0;
if(strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
	die();
}
$successMsg = "";
$errorMsg = "";
date_default_timezone_set('Asia/Kolkata');
$eid=$_SESSION['eid'];
if(isset($_POST['send'])){
    // print_r($_POST);

    $mispunch_time = $_POST['time'];
    $mispunch_time_tmp = explode(":", $mispunch_time);
    $chours = $mispunch_time_tmp[0];
    $cmins = $mispunch_time_tmp[1];
    $meridian;
    if($chours > 12){
        $meridian = 'PM';
        $chours -=  12;
    }else if ($chours < 12){
        $meridian = 'AM';
    }else{
        $meridian = 'PM';
    }

    $time = $chours.':'.$cmins.' '.$meridian;
    $empname = $_POST['nameFilter'];
    $manager = $_POST['manager'];
    $date = $_POST['date'];
    
    $empid = $_POST['empid'];
    $type = $_POST['type'];

    if($manager <= 0 || $empid <= 0 || trim($date) == "" || trim($time) == "" || trim($type) == ""){
        $errorMsg = "Please fill all the fields";
    }else{
        $insertId = addMissPunch($empid, $manager, $date, $type, $time);
        if($insertId <= 0){
            $errorMsg = "Unable to send miss punch request.";
        }else{
            $sql = "select e.*,e1.FirstName as mFirstName,e1.LastName as mLastName,
            e1.EmailId as ManagerEmail from tblemployees e 
            LEFT JOIN tblemployees e1 ON e.reporting_manager = e1.id 
            where e.id=$empid";
            $query = $dbh -> prepare($sql);
            $query->execute();
            $empDetail = $query->fetch(PDO::FETCH_OBJ);

            $id_encoded= base64_encode(base64_encode(base64_encode($insertId)));
            $m_approve= base64_encode(base64_encode(base64_encode("approved_ok")));
            $m_disapprove= base64_encode(base64_encode(base64_encode("disapproved_not_ok")));

            $arr = array(
                'MISS_PUNCH_TYPE'         => ($type==1)?'In-Time':'Out-Time', 
                'DATE'    => $date,
                'TIME'    => $time,
                'EMP_NAME'          => $empDetail->FirstName." ".$empDetail->LastName,
                'LINK_APPROVE'      => SITE_URL.'m_action/misspunch_action.php?id='.$id_encoded.'&act='.$m_approve,
                'LINK_DISAPPROVE'   => SITE_URL.'m_action/misspunch_action.php?id='.$id_encoded.'&act='.$m_disapprove,
                'SIGNATURE_NAME'    => 'Kriti'
            );
            $email_body = getEmailContent($arr,EMAIL_MISS_PUNCH_MGR);

            $subject= "Application for miss punch approval";
            $bcc= "";
            $cc= "";
            send_email($empDetail->ManagerEmail, 'kriti@netsutra.com', $bcc, $cc, $subject, $email_body);

            $arr = array(
                'EMP_NAME'          => $empDetail->FirstName." ".$empDetail->LastName,
                'MISS_PUNCH_TYPE'   => ($type==1)?'in time':'out time', 
                'DATE'              => $date,
                'TIME'              => $time,
                'SIGNATURE_NAME'    => 'Kriti'
            );
            $email_body = getEmailContent($arr,EMAIL_MISS_PUNCH_EMP);

            $subject= "Application for miss punch approval";
            $bcc= "";
            $cc= "";
            send_email($empDetail->EmailId, 'kriti@netsutra.com', $bcc, $cc, $subject, $email_body);

            $successMsg = "Miss punch approval request sent to manager successfully.";
        }
    }
}

function addMissPunch($empid, $managerid, $date, $type, $time){
    global $dbh;
    $sql = "INSERT IGNORE INTO tblmisspunch set empid=$empid, managerid=$managerid, type=$type, misspunch_date='$date', misspunch_time='$time'";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId <= 0 ){
        $sql = "select * from tblmisspunch where empid=$empid AND managerid=$managerid AND type=$type AND misspunch_date='$date'";
        $query = $dbh -> prepare($sql);
        $query->execute();
        $missPunchDetail = $query->fetch(PDO::FETCH_OBJ);
        return (isset($missPunchDetail->id)) ? $missPunchDetail->id : 0;
    }else{
        return $lastInsertId;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Title -->
        <title>Netsutra - Miss Punch</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet"> 
        <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
  	<?php include('includes/header.php');?> 
    <?php include('includes/sidebar.php');?>
   	<main class="mn-inner">
        <div class="row">
            <div class="col s12">
                <div class="page-title">Request for Miss Punch Approval</div>
            </div>
            <div class="col s12 m12 l8">
                <div class="card">
                    <div class="card-content">
                        <form id="leave_form" method="post" name="addemp" onSubmit="return checkValidation()">
                            <h3>Request for Miss Punch Approval</h3>
                            <section>
                                <div class="wizard-content">
                                    <?php 
                                    if(trim($errorMsg) != "")
                                        echo '<div class="errorWrap"><strong>ERROR</strong>:'.htmlentities($errorMsg).'</div>';
                                    if(trim($successMsg) != "")
                                        echo '<div class="succWrap"><strong>SUCCESS</strong>:'.htmlentities($successMsg).'</div>';
                                    ?>
                                    <div class="row">

										<div class="input-field col  s12">
											<span class="label_up">Employee Name</span>
											<input id="nameFilter" name="nameFilter" value="<?php echo $_POST['nameFilter'] ?>">
											<input type="hidden" name="empid" id="empid" value="<?php echo $_POST['empid'] ?>">
										</div>

                                        <div class="input-field col m6  s12">
                                            <select  name="type" id="type" style="border: 2px solid red!important;">
                                                <option value="" selected>Select Type...</option>
                                                <option value="1" <?php echo ($type == 'in time') ? 'selected':'' ?> >In Time</option>
                                                <option value="2" <?php echo ($type == 'out time') ? 'selected':'' ?> >Out Time</option>
                                            </select>
                                        </div>

										<div class="input-field col m6 s12" style="text-align: left;">
											<select  name="manager" id="manager" style="border: 2px solid red!important;">
												<option value="" selected>Select Manager...</option>
												<?php $sql = "SELECT  FirstName, LastName, id from tblemployees where UserType= 'project manager' or UserType= 'director'";
												$query = $dbh -> prepare($sql);
												$query->execute();
												$results=$query->fetchAll(PDO::FETCH_OBJ);
												$cnt=1;
												foreach($results as $result)
												{   ?>                                            
												<option value="<?php echo htmlentities($result->id);?>" <?php echo ($manager == $result->id) ? 'selected':'' ?> >
                                                    <?php echo htmlentities($result->FirstName)." ".htmlentities($result->LastName);?>
                                                </option>
												<?php } ?>
											</select>
										</div>

										<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
									  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
									  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
										<script>
											  $( function() {
												$( ".datepicker_apply_leave" ).datepicker();
												$( ".datepicker_apply_leave" ).datepicker("option", "dateFormat", "dd-mm-yy");
											  } );
										</script>

										<div class="input-field col m6 s12">
											<span style="color: #7F9FAF; font-family: Roboto, sans-serif; font-size: 11px;" id="label_changable">Date</span>
											<input type="text" id="" name="date"  class="datepicker_apply_leave from_date" size="30" readonly="readonly" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd/mm/yyyy" />
										</div>
                                        <div class="input-field col m6 s12">
                                            <span style="color: #7F9FAF; font-family: Roboto, sans-serif; font-size: 11px;" id="label_changable">Time</span>
											<input type="time" id="" name="time"  class="timepicker_apply_leave from_time" size="30" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" />
                                        </div> 

                                        <div class="input-field col  s12">
                                            <button type="submit" name="send" class="waves-effect waves-light btn indigo m-b-xs">Send</button>   
                                        </div>                                        

                                    </div>
                                </div>
                            </section>
                                     
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="left-sidebar-hover"></div>
        
    <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
    <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
    <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
    <script src="../assets/js/alpha.min.js"></script>
    <script>
	$('#nameFilter').autocomplete({
        type: "POST",
        minLength: 3,
        source : function (request, response) 
        {                         
            var source_url = "leaves-data.php?action=name";

            $.ajax({
                url: source_url,
                dataType: "json",
                data: request,
                success: function (data) { response(data); },
                error : function (a,b,c) { HandleLookUpError(a); }
                });
        },                
        select: function (event, ui) { $('#empid').val(ui.item.id);getManager(); }               
    });
    function getManager(){
        var empid =  $('#empid').val();
        $.ajax({
            url: "get-manager.php",
            dataType: "json",
            data: {empid:empid},
            method:"POST",
            success: function (res) { 
                $("#manager").val(res.manager.managerid);
                $("#manager").material_select();
            },
            error : function (a,b,c) { HandleLookUpError(a); }
        });
    }
	</script>
    </body>
</html>