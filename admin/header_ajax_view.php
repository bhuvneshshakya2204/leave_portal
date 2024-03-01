<?php
session_start();
error_reporting(0);
include('includes/config.php');
//include('../includes/send_email.php');
?>
<ul class="right col s9 m3 nav-right-menu">
                        
<li class="hide-on-small-and-down"><a href="javascript:void(0)" data-activates="dropdown1" class="dropdown-button dropdown-right show-on-large"><i class="material-icons" id="show_notification">notifications_none</i>
<?php 
//$isread=0;
$m_id= $_SESSION['eid'];
if(strlen($_SESSION['mlogin'])!=0){
	$isread_manager= 0;
	$sql = "SELECT id from tblleaves where IsRead_Manager=:isread_manager and ManagerID=:m_id";
}else{
	$isread= 0;
	$sql = "SELECT id from tblleaves where IsRead=:isread";
}
$query = $dbh -> prepare($sql);
if(strlen($_SESSION['mlogin'])!=0){
	$query->bindParam(':m_id',$m_id,PDO::PARAM_STR);
	$query->bindParam(':isread_manager',$isread_manager,PDO::PARAM_STR);
}else{
	$query->bindParam(':isread',$isread,PDO::PARAM_STR);
}
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$unreadcount=$query->rowCount();?>


                                <span class="badge"><?php echo htmlentities($unreadcount);?></span></a></li>
                            <li class="hide-on-med-and-up"><!--<a href="javascript:void(0)" class="search-toggle"><i class="material-icons">search</i></a>--></li>
                        </ul>
                        
                        <ul id="dropdown1" class="dropdown-content notifications-dropdown" style="width: 52px; position: absolute; top: 0px; left: 1017.75px; opacity: 1; display: none; max-height: 570px; background: #ffffff;">
                            <li class="notificatoins-dropdown-container">
                                <ul>
                                    <li class="notification-drop-title">Notifications</li>
<?php 
//$isread=0;
if(strlen($_SESSION['mlogin'])!=0)
	$sql = "SELECT tblleaves.id as lid,tblemployees.FirstName,tblemployees.LastName,tblemployees.EmpId,tblleaves.PostingDate from tblleaves join tblemployees on tblleaves.empid=tblemployees.id where tblleaves.IsRead_Manager=:isread_manager and tblleaves.ManagerID=:m_id";
else
	$sql = "SELECT tblleaves.id as lid,tblemployees.FirstName,tblemployees.LastName,tblemployees.EmpId,tblleaves.PostingDate from tblleaves join tblemployees on tblleaves.empid=tblemployees.id where tblleaves.IsRead=:isread";
$query = $dbh -> prepare($sql);
if(strlen($_SESSION['mlogin'])!=0){
	$query->bindParam(':m_id',$m_id,PDO::PARAM_STR);
	$query->bindParam(':isread_manager',$isread_manager,PDO::PARAM_STR);
}else{
	$query->bindParam(':isread',$isread,PDO::PARAM_STR);
}
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?>  


                                    <li>
                                        <a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));  //echo htmlentities($result->lid);?>">
                                        <div class="notification">
                                            <div class="notification-icon circle cyan"><i class="material-icons">done</i></div>
                                            <div class="notification-text"><p><b><?php echo htmlentities($result->FirstName." ".$result->LastName);?><br />(<?php echo htmlentities($result->EmpId);?>)</b> applied for leave</p><span>at <?php echo htmlentities($result->PostingDate);?></b</span></div>
                                        </div>
                                        </a>
                                    </li>
                                   <?php }} ?>
                                   
                                  
                        </ul>
						</li>
						</ul>
						<style>
						.ajax_style{
							width: 52px; position: absolute; top: 0px; left: 1017.75px; opacity: 1; display: block; max-height: 570px; background: #ffffff;
						}
						</style>
						<!--<script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>-->
						<script>
							$(document).ready(function(){
								$("#show_notification").click(function(){
									//$(this).addClass("active");
									$(".notifications-dropdown").slideToggle("fast");
								});
							});
						</script>