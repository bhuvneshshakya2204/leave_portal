<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['hrlogin'])==0 && strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['emplogin'])==0)
{   
	header('location:../index.php');
}
else{
// code for Inactive  employee    
if(isset($_GET['inid']))
{
$id=$_GET['inid'];
$status=0;
$sql = "update tblemployees set Status=:status  WHERE id=:id";
$query = $dbh->prepare($sql);
$query -> bindParam(':id',$id, PDO::PARAM_STR);
$query -> bindParam(':status',$status, PDO::PARAM_STR);
$query -> execute();
header('location:manageemployee.php');
}



//code for active employee
if(isset($_GET['id']))
{
$id=$_GET['id'];
$status=1;
$releiving_date= "00-00-0000";
$sql = "update tblemployees set Status=:status,releiving_date=:releiving_date  WHERE id=:id";
$query = $dbh->prepare($sql);
$query -> bindParam(':id',$id, PDO::PARAM_STR);
$query -> bindParam(':status',$status, PDO::PARAM_STR);
$query -> bindParam(':releiving_date',$releiving_date, PDO::PARAM_STR);
$query -> execute();
header('location:manageemployee.php');
}
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - <?php if(isset($_SESSION['hrlogin'])){ echo "Manage Employees"; }else{ echo "Employees Directory"; } ?></title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
        <link href="../assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">

            
        <!-- Theme Styles -->
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
#advance_filter{
	cursor: pointer;
}
.hide_span{
	display: none;
}
#overlay{
	height: 100%;
	width: 100%;
	position: fixed;
	top: 0;
	left: 0;
	z-index: 99999;
	background: #000000;
	opacity: 0.5;
}
.loading_style{
	position: absolute;
	top: 30%;
	left: 45%;
}
.emp_info, #close_emp_modal{
	cursor: pointer;
}
#overlay{
	height: 100%;
	width: 100%;
	position: fixed;
	top: 0;
	left: 0;
	z-index: 99999;
	background: #000000;
	opacity: 0.5;
}
.loading_style{
	position: absolute;
	top: 30%;
	left: 45%;
}
#overlay1{
	height: 100%;
	width: 100%;
	position: fixed;
	top: 0;
	left: 0;
	z-index: 99999;
	background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.8); /* Black w/ opacity */
}
.loading_style1{
	margin: 40px auto;
	background-color: #fefefe;
	border: 2px solid #ffffff;
	border-radius: 4px;
	padding: 10px;
}
.skill{
	color: #0277bb;
	cursor: pointer;
}
.hide_span{
	display: none;
}
</style>
    </head>
    <body>
	<div id="overlay" style="display: none;">
	<div class="loading_style">
		<?php include_once("loader.php"); ?>
		</div>
		</div>
	<div id="overlay1" style="display: none;">
	<div class="loading_style1" style="width: 50%;">
		<?php //include_once("loader.php"); ?>
		<span style="float: right; font-size: 24px;" id="close_emp_modal">&times;</span>
		<table id="example_modal" class="display responsive-table" style="width: 95%;">
           <tbody>
			<tr>
				<td style="width: 80px; text-align: left;"><b>Skill Set:</b></td>
				<td style="text-align: left;"><span id="skill_modal"></span></td>
				
			</tr>
			<!--<tr>
				<td colspan="2"><button type="button" id="close_emp_modal" style="float: right;" class="waves-effect waves-light btn red m-b-xs">Close</button></td>
			</tr>-->
           </tbody>                   
         </table>                          
	</div>
	</div>
	<?php
	if(isset($_SESSION['emplogin'])){ ?>
       <?php include('../includes/header.php');?>  
       <?php include('../includes/sidebar.php');?>
	<?php }else{ ?>
		<?php include('includes/header.php');?>
       <?php include('includes/sidebar.php');?>
	<?php } ?>
            <main class="mn-inner">
                <div class="row">
                    <!--<div class="col s12">
                        <div class="page-title">Manage Employees</div>
                    </div>-->
                   
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                                <h3><?php if(isset($_SESSION['hrlogin'])){ echo "Manage Employees"; }else{ echo "Employees Directory"; } ?></h3><br><br>
								<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
								<!--<link rel="stylesheet" href="/resources/demos/style.css">-->
								<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
								<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
								<script>
														  $( function() {
															  var names = [
															  <?php
																//$sql= "select EmailId from tblemployees";
																if(isset($_SESSION['hrlogin']))
																	$sql= "select FirstName, LastName from tblemployees";
																else
																	$sql= "select FirstName, LastName from tblemployees where Status= 1";
																$query = $dbh -> prepare($sql);
																$query->execute();
																$results_emp_filter= $query->fetchAll(PDO::FETCH_OBJ);
																$total_record= $query->rowCount();
																//if($query->rowCount() > 0)
																//{
																	$last= 1;
																	foreach($results_emp_filter as $val_emp_filter){
																		$emp_full_name= htmlentities($val_emp_filter->FirstName)." ".htmlentities($val_emp_filter->LastName);
																		if($last!= $total_record)
																			echo '"'.$emp_full_name.'",';
																		else
																			echo '"'.$emp_full_name.'"';
															
																   $last++; } ?>
																];
															var accentMap = {
															  "รก": "a",
															  "รถ": "o"
															};
															var normalize = function( term ) {
															  var ret = "";
															  for ( var i = 0; i < term.length; i++ ) {
																ret += accentMap[ term.charAt(i) ] || term.charAt(i);
															  }
															  return ret;
															};
														 
															$( "#developer" ).autocomplete({
															  source: function( request, response ) {
																var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term ), "i" );
																response( $.grep( names, function( value ) {
																  value = value.label || value.value || value;
																  return matcher.test( value ) || matcher.test( normalize( value ) );
																}) );
															  }
															});
														  } );
													</script>
								<script>
									  $( function() {
										$( ".datepicker_filter" ).datepicker();
										$( ".datepicker_filter" ).datepicker("option", "dateFormat", "dd-mm-yy");
									  } );
									</script>
								<?php
								if(!isset($_SESSION['emplogin'])){ ?>
								<div class="col m6">
									<div class="row">
										<div class="input-field col m6 s12">
											<span id="advance_filter"><u>Hide advance filter</u></span>
										</div>
										<div class="input-field col m6 s12">
												&nbsp;		
										</div>
									</div>
								</div>
								<div class="col m6">
									<div class="row">
										<div class="input-field col m6 s12">
											&nbsp;
										</div>
											<div class="input-field col m6 s12">
												<!--<input type="button" name="remove_filter" class="waves-effect waves-light btn" value="Remove all filters" />-->
												&nbsp;												
											</div>
									</div>
								</div>
								<!--code start for date filter-->
								<form name="filter_form" id="filter_form">
								<div class="col m12">
									<div class="row">
										<div class="input-field col <?php if(isset($_SESSION['emplogin'])){ echo "m6"; }else{ echo "m4"; } ?> s12">
											<span class="label_up">Employee Name</span>
											<!--<input  name="empcode" id="empcode" value="" type="text" autocomplete="off" readonly required>-->
											<input id="developer" name="email_filter" class="email_filter" placeholder="Enter name">
										</div>
												
												
													<div class="input-field col m4 s12" style="display: <?php if(isset($_SESSION['emplogin'])){ echo "none"; } ?>">
														<span class="label_up">Skill Set</span>
														<input type="text" name="skill_filter" id="skill_filter" placeholder="Enter skills separated by coma or space" />
													</div>
												
											<div class="input-field col <?php if(isset($_SESSION['emplogin'])){ echo "m6"; }else{ echo "m4"; } ?> s12">
												<span class="label_up">Reporting Manager</span>
												<select name="manager_dropdown[]" class="multiselect-ui" multiple="multiple" id="manager_dropdown" onchange= "filterData('loader')">
												<option value="" selected disabled>Select manager</option>
												<?php
													$sql= "select id, FirstName, LastName from tblemployees where UserType in ('project manager', 'director')";
													$query = $dbh -> prepare($sql);
													$query->execute();
													$rs_manager=$query->fetchAll(PDO::FETCH_OBJ);
													if($query->rowCount() > 0){
														foreach($rs_manager as $val_manager){ ?>
															<option value="<?php echo $val_manager->id; ?>"><?php echo $val_manager->FirstName." ".$val_manager->LastName; ?></option>
														<?php } ?>
														<?php }else{ ?>
															<option value="">No record found</option>
														<?php } ?>
												</select>
											</div>

										
                                     
									</div>
								</div>
									<!--<div class="col m6">
										<div class="row">
											<div class="input-field col m6 s12">
												<span class="label_up">From Date</span>
												<input type="text" id="from_date_filter" name="from_date_filter" class="datepicker_filter" size="30" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd/mm/yyyy" onchange= "filterData()" />
											</div>

											<div class="input-field col m6 s12">
												<span class="label_up">To Date</span>
												<input type="text" id="to_date_filter" name="to_date_filter" class="datepicker_filter" size="30" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd/mm/yyyy" onchange= "filterData()" />
											</div>

										</div>
                                     </div>-->
									 <div style="float: right; margin-right: 20px; display: none;">
												<img src="../assets/images/filter_btn.jpg" alt="filter button" id="filter_btn" />
											</div>
											</form>
									<?php } ?>
                                <?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
                                <div id="ajax_container">
								<table id="example" class="display responsive-table " <?php if(isset($_SESSION['mlogin'])){ echo "style= 'font-size: 13px'"; } ?>>
                                    <thead>
                                        <tr>
                                            <!--<th>Sr no</th>-->
											<?php
											if(isset($_SESSION['hrlogin'])){ ?>
                                            <th class="no-sort">Emp Id</th>
                                            <th>Name</th>
                                            <th>Designation</th>
                                             <th>Joining Date</th>
                                             <th>Relieving Date</th>
                                             <th>Status</th>
                                            <th class="no-sort">Action</th>
											<?php }else if(isset($_SESSION['mlogin'])){ ?>
                                            <th width="15%">Name</th>
                                            <th width="15%">Designation</th>
                                             <th>Contact #</th>
                                             <th>Email</th>
											 <th>Manager</th>
											 <th width="15%">Skills</th>
											 <!--<th>Previous Exp.</th>
											 <th>Joining Date</th>-->
											<?php }else{ ?>
											<th>Name</th>
                                            <th>Designation</th>
                                             <th>Contact #</th>
                                             <th>Email</th>
											 <th>Manager</th>
											<?php } ?>
                                        </tr>
                                    </thead>
                                 
                                    <tbody>
