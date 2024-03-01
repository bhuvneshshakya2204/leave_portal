<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['hrlogin'])==0 && strlen($_SESSION['emplogin'])==0)
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
	$manager_dropdown_array= $_POST['manager_dropdown'];
		$condition= "";
		if(!empty($_POST['email_filter'])){
				//if($_POST['skill_filter'] == "" && $_POST['manager_dropdown'][0] == "")
					// 1970-01-01  is for localhost
				$condition.= "emp.FirstName like '%$fname%' and emp.LastName like '%$lname%'";
		}
		if($_POST['manager_dropdown'][0] != ""){
			if($_POST['email_filter'] == ""){
				$condition.= "reporting_manager in ('".implode("','",$manager_dropdown_array)."')";
			}else{
				//echo $from_date.$to_date.$_POST['email_filter'].$_SERVER["HTTP_HOST"];die;
				$condition.= " and reporting_manager in ('".implode("','",$manager_dropdown_array)."')";
			}
		}
		if($_POST['skill_filter'] != ""){
			$skill_filter= trim($_POST['skill_filter']);
			$skill_filter= str_replace(", ", " ", $skill_filter);
			$skill_filter= str_replace(",", " ", $skill_filter);
			$skill_filter= explode(" ", $skill_filter);
			$skill= "";
			foreach($skill_filter as $val_skill){
				$skill.= " or skill_set like '%".$val_skill."%'";
			}
			$skill= ltrim($skill, " or");
			//$skill= rtrim($skill);
			if($_POST['email_filter'] == "" && $_POST['manager_dropdown'][0]== ""){
				$condition.= "(".$skill.")";
			}else{
				$condition.= " and (".$skill.")";
			}
		}
		if($condition != ""){
			if(isset($_SESSION['hrlogin']))
				$sql = "SELECT * from tblemployees as emp where ".$condition." order by emp.FirstName";
			else
				$sql = "SELECT * from tblemployees as emp where ".$condition." and Status= 1 order by emp.FirstName";
		}else{
			if(isset($_SESSION['hrlogin']))
				$sql = "SELECT * from tblemployees as emp order by emp.FirstName";
			else
				$sql = "SELECT * from tblemployees as emp where Status= 1 order by emp.FirstName";
		}
		//echo $sql;die;
		$query = $dbh -> prepare($sql);
		$query->execute();
		$results=$query->fetchAll(PDO::FETCH_OBJ);
		$cnt=1;
		?>
		<table id="example" class="display responsive-table " <?php if(isset($_SESSION['mlogin'])){ echo "style= 'font-size: 13px'"; } ?>>
                                    <thead>
                                        <tr>
                                            <!--<th>Sr no</th>-->
											<?php
											if(isset($_SESSION['hrlogin'])){ ?>
                                            <th class="no-sort">Emp Id</th>
                                            <th>Name</th>
                                            <th>Designation</th>
                                             <th>Joining Date</th>
                                             <th>Relieving Date</th>
                                             <th>Status</th>
                                            <th class="no-sort">Action</th>
											<?php }else if(isset($_SESSION['mlogin'])){ ?>
                                            <th width="13%">Name</th>
                                            <th width="13%">Designation</th>
                                             <th>Contact #</th>
                                             <th>Email</th>
											 <th>Manager</th>
											 <th width="5%">Skills</th>
											 <th>Previous Exp.</th>
											 <th>Joining Date</th>
											<?php }else{ ?>
											<th>Name</th>
                                            <th>Designation</th>
                                             <th>Contact #</th>
                                             <th>Email</th>
											 <th>Manager</th>
											<?php } ?>
                                        </tr>
                                    </thead>
                                 
                                    <tbody>
