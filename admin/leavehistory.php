<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
}
else{
date_default_timezone_set('Asia/Kolkata');
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Leave History</title>
        
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
#l_type{
    position: relative;
    cursor: pointer;
    background-color: transparent;
    border: none;
    border-bottom: 1px solid #9e9e9e;
    outline: none;
    height: 3rem;
    line-height: 3rem;
    width: 100%;
    font-size: 1rem;
    margin: 0 0 20px 0;
    padding: 0;
    color: black;
    display: block;    
}
</style>
    </head>
    <!--<body oncontextmenu="return false">-->
    	<body>
    		<div id="overlay" style="display: none;">
				<div class="loading_style">
					<?php include_once("loader.php"); ?>
				</div>
			</div>
       <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
            <main class="mn-inner">
                <div class="row">
                    <!-- <div class="col s12">
                        <div class="page-title">Leave History</div>
                    </div> -->
                   
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                                <h3>Leave History</h3><br><br>
								<div id="modal_show" style="position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: 999; background: #000000; opacity: 0.8; display: none;">
								<div style="position: absolute; top: 80px; left: 30%; width: 50%; border: 1px solid gray; z-index: 99999; background: #ffffff;">
								   <form style="width: 100%;" id="leave_edit_form">
								   <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
								  <link rel="stylesheet" href="/resources/demos/style.css">
								  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
								  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
								  <script>
									  $( function() {
										$( ".datepicker" ).datepicker();
										$( ".datepicker" ).datepicker("option", "dateFormat", "dd-mm-yy");
										//$( "#format" ).on( "change", function() {
										 // $( "#datepicker" ).datepicker( "option", "dateFormat", $( this ).val() );
										//});
									  } );
                                      function hideToDate(leave_type_hidable){
                                        if(leave_type_hidable== "Late Arrival (LA)" || leave_type_hidable== "Leaving Early (LE)"){
                                            $("#to_date_hidable").hide();
                                            $("#label_changable").html("Select Date");
                                        }else{
                                            $("#label_changable").html("From");
                                            $("#to_date_hidable").show();
                                            $(".to_date").val("");
                                        }
                                    }
                                    function populateToDate(value){
                                        if($("#leavetype").val()== "Late Arrival (LA)" || $("#leavetype").val()== "Leaving Early (LE)"){
                                            $(".to_date").val(value);
                                        }else{
                                            $(".to_date").val("");
                                        }
                                    }
								</script>
									<div class="input-field col s12">
										<label for="l_type" class="label_up" style="color: black; font-weight: bold;">Leave Type</label>
										<!--<input id="l_type" name="l_type" type="text" style="color: black;" readonly="readonly" />-->
                                        <div id="ajax_leave_type"></div>
									</div>
									<div class="input-field col s12">
										<label for="l_from" class="label_up" style="color: black; font-weight: bold;"  id="label_changable">From</label>
										<input type="text" id="l_from" name="l_from" class="datepicker" size="30" readonly="readonly" style="color: black;" onChange="populateToDate(this.value)" />
									</div>
									<div class="input-field col s12" id="to_date_hidable">
										<label for="l_to" class="label_up" style="color: black; font-weight: bold;">To</label>
										<input type="text" id="l_to" name="l_to" class="datepicker to_date" size="30" readonly="readonly" style="color: black;" />
									</div>
									<div class="input-field col s12">
										<span id="ajax_msg"></span>
										<button type="button" name="save_btn"  id="save_btn" class="waves-effect waves-light btn indigo m-b-xs" style="float: right;">Save</button>
										<button type="button" name="close_modal"  id="close_modal" class="waves-effect waves-light btn red m-b-xs" style="float: right; margin-right: 20px;">Cancel</button>
									</div>
									<!--<div class="input-field col m6 s12">
										<button type="button" name="cancel"  id="cancel" class="waves-effect waves-light btn red m-b-xs" style="float: right;">Cancel</button>
										
									</div>-->
									<input type="hidden" name="l_id" id="l_id" value="" />
								   </form>
								  </div>
								  </div>
								  <!--filter starts-->
								  <script>
									  $( function() {
										$( ".datepicker_filter" ).datepicker();
										$( ".datepicker_filter" ).datepicker("option", "dateFormat", "dd-mm-yy");
									  } );
									</script>
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
										<div class="input-field col m3 s12">
														<span class="label_up">Leave Type</span>
														<select name="leave_type_dropdown[]" class="multiselect-ui" multiple="multiple" id="leave_type_dropdown" onchange= "filterData('loader')">
														<option value="" selected disabled>Filter by leave type</option>
														<?php
															$sql= "select LeaveType from tblleavetype where status= 1";
															$query = $dbh -> prepare($sql);
															$query->execute();
															$results_filter= $query->fetchAll(PDO::FETCH_OBJ);
															if($query->rowCount() > 0)
															{
																$short_form_leave= "";
															foreach($results_filter as $val_filter){ ?>
															<?php
															if(htmlentities($val_filter->LeaveType)== "Late Arrival (LA)"){
																$short_form_leave= "LA";
															}else if(htmlentities($val_filter->LeaveType)== "Leaving Early (LE)"){
																$short_form_leave= "LE";
															}else{
																$short_form_leave= "Leave";
															} ?>
																<option value="<?php echo htmlentities($val_filter->LeaveType);?>"><?php echo $short_form_leave;?></option>
															<?php } }else{ ?>
															<option value="">No leave type available in database</option>
															<?php } ?>
														</select>
													</div>
													<div class="input-field col m3 s12">
														<span class="label_up">Leave Status</span>
														<select name="leave_status_dropdown[]" class="multiselect-ui" multiple="multiple" id="leave_status_dropdown" onchange= "filterData('loader')">
														<option value="" selected disabled>Filter by leave status</option>
														<option value="0">Waiting for approval</option>
														<option value="4">Requested for edit</option>
														<option value="5">Cancel request</option>
														<option value="1">Approved</option>
														<option value="2">Not approved</option>
														<option value="3">Cancelled</option>
														</select>
													</div>
													
										
											<div class="input-field col m3 s12">
												<span class="label_up">From Date</span>
												<input type="text" id="from_date_filter" name="from_date_filter" class="datepicker_filter" size="30" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd-mm-yyyy" onchange= "filterData('loader')" />
											</div>

											<div class="input-field col m3 s12">
												<span class="label_up">To Date</span>
												<input type="text" id="to_date_filter" name="to_date_filter" class="datepicker_filter" size="30" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd-mm-yyyy" onchange= "filterData('loader')" />
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
								<!--code ends for date filter-->
                                <?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
                                <div id="ajax_container">
                                <table id="example" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            
                                            <th width="16%">Leave Type</th>
                                            <th width="12%">From</th>
                                            <th width="12%">To</th>
                                             <!--<th>Description</th>-->
                                             <th width="17%">Posting Date</th>
                                            <th width="18%">Manager Remark</th>
                                            <th width="15%">Status</th>
											<th class="no-sort">Action(s)</th>
                                        </tr>
                                    </thead>
                                 
                                    <tbody>