<?php
//$sql = "SELECT EmpId,FirstName,LastName,AnniversaryDate,Status,JoiningDate,id,reporting_manager,skill_set,previous_exp,designation from tblemployees order by FirstName";
if(isset($_SESSION['hrlogin']))
	$sql = "SELECT * from tblemployees order by FirstName";
else
	$sql = "SELECT * from tblemployees where Status= 1 order by FirstName";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{ ?>  
	<tr>
	<?php if(isset($_SESSION['hrlogin'])){ ?>
		<td><?php echo htmlentities($result->EmpId);?></td>
		<td><?php echo htmlentities($result->FirstName);?>&nbsp;<?php echo htmlentities($result->LastName);?></td>
		<td><?php echo $result->designation?></td>
        <td>
        	<span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->JoiningDate)))); ?></span>
			<?php echo ($result->JoiningDate != "") ? date("d-m-Y", strtotime(str_replace(',', '', htmlentities($result->JoiningDate)))) : "";?>
		</td>
		<td><?php echo $result->releiving_date != '00-00-0000' ? 
			date("d-m-Y", strtotime(str_replace(',', '', htmlentities($result->releiving_date)))): '';?>
		</td>
		<td><?php echo $result->Status == '1' ? '<a class="waves-effect waves-green btn-flat m-b-xs">Active</a>':'<a class="waves-effect waves-red btn-flat m-b-xs">Inactive</a>';?></td>
                                            <td><a href="editemployee.php?empid=<?php echo htmlentities($result->id);?>" target="_blank"><i class="material-icons">mode_edit</i></a>
                                        <?php if($result->Status==1)
 {?>
<!--<a href="manageemployee.php?inid=<?php echo htmlentities($result->id);?>" onclick="return confirm('Are you sure you want to inactive this Employe?');"" > <i class="material-icons" title="Inactive">clear</i>-->
&nbsp;
<?php } else {?>

                                            <a href="manageemployee.php?id=<?php echo htmlentities($result->id);?>" onclick="return confirm('Are you sure you want to active this employee?');" target="_blank"><i class="material-icons" title="Active">done</i>
                                            <?php } ?> </td>
											<?php }else if(isset($_SESSION['mlogin'])){ ?>
        <td><a href="employee-details.php?id=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->FirstName);?>&nbsp;<?php echo htmlentities($result->LastName);?></a></td>
												<td><?php echo htmlentities($result->designation)== 'Manager - HR' ? htmlentities($result->designation):ucwords(htmlentities($result->designation));?></td>
												<td><?php echo htmlentities($result->Phonenumber);?></td>
												<td><?php echo htmlentities($result->EmailId);?></td>
												<td>
													<?php
														$sql= "select FirstName, LastName from tblemployees where id= '".$result->reporting_manager."'";
														$query = $dbh -> prepare($sql);
														$query->execute();
														$manager_rs=$query->fetchAll(PDO::FETCH_OBJ);
														if($query->rowCount() > 0){
															echo $manager_rs[0]->FirstName." ".$manager_rs[0]->LastName;
														}else{
															echo "Not Allotted";
														}
														
													?>
												</td>
												<td>
													<?php 
														$skills= explode(",", htmlentities($result->skill_set));
														$skills= $skills[0];
														$skills= trim($skills, ";");
														$skills= trim($skills, "-");
														echo "<span data-id='$result->id' class='skill'>$skills</span>";
													?>
												</td>
												<!--<td><?php echo htmlentities($result->previous_exp);?></td>
												<td>
											  <span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->JoiningDate)))); ?></span>
													<?php
														echo date("d-m-Y", strtotime(htmlentities($result->JoiningDate)));
													?>
											  </td>-->
											<?php }else{ ?>
												<td><?php echo htmlentities($result->FirstName);?>&nbsp;<?php echo htmlentities($result->LastName);?></td>
												<td><?php echo htmlentities($result->designation)== 'Manager - HR' ? htmlentities($result->designation):ucwords(htmlentities($result->designation));?></td>
												<td><?php echo htmlentities($result->Phonenumber);?></td>
												<td><?php echo htmlentities($result->EmailId);?></td>
												<td>
													<?php
														$sql= "select FirstName, LastName from tblemployees where id= '".$result->reporting_manager."'";
														$query = $dbh -> prepare($sql);
														$query->execute();
														$manager_rs=$query->fetchAll(PDO::FETCH_OBJ);
														if($query->rowCount() > 0){
															echo $manager_rs[0]->FirstName." ".$manager_rs[0]->LastName;
														}else{
															echo "Not Allotted";
														}
														
													?>
												</td>
											<?php } ?>
                                        </tr>
                                         <?php $cnt++;} }?>
                                    </tbody>
                                </table>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
         
        </div>
        <div class="left-sidebar-hover"></div>
        
        <!-- Javascripts -->
		<!--<script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>-->
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
		<script>
		$(document).ready(function(){
			  $(document).mouseup(function (e)
				{
					var container = $("#overlay1");

					if (container.is(e.target)) // ... nor a descendant of the container
					{
						$("#overlay1").hide();
					}
				});
			$(document).on("click", ".skill", function(){
				emp_id= $(this).attr("data-id");
				$.ajax({
					type: "post",
					url: "../get_emp_directory.php",
					data: {id: emp_id},
					success: function(data){
						parsed= JSON.parse(data);
						name= parsed[0];
						mobile= parsed[1];
						address= parsed[2];
						blood_group= parsed[3];
						skill= parsed[4];
						designation= parsed[5];
						email= parsed[6];
						email_pers= parsed[7];
						manager= parsed[8];
						exp= parsed[9];
						
						// $("#name_modal").html(name);
						// $("#mobile_modal").html(mobile);
						// $("#address_modal").html(address);
						$("#skill_modal").html(skill);
						// $("#designation_modal").html(designation);
						// $("#email_modal").html(email);
						// $("#p_email_modal").html(email_pers);
						// $("#manager_modal").html(manager);
						// $("#exp_modal").html(exp);
						// $("#blood_modal").html(blood_group);
						
						$("#overlay1").show();
					}
				});
			});
			$("#close_emp_modal").click(function(){
				$("#overlay1").hide();
			});
			$('.email_filter').on('keyup blur change', function(e) {
					filterData();
				});
				$('#skill_filter').on('keyup blur change', function(e) {
					filterData();
				});
			$("#advance_filter").click(function(){
			$("#filter_form").toggle();
			if($("#filter_form").is(":visible")){
				$("#advance_filter").html("<u>Hide advance filter</u>");
			}else{
				$("#advance_filter").html("<u>Show advance filter</u>");
			}
		});
	});
	
	function filterData(flag= ""){
		var flag_clone= flag;
			email_filter= $(".email_filter").val();
			manager_filter= $("#manager_dropdown").val();
			skill_filter= $("#skill_filter").val();
			if(flag_clone== "loader"){
				$("#overlay").show();
			}
			$.ajax({
				type: "post",
				url: "../filter_skill.php",
				//data: filter_form.serialize(),
				data: {email_filter: email_filter, manager_dropdown: manager_filter, skill_filter: skill_filter},
				success: function(data){
					$("#overlay").hide();
					$("#ajax_container").html(data);
				}
			});
		}
				
		var table = $('#example').DataTable({
       orderCellsTop: true,
	   "ordering": true,
        columnDefs: [
   { 
   orderable: false, targets:  "no-sort"}
],
"aaSorting": [],
language: {
        searchPlaceholder: "Search any of the columns"
    }
    });
	 $('.dataTables_length select').addClass('browser-default');
		</script>
        
        <!--<script src="../assets/js/pages/table-data.js"></script>-->
        
    </body>
</html>
<?php } ?>