<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['hrlogin']) == 0) {
    header('location:../index.php');
} else {
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>

        <!-- Title -->
        <title>Netsutra - Dashboard</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />

        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../assets/plugins/metrojs/MetroJs.min.css" rel="stylesheet">
        <link href="../assets/plugins/weather-icons-master/css/weather-icons.min.css" rel="stylesheet">


        <!-- Theme Styles -->
        <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .total-reg-emp {
                cursor: pointer;
            }

            .tab,
            .tab_email,
            .tab_email_anni {
                cursor: pointer;
            }
        </style>
    </head>

    <body>
        <?php include('includes/header.php'); ?>

        <?php include('includes/sidebar.php'); ?>

        <main class="mn-inner">
            <!-- <div class="middle-content"> -->
            <div class="content">
                <!--<div class="row no-m-t no-m-b">-->
                <h4 style="text-align: center;">Welcome to Employee Leave Management System</h4>
                <br>
                <div class="row">
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content">

                                <span class="card-title">Total Regd. Employee</span>
                                <span class="stats-counter">
                                    <?php
                                    $sql = "SELECT id from tblemployees where Status=1 and UserType != 'director'";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    $empcount = $query->rowCount();
                                    ?>

                                    <!-- <span class="counter"> -->
                                    <!-- comment by vikash -->
                                    <span class="">
                                        <?php echo htmlentities($empcount); ?>

                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-bar"></div>
                        </div>
                    </div>
                    <a href="#wish_employee" class="modal-trigger">
                        <div class="col m3 s12">
                            <div class="card stats-card">
                                <div class="card-content">

                                    <span class="card-title">Birthday(s) Today</span>
                                    <?php
                                    date_default_timezone_set('Asia/Kolkata');
                                    $today_date = date("Y-m-d");
                                    $date_object = new DateTime($today_date);
                                    $today_date = $date_object->format('j F');
                                    $sql = "SELECT FirstName, LastName, Dob, id, EmailId from tblemployees where Status=1 and Dob like '" . $today_date . "%'";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results_wish = $query->fetchAll(PDO::FETCH_OBJ);
                                    $wish_count = $query->rowCount();
                                    ?>
                                    <span class="stats-counter">
                                        <!-- <span class="counter"> -->
                                        <span class="">
                                            <?php echo htmlentities($wish_count); ?>
                                        </span>
                                    </span>
                                </div>
                                <div id="sparkline-line"></div>
                            </div>
                        </div>
                    </a>
                    <a href="#wish_employee_anniversary" class="modal-trigger">
                        <div class="col m3 s12">
                            <div class="card stats-card">
                                <div class="card-content">

                                    <span class="card-title">Anniversary(s) Today</span>
                                    <?php
                                    $sql = "SELECT FirstName, LastName, AnniversaryDate, id, EmailId from tblemployees where Status=1 and AnniversaryDate like '" . $today_date . "%'";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results_wish_anniversary = $query->fetchAll(PDO::FETCH_OBJ);
                                    $wish_count_anniversary = $query->rowCount();
                                    ?>
                                    <span class="stats-counter">
                                        <!-- <span class="counter"> -->
                                        <span class="">
                                            <?php echo htmlentities($wish_count_anniversary); ?>
                                        </span>
                                    </span>
                                </div>
                                <div id="sparkline-line"></div>
                            </div>
                        </div>
                    </a>
                    <a href="#wish_employee_work_anniversary" class="modal-trigger">
                        <div class="col m3 s12">
                            <div class="card stats-card">
                                <div class="card-content">

                                    <span class="card-title">Work Anniversary(s) Today</span>
                                    <?php
                                    $sql = "SELECT FirstName, LastName, JoiningDate, id, EmailId from tblemployees where Status=1 and JoiningDate like '" . $today_date . "%'";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results_wish_work_anniversary = $query->fetchAll(PDO::FETCH_OBJ);
                                    $wish_count_anniversary = $query->rowCount();
                                    ?>
                                    <span class="stats-counter">
                                        <!-- <span class="counter"> -->
                                        <span class="">
                                            <?php echo htmlentities($wish_count_anniversary); ?>
                                        </span>
                                    </span>
                                </div>
                                <div id="sparkline-line"></div>
                            </div>
                        </div>
                    </a>

                    
                </div>
                <div class="row">
                <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="add_employee">

                                <span class="card-title">Add Employee</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="manage_employee">

                                <span class="card-title">Manage Employee</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="add_leave_type">

                                <span class="card-title">Add Leave Type</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="manage_leave_type">

                                <span class="card-title">Manage Leave Type</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="add_leave">

                                <span class="card-title">Add Leave/LA/LE</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="all_leaves">

                                <span class="card-title">All Leaves</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="pending_leave">

                                <span class="card-title">Pending Leaves</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="add_designation_type">

                                <span class="card-title">Add Designation Type</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="manage_designation_type">

                                <span class="card-title">Manage Designation Type</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="manage_miss_punch">

                                <span class="card-title">Manage Miss Punch</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="add_miss_punch">

                                <span class="card-title">Add Miss Punch</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content" style="min-height: 120px;">

                                <span class="card-title">Leaves For <?php echo date('F') ?></span>
                                <?php
                                $month = date('m');
                                $year = date('Y');
                                $sql = "SELECT lc.* from leave_counts lc where lc.emp_id = " . $eid . " AND lc.month =" . $month . " AND lc.year =" . $year;
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $result = $query->fetch(PDO::FETCH_OBJ);
                                $empId =   intval($result->EmpId);
                                $totalIL = intval($result->total_il);
                                $totalCL = intval($result->total_cl);
                                $totalCO = intval($result->total_co);
                                ?>
                                <span class="stats-counter" style="font-size: 12px;font-weight: bold;">
                                    <!-- <span class="counter"> -->
                                    <div>
                                        <div class="col s6">IL</div>
                                        <div class="col s6">CL</div>
                                    </div>
                                    <div>
                                        <div class="col s6"><?php echo $totalIL ?></div>
                                        <div class="col s6"><?php echo $totalCL ?></div>
                                    </div>
                                </span>

                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content" style="min-height: 120px;">

                                <span class="card-title">Leaves For <?php echo date('F', strtotime(date('Y-m') . " -1 month")) ?></span>
                                <?php
                                $month = date('m');
                                $month = intval($month - 1);
                                $year = date('Y');
                                $sql = "SELECT lc.* from leave_counts lc where lc.emp_id = " . $eid . " AND lc.month =" . $month . " AND lc.year =" . $year;
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $result = $query->fetch(PDO::FETCH_OBJ);
                                $empId =   intval($result->EmpId);
                                $totalIL = intval($result->total_il);
                                $totalCL = intval($result->total_cl);
                                $totalCO = intval($result->total_co);
                                ?>
                                <span class="stats-counter" style="font-size: 12px;font-weight: bold;">
                                    <!-- <span class="counter"> -->
                                    <div>
                                        <div class="col s6">IL</div>
                                        <div class="col s6">CL</div>
                                    </div>
                                    <div>
                                        <div class="col s6"><?php echo $totalIL ?></div>
                                        <div class="col s6"><?php echo $totalCL ?></div>
                                    </div>
                                </span>

                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                    <div class="col m3 s12">
                        <div class="card stats-card">
                            <div class="card-content tab" id="leave_adjustment">

                                <span class="card-title">Leave Adjustment</span>
                                <span class="stats-counter">
                                    <!-- <span class="counter"> -->
                                    <span class="">
                                        &nbsp;
                                    </span>
                                </span>
                            </div>
                            <div id="sparkline-line"></div>
                        </div>
                    </div>
                </div>
        </main>

        </div>
        <!--code for birthday model strats-->
        <div id="wish_employee" class="modal modal-fixed-footer" style="height: 60%; display: block;">
            <div class="modal-content" style="width:100%">
                <span class="card-title" style="font-size: 14px; font-weight: bold;">BIRTHDAY</span>
                <table width="100%" class="display responsive-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Employee Name</th>
                            <th style="text-align:center;">Birthday</th>
                            <th style="text-align:center;">Send Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($results_wish as $emp_record) {
                            $emp_name = htmlentities($emp_record->FirstName . " " . $emp_record->LastName);
                        ?>
                            <tr>
                                <td style="text-align:center;"><span style="color: #0277BB;"><?php echo $emp_name; ?></span></td>
                                <td style="text-align:center;"><?php echo htmlentities($emp_record->Dob); ?></td>
                                <td style="text-align:center;">
                                    <?php if ($_SESSION['hrlogin'] != $emp_record->EmailId) { ?>
                                        <a href="#wish_employee_view" class="modal-trigger">
                                            <i class="material-icons icon_hover email_birthday_view" title="View Message" data-to="<?php echo $emp_record->id; ?>">edit</i>
                                        </a>
                                        <i class="material-icons tab_email" title="Send Message" data-to="<?php echo $emp_record->id; ?>">email</i>
                                    <?php } else {
                                        echo "N/A";
                                    } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer" style="width:100%;">
                <input type="button" name="cancel_btn" value="Close" class="waves-effect waves-light btn blue m-b-xs modal-close" style="margin-right: 10px;" />
            </div>
        </div>
        <!--code for birthday model ends-->

        <!--code for view message model strats-->
        <div id="wish_employee_view" class="modal modal-fixed-footer" style="height: 60%; display: block;">
            <div class="modal-content" style="width:100%">
                <script type="text/javascript">
                    $(document).ready(function() {
                        $(".email_birthday_view").click(function() {
                            id = $(this).attr("data-to");
                            $.ajax({
                                type: "post",
                                url: "view_wish.php",
                                data: {
                                    id: id
                                },
                                success: function(data) {
                                    if (data == "expired") {
                                        alert("Sorry!! Your session has expired, please login");
                                    } else {
                                        $("#displayMessage").html(data);
                                    }
                                }
                            });
                        });
                        $(".email_aniversary_view").click(function() {
                            id = $(this).attr("data-to-anni");
                            $.ajax({
                                type: "post",
                                url: "view_wish.php",
                                data: {
                                    id: id,
                                    anniversary: 1
                                },
                                success: function(data) {
                                    if (data == "expired") {
                                        alert("Sorry!! Your session has expired, please login");
                                    } else {
                                        $("#displayMessage").html(data);
                                    }
                                }
                            });
                        });
                        $(".email_work_aniversary_view").click(function() {
                            id = $(this).attr("data-to-anni");
                            $.ajax({
                                type: "post",
                                url: "view_wish.php",
                                data: {
                                    id: id,
                                    work_anniversary: 1
                                },
                                success: function(data) {
                                    if (data == "expired") {
                                        alert("Sorry!! Your session has expired, please login");
                                    } else {
                                        $("#displayMessage").html(data);
                                    }
                                }
                            });
                        });
                    });
                </script>
                <div id="displayMessage"></div>
            </div>
            <div class="modal-footer" style="width:100%">
                <input type="button" name="cancel_btn" value="Close" class="waves-effect waves-light btn blue m-b-xs modal-close" style="margin-right: 10px;" />
            </div>
        </div>
        <!--code for view message model ends-->

        <!--code for anniversary model strats-->
        <div id="wish_employee_anniversary" class="modal modal-fixed-footer" style="height: 60%; display: block;">
            <div class="modal-content" style="width:100%">
                <span class="card-title" style="font-size: 14px; font-weight: bold;">ANNIVERSARY</span>
                <table width="100%" class="display responsive-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Employee Name</th>
                            <th style="text-align:center;">Anniversary</th>
                            <th style="text-align:center;">Send Message</th>
                        </tr>
                    </thead>
                    <?php
                    foreach ($results_wish_anniversary as $emp_record) {
                        $emp_name = htmlentities($emp_record->FirstName . " " . $emp_record->LastName);
                    ?>
                        <tr>
                            <td style="text-align:center;"><span style="color: #0277BB;"><?php echo $emp_name; ?></span></td>
                            <td style="text-align:center;"><?php echo htmlentities($emp_record->AnniversaryDate); ?></td>
                            <td style="text-align:center;">
                                <?php if ($_SESSION['hrlogin'] != $emp_record->EmailId) { ?>
                                    <a href="#wish_employee_view" class="modal-trigger">
                                        <i class="material-icons icon_hover email_aniversary_view" title="View Message" data-to-anni="<?php echo $emp_record->id; ?>">edit</i>
                                    </a>
                                    <i class="material-icons tab_email_anni" title="Send Message" data-to-anni="<?php echo $emp_record->id; ?>">email</i>
                                <?php } else {
                                    echo "N/A";
                                } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="modal-footer" style="width:100%">
                <input type="button" name="cancel_btn" value="Close" class="waves-effect waves-light btn blue m-b-xs modal-close" style="margin-right: 10px;" />
            </div>
        </div>
        <!--code for anniversary model ends-->


        <!--code for anniversary model strats-->
        <div id="wish_employee_work_anniversary" class="modal modal-fixed-footer" style="height: 60%; display: block;">
            <div class="modal-content" style="width:100%">
                <span class="card-title" style="font-size: 14px; font-weight: bold;">Work ANNIVERSARY</span>
                <table width="100%" class="display responsive-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;">Employee Name</th>
                            <th style="text-align:center;">Work Anniversary</th>
                            <th style="text-align:center;">Send Message</th>
                        </tr>
                    </thead>
                    <?php
                    foreach ($results_wish_work_anniversary as $emp_record) {
                        $emp_name = htmlentities($emp_record->FirstName . " " . $emp_record->LastName);
                    ?>
                        <tr>
                            <td style="text-align:center;"><span style="color: #0277BB;"><?php echo $emp_name; ?></span></td>
                            <td style="text-align:center;"><?php echo htmlentities($emp_record->JoiningDate); ?></td>
                            <td style="text-align:center;">
                                <?php if ($_SESSION['hrlogin'] != $emp_record->EmailId) { ?>
                                    <a href="#wish_employee_view" class="modal-trigger">
                                        <i class="material-icons icon_hover email_work_aniversary_view" title="View Message" data-to-anni="<?php echo $emp_record->id; ?>">edit</i>
                                    </a>
                                    <i class="material-icons tab_email_work_anni" title="Send Message" data-to-anni="<?php echo $emp_record->id; ?>">email</i>
                                <?php } else {
                                    echo "N/A";
                                } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="modal-footer" style="width:100%">
                <input type="button" name="cancel_btn" value="Close" class="waves-effect waves-light btn blue m-b-xs modal-close" style="margin-right: 10px;" />
            </div>
        </div>
        <!--code for anniversary model ends-->


        <!-- Javascripts -->
        <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $(".tab_email").click(function() {
                    id = $(this).attr("data-to");
                    $.ajax({
                        type: "post",
                        url: "send_wish.php",
                        data: {
                            id: id
                        },
                        success: function(data) {
                            if (data == "expired") {
                                alert("Sorry!! Your session has expired, please login");
                            } else if (data == "sent") {
                                alert("Message sent successfully");
                            } else {
                                alert("Invalid parameter received");
                            }
                        }
                    });
                });
                $(".tab_email_anni").click(function() {
                    id = $(this).attr("data-to-anni");
                    $.ajax({
                        type: "post",
                        url: "send_wish.php",
                        data: {
                            id: id,
                            anniversary: 1
                        },
                        success: function(data) {
                            if (data == "expired") {
                                alert("Sorry!! Your session has expired, please login");
                            } else if (data == "sent") {
                                alert("Message sent successfully");
                            } else {
                                alert("Invalid parameter received");
                            }
                        }
                    });
                });
                $(".tab_email_work_anni").click(function() {
                    id = $(this).attr("data-to-anni");
                    $.ajax({
                        type: "post",
                        url: "send_wish.php",
                        data: {
                            id: id,
                            work_anniversary: 1
                        },
                        success: function(data) {
                            if (data == "expired") {
                                alert("Sorry!! Your session has expired, please login");
                            } else if (data == "sent") {
                                alert("Message sent successfully");
                            } else {
                                alert("Invalid parameter received");
                            }
                        }
                    });
                });
                // $(".total-reg-emp").on("click",function(){
                // window.open("manageemployee.php", "_blank");
                // });
                $("#add_employee").on("click", function() {
                    window.open("addemployee.php", "_blank");
                });
                $("#manage_employee").on("click", function() {
                    window.open("manageemployee.php", "_blank");
                });
                $("#add_leave_type").on("click", function() {
                    window.open("addleavetype.php", "_blank");
                });
                $("#manage_leave_type").on("click", function() {
                    window.open("manageleavetype.php", "_blank");
                });
                $("#add_leave").on("click", function() {
                    window.open("add_leave.php", "_blank");
                });
                $("#all_leaves").on("click", function() {
                    window.open("leaves.php", "_blank");
                });
                $("#pending_leave").on("click", function() {
                    window.open("pending-leavehistory.php", "_blank");
                });
                $("#add_designation_type").on("click", function() {
                    window.open("adddesignationtype.php", "_blank");
                });
                $("#manage_designation_type").on("click", function() {
                    window.open("managedesignationtype.php", "_blank");
                });
                $("#manage_miss_punch").on("click", function() {
                    window.open("miss_punch_detail.php", "_blank");
                });
                $("#add_miss_punch").on("click", function() {
                    window.open("miss_punch.php", "_blank");
                });
                $("#leave_adjustment").on("click", function() {
                    window.open("leave_adjustment.php", "_blank");
                });

            });
        </script>
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="../assets/plugins/counter-up-master/jquery.counterup.min.js"></script>
        <script src="../assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="../assets/plugins/chart.js/chart.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.time.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.symbol.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.resize.min.js"></script>
        <script src="../assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
        <script src="../assets/plugins/curvedlines/curvedLines.js"></script>
        <script src="../assets/plugins/peity/jquery.peity.min.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
        <script src="../assets/js/pages/dashboard.js"></script>

    </body>

    </html>
<?php } ?>