<?php
session_start();
error_reporting(0);
include('includes/config.php');
include('../includes/send_email.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
}
else{

// code for update the read notification status
$did= base64_decode(base64_decode($_GET['leaveid']));
if(isset($_SESSION['mlogin'])){
	$isread_manager= 1;
	$sql="update tblleaves set IsRead_Manager=:isread_manager where id=:did";
}else{	
	$isread= 1;
	$sql="update tblleaves set IsRead=:isread where id=:did";
}
//$did=intval($_GET['leaveid']);  
date_default_timezone_set('Asia/Kolkata');
$admremarkdate=date('Y-m-d G:i:s ', strtotime("now"));
//$sql="update tblleaves set IsRead=:isread where id=:did";
$query = $dbh->prepare($sql);
if(isset($_SESSION['mlogin']))
	$query->bindParam(':isread_manager',$isread_manager,PDO::PARAM_STR);
else
	$query->bindParam(':isread',$isread,PDO::PARAM_STR);
$query->bindParam(':did',$did,PDO::PARAM_STR);
$query->execute();

// code for action taken on leave
if(isset($_POST['update']))
{ 
$did= base64_decode(base64_decode($_GET['leaveid']));
$description=$_POST['description'];
$status=$_POST['status'];   
date_default_timezone_set('Asia/Kolkata');
$admremarkdate=date('Y-m-d G:i:s ', strtotime("now"));
$sql="update tblleaves set AdminRemark=:description,Status=:status,AdminRemarkDate=:admremarkdate where id=:did";
$query = $dbh->prepare($sql);
$query->bindParam(':description',$description,PDO::PARAM_STR);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->bindParam(':admremarkdate',$admremarkdate,PDO::PARAM_STR);
$query->bindParam(':did',$did,PDO::PARAM_STR);
$query->execute();
//Code to send an email to employee
$sql="select FirstName, EmailId, ManagerID, FromDate, ToDate, LeaveType from tblemployees as e
	  join tblleaves as l on e.id= l.empid
	  where l.id=:did";
$query = $dbh->prepare($sql);
$query->bindParam(':did',$did,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll();
foreach($results as $result){}
$fname= $result['FirstName'];
$to= $result['EmailId'];
$from_date= $result['FromDate'];
$to_date= $result['ToDate'];
$managerId= $result['ManagerID'];
$leave_type= $result['LeaveType'];
$userType= "manager";
$sql= "select FirstName, LastName, EmailId from tblemployees where id=:managerId and UserType=:userType";
$query = $dbh->prepare($sql);
$query->bindParam(':managerId',$managerId,PDO::PARAM_STR);
$query->bindParam(':userType',$userType,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll();
foreach($results as $result){}
$m_f_name= $result['FirstName'];
$m_l_name= $result['LastName'];
$from= $result['EmailId'];
$bcc= "gurjeet.singh@netsutra.com";
$cc= "";
$subject= "Re: Application for ".$leave_type;
if($status== 1)
	$status_inword= "Approved";
else
	$status_inword= "Not Approved";
$manager_name= htmlentities($m_f_name." ".$m_l_name);	
$email_body= "<table><tr><td><strong>Dear $fname</strong><br><br>\r\n";
			$email_body.= "<tr><td>Your leave from $from_date to $to_date is $status_inword.</td></tr>\r\n";
			if($description!= "")
				$email_body.= "<tr><td colspan='2'><strong>Description:</strong> $description</td></tr>\r\n";
			$email_body.= "<tr><td><br><font face='verdana' size='-1' color='navy'><strong>Thanks,</strong><br>$manager_name</font></td></tr></table>\r\n";
			send_email($to, $from, $bcc, $cc, $subject, $email_body);
$msg="Leave updated Successfully";
}



 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Leave Details </title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
        <link href="../assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">

                <link href="../assets/plugins/google-code-prettify/prettify.css" rel="stylesheet" type="text/css"/>  
        <!-- Theme Styles -->
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
        </style>
    </head>
    <body>
       <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
            <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">Leave Details</div>
                    </div>
                   
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                                <h3>Leave Details</h3><br><br>
                                <?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
                                <table id="example" class="display responsive-table ">
                               
                                 
                                    <tbody>
<?php 
$lid= base64_decode(base64_decode($_GET['leaveid']));
$sql = "SELECT tblleaves.id as lid,tblemployees.FirstName,tblemployees.LastName,tblemployees.EmpId,tblemployees.id,tblemployees.Gender,tblemployees.Phonenumber,tblemployees.EmailId,tblleaves.LeaveType,tblleaves.ToDate,tblleaves.FromDate,tblleaves.Description,tblleaves.PostingDate,tblleaves.Status,tblleaves.AdminRemark,tblleaves.AdminRemarkDate from tblleaves join tblemployees on tblleaves.empid=tblemployees.id where tblleaves.id=:lid";
$query = $dbh -> prepare($sql);
$query->bindParam(':lid',$lid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{         
      ?>  

                                        <tr>
                                            <td style="font-size:16px;"> <b>Employe Name :</b></td>
                                              <td><a href="editemployee.php?empid=<?php echo htmlentities($result->id);?>" target="_blank">
                                                <?php echo htmlentities($result->FirstName." ".$result->LastName);?></a></td>
                                              <td style="font-size:16px;"><b>Emp Id :</b></td>
                                              <td><?php echo htmlentities($result->EmpId);?></td>
                                              <td style="font-size:16px;"><b>Gender :</b></td>
                                              <td><?php echo htmlentities($result->Gender);?></td>
                                          </tr>

                                          <tr>
                                             <td style="font-size:16px;"><b>Emp Email ID :</b></td>
                                            <td><?php echo htmlentities($result->EmailId);?></td>
                                             <td style="font-size:16px;"><b>Emp Contact No. :</b></td>
                                            <td><?php echo htmlentities($result->Phonenumber);?></td>
                                            <td>&nbsp;</td>
                                             <td>&nbsp;</td>
                                        </tr>

  <tr>
                                             <td style="font-size:16px;"><b>Leave Type :</b></td>
                                            <td><?php echo htmlentities($result->LeaveType);?></td>
                                             <td style="font-size:16px;"><b>Leave Date :</b></td>
                                            <td><b>From</b> <?php echo htmlentities($result->FromDate);?> <b>to</b> <?php echo htmlentities($result->ToDate);?></td>
                                            <td style="font-size:16px;"><b>Posting Date</b></td>
                                           <td><?php echo htmlentities($result->PostingDate);?></td>
                                        </tr>

<tr>
                                             <td style="font-size:16px;"><b>Employe Leave Description : </b></td>
                                            <td colspan="5"><?php echo htmlentities($result->Description);?></td>
                                          
                                        </tr>

<tr>
<td style="font-size:16px;"><b>Leave Status :</b></td>
<td colspan="5"><?php $stats=$result->Status;
if($stats==1){
?>
<span style="color: green">Approved</span>
 <?php } if($stats==2)  { ?>
<span style="color: red">Not Approved</span>
<?php } if($stats==0)  { ?>
 <span style="color: blue">Waiting for approval</span>
 <?php } ?>
</td>
</tr>

<tr>
<td style="font-size:16px;"><b>Manager Remark: </b></td>
<td colspan="5"><?php
if($result->AdminRemark!=""){
  echo htmlentities($result->AdminRemark);  
}
?></td>
 </tr>

 <tr>
<td style="font-size:16px;"><b>Action Date : </b></td>
<td colspan="5"><?php
if($result->AdminRemarkDate==""){
  echo "NA";  
}
else{
echo htmlentities($result->AdminRemarkDate);
}
?></td>
 </tr>
<?php 
if($stats==0)
{

?>
<?php
 if(strlen($_SESSION['mlogin'])!=0){ ?>
<tr>
 <td colspan="5">
  <a class="modal-trigger waves-effect waves-light btn" href="#modal1" id="take_action">Take&nbsp;Action</a>
<form name="adminaction" method="post" onSubmit="return validateAction();" id="take_action_form">
<div id="modal1" class="modal modal-fixed-footer" style="height: 60%; display: block;">
    <div class="modal-content" style="width:90%">
        <h4>Leave take action</h4>
          <select class="browser-default" name="status" id="take_action_dropdown">
                                            <option value="">Choose your option</option>
                                            <option value="1">Approved</option>
                                            <option value="2">Not Approved</option>
                                        </select></p>
                                        <p><textarea id="textarea1" name="description" class="materialize-textarea" name="description" placeholder="Description" length="500" maxlength="500"></textarea></p>
    </div>
    <div class="modal-footer" style="width:90%">
       <input type="submit" class="waves-effect waves-light btn blue m-b-xs" name="update" id="take_action_btn" value="Submit" />
	   <input type="button" name="cancel_btn" id="cancel_btn1" value="Cancel" style="margin-right: 7px;" class="waves-effect waves-light btn blue m-b-xs modal-close" />
    </div>

</div>   

 </td>
</tr>
 <?php } ?>
<?php } ?>
   </form>                                     </tr>
                                         <?php $cnt++;} }?>
                                    </tbody>
                                </table>
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
        <script src="../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
        <script src="../assets/js/pages/table-data.js"></script>
         <script src="assets/js/pages/ui-modals.js"></script>
        <script src="assets/plugins/google-code-prettify/prettify.js"></script>
		<script>
		$('#take_action_form').find('select').on('focus', function(){
					$(this).removeClass('red_border_take_action');
				});
				$('#take_action_form').find('select').on('blur',function(){
					$(this).closest('select').each(function(){
					var inputval = $(this).val();
					if(inputval== null || inputval == "")
					{
						$(this).addClass('red_border_take_action');	
					}
				//});	
			});
	});	
		function validateAction(){
			if($("#take_action_dropdown").val()== ""){
				$("#take_action_dropdown").addClass("red_border_take_action");
				return false;
			}
		}
		</script>
    </body>
</html>
<?php } ?>