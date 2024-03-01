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
	
	/*$sql= "select * from tblleaves
		where str_to_date(FromDate, '%d/%m/%Y') >= '$from_date'
		and str_to_date(ToDate, '%d/%m/%Y') <= '$to_date'";*/
		//$sql= "select * from tblleaves where PostingDate between '$from_date' and '$to_date' and ";
		$condition= "";
		/*if($_SERVER["HTTP_HOST"]== "www.netsutra.com" || $_SERVER["HTTP_HOST"]== "netsutra.com"){
			$blank_date= "1969-12-31";
		}else{
			$blank_date= "1970-01-01";
		}*/
		if($_SERVER["HTTP_HOST"]== "www.netsutra.com" || $_SERVER["HTTP_HOST"]== "netsutra.com"){
			$blank_date= "1970-01-01";
		}else{
			$blank_date= "1970-01-01";
		}
		if($from_date!= $blank_date && $to_date != $blank_date){
			//$condition= "L.PostingDate between '$from_date' and '$to_date'";
			if(isset($_SESSION['mlogin']) || isset($_SESSION['hrlogin'])){
				//$condition= "(str_to_date(FromDate, '%d-%m-%Y') >= '$from_date' or str_to_date(ToDate, '%d-%m-%Y') <= '$to_date')";
				$sql= "select id, str_to_date(FromDate, '%d-%m-%Y') as f_date, str_to_date(ToDate, '%d-%m-%Y') as t_date from tblleaves";
				$query = $dbh -> prepare($sql);
				$query->execute();
				$results_range=$query->fetchAll(PDO::FETCH_OBJ);
				if($query->rowCount() > 0){
					$arr_leave_id= array();
					foreach($results_range as $val_range){
						//$new_date = date('Y-m-d', strtotime($day . " +1 days"));
						$from_date_first= $val_range->f_date;
						$to_date_first= $val_range->t_date;
						$to_date_first = date('Y-m-d', strtotime($to_date_first . " +1 days"));
						$leave_period = new DatePeriod(
						 new DateTime($from_date_first),
						 new DateInterval('P1D'),
						 new DateTime($to_date_first)
						 );
						 
						 $leave_period_array= array();
						 foreach ($leave_period as $key => $value) {
							$leave_period_array[]= $value->format('Y-m-d');  
						}
						
						 $filter_period = new DatePeriod(
						 new DateTime(date('Y-m-d', strtotime($from_date))),
						 new DateInterval('P1D'),
						 new DateTime(date('Y-m-d', strtotime($to_date . " +1 days")))
						 );
						 
						$filter_period_array= array();
						 foreach ($filter_period as $key => $value) {
							$filter_period_array[]= $value->format('Y-m-d');  
						}
						 
						 $intersect = !empty(array_intersect($leave_period_array, $filter_period_array));
						 if($intersect!= ""){
							 $arr_leave_id[]= $val_range->id;
						 }
					}
					if(!empty($arr_leave_id)){
						$condition= "L.id in ('".implode("','",$arr_leave_id)."')";
					}
				}
				
			}else{
				$condition= "(str_to_date(FromDate, '%d-%m-%Y') >= '$from_date' and str_to_date(ToDate, '%d-%m-%Y') <= '$to_date')";
			}
		}
		if($from_date!= $blank_date && $to_date == $blank_date){
			//$condition= "L.PostingDate >= '$from_date'";
			$condition= "str_to_date(FromDate, '%d-%m-%Y') >= '$from_date'";
		}
		if($from_date== $blank_date && $to_date != $blank_date){
			//$condition= "L.PostingDate <= '$to_date'";
			$condition= "str_to_date(ToDate, '%d-%m-%Y') <= '$to_date'";
		}
		if(!empty($_POST['email_filter'])){
				if($from_date== $blank_date && $to_date == $blank_date){
					// 1970-01-01  is for localhost
				$condition.= "emp.FirstName like '%$fname%' and emp.LastName like '%$lname%'";
			}else{
				$condition.= " and emp.FirstName like '%$fname%' and emp.LastName like '%$lname%'";
			}
		}
		//if(!empty($_POST['leave_type_dropdown'])){
			if($_POST['leave_type_dropdown'][0] != ""){
				if($from_date== $blank_date && $to_date == $blank_date && $_POST['email_filter']== ""){
                    if (in_array("All", $leave_type_array)){
                        $condition.= " L.LeaveType IS NOT NULL ";
                    }else{
                        $condition.= " L.LeaveType in ('".implode("','",$leave_type_array)."')";
                    }	
				}else{
                    if (in_array("All", $leave_type_array)){
                        $condition.= " ";
                    }else{
					   $condition.= " and L.LeaveType in ('".implode("','",$leave_type_array)."')";
                    }
				}
		}
		if($_POST['leave_status_dropdown'][0] != ""){
				if($from_date== $blank_date && $to_date == $blank_date && $_POST['email_filter']== "" && $_POST['leave_type_dropdown'][0]== "")
					$condition.= " L.Status in ('".implode("','",$leave_status_array)."')";
				else
					$condition.= " and L.Status in ('".implode("','",$leave_status_array)."')";
		}
		/*$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate from tblemployees as emp 
		join tblleaves as leave on emp.id= leave.empid where leave.PostingDate between '$from_date' and '$to_date' and emp.FirstName= '$fname' and emp.LastName= '$lname' and leave.LeaveType= '$l_type'";*/
		$m_id= $_SESSION['eid'];
		if($from_date!= $blank_date || $to_date != $blank_date || $_POST['email_filter'] != "" || $_POST['leave_type_dropdown'][0] != "" || $_POST['leave_status_dropdown'][0] != ""){
			if(strlen($_SESSION['mlogin'])!=0){
				$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate, PostingDate, L.Status as Status, emp.id as id, L.id as lid, L.leave_order from tblemployees as emp
				join tblleaves as L on emp.id= L.empid where ".$condition." and L.ManagerID= $m_id order by L.leave_order";
			}else{
				$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate, PostingDate, L.Status as Status, emp.id as id, L.id as lid, L.leave_order from tblemployees as emp
				join tblleaves as L on emp.id= L.empid where ".$condition." order by STR_TO_DATE(FromDate, '%d-%m-%Y') desc";
			}
		}else{
			if(strlen($_SESSION['mlogin'])!=0){
				$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate, PostingDate, L.Status as Status, emp.id as id, L.id as lid, L.leave_order from tblemployees as emp
				join tblleaves as L on emp.id= L.empid where L.ManagerID= $m_id order by L.leave_order";
			}else{
				$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate, PostingDate, L.Status as Status, emp.id as id, L.id as lid, L.leave_order from tblemployees as emp
				join tblleaves as L on emp.id= L.empid order by STR_TO_DATE(FromDate, '%d-%m-%Y') desc";
			}
		}
		
		//echo $sql;die;
		$query = $dbh -> prepare($sql);
		$query->execute();
		$results=$query->fetchAll(PDO::FETCH_OBJ);
		$cnt=1;
		?>
		<table id="example" class="display responsive-table" width="100%">
                                    <thead>
                                        <tr>
                                            <!--<th width="5%">#</th>-->
                                            <th width="15%">Employee Name</th>
                                            <th width="17%">Leave Type</th>
											<th width="10%">From Date</th>
											<th width="10%">To Date</th>
											<?php
											if(isset($_SESSION['hrlogin'])){ ?>
												<th width="17%">Posting Date</th>
											<?php } ?>
                                            <th width="15%">Status</th>
											<?php
											if(strlen($_SESSION['mlogin'])!=0){ ?>
													<th width="15%" class="no-sort">Action(s)</th>
											<?php } else{ ?>
											<th width="15%" class="no-sort">Action</th>
											<?php } ?>
												
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
                                            <!--<td> <b><?php echo htmlentities($cnt);?></b></td>-->
											<?php
											if(isset($_SESSION['hrlogin'])){ ?>
												<td><a href="editemployee.php?empid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->FirstName." ".$result->LastName);?><!--(<?php echo htmlentities($result->EmpId);?>)--></a></td>
											<?php }else{ ?>
												<td><?php echo htmlentities($result->FirstName." ".$result->LastName);?><!--(<?php echo htmlentities($result->EmpId);?>)--></td>
											<?php } ?>
											  <td><?php echo htmlentities($result->LeaveType);?></td>
											  <td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->FromDate)))); ?></span><?php echo htmlentities($result->FromDate);?></td>
											  <td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->ToDate)))); ?></span><?php echo htmlentities($result->ToDate);?></td>
											  <?php
												if(isset($_SESSION['hrlogin'])){ ?>
													<td><?php echo date("d-m-Y H:i:s", strtotime(htmlentities($result->PostingDate)));?></td>
												<?php } ?>
                                                                       <td><?php $stats=$result->Status;
											if($stats==1){
                                             ?>
											 <?php if($result->leave_order== 4.5){ ?>
												 <span style="color: green">Approved by HR</span>
											 <?php }else{ ?>
                                                 <span style="color: green">Approved</span>
											 <?php } ?>
                                                 <?php }else if($stats==2)  { ?>
                                                <span style="color: red">Not Approved</span>
                                                 <?php }else if($stats==0)  { ?>
 <span style="color: blue">Waiting for approval</span>
 <?php }else if($stats==3){ ?>
	<span style="color: red">Cancelled</span>
<?php }else if($stats==4){ ?>
	<span style="color: blue">Requested for edit</span>
<?php }else if($stats==5){ ?>
	<span style="color: red">Requested for cancellation</span>
<?php } ?>


                                             </td>

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
				<td><a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));?>"><i class="material-icons icon_hover edit_icon" title="View details">visibility</i></a>
				<i class="material-icons icon_hover clear_icon cancelLeave" data-id="<?php echo base64_encode(base64_encode($result->lid));?>" title="Cancel">clear</i>
                <?php if($stats==1){ ?>
				    <i class="material-icons icon_hover edit_icon edit_action" data-id="<?php echo $result->lid; ?>" title="Edit Leave">edit</i>
				<?php } ?>        
            </td>
			<?php } ?>
                                    </tr>
                                         <?php $cnt++;} }?>
                                    </tbody>
									<!--<tfoot>
									<tr class="col_filter">
											<th>Employee Name</th>
											<th>Leave Type</th>
											<th>From Date</th>
											<th>To Date</th>
											<th>Posting Date</th>
											<th>Status</th>
											<th style="display: none;">NA</th>
										</tr>
									</tfoot>-->
                                </table>
			<?php //}
		//}
		
} ?>
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