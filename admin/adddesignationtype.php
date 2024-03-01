<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['hrlogin'])==0)
{   
	header('location:../index.php');
}
else{
if(isset($_POST['add']))
{
$designationtype=$_POST['designationtype'];
$description=$_POST['description'];
$sql = "SELECT id FROM tbldesignationtype WHERE DesignationType='".$designationtype."'";
$query = $dbh -> prepare($sql);
$query->execute();
if($query->rowCount() > 0) {
    $error="Duplicate Designation Type. Please try again";
} else {
    $sql="INSERT INTO tbldesignationtype(DesignationType,Description) VALUES(:designationtype,:description)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':designationtype',$designationtype,PDO::PARAM_STR);
    $query->bindParam(':description',$description,PDO::PARAM_STR);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {
        $msg="Designation type added Successfully";
    } else {
        $error="Something went wrong. Please try again";
    } 
}
}

    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Netsutra - Add Designation Type</title>
        
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
        .errorWrap {
	color: red!important;
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
    </head>
    <body>
  <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
            <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">Add Designation Type</div>
                    </div>
                    <div class="col s12 m12 l8">
                        <div class="card">
                            <div class="card-content">
                              <h3>Add Designation Type</h3><br><br>
                                <div class="row">
                                    <form class="col s12" name="designationadd" method="post">
                                          <?php if($error){?><div class="errorWrap"><strong>ERROR</strong> : <?php echo htmlentities($error); ?> </div><?php } 
                else if($msg){?><div class="succWrap"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php }?>
                                        <div class="row">
                                            <div class="input-field col s12">
<input id="designationtype" type="text"  class="validate" autocomplete="off" name="designationtype"  required>
                                                <label for="designationtype">Designation Type</label>
                                            </div>


                                            <div class="input-field col s12">
<textarea id="textarea1" name="description" class="materialize-textarea" name="description" length="500"></textarea>
                                                <label for="description">Description</label>
                                            </div>


                                            <div class="input-field col s12">
<button type="submit" name="add" class="waves-effect waves-light btn indigo m-b-xs">ADD</button>
                                            </div>

                                        </div>
                                       
                                    </form>
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
        
    </body>
</html>
<?php } ?> 