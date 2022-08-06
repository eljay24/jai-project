<?php

require "../../views/includes/dbconn.php";

if (isset($_POST['b_id'])) {
  // $b_id = $_POST['b_id'];

  // $id = $b_id;

  // $statement = $conn->prepare("SELECT * FROM jai_db.borrowers WHERE b_id = :b_id");
  // $statement->bindValue(':b_id', $id);
  // $statement->execute();
  // $borrower = $statement->fetch(PDO::FETCH_ASSOC);

  date_default_timezone_set("Asia/Manila");


  $errors = [];

  $b_id = $_POST['b_id'];
  $data_row = $_POST['data-row'];
  $borrower = $_POST['borrower'];
  $amount = $_POST['amount'];
  $mode = $_POST['mode'];
  $term = $_POST['term'];
  $collector = $_POST['collector'];
  // $release_date = $_POST['release-date'];
  // $comaker = $_POST['comaker'];
  // $comakerno = $_POST['comakerno'];
  // $remarks = $_POST['remarks'];
  // $datecreated = '';


  // $details = [
  //   'firstname' => $firstname,
  //   'middlename' => $middlename,
  //   'lastname' => $lastname,
  //   'birthday' => $birthday,
  //   'contactno' => $contactno,
  //   'address' => $address,
  //   'occupation' => $occupation,
  //   'businessname'  => $businessname,
  //   'comaker' => $comaker,
  //   'comakerno' => $comakerno,
  //   'remarks' => $remarks,
  //   'datecreated' => ''

  // ];



  // if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  echo json_encode('working');

  if (empty($errors)) {

    $statement = $conn->prepare("INSERT INTO jai_db.loans (b_id, amount, mode, term,
    releasedate, c_id)
VALUES (:b_id, :amount, :mode, :term,
    :releasedate, :c_id)");

    $statement2->bindValue(':b_id', $b_id);
    $statement2->bindValue(':c_id', $collector);
    $statement2->bindValue(':amount', $amount);
    // $statement2->bindValue(':payable', $rate['payable']);
    // $statement2->bindValue(':balance', $rate['payable']);
    $statement2->bindValue(':mode',  $mode);
    $statement2->bindValue(':term', $term);

    $statement->execute();

    //     echo json_encode($details);
    //   } else {
    //   }
  }
}
