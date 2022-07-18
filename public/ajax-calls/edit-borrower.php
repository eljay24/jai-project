<?php

include "../../dbconn.php";

if (isset($_POST['bid'])) {
    $fname = $_POST['firstname'];
    $mname = $_POST['middlename'];
    $lname = $_POST['lastname'];
    $b_id = $_POST['bid'];
}

$id = $b_id;

$statement = $conn->prepare("SELECT * FROM jai_db.borrowers WHERE b_id = :b_id");
$statement->bindValue(':b_id', $id);
$statement->execute();
$borrower = $statement->fetch(PDO::FETCH_ASSOC);

date_default_timezone_set("Asia/Manila");

$errors = [];

$firstname = $fname;
$middlename = $mname;
$lastname = $lname;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($errors)) {

        $statement = $conn->prepare("UPDATE jai_db.borrowers SET firstname=:firstname, middlename=:middlename, lastname=:lastname WHERE b_id=:b_id");

        // UPDATE `borrowers` SET `firstname` = 'asdddd' WHERE `borrowers`.`b_id` = 14;
        $statement->bindValue(':b_id', $id);
        $statement->bindValue(':firstname', $firstname);
        $statement->bindValue(':middlename', $middlename);
        $statement->bindValue(':lastname', $lastname);

        $statement->execute();

        // header('Location: ../borrowers/index.php');
        echo $firstname . ' ' . $middlename . ' ' . $lastname;
    }
}
