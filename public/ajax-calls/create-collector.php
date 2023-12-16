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
  $datecreated = '';

  $fullname = $firstname . '-' . $middlename . '-' . $lastname;


  $details = [
    'firstname' => $firstname,
    'middlename' => $middlename,
    'lastname' => $lastname,
    'birthday' => $birthday,
    'contactno' => $contactno,
    'address' => $address,
    'datecreated' => ''

  ];

  if (!is_dir(__DIR__ . '/../borrower-pictures')) {
    mkdir(__DIR__ . '/../borrower-pictures');
  }

  // START PICTURE

  $picture = $_FILES['picture'] ?? null;
  // $picturePath = $borrower['picture'];

  if ($picture && $picture['name']) {

    // if ($borrower['picture']) {
    //   unlink(__DIR__ . '/public/' . $borrower['picture']);
    // }

    $picturePath = 'borrower-pictures/' . $fullname . '/' . $picture['name'];

    if (!is_dir($picturePath)) {
      mkdir(dirname(__DIR__ . '/../borrower-pictures/' . $picturePath));
    }
    move_uploaded_file($picture['tmp_name'], __DIR__ . '/../borrower-pictures/' . $picturePath);
  } elseif (!isset($picture['name'])) {

    // DEFAULT BORROWER PICTURE (IF NONE SELECTED)
    $picturePath = 'assets/icons/borrower-picture-placeholder.jpg';
  }

  // END - PICTURE

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($errors)) {

      $statement = $conn->prepare("INSERT INTO jai_db.collectors (picture, firstname, middlename, lastname, address, contactno, birthday, datecreated, is_deleted)
   VALUES (:picture, :firstname, :middlename, :lastname, :address, :contactno, :birthday, :datecreated, :is_deleted)");

      $statement->bindValue(':picture', $picturePath);
      $statement->bindValue(':firstname', $firstname);
      $statement->bindValue(':middlename', $middlename);
      $statement->bindValue(':lastname', $lastname);
      $statement->bindValue(':birthday', $birthday);
      $statement->bindValue(':contactno', $contactno);
      $statement->bindValue(':address', $address);
      $statement->bindValue(':datecreated', date('Y-m-d H:i:s'));
      $statement->bindValue(':is_deleted', 0);


      $statement->execute();

      echo json_encode($picture);
    } else {
    }
  }
}
