<?php

require "../../views/includes/dbconn.php";

if (isset($_POST['firstname'])) {
  // $b_id = $_POST['b_id'];

  // $id = $b_id;

  // $statement = $conn->prepare("SELECT * FROM jai_db.borrowers WHERE b_id = :b_id");
  // $statement->bindValue(':b_id', $id);
  // $statement->execute();
  // $borrower = $statement->fetch(PDO::FETCH_ASSOC);

  date_default_timezone_set("Asia/Manila");


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
  $datecreated = '';


  $details = [
    'firstname' => $firstname,
    'middlename' => $middlename,
    'lastname' => $lastname,
    'birthday' => $birthday,
    'contactno' => $contactno,
    'address' => $address,
    'occupation' => $occupation,
    'businessname'  => $businessname,
    'comaker' => $comaker,
    'comakerno' => $comakerno,
    'remarks' => $remarks,
    'datecreated' => ''

  ];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($errors)) {

      $statement = $conn->prepare("INSERT INTO jai_db.borrowers (picture, firstname, middlename, lastname, address, contactno, birthday, businessname,
            occupation, comaker, comakerno, remarks, datecreated)
   VALUES (:picture, :firstname, :middlename, :lastname, :address, :contactno, :birthday, :businessname,
            :occupation, :comaker, :comakerno, :remarks, :datecreated)");

      $statement->bindValue(':picture', 'assets/icons/borrower-picture-placeholder.jpg');
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
      $statement->bindValue(':datecreated', date('Y-m-d H:i:s'));


      $statement->execute();

      echo json_encode($details);
    } else {
    }
  }
}
