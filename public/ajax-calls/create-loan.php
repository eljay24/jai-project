<?php

require "../../views/includes/dbconn.php";

if (isset($_POST['b_id'])) {
  // $b_id = $_POST['b_id'];

  // $id = $b_id;

  // $statement = $conn->prepare("SELECT * FROM jai_db.borrowers WHERE b_id = :b_id");
  // $statement->bindValue(':b_id', $id);
  // $statement->execute();
  // $borrower = $statement->fetch(PDO::FETCH_ASSOC);

  $errors = [];

  $rAmount = floatval($_POST['amount']);
  $rMode = $_POST['mode'];
  $rTerm = $_POST['term'];

  $statement = $conn->prepare("SELECT *
                                  FROM jai_db.rates as r
                                  WHERE (r.amount = '$rAmount') AND (r.mode = '$rMode') AND (r.term = '$rTerm')                                
                                ");
  $statement->execute();
  $rates = $statement->fetch(PDO::FETCH_ASSOC);

  // echo "<pre>";
  // var_dump($rates);
  // exit;


  // $b_id = $_POST['b_id'];
  // $data_row = $_POST['data-row'];
  $borrower = $_POST['borrower'];
  $amount = $_POST['amount'];
  $mode = $_POST['mode'];
  $term = $_POST['term'];
  $collector = $_POST['collector'];
  $release_date = $_POST['release-date'];


  // echo json_encode($b_id.$data_row.$borrower.$amount.$mode.$term.$collector.$release_date);
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



  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    echo json_encode('working');

    if (empty($errors)) {

      $statement2 = $conn->prepare("INSERT INTO jai_db.loans (b_id, amount, mode, term, payable, balance, interestrate, amortization, releasedate, duedate, status, c_id, activeloan)
                                  VALUES (:b_id, :amount, :mode, :term, :payable, :balance, :interestrate, :amortization, :releasedate, :duedate, :status, :c_id, :activeloan);
                                  UPDATE jai_db.borrowers
                                  SET activeloan = 1
                                  WHERE b_id = :b_id");

      $statement2->bindValue(':b_id', $borrower);
      $statement2->bindValue(':c_id', $collector);
      $statement2->bindValue(':amount', $amount);
      $statement2->bindValue(':payable', $rates['payable']);
      $statement2->bindValue(':balance', $rates['payable']);
      $statement2->bindValue(':interestrate', $rates['interestrate']);
      $statement2->bindValue(':amortization', $rates['amortization']);
      $statement2->bindValue(':mode',  $mode);
      $statement2->bindValue(':term', $term);
      $statement2->bindValue(':releasedate', $release_date);
      $statement2->bindValue(':duedate', 'TEST');
      $statement2->bindValue(':activeloan', 1);
      $statement2->bindValue(':status', 'Active');

      $statement2->execute();

      // $statement3 = $conn->prepare("");

      //     echo json_encode($details);
      //   } else {
      //   }
    }
  }
}
