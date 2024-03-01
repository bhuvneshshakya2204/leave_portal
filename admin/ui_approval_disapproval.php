<table id="example" class="display responsive-table" width="100%">
                                    <thead>
                                        <tr>
                                           <!-- <th width="5%">#</th>-->
                                            <th width="15%">Employee Name</th>
                                            <th width="17%">Leave Type</th>
											<th width="10%">From Date</th>
											<th width="10%">To Date</th>
											<?php
											if(isset($_SESSION['hrlogin'])){ ?>
                                            <th width="17%">Posting Date</th>                 
                                            <th width="15%">Status</th>
											<?php } ?>
											<?php
											if(strlen($_SESSION['mlogin'])!=0){ ?>
												<th width="15%" class="no-sort">Action(s)</th>
												
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
	$sql = "SELECT tblleaves.id as lid,tblleaves.FromDate as fdate, tblleaves.ToDate as tdate,tblemployees.FirstName,tblemployees.LastName,tblemployees.EmpId,tblemployees.id,tblleaves.LeaveType,tblleaves.PostingDate,tblleaves.Status from tblleaves join tblemployees on tblleaves.empid=tblemployees.id where (tblleaves.Status=:status or tblleaves.Status=4 or tblleaves.Status=5) and tblleaves.ManagerID=:m_id order by PostingDate desc";
else
	$sql = "SELECT tblleaves.id as lid,tblleaves.FromDate as fdate, tblleaves.ToDate as tdate,tblemployees.FirstName,tblemployees.LastName,tblemployees.EmpId,tblemployees.id,tblleaves.LeaveType,tblleaves.PostingDate,tblleaves.Status from tblleaves join tblemployees on tblleaves.empid=tblemployees.id where (tblleaves.Status=:status or tblleaves.Status=4 or tblleaves.Status=5) order by PostingDate desc";
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
				<td><a href="leave-details.php?leaveid=<?php echo base64_encode(base64_encode($result->lid));?>"><i class="material-icons icon_hover edit_icon" title="View details">edit</i></a></td>
			<?php } ?>
								   </tr>
                                         <?php $cnt++;} }?>
                                    </tbody>
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