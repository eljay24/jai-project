<?php

try {
  /** @var $conn \PDO */
  require_once "../../views/includes/dbconn.php";

  // echo "DB connected successfully";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

$statement = $conn->prepare("SELECT DISTINCT b.b_id, b.firstname, b.middlename, b.lastname
                                FROM jai_db.borrowers as b
                                LEFT JOIN jai_db.loans as l
                                ON b.b_id = l.b_id 
                                WHERE b.isdeleted = 0 AND b.activeloan = 0
                                ORDER BY b.b_id ASC");
$statement->execute();
$loans = $statement->fetchAll(PDO::FETCH_ASSOC);

date_default_timezone_set("Asia/Manila");

$errors = [];

$b_id = '';
$amount = '';
$payable = '';
$balance = '';
$mode = '';
$term = '';
$interestrate = '';
$amortization = '';
$releasedate = '';
$duedate = '';
$status = '';

$loan = [
  'b_id' => '',
  'amount' => '',
  'payable' => '',
  'balance' => '',
  'mode' => '',
  'term' => '',
  'interestrate' => '',
  'amortization' => '',
  'releasedate' => '',
  'duedate' => '',
  'status' => '',
];

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {

//   require_once "../../validate_loan.php";

//   if (empty($errors)) {

//     $statement = $conn->prepare("INSERT INTO jai_db.borrowers (picture, firstname, middlename, lastname, address, contactno, birthday, businessname,
//                                                                     occupation, comaker, remarks, datecreated)
//                                                            VALUES (:picture, :firstname, :middlename, :lastname, :address, :contactno, :birthday, :businessname,
//                                                                     :occupation, :comaker, :remarks, :datecreated)");

//     $statement->bindValue(':picture', $picturePath);
//     $statement->bindValue(':firstname', $firstname);
//     $statement->bindValue(':middlename', $middlename);
//     $statement->bindValue(':lastname', $lastname);
//     $statement->bindValue(':address', $address);
//     $statement->bindValue(':contactno', $contactno);
//     $statement->bindValue(':birthday', $birthday);
//     $statement->bindValue(':businessname', $businessname);
//     $statement->bindValue(':occupation', $occupation);
//     $statement->bindValue(':comaker', $comaker);
//     $statement->bindValue(':remarks', $remarks);
//     $statement->bindValue(':datecreated', date('Y-m-d H:i:s'));

//     $statement->execute();

//     header('Location: index.php');
//   }
// }


?>

<?php include_once "../../views/partials/header.php"; ?>

<div class="content-container">
  <p>
    <a href="index.php" class="btn btn-secondary">Go back</a>
  </p>
  <h1>Add new loan</h1>

  <?php

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rAmount = floatval($_POST['amount']);
    $rMode = $_POST['mode'];
    $rTerm = $_POST['term'];

    $statement = $conn->prepare("SELECT *
                                  FROM jai_db.rates as r
                                  WHERE (r.amount = '$rAmount') AND (r.mode = '$rMode') AND (r.term = '$rTerm')                                
                                ");
    $statement->execute();
    $rates = $statement->fetchAll(PDO::FETCH_ASSOC);


    // echo $_POST['borrower'] . "<br>";
    // echo $_POST['amount'] . "<br>";
    // echo $_POST['mode'] . "<br>";
    // echo $_POST['term'] . "<br>";


    // echo "<pre>";
    // var_dump($rates);
    // echo "</pre>";


    foreach ($rates as $i => $rate) {

      // echo "TEST <br>";
      // echo $_POST['borrower'] . '<br>';
      // echo 'Amount: ' . number_format($rate['amount'], 2) . '<br>';
      // echo 'Payable: ' . number_format($rate['payable'], 2) . '<br>';
      // echo 'Amortization: ' . number_format($rate['amortization']) . '<br>';
      // echo 'Interest Rate: ' . number_format($rate['interestrate'], 2) . '<br>';
      // echo ucwords(strtolower($rate['mode'])) . ', ' . ucwords(strtolower($rate['term'])) . '<br>';

    }

    $statement2 = $conn->prepare("INSERT INTO jai_db.loans (b_id, amount, payable, balance, mode, term,
                                                            interestrate, amortization, releasedate, duedate, status, c_id, activeloan)
                                                    VALUES (:b_id, :amount, :payable, :balance, :mode, :term,
                                                            :interestrate, :amortization, :releasedate, :duedate, :status, :c_id, 1)");

    $statement2->bindValue(':b_id', $_POST['borrower']);
    $statement2->bindValue(':c_id', $_POST['collector']);
    $statement2->bindValue(':amount', $rate['amount']);
    $statement2->bindValue(':payable', $rate['payable']);
    $statement2->bindValue(':balance', $rate['payable']);
    $statement2->bindValue(':mode', $rate['mode']);
    $statement2->bindValue(':term', $rate['term']);
    $statement2->bindValue(':interestrate', $rate['interestrate']);
    $statement2->bindValue(':amortization', $rate['amortization']);
    $statement2->bindValue(':releasedate', 'TEST');
    $statement2->bindValue(':duedate', 'TEST');
    $statement2->bindValue(':status', 'Active');

    $statement2->execute();

    $statementUpdateBorrower = $conn->prepare("UPDATE jai_db.borrowers
                                               SET activeloan = 1
                                               WHERE :b_id = b_id");

    $statementUpdateBorrower->bindValue(':b_id', $_POST['borrower']);
    $statementUpdateBorrower->execute();

    header('Location: ../loans/index.php');


  }

  ?>

  <br>

  <!-- <script>
    var existingNames = ["lee", "jordan", "angelo", "ivan", "willie", "ann"];

    $("#namesearch").autocomplete({
      source: existingNames
    }, {
      
    });
  </script> -->


  <?php include_once "../../views/loans/form.php" ?>

  </body>

  </html>