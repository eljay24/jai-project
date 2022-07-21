<?php

include "../../dbconn.php";

if (isset($_POST['b_id'])) {
    $b_id = $_POST['b_id'];
    $values = $_POST['form_values'];
}

$id = $b_id;

$statement = $conn->prepare("SELECT * FROM jai_db.borrowers WHERE b_id = :b_id");
$statement->bindValue(':b_id', $id);
$statement->execute();
$borrower = $statement->fetch(PDO::FETCH_ASSOC);

date_default_timezone_set("Asia/Manila");

$errors = [];

$firstname = $values[0]['value'];
$middlename = $values[1]['value'];
$lastname = $values[2]['value'];
$birthday = $values[3]['value'];
$contactno = $values[4]['value'];
$address = $values[5]['value'];
$occupation = $values[6]['value'];
$businessname = $values[7]['value'];
$comaker = $values[8]['value'];
$comakerno = $values[9]['value'];
$remarks = $values[10]['value'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($errors)) {

        $statement = $conn->prepare("UPDATE jai_db.borrowers SET firstname=:firstname, middlename=:middlename, lastname=:lastname, address=:address,
        contactno=:contactno, birthday=:birthday, businessname=:businessname, occupation=:occupation,
        comaker=:comaker, comakerno=:comakerno, remarks=:remarks WHERE b_id=:b_id");

        $statement->bindValue(':b_id', $id);
        $statement->bindValue(':firstname', $firstname);
        $statement->bindValue(':middlename', $middlename);
        $statement->bindValue(':lastname', $lastname);
        $statement->bindValue(':birthday', $birthday);
        $statement->bindValue(':contactno', $contactno);
        $statement->bindValue(':address', $address);
        $statement->bindValue(':occupation', $occupation);
        $statement->bindValue(':businessname', $businessname);
        $statement->bindValue(':comaker', $comaker);
        $statement->bindValue(':comakerno', $comakerno);
        $statement->bindValue(':remarks', $remarks);

        $statement->execute();

        echo json_encode($values);
    }
}
