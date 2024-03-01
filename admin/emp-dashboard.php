<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0)
{   
    header('location:../index.php');
}
else
{
$eid=$_SESSION['mlogin'];
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Dashboard</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">    
        <link href="../assets/plugins/metrojs/MetroJs.min.css" rel="stylesheet">
        <link href="../assets/plugins/weather-icons-master/css/weather-icons.min.css" rel="stylesheet">

        	
        <!-- Theme Styles -->
        <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
        <style type="text/css">
            .tab,.tab_email,.tab_email_anni
            {
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <?php include('includes/header.php');?>            
        <?php include('includes/sidebar.php');?>

            <main class="mn-inner">
                <!-- <div class="middle-content"> -->
                <div class="content">
                    <!--<div class="row no-m-t no-m-b">-->
					<h4 style="text-align: left;margin-left: 10px;">Welcome to Employee Leave Management System</h4>
					<br>
					<div class="row">
					<a href="#wish_employee" class="modal-trigger">
                        <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content">
                            
                                <span class="card-title">Birthday(s) Today</span><br>
                                <?php
                                    date_default_timezone_set('Asia/Kolkata');
                                    $today_date= date("Y-m-d");
                                    $date_object = new DateTime($today_date);
                                    $today_date = $date_object->format('j F');
                                    $sql = "SELECT FirstName, LastName, Dob, id, EmailId from tblemployees where Status=1 and Dob like '".$today_date."%'";
                                    $query = $dbh -> prepare($sql);
                                    $query->execute();
                                    $results_wish=$query->fetchAll(PDO::FETCH_OBJ);
                                    $wish_count=$query->rowCount();
                                ?>                            
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        <?php echo htmlentities($wish_count);?>
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
					</a>
                    <a href="#wish_employee_anniversary" class="modal-trigger">
                        <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content">
                            
                                <span class="card-title">Anniversary(s) Today</span><br>
                                <?php
                                    $sql = "SELECT FirstName, LastName, AnniversaryDate, id, EmailId from tblemployees where Status=1 and AnniversaryDate like '".$today_date."%'";
                                    $query = $dbh -> prepare($sql);
                                    $query->execute();
                                    $results_wish_anniversary=$query->fetchAll(PDO::FETCH_OBJ);
                                    $wish_count_anniversary=$query->rowCount();
                                ?>                            
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        <?php echo htmlentities($wish_count_anniversary);?>
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
					</a>

                    <a href="#wish_employee_work_anniversary" class="modal-trigger">
                        <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content">
                            
                                <span class="card-title">Work Anniversary(s) Today</span><br>
                                <?php
                                    $sql = "SELECT FirstName, LastName, JoiningDate, id, EmailId from tblemployees where Status=1 and JoiningDate like '".$today_date."%'";
                                    $query = $dbh -> prepare($sql);
                                    $query->execute();
                                    $results_wish_anniversary=$query->fetchAll(PDO::FETCH_OBJ);
                                    $wish_count_anniversary=$query->rowCount();
                                ?>                            
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        <?php echo htmlentities($wish_count_anniversary);?>
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
					</a>

                    <a href="leaves.php">
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content">
                            
                                <span class="card-title">All Leaves</span><br>
                                <span class="stats-counter">
                                    <span class="">&nbsp;</span>
                                </span>                         
                                
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    </a>

                    <a href="pending-leavehistory.php">
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content">
                            
                                <span class="card-title">Pending Leaves</span><br>
                                <span class="stats-counter">
                                    <span class="">&nbsp;</span>
                                </span>                          
                                
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    </a>


                    <a href="miss_punch_detail.php">
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content">
                            
                                <span class="card-title">Miss Punch</span><br>
                                <span class="stats-counter">
                                    <span class="">&nbsp;</span>
                                </span>                          
                                
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    </a>

                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content" style="min-height: 140px;">
                            
                                <span class="card-title">Leaves For <?php echo date('F')?></span>
                                <?php
                                    $month = date('m');
                                    $year = date('Y');
                                    $sql = "SELECT lc.* from leave_counts lc where lc.emp_id = ".$eid ." AND lc.month =".$month." AND lc.year =".$year;
                                    $query = $dbh -> prepare($sql); 
                                    $query->execute();
                                    $result=$query->fetch(PDO::FETCH_OBJ);
                                    $empId =   intval($result->EmpId);
                                    $totalIL = intval($result->total_il);
                                    $totalCL = intval($result->total_cl);
                                    $totalCO = intval($result->total_co);
                                ?>                            
                                <span class="stats-counter" style="font-size: 12px;font-weight: bold;">
                                    <!-- <span class="counter"> -->
                                        <div >
                                            <div class="col s6">IL</div>
                                            <div class="col s6">CL</div>
                                        </div>
                                        <div>
                                            <div class="col s6"><?php echo $totalIL ?></div>
                                            <div class="col s6"><?php echo $totalCL ?></div>
                                        </div>
                                </span>                       
                                
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>

                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content" style="min-height: 140px;">
                            
                                <span class="card-title">Leaves For <?php echo date('F',strtotime(date('Y-m')." -1 month"))?></span>
                                <?php
                                    $month = date('m');
                                    $month = intval($month - 1);
                                    $year = date('Y');
                                    $sql = "SELECT lc.* from leave_counts lc where lc.emp_id = ".$eid ." AND lc.month =".$month." AND lc.year =".$year;
                                    $query = $dbh -> prepare($sql); 
                                    $query->execute();
                                    $result=$query->fetch(PDO::FETCH_OBJ);
                                    $empId =   intval($result->EmpId);
                                    $totalIL = intval($result->total_il);
                                    $totalCL = intval($result->total_cl);
                                    $totalCO = intval($result->total_co);
                                ?>                            
                                <span class="stats-counter" style="font-size: 12px;font-weight: bold;">
                                    <!-- <span class="counter"> -->
                                        <div >
                                            <div class="col s6">IL</div>
                                            <div class="col s6">CL</div>
                                        </div>
                                        <div>
                                            <div class="col s6"><?php echo $totalIL ?></div>
                                            <div class="col s6"><?php echo $totalCL ?></div>
                                        </div>
                                </span>                       
                                
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>

                </div>
            </main>
          
        </div>
		<!--code for birthday model strats-->
			<div id="wish_employee" class="modal modal-fixed-footer" style="height: 60%; display: block;">
				<div class="modal-content" style="width:100%">
				<span class="card-title" style="font-size: 14px; font-weight: bold;">BIRTHDAY</span>
					<table width="100%" class="display responsive-table">
					<thead>
						<tr>
							<th style="text-align:center;">Employee Name</th>
							<th style="text-align:center;">Birthday</th>
							<th style="text-align:center;">Send Message</th>
						</tr>
						</thead>
						<tbody>
						<?php
							foreach($results_wish as $emp_record){
							$emp_name= htmlentities($emp_record->FirstName." ".$emp_record->LastName);
						?>
						<tr>
							<td style="text-align:center;"><span style="color: #0277BB;"><?php echo $emp_name; ?></span></td>
							<td style="text-align:center;"><?php echo htmlentities($emp_record->Dob); ?></td>
							<td style="text-align:center;">
                                <?php if($_SESSION['mlogin'] != $emp_record->EmailId){?>
                                <a href="#wish_employee_view" class="modal-trigger">
                                    <i class="material-icons icon_hover email_birthday_view" title="View Message" data-to="<?php echo $emp_record->id; ?>">edit</i>
                                </a>
                                <i class="material-icons tab_email" title="Send Message" data-to="<?php echo $emp_record->id; ?>">email</i>
                                <?php }else{ echo "N/A";}?>
                            </td>
						</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="modal-footer" style="width:100%;">
				   <input type="button" name="cancel_btn" value="Close" class="waves-effect waves-light btn blue m-b-xs modal-close" style="margin-right: 10px;" />
				</div>
			</div>
		<!--code for birthday model ends-->
        
        <!--code for view message model strats-->
			<div id="wish_employee_view" class="modal modal-fixed-footer" style="height: 60%; display: block;">
				<div class="modal-content" style="width:100%">
                    <script type="text/javascript">
                    $(document).ready(function(){
                        $(".email_birthday_view").click(function(){
                            id= $(this).attr("data-to");
                            $.ajax({
                                type: "post",
                                url: "view_wish.php",
                                data: {id: id},
                                success: function(data){
                                    if(data== "expired"){
                                        alert("Sorry!! Your session has expired, please login");
                                    }else{
                                        $("#displayMessage").html(data);
                                    }
                                }
                            });
                        });
                        $(".email_aniversary_view").click(function(){
                            id= $(this).attr("data-to-anni");
                            $.ajax({
                                type: "post",
                                url: "view_wish.php",
                                data: {id: id, anniversary: 1},
                                success: function(data){
                                    if(data== "expired"){
                                        alert("Sorry!! Your session has expired, please login");
                                    }else{
                                        $("#displayMessage").html(data);
                                    }
                                }
                            });
                        });
                        $(".email_work_aniversary_view").click(function(){
                            id= $(this).attr("data-to-anni");
                            $.ajax({
                                type: "post",
                                url: "view_wish.php",
                                data: {id: id, work_anniversary: 1},
                                success: function(data){
                                    if(data== "expired"){
                                        alert("Sorry!! Your session has expired, please login");
                                    }else{
                                        $("#displayMessage").html(data);
                                    }
                                }
                            });
                        });
                    });
                    </script>
                    <div id="displayMessage"></div>	
				</div>
				<div class="modal-footer" style="width:100%">
				   <input type="button" name="cancel_btn" value="Close" class="waves-effect waves-light btn blue m-b-xs modal-close" style="margin-right: 10px;" />
				</div>
			</div>
		<!--code for view message model ends--> 
		
		<!--code for anniversary model strats-->
			<div id="wish_employee_anniversary" class="modal modal-fixed-footer" style="height: 60%; display: block;">
				<div class="modal-content" style="width:100%">
				<span class="card-title" style="font-size: 14px; font-weight: bold;">ANNIVERSARY</span>
					<table width="100%" class="display responsive-table">
					<thead>
						<tr>
							<th style="text-align:center;">Employee Name</th>
							<th style="text-align:center;">Anniversary</th>
							<th style="text-align:center;">Send Message</th>
						</tr>
						</thead>
						<?php
							foreach($results_wish_anniversary as $emp_record){
							$emp_name= htmlentities($emp_record->FirstName." ".$emp_record->LastName);
						?>
						<tr>
							<td style="text-align:center;"><span style="color: #0277BB;"><?php echo $emp_name; ?></span></td>
							<td style="text-align:center;"><?php echo htmlentities($emp_record->AnniversaryDate); ?></td>
							<td style="text-align:center;">
                                <?php if($_SESSION['mlogin'] != $emp_record->EmailId){?>
                                <a href="#wish_employee_view" class="modal-trigger">
                                    <i class="material-icons icon_hover email_aniversary_view" title="View Message" data-to-anni="<?php echo $emp_record->id; ?>">edit</i>
                                </a>
                                <i class="material-icons tab_email_anni" title="Send Message" data-to-anni="<?php echo $emp_record->id; ?>">email</i>
                                <?php }else{ echo "N/A";}?>
                            </td>
						</tr>
							<?php } ?>
					</table>
				</div>
				<div class="modal-footer" style="width:100%">
				   <input type="button" name="cancel_btn" value="Close" class="waves-effect waves-light btn blue m-b-xs modal-close" style="margin-right: 10px;" />
				</div>
			</div>
		<!--code for anniversary model ends-->

        <!--code for Work anniversary model strats-->
			<div id="wish_employee_work_anniversary" class="modal modal-fixed-footer" style="height: 60%; display: block;">
				<div class="modal-content" style="width:100%">
				<span class="card-title" style="font-size: 14px; font-weight: bold;">WORK ANNIVERSARY</span>
					<table width="100%" class="display responsive-table">
					<thead>
						<tr>
							<th style="text-align:center;">Employee Name</th>
							<th style="text-align:center;">Work Anniversary</th>
							<th style="text-align:center;">Send Message</th>
						</tr>
						</thead>
						<?php
							foreach($results_wish_anniversary as $emp_record){
							$emp_name= htmlentities($emp_record->FirstName." ".$emp_record->LastName);
						?>
						<tr>
							<td style="text-align:center;"><span style="color: #0277BB;"><?php echo $emp_name; ?></span></td>
							<td style="text-align:center;"><?php echo htmlentities($emp_record->JoiningDate); ?></td>
							<td style="text-align:center;">
                                <?php if($_SESSION['mlogin'] != $emp_record->EmailId){?>
                                <a href="#wish_employee_view" class="modal-trigger">
                                    <i class="material-icons icon_hover email_work_aniversary_view" title="View Message" data-to-anni="<?php echo $emp_record->id; ?>">edit</i>
                                </a>
                                <i class="material-icons tab_email_work_anni" title="Send Message" data-to-anni="<?php echo $emp_record->id; ?>">email</i>
                                <?php }else{ echo "N/A";}?>
                            </td>
						</tr>
							<?php } ?>
					</table>
				</div>
				<div class="modal-footer" style="width:100%">
				   <input type="button" name="cancel_btn" value="Close" class="waves-effect waves-light btn blue m-b-xs modal-close" style="margin-right: 10px;" />
				</div>
			</div>
		<!--code for anniversary model ends-->
        
        
        <!-- Javascripts -->
        <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
				$(".tab_email").click(function(){
					id= $(this).attr("data-to");
					$.ajax({
						type: "post",
						url: "send_wish.php",
						data: {id: id},
						success: function(data){
							if(data== "expired"){
								alert("Sorry!! Your session has expired, please login");
							}else if(data== "sent"){
								alert("Message sent successfully");
							}else{
								alert("Invalid parameter received");
							}
						}
					});
				});
				$(".tab_email_anni").click(function(){
					id= $(this).attr("data-to-anni");
					$.ajax({
						type: "post",
						url: "send_wish.php",
						data: {id: id, anniversary: 1},
						success: function(data){
							if(data== "expired"){
								alert("Sorry!! Your session has expired, please login");
							}else if(data== "sent"){
								alert("Message sent successfully");
							}else{
								alert("Invalid parameter received");
							}
						}
					});
				});

                $(".tab_email_work_anni").click(function(){
					id= $(this).attr("data-to-anni");
					$.ajax({
						type: "post",
						url: "send_wish.php",
						data: {id: id, work_anniversary: 1},
						success: function(data){
							if(data== "expired"){
								alert("Sorry!! Your session has expired, please login");
							}else if(data== "sent"){
								alert("Message sent successfully");
							}else{
								alert("Invalid parameter received");
							}
						}
					});
				});
            });
        </script>            
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="../assets/plugins/counter-up-master/jquery.counterup.min.js"></script>
        <script src="../assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="../assets/plugins/chart.js/chart.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.time.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="../assets/plugins/curvedlines/curvedLines.js"></script>
        <script src="../assets/plugins/peity/jquery.peity.min.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
        <script src="../assets/js/pages/dashboard.js"></script>        
        
    </body>
</html>
<?php } ?>
