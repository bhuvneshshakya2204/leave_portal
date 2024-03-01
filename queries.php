<?php
// echo '1';
session_start();
error_reporting(1);
include('includes/config.php');

$sql = "ALTER TABLE `tblmisspunch` ADD `misspunch_time` VARCHAR(255) NULL AFTER `misspunch_date`"; 
$query = $dbh -> prepare($sql);
$run_query = $query->execute();

if($run_query){
    echo 'sucess';
}else{
    echo 'fail';
}