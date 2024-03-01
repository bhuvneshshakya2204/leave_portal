<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
	die;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Leave Adjustment </title>
        
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
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>

    </head>
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
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                                <h3>Leave Adjustment</h3><br><br>

                                <div class="col s4">
                                	<select id="ddMonth" onchange="filterData();">
                                	<?php for($i=1;$i<=12;$i++) {
                                		$dateObj   = DateTime::createFromFormat('!m', $i);
										$monthName = $dateObj->format('F');
                                	?>
                                		<option <?php echo date('m')==$i?'selected':''; ?>  value="<?php echo $i ?>"><?php echo $monthName  ?></option>
                                	<?php }?>
                                	</select>
                                </div>
                                <div class="col s8"></div>
								<table id="tbl-leave-ajdustment" class="display" style="width:100%">
							        <thead>
							            <tr>
							            	<th  class="no-sort">Emp ID</th>
							            	<th>Employee Name</th>
							                <th>Mobile Number</th>
							                <th>IL</th>
							                <th>CL</th>
							                <!-- <th></th> -->
							                <th class="no-sort">Action</th>
							            </tr>
							        </thead>
							    </table>
							</div>
                        </div>
                    </div>
                </div>
            </main>
         
        </div>
        <div class="left-sidebar-hover"></div>
        <?php require_once('../shared/popup.php'); ?>
	<script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
    <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
    <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
    <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
    <script src="../assets/js/alpha.min.js"></script>
    <script src="../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        
        
        
    </body>

    <script>
	var extreParams = {
    		filter:{

    		}
    	}
	$(document).ready(function() {
		var values = {};
		values["lc.month"]= $("#ddMonth").val();
		extreParams.filter = values;
		var table = $('#tbl-leave-ajdustment').DataTable( {
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
				"url" : "leave_adjustment_data.php",
				"type": "POST",
				data: function (d) {
				    Object.assign(d, extreParams);
				    return d;
				}
			},
	        "order": [[ 1, "asc" ]],
			"fnDrawCallback": function( oSettings ) {
		      bindAction();
		    },
		    oLanguage: {sProcessing: "<div id='loader'></div>"},
		    columnDefs: [{orderable: false, targets:  "no-sort"}],
		    "bFilter": false
	    } );
	    $('.dataTables_length select').addClass('browser-default');

	} );

	function  bindAction(){
		var action = "";
		$(".btnAction").each(function(){
			action = $( this ).attr( "action" );
			var id = $( this ).parent().children().last().attr("data-id");
			
			if(action == 'adjustLeave'){
				$(this).bind("click",{ id: id },function(event){
				  var data = event.data;
				  openAdjestLeave(data.id,$("#ddMonth").val());
				});
			}
		});
	}

	function filterData(){
		var values = {};
		values["lc.month"]= $("#ddMonth").val();
		extreParams.filter = values;
		$("#tbl-leave-ajdustment").DataTable().ajax.reload();
	}

	function openAdjestLeave(id,month){
		leaveCountsDetail(id,month,<?php echo date('Y')?>)
	}
    </script>
</html>
