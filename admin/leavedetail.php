<?php
session_start();
error_reporting(0);
include('includes/config.php');
$lid= base64_decode(base64_decode($_POST['leaveid']));
$sql = "SELECT tl.id,tl.empid, tl.ManagerID, tl.Status, tl.FromDate, tl.ToDate, tl.LeaveType, te1.FirstName, 
		te1.LastName, te1.EmailId,te1.Phonenumber,te1.EmpId,te1.Gender, te2.FirstName AS ManagerName, te2.LastName AS ManagerLastName, 
		te2.EmailId AS ManagerEmail FROM tblleaves AS tl INNER JOIN tblemployees AS te1 ON tl.empid = te1.id
		INNER JOIN tblemployees AS te2 ON tl.ManagerID = te2.id WHERE tl.id =$lid";
$query = $dbh -> prepare($sql);
$query->execute();
$data= $query->fetch(PDO::FETCH_OBJ);
if($data->Status == 4){
	$sql= "select leave_type, from_date, to_date from tblleaves_history where leave_id=$lid order by id desc limit 0,1";
	$query = $dbh -> prepare($sql);
	$query->execute();
	$results_edited= $query->fetch(PDO::FETCH_OBJ);
}
if(!$data){
	echo "<h3 class='text-center'>Data Not Found !!</h3>";
	die;
}
?>
<style>
	.popup-data td{
		padding: 3px;
		font-size: 10px;
	}
	.row-edit{
		margin:10px auto !important;text-align:center;border-top:1px solid #CCC;width: 50%
	}
	.btn-open-edit{
		border:1px solid #CCC;
		border-top:none;
		padding:3px 20px;
		font-size: 12px;
		color:#000;
		background-color: #fafafa;
		cursor: pointer;
	}
	.btn-open-edit:hover{
		color:#317307;
	}
	.btn-popup{
		font-size: 12px;
		height: 26px;
		padding: 3px 20px;
		line-height: 22px;
		float:right;
	}
	#editLeaveDates{
		display: none;
	}
</style>
<table class="display responsive-table popup-data ">
	<tr>
		<td> <b>Employee Name :</b></td>
		<td colspan="4"><?php echo htmlentities($data->FirstName." ".$data->LastName);?></td>
		<td><b>Gender :</b></td>
		<td><?php echo htmlentities($data->Gender);?></td>
	</tr>
	<tr>
        <td><b>Emp Email ID :</b></td>
        <td colspan="4"><?php echo htmlentities($data->EmailId);?></td>
        <td><b>Emp Id :</b></td>
		<td><?php echo htmlentities($data->EmpId);?></td>
    </tr>
    <tr>
        <td><b>Emp Contact No :</b></td>
        <td colspan="4"><?php echo htmlentities($data->Phonenumber);?></td>
        <td><b>Manager :</b></td>
        <td><?php echo htmlentities($data->ManagerName." ".$data->ManagerLastName);?></td>
    </tr>
    <tr>
        <td colspan="7"><hr></td>
    </tr>
    <tr>
        <td><b>Leave Type :</b></td>
        <td colspan="4"><?php echo htmlentities($data->LeaveType);?></td>
        <td><b>Leave Status :</b></td>
        <td class="<?php echo $gbl_statColorArr[$data->Status] ?>"><?php echo $gbl_statusArr[$data->Status];?></td>
    </tr>
    <tr>
        <td><b>Leave Date :</b></td>
        <td colspan="4">From</b> <?php echo htmlentities($data->FromDate);?> <b>to</b> <?php echo htmlentities($data->ToDate);?></td>
        <td><b>Posting Date :</b></td>
        <td>
        	<?php $date_india= new DateTime($result->PostingDate);
			$date_india= $date_india->format("d-m-Y H:i:s");
			echo htmlentities($date_india);?>
		</td>
    </tr>
    <?php if($data->Status == 4){?>
    <tr bgcolor="#FFFF00" border="1">
        <td><b>Modified Leave Type :</b></td>
        <td colspan="4"><?php echo $results_edited->leave_type ?></td>
        <td><b>Leave Date :</b></td>
        <td>From</b> <?php echo htmlentities($results_edited->from_date);?> <b>to</b> <?php echo htmlentities($results_edited->to_date);?></td>
    </tr>
    <?php }?>
    <tr>
        <td colspan="7" style=""><p class="row-edit"><span class="btn-open-edit">Edit</span></p></td>
    </tr>
</table>
<form id="editLeaveDates">
<table class="display responsive-table popup-data ">
	<tr>
		<td> <b>From Date :</b></td>
		<td colspan="2">
			<input type="text" id="fDate" name="fDate" class="datepicker_filter" size="10" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd-mm-yyyy" value="<?php echo $data->FromDate;?>" />
			<input type="hidden" id="defaultFromDate" value="<?php echo $data->FromDate;?>">
		</td>
		<td><b>To Date :</b></td>
		<td colspan="2">
			<input type="text" id="toDate" name="toDate" class="datepicker_filter" size="10" style="color: #7F9FAF; border-bottom: 1px solid #BCBCBC;" placeholder="dd-mm-yyyy" value="<?php echo htmlentities($data->ToDate);?>" />
			<input type="hidden" id="defaultToDate" value="<?php echo $data->ToDate;?>">
		</td>
	</tr>
	
     <tr>
        <td colspan="7">
        	<span class="btn btn-default pull-right btn-popup" onclick="cancelEditLeaveDates();">Cancel</span>
        	<span class="btn btn-default pull-right btn-popup" style="margin-right: 5px;" onclick="submitEditLeaveDates();">Edit</span>
        	<input type="hidden" name="leaveId" value="<?php echo base64_encode(base64_encode($data->id)) ?>">
        	<input type="hidden" name="action" value="update-date">
        	<input type="hidden" name="leaveType" value="<?php echo $data->LeaveType?>">
        </td>
    </tr>
</table>
</form>