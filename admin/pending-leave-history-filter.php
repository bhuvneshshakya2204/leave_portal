<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0)
{   
	echo "error";
}else{
	//print_r($_POST);DIE;
	$email= $_POST['email_filter'];
	$email= explode(" ", $email);
	$fname= $email[0];
	$lname= $email[1];
	if($email[2]!= ""){
		$extra_name= $email[2];
		//$lname= $lname.' '.$extra_name;
	}
	$leave_type_array= $_POST['leave_type_dropdown'];
	$leave_status_array= $_POST['leave_status_dropdown'];
	$from_date= $_POST['from_date'];
	$from_date= str_replace("/", "-", $from_date);
	$from_date = date('Y-m-d',strtotime($from_date));
	
	$to_date= $_POST['to_date'];
	$to_date= str_replace("/", "-", $to_date);
	$to_date = date('Y-m-d',strtotime($to_date));
	$sortColumn =  $_POST['sortColumn'];
	$sortDirection =  $_POST['sortDirection'];
	
	$condition= "";
	if($_SERVER["HTTP_HOST"]== "www.netsutra.com" || $_SERVER["HTTP_HOST"]== "netsutra.com"){
		$blank_date= "1970-01-01";
	}else{
		$blank_date= "1970-01-01";
	}

	if($from_date!= $blank_date){
		$condition.= " and str_to_date(FromDate, '%d-%m-%Y') >= '$from_date'";
	}
	if($to_date != $blank_date){
		$condition.= "and str_to_date(ToDate, '%d-%m-%Y') <= '$to_date'";
	}
	if(!empty($_POST['email_filter'])){
		$condition.= " and emp.FirstName like '%$fname%' and emp.LastName like '%$lname%'";
	}
		//if(!empty($_POST['leave_type_dropdown'])){
	if($_POST['leave_type_dropdown'][0] != ""){
		if (in_array("All", $leave_type_array)){
			$condition.= " ";
		}else{
			$condition.= " and L.LeaveType in ('".implode("','",$leave_type_array)."')";
		}
	}

	if($_POST['leave_status_dropdown'][0] != ""){
		$condition.= " and L.Status in ('".implode("','",$leave_status_array)."')";
	}


	if(strlen($_SESSION['mlogin'])!=0){
		$m_id= $_SESSION['eid'];
		$condition .= " and L.ManagerID= $m_id ";
	}

	$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate, PostingDate, L.Status as Status, emp.id as id, L.id as lid, L.leave_order,L.AdminRemark from tblemployees as emp join tblleaves as L on emp.id= L.empid where (L.Status=0 or L.Status=4 or L.Status=5) ".$condition." order by STR_TO_DATE(FromDate, '%d-%m-%Y') asc";

	$query = $dbh -> prepare($sql);
	$query->execute();
	$results=$query->fetchAll(PDO::FETCH_OBJ);
	$cnt=1;
	?>
	<table id="example" class="display responsive-table" width="100%">
		<thead>
			<tr>
				<th width="14%">Employee Name</th>
                <th width="17%">Leave Type</th>
				<th width="13%">From Date</th>
				<th width="13%">To Date</th>
				<th width="17%">Posting Date</th>
				<th width="15%">Status</th>
				<th width="15%" class="no-sort">Action(s)</th>

			</tr>
		</thead>
		<tbody>
			<?php
		//echo $sql; die;
			if($query->rowCount() > 0)
			{
				foreach($results as $result)
					{ ?>
						<tr style="border-bottom: 1px solid gray;">
							<td style="cursor: pointer;color:#0277bb;" onclick="getUserDetail(<?php echo $result->id ?>)"><?php echo htmlentities($result->FirstName." ".$result->LastName);?></td>
							
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
											<td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $val_edited['from_date'])))); ?></span><?php echo $val_edited['from_date']." (<span style='color: red;'>".$result->FromDate."</span>)";?></td>
											<td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $val_edited['to_date'])))); ?></span><?php echo $val_edited['to_date']." (<span style='color: red;'>".$result->ToDate."</span>)";?></td>
											<?php }else{ ?>
											<td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->FromDate)))); ?></span><?php echo htmlentities($result->FromDate);?></td>
											<td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->ToDate)))); ?></span><?php echo htmlentities($result->ToDate);?></td>
											<?php } ?>

							<td><?php echo date("d-m-Y H:i:s", strtotime(htmlentities($result->PostingDate)));?></td>
							<td class="<?php echo $gbl_statColorArr[$result->Status]; ?>"> 
								<?php echo $gbl_statusArr[$result->Status]; ?>
                            </td>
							


                                             

							<?php
							if(isset($_SESSION['mlogin'])){ ?>
							<td>
								<i onclick="openUpdateLeavePopup('<?php echo base64_encode(base64_encode($result->lid));?>');" class="material-icons icon_hover edit_icon" title="Approve/Disapprove with description">edit</i>&nbsp;&nbsp;
								<i class="material-icons icon_hover check_icon check_action" data-id="<?php echo base64_encode(base64_encode($result->lid));?>" title="Approve">check</i>&nbsp;<i class="material-icons icon_hover clear_icon clear_action" data-id="<?php echo base64_encode(base64_encode($result->lid));?>" title="Disapprove">clear</i>
								
							</td>
							<?php }else{ ?>
							   <td>
									<i class="material-icons icon_hover clear_icon cancelLeave" data-id="<?php echo base64_encode(base64_encode($result->lid));?>" title="Cancel">clear</i>
								</td>
							<?php }?>

						</tr>
						<?php $cnt++;} }?>
					</tbody>
				</table>
			<?php //}
		//}

		} ?>
		<script>
		var table = $('#example').DataTable({
       		orderCellsTop: true,
	   		"ordering": true,
        	columnDefs: [{orderable: false, targets:  "no-sort"}],
 			"aaSorting": [],
			language: {searchPlaceholder: "Search any of the columns"},
			"order": [[ <?php echo $sortColumn?>, "<?php echo $sortDirection?>" ]]
    	});
	 	$('.dataTables_length select').addClass('browser-default');
		</script>