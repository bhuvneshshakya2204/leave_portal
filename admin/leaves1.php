<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <!-- Title -->
    <title>Netsutra - Total Leave </title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta charset="UTF-8">
    
    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
    <link href="../assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">

    <link href="../assets/plugins/google-code-prettify/prettify.css" rel="stylesheet" type="text/css"/>  
    <!-- Theme Styles -->
    <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/custom.css?v=20190323" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="../assets/css/jquery-ui.css">

</head>
    
<body>
	

	<?php 
	include('includes/header.php');
	include('includes/sidebar.php');
	?>
    <div id="overlay" style="display: none;">
		<div class="loading_style"><?php include_once("loader.php"); ?></div>
	</div>
    <main class="mn-inner">
    	<div class="row">
    		<div class="col s12 m12 l12">
		    	<div class="card">
					<div class="card-content">
			    		<h3>Leave History <span id="advFilter" class="advFilterUp">Advance Filter</span></h3><br>

			    		<form id="filterForm">
							<div class="input-field col m2 s12">
								<span class="label_up">Employee Name</span>
								<input id="nameFilter" name="nameFilter">
							</div>

							<div class="input-field col m2 s12">
								<span class="label_up">Leave Type</span>
								<select id="leaveTypeFilter" name="leaveTypeFilter[]" class="multiselect-ui" multiple="multiple" onchange= "filterData('loader')">
								<option value="" selected disabled="disabled">Filter by leave type</option>
                                <?php if(strlen($_SESSION['hrlogin'])) { ?>
                                    <option value="All">All</option>  
                                <?php } ?>
								<?php
									$sql= "select LeaveType from tblleavetype where status= 1";
									$query = $dbh -> prepare($sql);
									$query->execute();
									$results_filter= $query->fetchAll(PDO::FETCH_OBJ);
									if($query->rowCount() > 0)
									{
										$short_form_leave= "";
									foreach($results_filter as $val_filter){ ?>
										<option value="<?php echo htmlentities($val_filter->LeaveType);?>"><?php echo htmlentities($val_filter->LeaveType);?></option>
									<?php } }else{ ?>
									<option value="">No leave type available in database</option>
									<?php } ?>
								</select>
							</div>

							<div class="input-field col m2 s12">
								<span class="label_up">Leave Status</span>
								<select id="leaveStatusFilter" name="leaveStatusFilter[]" class="multiselect-ui" multiple="multiple" onchange= "filterData()">
								<option value="" selected disabled="disabled">Filter by status</option>
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
								<input type="text" id="fDateFilter" name="fDateFilter" class="datepicker_filter" size="30" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd-mm-yyyy" onchange= "filterData('loader')" />
							</div>

							<div class="input-field col m3 s12">
								<span class="label_up">To Date</span><span id="clearFilter">Reset Filter</span>
								<input type="text" id="toDateFilter" name="toDateFilter" class="datepicker_filter" size="30" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd-mm-yyyy" onchange= "filterData('loader')" />
							</div>
						</form>
						
						<table id="tbl-leave" class="display" style="width:100%">
					        <thead>
					            <tr>
					            	<th>Employee Name</th>
					                <th>Leave Type</th>
					                <th>From Date</th>
					                <th>To Date</th>
					                <th>Posting Date</th>
					                <th>Status</th>
					                <th class="no-sort">Action</th>
					            </tr>
					        </thead>
					    </table>
			    	</div>
			    </div>
			</div>
		</div>
    </main>

    <div class="left-sidebar-hover"></div>
	<?php require_once('../shared/popup.php'); ?>
    <!-- Javascripts -->
    <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
    <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
    <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
    <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
    <script src="../assets/js/alpha.min.js"></script>
    <script src="../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    	var extreParams = {
    		filter:{

    		}
    	}
    	$(document).ready(function() {
    		var table = $('#tbl-leave').DataTable( {
		        "processing": true,
		        "serverSide": true,
		        "ajax": {
					"url" : "leaves-data.php",
					"type": "POST",
					data: function (d) {
					    Object.assign(d, extreParams);
					    return d;
					}
				},
		        "order": [[ 2, "desc" ]],
				"fnDrawCallback": function( oSettings ) {
			      bindAction();
			    },
			    oLanguage: {sProcessing: "<div id='loader'></div>"},
			    columnDefs: [{orderable: false, targets:  "no-sort"}],
			    "bFilter": false
		    } );
		    $('.dataTables_length select').addClass('browser-default');

		    $("#advFilter").click(function(){
			  $(this).toggleClass("advFilterUp advFilterDown");
			  $("#filterForm").slideToggle();
			});

			$("#clearFilter").on('click',function(){
				$('#filterForm').trigger("reset");
				filterData();
			});

		} );

		function  bindAction(){
			var action = "";

			$(".btnAction").each(function(){
				action = $( this ).attr( "action" );
				var id = $( this ).parent().children().last().attr("data-id");
				
				if(action == 'leaveDetail'){
					$(this).bind("click",{ id: id },function(event){
					  var data = event.data;
					  leaveDetail(data.id);
					});
				}

				if(action == 'cancelLeave'){
					$(this).bind("click",{ id: id },function(event){
					  var data = event.data;
					  cancelLeave(data.id);
					});
				}

				if(action == 'approve' || action == 'disapprove'){
					$(this).bind("click",{ id: id,action:action },function(event){
					  var data = event.data;
					  leaveApproval(data.id,data.action);
					});
				}
			});
		}

		function cancelLeave(id){
			response= confirm("Are you sure you want to cancel your leave ?");
			if(!response)
				return false;
			$.ajax({
				type: "post",
				url: "update-leave-status.php",
				data: {leaveId: id,action:'cancel'},
				success: function(data){
					reloadDataTable('#tbl-leave');
					response = JSON.parse(data);
					alert(response.msg);
				}
			});
		}

		function reloadDataTable(element){
			$(element).DataTable().ajax.reload();
		}

		
		function filterData(){
			var $inputs = $('#filterForm :input');
		    var values = {};
		    $inputs.each(function() {
		    	if(typeof($(this).val()) != 'object')
		        	values[this.name] = $(this).val();
		    	else
		    		values[this.id] = $(this).val();
		    });
			extreParams.filter = values;
			reloadDataTable('#tbl-leave');
		}

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
            select: function (event, ui) { $('#nameFilter').val(ui.item.value);filterData(); }               
        });

        $("#nameFilter").keyup(function(){
		  if($('#nameFilter').val().length == 0){
			filterData();
		  }
		});

		$( function() {
			$( ".datepicker_filter" ).datepicker();
			$( ".datepicker_filter" ).datepicker("option", "dateFormat", "dd-mm-yy");
		});

    </script>

</body>
</html>    