<?php
session_start();
error_reporting(0);
include('includes/config.php');
$param = $_REQUEST;
if(isset($param['showLeaveCounts'])){
	$id= base64_decode(base64_decode($param['empId']));
	$month = $param['month'];
	$year = $param['year'];
	$sql = "SELECT e.EmpId, lc.* from tblemployees e 
	LEFT JOIN leave_counts lc ON e.id=lc.emp_id where e.id= ".$id." AND month =".$month." AND year =".$year;
	$query = $dbh -> prepare($sql); 
	$query->execute();
	$result=$query->fetch(PDO::FETCH_OBJ);
	$totalIL = intval($result->total_il);
	$totalCL = intval($result->total_cl);
	$totalCO = intval($result->total_co);
	$empId = intval($result->EmpId);
?>
<table style="width:600px;">
	<tr>
		<td colspan="2"><b>Emp ID:</b> <?php echo $empId ?></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td>TOTAL IL</td>
		<td><input type="text" id="empTotalIL" value="<?php echo $totalIL ?>"></td>
		<td>TOTAL CL</td>
		<td><input type="text" id="empTotalCL" value="<?php echo $totalCL ?>"></td>
	</tr>
	<tr>
		<!-- <td>TOTAL CO</td> -->
		<!-- <td><input type="text" id="empTotalCO" value="<?php //echo $totalCO ?>"></td> -->
		<td></td>
		<td></td>
		<td  colspan="2"><span style="float:right;" class="btn btn-primary pull-right" id="btnUpdateLeaveAdjust">Update Data</span></td>
	</tr>
</table>
<?php }?>
<?php 
if(isset($param['updateLeaveCounts'])){
	$id= base64_decode(base64_decode($param['empId']));
	
	$month = $param['month'];
	$year = $param['year'];
	$totalIL = intval($param['totalIL']);
	$totalCL = intval($param['totalCL']);
	$totalCO = intval($param['totalCO']);

	$sql = "select * from leave_counts where emp_id= ".$id." AND month =".$month." AND year =".$year;
	$query = $dbh -> prepare($sql); 
	$query->execute();
    $data=$query->fetch(PDO::FETCH_ASSOC);
    if($data){
		$sql = "UPDATE leave_counts set total_il=".$totalIL.", total_cl = ".$totalCL.", total_co = ".$totalCO." where emp_id= ".$id." AND month =".$month." AND year =".$year;
    }else{
    	$sql = "insert into leave_counts set total_il=".$totalIL.", total_cl = ".$totalCL.", total_co = ".$totalCO.", emp_id= ".$id.", month =".$month.", year =".$year;
    }
	
	$query = $dbh -> prepare($sql); 
	$query->execute();
	echo json_encode(array("status"=>200,"message"=>"Data updated successfully"));
}
?>