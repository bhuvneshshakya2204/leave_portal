<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
}
else{



 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Approved Leave leaves </title>
        
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

                <link href="../assets/plugins/google-code-prettify/prettify.css" rel="stylesheet" type="text/css"/>  
        <!-- Theme Styles -->
        <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/custom.css?dt=20181218" rel="stylesheet" type="text/css"/>
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
.icon_hover:hover{
	cursor: pointer;
}
.check_icon{
	color: green;
}
.clear_icon{
	color: red;
}
.edit_icon{
	color: blue;
}
.red {
  background-color: #F44336;
  color: white;
}
.hide_span{
	display: none;
}
</style>
    </head>
    <body>
	<div id="modal_show_description" style="position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: 999; background: #000000; opacity: 0.9; display: none;">
		<div style="position: absolute; top: 200px; left: 30%; width: 50%; border: 1px solid gray; z-index: 99999; background: #ffffff;">
			<form style="width: 100%; padding: 15px 5px 60px 7px;" id="leave_cancel_form">
				<div style="color: black; font-weight: bold; font-size: 20px;">Description</div>
				<div style="margin-top: 20px;">
					<!--<label for="l_type" class="label_up" style="color: black; font-weight: bold;">Description</label>-->
					<textarea id="description" name="description" placeholder="Write your decription here" rows="10" cols="35" style="height: 100px; padding: 7px;"></textarea>
					<!--<input id="description_text" name="description_text" type="text" style="color: black;" placeholder="Write your decription here" />-->
				</div>
				<div style="margin-top: 20px;">
				<input type="hidden" name="hidden_lid" id="hidden_lid" value="" />
					<span id="ajax_msg" style="color: green;"></span>
					<button type="button" name="close_modal_description"  id="close_modal_description" class="waves-effect waves-light btn red m-b-xs" style="float: right;">Cancel</button>
					<button type="button" name="save_btn"  id="save_btn" class="waves-effect waves-light btn indigo m-b-xs" style="float: right; margin-right: 20px;">Send</button>
				</div>
			</form>
		</div>
		</div>
	<div id="modal_show" style="position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: 999; background: #cccccc; opacity: 0.8; display: none;">
		<div style="position: absolute; top: 180px; left: 50%; z-index: 99999;">
			<img src="../assets/images/spinner1.gif" alt="spinner image" />
		</div>
	</div>
       <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
            <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">Pending Leave History</div>
                    </div>
                   
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                                <h3>Pending Leave History</h3><br><br>
                                <?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
								<div id="ajax_container">
                                <table id="example" class="display responsive-table" width="100%">
                                    <thead>
                                        <tr>
                                           <!-- <th width="5%">#</th>-->
                                            <th width="14%">Employee Name</th>
                                            <th width="17%">Leave Type</th>
											<th width="13%">From Date</th>
											<th width="13%">To Date</th>
											<?php
											if(isset($_SESSION['hrlogin'])){ ?>
												<th width="17%">Posting Date</th>                 
												<th width="15%">Status</th>
											<?php } ?>
													<?php
											if(strlen($_SESSION['mlogin'])!=0){ ?>
												<th width="10%" class="no-sort">Action(s)</th>
												
											<?php }else{ ?>
											<th width="15%" class="no-sort">Action</th>
											<?php } ?>
												
                                        </tr>
                                    </thead>
                                 
                                    <tbody>
<?php 
$status=0;
$m_id= $_SESSION['eid'];
if(strlen($_SESSION['mlogin'])!=0)
	$sql = "SELECT tblleaves.id as lid,tblleaves.FromDate as fdate, tblleaves.ToDate as tdate,tblemployees.FirstName,tblemployees.LastName,tblemployees.EmpId,tblemployees.id,tblleaves.LeaveType,tblleaves.PostingDate,tblleaves.Status from tblleaves join tblemployees on tblleaves.empid=tblemployees.id where (tblleaves.Status=:status or tblleaves.Status=4 or tblleaves.Status=5) and tblleaves.ManagerID=:m_id order by STR_TO_DATE(FromDate, '%d-%m-%Y') asc";
else
	$sql = "SELECT tblleaves.id as lid,tblleaves.FromDate as fdate, tblleaves.ToDate as tdate,tblemployees.FirstName,tblemployees.LastName,tblemployees.EmpId,tblemployees.id,tblleaves.LeaveType,tblleaves.PostingDate,tblleaves.Status from tblleaves join tblemployees on tblleaves.empid=tblemployees.id where (tblleaves.Status=:status or tblleaves.Status=4 or tblleaves.Status=5) order by STR_TO_DATE(FromDate, '%d-%m-%Y') desc";
$query = $dbh -> prepare($sql);
$query->bindParam(':status',$status,PDO::PARAM_STR);
if(strlen($_SESSION['mlogin'])!=0)
	$query->bindParam(':m_id',$m_id,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{         
      ?>  

                                        <tr>
                                           <!-- <td> <b><?php echo htmlentities($cnt);?></b></td>-->
											<?php
											if(isset($_SESSION['hrlogin'])){ ?>
												<td><a href="editemployee.php?empid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->FirstName." ".$result->LastName);?><!--(<?php echo htmlentities($result->EmpId);?>)--></a></td>
											<?php }else{ ?>
												<td><?php echo htmlentities($result->FirstName." ".$result->LastName);?><!--(<?php echo htmlentities($result->EmpId);?>)--></td>
											<?php } ?>
											<?php
											if(isset($_SESSION['mlogin'])){ ?>
                                            <td><?php echo htmlentities($result->LeaveType);?><?php if($result->Status== 4){ echo " (<span style='color: blue;'>Edit Request</span>)"; }else if($result->Status== 5){ echo " (<span style='color: red;'>Cancel Request</span>)";} ?></td>
											<?php }else{ ?>
											<td><?php echo htmlentities($result->LeaveType);?></td>
											<?php } ?>
											<?php
											if($result->Status== 4){
												$sql= "select leave_type, from_date, to_date from tblleaves_history where leave_id= '".$result->lid."'";
												$query = $dbh -> prepare($sql);
												//$query->bindParam(':did',$did,PDO::PARAM_STR);
												$query->execute();
												$results_edited= $query->fetchAll();
												foreach($results_edited as $val_edited){}
												?>
											<td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $val_edited['from_date'])))); ?></span><?php echo $val_edited['from_date']." (<span style='color: red;'>".$result->fdate."</span>)";?></td>
											<td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $val_edited['to_date'])))); ?></span><?php echo $val_edited['to_date']." (<span style='color: red;'>".$result->tdate."</span>)";?></td>
											<?php }else{ ?>
											<td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->fdate)))); ?></span><?php echo htmlentities($result->fdate);?></td>
											<td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->tdate)))); ?></span><?php echo htmlentities($result->tdate);?></td>
											<?php } ?>
											<?php
											if(isset($_SESSION['hrlogin'])){ ?>
                                            <td><?php echo date("d-m-Y H:i:s", strtotime(htmlentities($result->PostingDate)));?></td>
                                                                       <td><?php $stats=$result->Status;
								if($stats==4){
                                             ?>
                                                 <span style="color: blue">Requested for edit</span>
                                                 <?php } else if($stats==0)  { ?>
 <span style="color: blue">Waiting for approval</span>
 <?php }else if($stats==5){ ?>
<span style="color: red">Requested for cancellation</span>
 <?php } ?>

                                             </td>
											<?php } ?>

           <?php
			if(isset($_SESSION['mlogin'])){ ?>
           <td>
			<?php if($stats!=3){ ?>
				<?php if($stats==1){ ?>
					<a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));?>"><i class="material-icons icon_hover edit_icon" title="View details">edit</i></a>&nbsp;&nbsp;<i class="material-icons icon_hover clear_icon clear_action" data-id="<?php echo base64_encode(base64_encode($result->lid));?>" title="Disapprove">clear</i><!--<a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));?>" class="waves-effect waves-light btn blue m-b-xs"  > View Details</a>-->
				<?php }else if($stats==2){ ?>
				<a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));?>"><i class="material-icons icon_hover edit_icon" title="View details">edit</i></a>&nbsp;&nbsp;<i class="material-icons icon_hover check_icon check_action" data-id="<?php echo base64_encode(base64_encode($result->lid));?>" title="Approve">check</i><!--<a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));?>" class="waves-effect waves-light btn blue m-b-xs"  > View Details</a>-->
				<?php }else{ ?>
				<a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));?>"><i class="material-icons icon_hover edit_icon" title="View details">edit</i></a>&nbsp;<i class="material-icons icon_hover check_icon check_action" data-id="<?php echo base64_encode(base64_encode($result->lid));?>" title="Approve">check</i>&nbsp;<i class="material-icons icon_hover clear_icon clear_action" data-id="<?php echo base64_encode(base64_encode($result->lid));?>" title="Disapprove">clear</i>
				<?php } ?>
			<?php }else{ ?>
			<a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));?>"><i class="material-icons icon_hover edit_icon" title="View details">edit</i></a>
			<?php } ?>
		   </td>
			<?php }else{ ?>
				<td><a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));?>"><i class="material-icons icon_hover edit_icon" title="View details">visibility</i></a></td>
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
        <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
		<script>
			$(document).ready(function(){
				$(document).on("click", ".check_action", function(){
				edit_leave_id= $(this).attr("data-id");
				//alert(edit_leave_id); return false;
				$("#modal_show").show();
				$.ajax({
					type: "post",
					url: "manager_approve_leave.php",
					data: {lid: edit_leave_id, status: 1, pending: 1},
					success: function(data){
						//$("#modal_show").hide();
						if(data== "Leave is already approved" || data== "Edit request and leave both are already approved" || data== "Opps!! Something went wrong, please try after some time" || data== "This leave is cancelled" || data== "Cancellation is already approved"){
							$("#modal_show").hide();
							setTimeout(function(){ alert(data); }, 1000);
						}else{
							//$("#modal_show").hide();
							$.ajax({
								type: "post",
								url: "header_ajax_view.php",
								success: function(data1){
									$("#ajax_container").html(data);
									//$(this).closest('tr').remove();
									$("#notification_ajax_container").html(data1);
									$("#modal_show").hide();
								}
							});
							//$("#ajax_container").html(data);
						}
					}
				});
				});
				$(document).on("click", ".clear_action", function(){
				edit_leave_id= $(this).attr("data-id");
				$("#hidden_lid").val(edit_leave_id);
				$("#ajax_msg").text("");
				$("#description").val("");
				$("#modal_show_description").show(); return false;
				$.ajax({
					type: "post",
					url: "manager_approve_leave.php",
					data: {lid: edit_leave_id, status: 2, pending: 1},
					success: function(data){
						//$("#modal_show").hide();
						if(data== "Leave is already not approved" || data== "Edit request is already disapproved" || data== "Opps!! Something went wrong, please try after some time" || data== "This leave is cancelled" || data== "Cancellation is already not approved"){
							$("#modal_show").hide();
							setTimeout(function(){ alert(data); }, 1000);
							
						}else{
							//$("#modal_show").hide();
							$.ajax({
								type: "post",
								url: "header_ajax_view.php",
								success: function(data1){
									$("#ajax_container").html(data);
									$("#notification_ajax_container").html(data1);
									$("#modal_show").hide();
								}
							});
							//$("#ajax_container").html(data);
						}
						//alert(data);
					}
				});
				});
				
				
				$(document).on("click", "#save_btn", function(){
				edit_leave_id= $("#hidden_lid").val();
				description= $("#description").val();
				$("#ajax_msg").text("Please wait...");
				$.ajax({
					type: "post",
					url: "manager_approve_leave.php",
					data: {lid: edit_leave_id, status: 2, pending: 1, description: description},
					success: function(data){
						//$("#modal_show").hide();
						if(data== "Leave is already not approved" || data== "Edit request is already disapproved" || data== "Opps!! Something went wrong, please try after some time" || data== "This leave is cancelled" || data== "Cancellation is already not approved"){
							$("#modal_show_description").hide();
							setTimeout(function(){ alert(data); }, 1000);
							
						}else{
							//$("#modal_show").hide();
							$.ajax({
								type: "post",
								url: "header_ajax_view.php",
								success: function(data1){
									$("#ajax_container").html(data);
									$("#notification_ajax_container").html(data1);
									$("#modal_show_description").hide();
								}
							});
							//$("#ajax_container").html(data);
						}
						//alert(data);
					}
				});
				});
				$("#close_modal_description").click(function(){
					$("#modal_show_description").hide();
				});
			});
		</script>
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
		<script>
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
        <script src="../assets/js/alpha.min.js"></script>
        
         <script src="assets/js/pages/ui-modals.js"></script>
        <script src="assets/plugins/google-code-prettify/prettify.js"></script>
        
    </body>
</html>
<?php } ?>
