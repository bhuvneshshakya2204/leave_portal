<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['hrlogin'])==0 && strlen($_SESSION['mlogin'])==0 && strlen($_SESSION['emplogin'])==0)
{   
    header('location:../index.php');
}
else{
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - FAQs</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet"> 
        <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
        <style>
            .bgcolore-odd {
                background-color: gainsboro;
                padding-bottom: 10px;
                padding-top: 10px;
                padding-left: 10px;
            }
            .bgcolor-even {
                padding-left: 10px;
            }
        </style>
    </head>
    <body>
    <?php
	if(isset($_SESSION['emplogin'])){ ?>
       <?php include('../includes/header.php');?>  
       <?php include('../includes/sidebar.php');?>
	<?php }else{ ?>
		<?php include('includes/header.php');?>
       <?php include('includes/sidebar.php');?>
	<?php } ?>
            <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">FAQs</div>
                    </div>
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                              <h3>FAQs</h3>
                                <div class="row bgcolore-odd">
                                    <b>1. What, if I need to update my info?</b>
                                    <br>Except Skills field all the fields are uneditable at employee end. So, Contact HR for updating.
                                </div>
                                <div class="row bgcolor-even">
                                    <b>2. How can I know about the Company Holidays?</b>
                                    <br>By Holiday calendar tab.
                                </div>
                                <div class="row bgcolore-odd">
                                    <b>3. How can I know if my leave is approved?</b>
                                    <br>If the project manager has approved the Leave you will get the mail as well as Go to My Leave->Leave History. It will show the status.
                                </div>
                                <div class="row bgcolor-even">
                                    <b>4. How can I know about the LA/LE /Leaves taken in a month?</b>
                                    <br>Go to Leave History->Advance filter->Choose the filters.
                                </div>
                                <div class="row bgcolore-odd">
                                    <b>5. If my leave is not approved but after reconsideration PM agrees to change the decision, then what is the next step forward?</b> 
                                    <br>PM will have the authority to edit the leave status.
                                </div>
                                <div class="row bgcolor-even">
                                    <b>6. If I got late but came within 1/2 hr., then do I have to apply for LA?</b> 
                                    <br>No need to apply but you must inform HR and he/she will apply for LA from his/her end.
                                </div>
                                <div class="row bgcolore-odd">
                                    <b>7. What to do if I apply for Leave, got approval but before going for the leave my plan changes?</b> 
                                    <br>In such case, you can send cancellation request.
                                </div>
                                <div class="row bgcolor-even">
                                    <b>8. Can I cancel the leave before the approval?</b>
                                    <br>Yes, cancel option is available in Employee leave history till the "from date" of leave.
                                </div>
                                <div class="row bgcolore-odd">
                                    <b>9. What, If I applied for 1 day leave and came back after 2 days?</b> 
                                    <br>Then you must apply leave for the remaining days within 3 days of coming back.
                                </div>
                                <div class="row bgcolor-even">
                                    <b>10. What, If I applied for 2 days leave and came back after 1 day?</b>
                                    <br>Contact HR person he/she can edit leave from HR dashboard.
                                </div>
                                <div class="row bgcolore-odd">
                                    <b>11. Is there any condition for applying LA?</b> 
                                    <br>LA should be applied in advance or on the same day of availing it.
                                </div>
                                <div class="row bgcolor-even">
                                    <b>12. What, If I forgot to apply LA on same day?</b>
                                    <br>Contact HR.
                                </div>
                                <div class="row bgcolore-odd">
                                    <b>13. What, If I could not apply CL within 15 days?</b>
                                    <br>Contact HR.
                                </div>
                                <div class="row bgcolor-even">
                                    <b>14. What to do if I applied for LA and taken Leave?</b>
                                    <br>You must send cancellation request for LA first then after cancellation has been approved by PM, apply for fresh leave.
                                </div>
                                <div class="row bgcolore-odd">
                                    <b>15. What to do if I applied for leave but came back on LA?</b> 
                                    <br>You must send cancellation for Leave first then after cancellation approved by PM, apply for fresh LA.
                                </div>
                                <div class="row bgcolor-even">
                                    <b>16. Till when can I edit or cancel my leave?</b>
                                    <br>Leave applied once can only be edited until "from date” has not passed.
                                </div>
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
        <script src="../assets/js/pages/form_elements.js"></script>
        <script>
            $(document).keydown(function (event) {
                if (event.keyCode == 123) { // Prevent F12
                    return false;
                } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) { // Prevent Ctrl+Shift+I        
                    return false;
                }
            });
		</script>
    </body>
</html>
<?php } ?> 