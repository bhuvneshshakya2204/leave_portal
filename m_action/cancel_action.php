<?php
session_start();
error_reporting(0);
include('../includes/config.php');
include('../includes/send_email.php');
include('../includes/custom_functions.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
<style>
.red_border{
	border-bottom: 2px solid red!important;
}
.errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
</style>
        <!-- Title -->
        <title>Netsutra - Home Page</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />

        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">


        <!-- Theme Styles -->
        <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>


        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="http://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="http://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>
    <body>
	<?php
	$msg= "";
	$error= "";
	$description= "";
		$lid= base64_decode(base64_decode(base64_decode($_REQUEST['lid'])));
		$status= base64_decode(base64_decode(base64_decode($_REQUEST['act'])));
		//query to fetch record related to leave id
		$sql = "SELECT tl.empid, tl.ManagerID, tl.Status, tl.FromDate, tl.ToDate, tl.LeaveType, tl.PostingDate, te1.FirstName, 
				te1.LastName, te1.EmailId, te2.FirstName AS ManagerName, te2.LastName AS ManagerLastName, 
				te2.EmailId AS ManagerEmail
				FROM tblleaves AS tl
				INNER JOIN tblemployees AS te1 ON tl.empid = te1.id
				INNER JOIN tblemployees AS te2 ON tl.ManagerID = te2.id
				WHERE tl.id =:lid";
		$query = $dbh -> prepare($sql);
		$query->bindParam(':lid',$lid,PDO::PARAM_STR);
		$query->execute();
		$results=$query->fetchAll();
		if($query->rowCount() > 0 && ($status== "approved_ok" || $status== "disapproved_not_ok")){
				foreach($results as $result_both){}
				$manager_name= $result_both['ManagerName']." ".$result_both['ManagerLastName'];
				$manager_email= $result_both['ManagerEmail'];
				$emp_email= $result_both['EmailId'];
				$emp_name= $result_both['FirstName']." ".$result_both['LastName'];
				$manager_id= $result_both['ManagerID'];
				$emp_id= $result_both['empid'];               
                $modify_posting_date= new DateTime($result_both['PostingDate']);
                $modify_posting_date= $modify_posting_date->format("d-m-Y H:i:s");
				$sql= "select * from tblleaves_history where emp_id=:emp_id and manager_id=:manager_id and leave_id=:lid order by id desc limit 1";
				$query = $dbh -> prepare($sql);
				$query->bindParam(':emp_id',$emp_id,PDO::PARAM_STR);
				$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
				$query->bindParam(':lid',$lid,PDO::PARAM_STR);
				$query->execute();
				$results_check= $query->fetchAll();
				foreach($results_check as $result_check){}
				$l_type= $result_check['leave_type'];
				$l_from= $result_check['from_date'];
				$l_to= $result_check['to_date'];
				if($result_check['status']== "cancelled by manager" && $status== "approved_ok"){
					$msg= "Cancellation is already approved";
				}else if($result_check['status']== "manager did not cancelled" && $status== "disapproved_not_ok"){
					$msg= "Cancellation is already not approved";
				}else{
					//code to update history and main table
					if($status== "approved_ok"){
						$status_updated= "cancelled by manager";
						$status_inword= "Approved";
						$status_main_table= 3;
						$leave_order= 6;
					}else{
						$status_updated= "manager did not cancelled";
						$status_inword= "Disapproved";
						$status_main_table= 1;
						$leave_order= 4;
					}
					date_default_timezone_set('Asia/Kolkata');
					$created_at= date("d-m-Y H:i:s");
						$sql="INSERT INTO tblleaves_history(leave_id,leave_type,from_date,to_date,created_at,status,emp_id,manager_id) VALUES(:lid,:l_type,:l_from,:l_to,:created_at,:status_updated,:emp_id,:manager_id)";
						$query = $dbh->prepare($sql);
						$query->bindParam(':lid',$lid,PDO::PARAM_STR);
						$query->bindParam(':l_type',$l_type,PDO::PARAM_STR);
						$query->bindParam(':l_from',$l_from,PDO::PARAM_STR);
						$query->bindParam(':l_to',$l_to,PDO::PARAM_STR);
						$query->bindParam(':created_at',$created_at,PDO::PARAM_STR);
						$query->bindParam(':status_updated',$status_updated,PDO::PARAM_STR);
						$query->bindParam(':emp_id',$emp_id,PDO::PARAM_STR);
						$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
						$query->execute();
						$lastInsertId = $dbh->lastInsertId();
						if($lastInsertId){
							
							$sql= "update tblleaves set Status=:status_main_table, leave_order=:leave_order where empid=:emp_id and ManagerID=:manager_id and id=:lid";
							$query = $dbh->prepare($sql);
							//$query->bindParam(':l_type',$l_type,PDO::PARAM_STR);
							//$query->bindParam(':l_from',$l_from,PDO::PARAM_STR);
							$query->bindParam(':status_main_table',$status_main_table,PDO::PARAM_STR);
							$query->bindParam(':leave_order',$leave_order,PDO::PARAM_STR);
							$query->bindParam(':emp_id',$emp_id,PDO::PARAM_STR);
							$query->bindParam(':manager_id',$manager_id,PDO::PARAM_STR);
							$query->bindParam(':lid',$lid,PDO::PARAM_STR);
							$query->execute();
                            //Code for display count total taken leave and LA/LE
                            $fromdate_array= explode("-", $l_from);
                            $this_year_print= $fromdate_array[2];
                            $this_month= $fromdate_array[1];
                            $this_long_month= date("M",strtotime($l_from));                       
                            $total_leave = total_leave_taken_current_month($dbh, $emp_id, $this_year_print, $this_month);
                            $total_short_leave = total_shortleave_taken_current_month($dbh, $emp_id, $this_year_print, $this_month);
                            //End code for display count total taken leave and LA/LE
							//codes to send email confirmation
							$email_body= "<table><tr><td><strong>Dear $emp_name</strong><br><br>\r\n";
							$email_body.= "Your cancellation request for $l_type from $l_from to $l_to is $status_inword.</td></tr>\r\n";
                            $email_body.= "<tr><td><br><strong>Total Leave Taken in $this_long_month $this_year_print:  </strong>$total_leave</td></tr>\r\n";
				            $email_body.= "<tr><td><strong>Total LA/LE Taken in $this_long_month $this_year_print:  </strong>$total_short_leave/4</td></tr>\r\n";                            
                            $email_body.= "<tr><td><strong>Posting Date: </strong>$modify_posting_date</td></tr>\r\n";
							$email_body.= "<tr><td><br><font face='verdana' size='-1'><strong>Thanks,</strong><br>$manager_name</font></td></tr></table>\r\n";
							$subject= "Re: Application for updating ".$l_type;
							$bcc= "";
							$cc= "kriti@netsutra.com";
							//$emp_email= "dipak.kumar@netsutra.com";
							//$manager_email= "dipak@yopmail.com";
							send_email($emp_email, $manager_email, $bcc, $cc, $subject, $email_body);
							$msg= "Cancel request updated successfully";
						}
					//end of update
				}
		}else{
			$error= "Invalid parameter received";
		}
	?>
       <div class="loader-bg"></div>
        <div class="loader">
            <div class="preloader-wrapper big active">
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-spinner-teal lighten-1">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mn-content fixed-sidebar">
            <header class="mn-header navbar-fixed">
                <nav class="cyan darken-1">
                    <div class="nav-wrapper row">
                        <section class="material-design-hamburger navigation-toggle">
                            <a href="#" data-activates="slide-out" class="button-collapse show-on-large material-design-hamburger__icon">
                                <span class="material-design-hamburger__layer"></span>
                            </a>
                        </section>
                        <div class="header-title col s3">
                            <a href="http://www.netsutra.com"><img src="../assets/images/Logo.png" alt="netsutra logo" style="margin-top: 3%;" /></a>
                        </div>


                        </form>


                    </div>
                </nav>
            </header>
			<?php include_once("../includes/left_navigation_action.php"); ?>
            <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title"><h4>Welcome to Leave Management System</h4></div>

                          <div class="col s12 m6 l8 offset-l2 offset-m3">
                              <div class="">
								<?php
								if($error){
									echo "<h4 style='color: red;'>$error</h4>";
								}else{
                                  echo "<h4 style='color: green;'>$msg</h4>";
								}
								?>
                              </div>
                          </div>
                    </div>
                </div>
            </main>

        </div>
        <div class="left-sidebar-hover"></div>

        <!-- Javascripts -->
        <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
    </body>
</html>
