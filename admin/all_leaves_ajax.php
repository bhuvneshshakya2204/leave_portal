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
$m_id= $_SESSION['eid'];
if(strlen($_SESSION['mlogin'])!=0){
	$sql = "SELECT tblleaves.id as lid,tblleaves.FromDate as fdate, tblleaves.ToDate as tdate,tblemployees.FirstName,tblemployees.LastName,tblemployees.EmpId,tblemployees.id,tblleaves.LeaveType,tblleaves.PostingDate,tblleaves.Status,tblleaves.leave_order from tblleaves
	join tblemployees on tblleaves.empid=tblemployees.id where ManagerID= $m_id order by leave_order";
}else{
	$sql = "SELECT tblleaves.id as lid,tblleaves.FromDate as fdate, tblleaves.ToDate as tdate,tblemployees.FirstName,tblemployees.LastName,tblemployees.EmpId,tblemployees.id,tblleaves.LeaveType,tblleaves.PostingDate,tblleaves.Status,tblleaves.leave_order from tblleaves
	join tblemployees on tblleaves.empid=tblemployees.id order by PostingDate desc";
}
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{         
      ?>  

                                        <tr style="border-bottom: 1px solid gray;">
                                            <!--<td> <b><?php echo htmlentities($cnt);?></b></td>-->
											<?php
											if(isset($_SESSION['hrlogin'])){ ?>
												<td><a href="editemployee.php?empid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->FirstName." ".$result->LastName);?><!--(<?php echo htmlentities($result->EmpId);?>)--></a></td>
											<?php }else{ ?>
												<td><?php echo htmlentities($result->FirstName." ".$result->LastName);?><!--(<?php echo htmlentities($result->EmpId);?>)--></td>
											<?php } ?>
											  <td><?php echo htmlentities($result->LeaveType);?></td>
											  <td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->fdate)))); ?></span><?php echo htmlentities($result->fdate);?></td>
											  <td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->tdate)))); ?></span><?php echo htmlentities($result->tdate);?></td>
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
				<td><a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));?>"><i class="material-icons icon_hover edit_icon" title="View details">edit</i></a></td>
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