<?php 
$eid=$_SESSION['eid'];
$sql = "SELECT id,LeaveType,ToDate,FromDate,Description,PostingDate,AdminRemarkDate,AdminRemark,Status from tblleaves where empid=:eid order by STR_TO_DATE(FromDate, '%d-%m-%Y') desc";
$query = $dbh -> prepare($sql);
$query->bindParam(':eid',$eid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{               ?>  
                                        <tr>
                                            <!--<td> <?php echo htmlentities($cnt);?></td>-->
                                            <td><?php echo htmlentities($result->LeaveType);?></td>
											
											<td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->FromDate)))); ?></span><?php echo htmlentities($result->FromDate);?></td>
                                            <td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->ToDate)))); ?></span><?php echo htmlentities($result->ToDate);?></td>
                                           <!--<td><?php echo htmlentities($result->Description);?></td>-->
										   <?php
												$date_india= new DateTime($result->PostingDate);
												$date_india= $date_india->format("d-m-Y H:i:s");
										   ?>
                                            <td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $date_india)))); ?></span><?php echo htmlentities($date_india);?></td>
                                            <td><?php if($result->AdminRemark=="")
                                            {
echo htmlentities('NA');
                                            } else
{

 echo htmlentities($result->AdminRemark);
}

                                            ?></td>
                                                                                 <td><?php $stats=$result->Status;
