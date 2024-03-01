<?php
session_start();
error_reporting(0);
include('../includes/config.php');
include('../shared/email-template.php');
include('../includes/custom_functions.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>

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


    </head>
    <body>
	<?php
	$msg= "";
	$error= "";
	$description= "";
	$id= base64_decode(base64_decode(base64_decode($_REQUEST['id'])));
	$status= base64_decode(base64_decode(base64_decode($_REQUEST['act'])));

    $sql = "SELECT * FROM tblmisspunch WHERE id =$id";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $result=$query->fetch(PDO::FETCH_OBJ);

    if($query->rowCount() > 0 && ($status== "approved_ok" || $status== "disapproved_not_ok")){
        $statusToSave = $status== "approved_ok" ? 1 : 2;
        $updateQuery = "update tblmisspunch set status = $statusToSave where id =$id";
        $query = $dbh -> prepare($updateQuery);
        $query->execute();

        $sql = "select e.*,e1.FirstName as mFirstName,e1.LastName as mLastName,
            e1.EmailId as ManagerEmail from tblemployees e 
            LEFT JOIN tblemployees e1 ON e.reporting_manager = e1.id 
            where e.id=".$result->empid;
        $query = $dbh -> prepare($sql);
        $query->execute();
        $empDetail = $query->fetch(PDO::FETCH_OBJ);

         $arr = $arr = array(
            'HR_NAME'           => 'Kriti',
            'EMP_NAME'          => $empDetail->FirstName." ".$empDetail->LastName,
            'STATUS'            => ($statusToSave == 1) ? 'approved':'disapproved',
            'DATE'              => $result->misspunch_date,
            'TIME'              => $result->misspunch_time,
            'MISS_PUNCH_TYPE'   => ($result->type ==1)?'In-Time':'Out-Time', 
            'SIGNATURE_NAME'    => $empDetail->mFirstName." ".$empDetail->mLastName
        );
        $email_body = getEmailContent($arr,EMAIL_MISS_PUNCH_APPROVAL);

        $subject= "Re: Application for miss punch approval";
        $bcc= "";
        $cc= $empDetail->EmailId;
        send_email('kriti@netsutra.com', $empDetail->ManagerEmail, $bcc, $cc, $subject, $email_body);
        $msg= "Miss punch approval request updated successfully";

    }else{
        $error= "Invalid parameter received";
    }
	
	?>
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
