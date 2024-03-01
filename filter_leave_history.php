<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0 && strlen($_SESSION['emplogin'])==0)
{   
	echo "error";
}else{
	//print_r($_POST);DIE;
	/*$email= $_POST['email_filter'];
	$email= explode(" ", $email);
	$fname= $email[0];
	$lname= $email[1];
	if($email[2]!= ""){
		$extra_name= $email[2];
		//$lname= $lname.' '.$extra_name;
	}*/
	$m_id= $_SESSION['eid'];
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
		/*if($_SERVER["HTTP_HOST"]== "www.netsutra.com" || $_SERVER["HTTP_HOST"]== "netsutra.com")
			$blank_date= "1969-12-31";
		else
			$blank_date= "1970-01-01";*/
			if($_SERVER["HTTP_HOST"]== "www.netsutra.com" || $_SERVER["HTTP_HOST"]== "netsutra.com")
			$blank_date= "1970-01-01";
		else
			$blank_date= "1970-01-01";
		if($from_date!= $blank_date && $to_date != $blank_date){
			//$condition= "L.PostingDate between '$from_date' and '$to_date'";
			if(true){
				//$condition= "(str_to_date(FromDate, '%d-%m-%Y') >= '$from_date' or str_to_date(ToDate, '%d-%m-%Y') <= '$to_date')";
				$sql= "select id, str_to_date(FromDate, '%d-%m-%Y') as f_date, str_to_date(ToDate, '%d-%m-%Y') as t_date from tblleaves where empid= $m_id";
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
		/*if(!empty($_POST['email_filter'])){
				if($from_date== $blank_date && $to_date == $blank_date){
					// 1970-01-01  is for localhost
				$condition.= "emp.FirstName like '%$fname%' and emp.LastName like '%$lname%'";
			}else{
				$condition.= " and emp.FirstName like '%$fname%' and emp.LastName like '%$lname%'";
			}
		}*/
		//if(!empty($_POST['leave_type_dropdown'])){
			if($_POST['leave_type_dropdown'][0] != ""){
				if($from_date== $blank_date && $to_date == $blank_date)
					$condition.= " L.LeaveType in ('".implode("','",$leave_type_array)."')";
				else
					$condition.= " and L.LeaveType in ('".implode("','",$leave_type_array)."')";
		}
		if($_POST['leave_status_dropdown'][0] != ""){
				if($from_date== $blank_date && $to_date == $blank_date && $_POST['leave_type_dropdown'][0]== "")
					$condition.= " L.Status in ('".implode("','",$leave_status_array)."')";
				else
					$condition.= " and L.Status in ('".implode("','",$leave_status_array)."')";
		}
		/*$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate from tblemployees as emp 
		join tblleaves as leave on emp.id= leave.empid where leave.PostingDate between '$from_date' and '$to_date' and emp.FirstName= '$fname' and emp.LastName= '$lname' and leave.LeaveType= '$l_type'";*/
		//$m_id= $_SESSION['eid'];
		if($from_date!= $blank_date || $to_date != $blank_date || $_POST['leave_type_dropdown'][0] != "" || $_POST['leave_status_dropdown'][0] != ""){
			if(true){
				$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate, PostingDate, L.Status as Status, L.AdminRemark, emp.id as id, L.id as lid from tblemployees as emp
				join tblleaves as L on emp.id= L.empid where ".$condition." and L.empid= $m_id order by L.leave_order";
			}else{
				$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate, PostingDate, L.Status as Status, emp.id as id, L.id as lid from tblemployees as emp
				join tblleaves as L on emp.id= L.empid where ".$condition." order by L.PostingDate desc";
			}
		}else{
			if(true){
				$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate, PostingDate, L.Status as Status, L.AdminRemark, emp.id as id, L.id as lid from tblemployees as emp
				join tblleaves as L on emp.id= L.empid where L.empid= $m_id order by L.leave_order";
			}else{
				$sql= "select FirstName, LastName, LeaveType, FromDate, ToDate, PostingDate, L.Status as Status, emp.id as id, L.id as lid from tblemployees as emp
				join tblleaves as L on emp.id= L.empid order by L.PostingDate desc";
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
		//echo $sql; die;
		if($query->rowCount() > 0)
		{
			foreach($results as $result)
			{ ?>
				<tr style="border-bottom: 1px solid gray;">
                                            <!--<td> <b><?php echo htmlentities($cnt);?></b></td>-->
											  <td><?php echo htmlentities($result->LeaveType);?></td>
											  <td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->FromDate)))); ?></span><?php echo htmlentities($result->FromDate);?></td>
											  <td><span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->ToDate)))); ?></span><?php echo htmlentities($result->ToDate);?></td>
													<td><?php echo date("d-m-Y H:i:s", strtotime(htmlentities($result->PostingDate)));?></td>
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
													<i class="material-icons icon_hover clear_icon clear_action" data-id="<?php echo $result->lid; ?>">clear</i>
												<?php } ?>
												<?php if($stats== 0 || $stats==1 || $stats==2){ ?>
													<i class="material-icons icon_hover edit_icon edit_action" data-id="<?php echo $result->lid; ?>">edit</i>
												<?php } ?>
											<?php } ?>
												</td>
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