if($stats==1){
                                             ?>
                                                 <span style="color: green">Approved</span>
                                                 <?php }else if($stats==2)  { ?>
                                                <span style="color: red">Not Approved</span>
                                                 <?php }else if($stats==3)  { ?>
 <span style="color: red">Cancelled</span>
 <?php }else if($stats==0){ ?>
 <span style="color: blue">Waiting for approval</span>
 <?php }else if($stats==4){ ?>
	<span style="color: blue">Requested for edit</span>
 <?php }else if($stats==5){ ?>
	<span style="color: blue">Requested for cancellation</span>
 <?php } ?>
                                             </td>
											<td class="modal_td">
											<?php
											$Date_from = $result->FromDate;
											$Date_from= str_replace("/", "-", $Date_from);
											$today_date= date("d-m-Y");
											//$today_date= date('Y-m-d', strtotime($today_date. ' + 1 days'));
											///////////////////
											//$fromdate= "11/07/2018";
											//$fromdate= str_replace("/", "-", $fromdate);
											$Date_from = date('d-m-Y',strtotime($Date_from));
											$Date_from1 =  date_create($Date_from);
										//$todate= "11/07/2018";
											//$todate= str_replace("/", "-", $todate);
											$today_date = date('d-m-Y',strtotime($today_date));
											$today_date1 =  date_create($today_date);
											$diff =  date_diff($Date_from1, $today_date1);
											if($diff->format("%R%a") <= 0){ ?>
												<?php if($stats==0 || $stats==1){ ?>
													<i class="material-icons icon_hover clear_icon clear_action" data-id="<?php echo $result->id; ?>" title="Cancel">clear</i>
												<?php } ?>
												<?php if($stats== 0 || $stats==1 || $stats==2){ ?>
													<i class="material-icons icon_hover edit_icon edit_action" data-id="<?php echo $result->id; ?>" title="Edit">edit</i>
												<?php } ?>
											<?php } ?>
												</td>
          
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
		<!--<script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>-->
		<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
		<script>
			function filterData(flag= ""){
					//email_filter= $(".email_filter").val();
					leave_type_filter= $("#leave_type_dropdown").val();
					leave_status_filter= $("#leave_status_dropdown").val();
					//alert(leave_type_filter.length);
					from_date_filter= $("#from_date_filter").val();
					to_date_filter= $("#to_date_filter").val();
					//filter_form= $("#filter_form");
					//$("#modal_show").show();
					if(flag== "loader"){
						$("#overlay").show();
					}
					$.ajax({
						type: "post",
						url: "../filter_leave_history.php",
						//data: filter_form.serialize(),
						data: {from_date: from_date_filter, to_date: to_date_filter, leave_type_dropdown: leave_type_filter, leave_status_dropdown: leave_status_filter},
						success: function(data){
							//$("#modal_show").hide();
							//alert(data);
							//console.log(data);
							$("#overlay").hide();
							$("#ajax_container").html(data);
						}
					});
				}
		$(document).keydown(function (event) {
    if (event.keyCode == 123) { // Prevent F12
        return false;
    } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) { // Prevent Ctrl+Shift+I        
        return false;
    }
});
		$(document).ready( function () {
			// $(".edit_action").click(function(){
				// edit_row_id= $(this).attr("data-id");
			// });
			$("#advance_filter").click(function(){
					$("#filter_form").toggle();
					if($("#filter_form").is(":visible")){
						$("#advance_filter").html("<u>Hide advance filter</u>");
					}else{
						$("#advance_filter").html("<u>Show advance filter</u>");
					}
				});
			$(document).on("click", ".edit_action", function(){
				edit_leave_id= $(this).attr("data-id");
				$.ajax({
					type: "post",
					url: "../provide_leave_details.php",
					data: {lid: edit_leave_id},
					success: function(data){
						if(data!= "invalid"){
							parsed= JSON.parse(data);
							l_id= parsed[0];
							l_type= parsed[1];
							l_from= parsed[2];
							l_to= parsed[3];
							l_status= parsed[4];
                            leave_type_hidable= parsed[5]; 
							//$("#l_type").addClass("label_up");
							//$("#l_type").val(l_type);
                            if(leave_type_hidable== "Late Arrival (LA)" || leave_type_hidable== "Leaving Early (LE)"){
                                $("#to_date_hidable").hide();
                            }
                            $("#ajax_leave_type").html(l_type);
							$("#l_from").val(l_from);
							$("#l_to").val(l_to);
							$("#l_id").val(l_id);
							$("#modal_show").show();
						}else{
							alert("Invalid data received, please try after some time.");
						}
					}
				});
				//$("#modal_show").show();
				
			});
            function countDays(date1, date2){
                var oneDay = 24*60*60*1000; 
                return Math.round((date1.getTime() - date2.getTime())/(oneDay));
            }
			$("#save_btn").click(function(){
				//code for date validation
					var flag= true;
					from_date= $("#l_from").val();
					from_date_array= from_date.split("-");
					var y_from= from_date_array[2];
					var m_from= from_date_array[1];
					var d_from= from_date_array[0];
					//alert(m_from); return false;
					var d = new Date();
					var y_current = d.getFullYear();
					var m_current = d.getMonth();
				    m_current++;
				    var d_current= d.getDate();
                    //count number of days                    
                    var diffDaysFromCurrent = countDays(new Date(y_from,m_from,d_from), new Date(y_current,m_current,d_current));
                    //End count number of days                
					if(from_date.length < 10 || diffDaysFromCurrent > 90 || diffDaysFromCurrent < -15){
						$("#l_from").addClass("red_border");
						flag= false;
					}else{
						$("#l_from").removeClass("red_border");
					}
                    if($("#l_type").val() == "Late Arrival (LA)" || $("#l_type").val() == "Leaving Early (LE)") {
                        to_date= from_date;   
                    } else {
					   to_date= $("#l_to").val();                        
                    }
					to_date_array= to_date.split("-");
					var y_to= to_date_array[2];
					var m_to= to_date_array[1];
					var d_to= to_date_array[0];
                    //count number of days                    
                    var diffDaysFrom = countDays(new Date(y_to,m_to,d_to), new Date(y_from,m_from,d_from));
                    //End count number of days                   
					if(to_date.length < 10 || diffDaysFrom < 0){
						$("#l_to").addClass("red_border");
						flag= false;
					}else{
						$("#l_to").removeClass("red_border");
					}
				//enf of validation
				if(!flag)
					return false;
				frm= $("#leave_edit_form");
				response= confirm("Are you sure you want to submit these details ?");
				if(!response)
					return false;
				$("#ajax_msg").css("color", "green");
				$("#ajax_msg").text("Sending request...");
				$.ajax({
					type: "post",
					url: "../edit_leave_ajax.php",
					data: frm.serialize(),
					success: function(data){
						//alert(data);
						//console.log(data);
						if(data== "sent"){
							//$("#modal_show").hide();
							//$("#ajax_msg").text("Your request has been sent to your manager");
							alert("Your request has been sent to your manager");
							setTimeout(function(){ location.reload(); }, 3000);
						}else if(data== "same_leave"){
							$("#ajax_msg").text("");
							$("#ajax_msg").css("color", "red");
							setTimeout(function(){ alert("Sorry!! You can't sent edit request for same leave"); }, 1000);
						}else if(data== "wrong_date_range"){
							$("#ajax_msg").text("");
							$("#ajax_msg").css("color", "red");
							setTimeout(function(){ alert("Sorry!! ToDate should be greater than FromDate"); }, 1000);
						}else if(data== "fields_blank"){
							$("#ajax_msg").text("");
							$("#ajax_msg").css("color", "red");
							setTimeout(function(){ alert("Sorry!! Please fill out all the fields properly"); }, 1000);
						}else if(data== "more_than_fifteen"){
							$("#ajax_msg").text("");
							$("#ajax_msg").css("color", "red");
							setTimeout(function(){ alert("Sorry!! more than fifteen back days are not allowed for leave"); }, 1000);
						}else if(data== "back_days_LA_LE"){
							$("#ajax_msg").text("");
							$("#ajax_msg").css("color", "red");
							setTimeout(function(){ alert("Sorry!! more than three back days are not allowed for LA/LE"); }, 1000);
						}else if(data== "too_much_advance"){
							$("#ajax_msg").text("");
							$("#ajax_msg").css("color", "red");
							setTimeout(function(){ alert("Sorry!! you can not apply leave in too much advance 90 days"); }, 1000);
						}else if(data== "already_range"){
							$("#ajax_msg").text("");
							$("#ajax_msg").css("color", "red");
							setTimeout(function(){ alert("Sorry!! you already have a leave in this range"); }, 1000);
						}else if(data== "official_off"){
							$("#ajax_msg").text("");
							$("#ajax_msg").css("color", "red");
							setTimeout(function(){ alert("Sorry!! you can not apply leave on official off"); }, 1000);
						}else{
							$("#ajax_msg").text("");
							$("#ajax_msg").css("color", "red");
							setTimeout(function(){ alert("Opps!! Something went wrong, please try after sometime"); }, 1000);
						}
						$("#modal_show").hide();
						//setTimeout(function(){ location.reload(); }, 3000);
						//location.reload();
					}
				});
			});
			$(document).on("click", ".clear_action", function(){
				cancel_leave_id= $(this).attr("data-id");
				response= confirm("Are you sure you want to cancel your leave ?");
				if(!response)
					return false;
				$.ajax({
					type: "post",
					url: "../cancel_leave.php",
					data: {id: cancel_leave_id},
					success: function(data){
						//alert(data);
						if(data== "canceled"){
							alert("Your leave has been cancelled successfully");
						}else if(data== "sent"){
							alert("Your leave cancellation request has been sent to your manager");
						}else{
							alert("Opps!! Something went wrong, please try after some time");
						}
						location.reload();
					}
				});
			});
			$(document).on("click", "#close_modal", function(){
				//edit_row_id= $(this).attr("data-id");
				$("#modal_show").hide();
				
			});
    // Setup - add a text input to each header cell
   /* $('#example thead tr:eq(1) th').each( function () {
        var title = $('#example thead tr:eq(0) th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } ); */
  
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
    // Apply the search
    /*table.columns().every(function (index) {
        $('#example thead tr:eq(1) th:eq(' + index + ') input').on('keyup change', function () {
            table.column($(this).parent().index() + ':visible')
                .search(this.value)
                .draw();
        });
    });*/
});
</script>
        <!--<script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>-->
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <!--<script src="assets/plugins/datatables/js/jquery.dataTables.min.js"></script>-->
        <script src="../assets/js/alpha.min.js"></script>
        <!--<script src="assets/js/pages/table-data.js"></script>-->
		
		
         <!-- Modal Structure -->
  
    </body>
</html>
<?php } ?>