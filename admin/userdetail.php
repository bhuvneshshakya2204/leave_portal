<?php
session_start();
error_reporting(0);
include('includes/config.php');
$sql= "select e.*,m.FirstName as mFirstName,m.LastName as mLastName from tblemployees e LEFT JOIN tblemployees m ON e.reporting_manager = m.id where e.id=" .$_POST['id'];
$query = $dbh -> prepare($sql);
$query->execute();
$user= $query->fetch(PDO::FETCH_OBJ);
if(!$user){
	echo "<h3 class='text-center'>User Not Found !!</h3>";
	die;
}
?>

<table class="display responsive-table ">
	<tr>
		<td> <b>Employee Name :</b></td>
		<td colspan="4"><?php echo htmlentities($user->FirstName." ".$user->LastName);?></td>
		<td><b>Gender :</b></td>
		<td><?php echo htmlentities($user->Gender);?></td>
	</tr>
	<tr>
        <td><b>Emp Email ID :</b></td>
        <td colspan="4"><?php echo htmlentities($user->EmailId);?></td>
        <td><b>Emp Id :</b></td>
		<td><?php echo htmlentities($user->EmpId);?></td>
    </tr>
    <tr>
        <td><b>Emp Contact No. :</b></td>
        <td colspan="6"><?php echo htmlentities($user->Phonenumber);?></td>
    </tr>
    <tr>
        <td><b>Manager :</b></td>
        <td colspan="6"><?php echo htmlentities($user->mFirstName." ".$user->mLastName);?></td>
    </tr>
</table>