<?php

try {
    /** @var $conn \PDO */
    require_once "../../dbconn.php";

    // echo "DB connected successfully";
    } 
    catch(PDOException $e) {
        echo "Connection failed: ".$e->getMessage();
    }

  date_default_timezone_set("Asia/Manila");

  $errors = [];

  $picture = '';
  $firstname = '';
  $middlename = '';
  $lastname = '';
  $address = '';
  $contactno = '';
  $birthday = '';
  $businessname = '';
  $occupation = '';
  $comaker = '';
  $remarks = '';
  $datecreated = '';

  $borrower = [
        'picture' => '',
        'firstname' => '',
        'middlename' => '',
        'lastname' => '',
        'address' => '',
        'contactno' => '',
        'birthday' => '',
        'businessname' => '',
        'occupation' => '',
        'comaker' => '',
        'remarks' => '',
        'datecreated' => ''
  ];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {  

    require_once "../../validate_borrower.php";

    if (empty($errors)) {

        $statement = $conn->prepare("INSERT INTO jai_db.borrowers (picture, firstname, middlename, lastname, address, contactno, birthday, businessname,
                                                                    occupation, comaker, remarks, datecreated)
                                                           VALUES (:picture, :firstname, :middlename, :lastname, :address, :contactno, :birthday, :businessname,
                                                                    :occupation, :comaker, :remarks, :datecreated)");

        $statement->bindValue(':picture', $picturePath);    
        $statement->bindValue(':firstname', $firstname);
        $statement->bindValue(':middlename', $middlename);
        $statement->bindValue(':lastname', $lastname);
        $statement->bindValue(':address', $address);
        $statement->bindValue(':contactno', $contactno);
        $statement->bindValue(':birthday', $birthday);
        $statement->bindValue(':businessname', $businessname);
        $statement->bindValue(':occupation', $occupation);
        $statement->bindValue(':comaker', $comaker);
        $statement->bindValue(':remarks', $remarks);
        $statement->bindValue(':datecreated', date('Y-m-d H:i:s'));

        $statement->execute();

        header('Location: index.php');

    }

  }


?>

<?php include_once "../../views/partials/header.php"; ?>

    <p>
        <a href="index.php" class="btn btn-secondary">Go back</a>
    </p>
    <h1>Create new borrower</h1>

    <?php include_once "../../views/borrowers/form.php" ?>

  </body>
</html>