<?php
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{ ?>  
	<tr>
	<?php if(isset($_SESSION['hrlogin'])){ ?>
		<td><?php echo htmlentities($result->EmpId);?></td>
		<td><?php echo htmlentities($result->FirstName);?>&nbsp;<?php echo htmlentities($result->LastName);?></td>
		<td><?php echo $result->designation?></td>
		 <td>
        	<span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->JoiningDate)))); ?></span>
			<?php echo ($result->JoiningDate != "") ? date("d-m-Y", strtotime(str_replace(',', '', htmlentities($result->JoiningDate)))) : "";?>
		</td>
		<td><?php echo $result->releiving_date != '00-00-0000' ? 
			date("d-m-Y", strtotime(str_replace(',', '', htmlentities($result->releiving_date)))): '';?>
		</td>
		<td><?php echo $result->Status == '1' ? '<a class="waves-effect waves-green btn-flat m-b-xs">Active</a>':'<a class="waves-effect waves-red btn-flat m-b-xs">Inactive</a>';?></td>
                                            <td><a href="editemployee.php?empid=<?php echo htmlentities($result->id);?>" target="_blank"><i class="material-icons">mode_edit</i></a>
                                        <?php if($result->Status==1)
 {?>
<!--<a href="manageemployee.php?inid=<?php echo htmlentities($result->id);?>" onclick="return confirm('Are you sure you want to inactive this Employe?');"" > <i class="material-icons" title="Inactive">clear</i>-->
&nbsp;
<?php } else {?>

                                            <a href="manageemployee.php?id=<?php echo htmlentities($result->id);?>" onclick="return confirm('Are you sure you want to active this employee?');" target="_blank"><i class="material-icons" title="Active">done</i>
                                            <?php } ?> </td>
											<?php }else if(isset($_SESSION['mlogin'])){ ?>
												<td><?php echo htmlentities($result->FirstName);?>&nbsp;<?php echo htmlentities($result->LastName);?></td>
												<td><?php echo htmlentities($result->designation)== 'Manager - HR' ? htmlentities($result->designation):ucwords(htmlentities($result->designation));?></td>
												<td><?php echo htmlentities($result->Phonenumber);?></td>
												<td><?php echo htmlentities($result->EmailId);?></td>
												<td>
													<?php
														$sql= "select FirstName, LastName from tblemployees where id= '".$result->reporting_manager."'";
														$query = $dbh -> prepare($sql);
														$query->execute();
														$manager_rs=$query->fetchAll(PDO::FETCH_OBJ);
														if($query->rowCount() > 0){
															echo $manager_rs[0]->FirstName." ".$manager_rs[0]->LastName;
														}else{
															echo "Not Allotted";
														}
														
													?>
												</td>
												<td>
													<?php 
														$skills= explode(" ", htmlentities($result->skill_set));
														$skills= trim($skills[0], ",");
														$skills= trim($skills, ";");
														$skills= trim($skills, "-");
														echo "<span data-id='$result->id' class='skill'>$skills</span>";
													?>
												</td>
												<td><?php echo htmlentities($result->previous_exp);?></td>
												<td>
											  <span class="hide_span"><?php echo str_replace('-', '', date('Y-m-d', strtotime(str_replace('/', '-', $result->JoiningDate)))); ?></span>
													<?php
														echo date("d-m-Y", strtotime(str_replace(',', '', htmlentities($result->JoiningDate))));
													?>
											  </td>
											<?php }else{ ?>
												<td><?php echo htmlentities($result->FirstName);?>&nbsp;<?php echo htmlentities($result->LastName);?></td>
												<td><?php echo htmlentities($result->designation)== 'Manager - HR' ? htmlentities($result->designation):ucwords(htmlentities($result->designation));?></td>
												<td><?php echo htmlentities($result->Phonenumber);?></td>
												<td><?php echo htmlentities($result->EmailId);?></td>
												<td>
													<?php
														$sql= "select FirstName, LastName from tblemployees where id= '".$result->reporting_manager."'";
														$query = $dbh -> prepare($sql);
														$query->execute();
														$manager_rs=$query->fetchAll(PDO::FETCH_OBJ);
														if($query->rowCount() > 0){
															echo $manager_rs[0]->FirstName." ".$manager_rs[0]->LastName;
														}else{
															echo "Not Allotted";
														}
														
													?>
												</td>
											<?php } ?>
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