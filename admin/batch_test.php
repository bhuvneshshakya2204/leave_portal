<?php
session_start();
error_reporting(1);
include('includes/config.php');
include('../includes/send_email.php');
if (strlen($_SESSION['hrlogin']) == 0) {
    header('location:../index.php');
} else {
    echo 'Batch Test';
}

echo '<br>';
echo 'TEST';

// $data=[
// ['John','Doe',22],
// ['Jane','Roe',19],
// ];
// $stmt=$pdo->prepare("INSERTINTOusers(name,surname,age)VALUES(?,?,?)");
// try{
// $pdo->beginTransaction();
// foreach($dataas$row)
// {
// $stmt->execute($row);
// }
// $pdo->commit();
// }catch(Exception$e){
// $pdo->rollback();
// throw$e;
// }
// ===============================

// <?php

// $host     = 'localhost';
// $db       = 'demos';
// $user     = 'root';
// $password = '';

// $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

// try {
//      $conn = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// } catch (PDOException $e) {
//      echo $e->getMessage();
// }

// $data = [
//      [
//           'title' => 'test title 1',
//           'content' => 'test content 1'
//      ],
//      [
//           'title' => 'test title 2',
//           'content' => 'test content 2'
//      ],
//      [
//           'title' => 'test title 3',
//           'content' => 'test content 3'
//      ]
// ];

// $sql = 'INSERT INTO posts(title, content) VALUES(:title, :content)';

// $statement = $conn->prepare($sql);

// foreach($data as $row) {
//     $statement->execute($row); 
// }

// echo "Posts saved successfully!";
