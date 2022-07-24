<?php

require "../../dbconn.php";

if (isset($_POST['b_id'])) {
    $b_id = $_POST['b_id'];

    $id = $b_id;

    $statement = $conn->prepare("SELECT * FROM jai_db.borrowers WHERE b_id = :b_id");
    $statement->bindValue(':b_id', $id);
    $statement->execute();
    $borrower = $statement->fetch(PDO::FETCH_ASSOC);

    $errors = [];

    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $birthday = $_POST['birthday'];
    $contactno = $_POST['contactno'];
    $address = $_POST['address'];
    $occupation = $_POST['occupation'];
    $businessname = $_POST['businessname'];
    $comaker = $_POST['comaker'];
    $comakerno = $_POST['comakerno'];
    $remarks = $_POST['remarks'];


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

            echo json_encode($_POST['b_id']);
        } else {
        }
    }
}
