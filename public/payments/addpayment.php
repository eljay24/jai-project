<?php

try {
  /** @var $conn \PDO */
  require_once "../../dbconn.php";

  // echo "DB connected successfully";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

$statement = $conn->prepare("SELECT b.b_id, b.firstname, b.middlename, b.lastname
                                FROM jai_db.borrowers as b
                                LEFT JOIN jai_db.loans as l
                                ON b.b_id = l.b_id 
                                WHERE (b.isdeleted = 0) AND (l.amount IS NOT NULL)
                                ORDER BY b.b_id ASC");
$statement->execute();
$borrowers = $statement->fetchAll(PDO::FETCH_ASSOC);

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
  <h1>Add new payment</h1>

  <?php

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";

    echo "TEST <br>";
    echo $_POST['borrower'] . '<br>';
    echo 'Amount: ' . $_POST['loanamount'] . '<br>';
    echo 'Payable: ' . number_format($_POST['payable'], 2) . '<br>';
    echo 'Balance: ' . number_format($_POST['remainingbalance'], 2) . '<br>';
    echo 'Amortization: ' . number_format($_POST['amortization']) . '<br>';
    echo 'Payment: ' . number_format($_POST['payment'], 2) . '<br>';
    echo 'Type of Payment: ' . $_POST['type'] . '<br>';
    echo 'Loan ID: ' . $_POST['loanid'] . '<br>';
    echo 'Collector ID: ' . $_POST['collectorid'] . '<br>';
    echo ucwords(strtolower($_POST['mode'])) . ', ' . ucwords(strtolower($_POST['term'])) . '<br>';


    $statementPayment = $conn->prepare("INSERT INTO jai_db.payments
                                  (b_id, l_id, c_id, amount, type, date)
                                  VALUES
                                  (:b_id, :l_id, :c_id, :amount, :type, :date)
    ");

    $paymentAmount = $_POST['payment'];

    $statementPayment->bindValue(':b_id', $_POST['borrower']);
    $statementPayment->bindValue(':l_id', $_POST['loanid']);
    $statementPayment->bindValue(':c_id', $_POST['collectorid']);
    $statementPayment->bindValue(':amount', $_POST['payment']);
    $statementPayment->bindValue(':type', $_POST['type']);
    $statementPayment->bindValue(':date', "TEST");

    $statementPayment->execute();

    $statementUpdateLoan = $conn->prepare("UPDATE jai_db.loans
                                           SET balance = balance - :paidamount
                                           WHERE b_id = :b_id AND l_id = :l_id
    ");

    $statementUpdateLoan->bindValue(':b_id', $_POST['borrower']);
    $statementUpdateLoan->bindValue(':l_id', $_POST['loanid']);
    $statementUpdateLoan->bindValue(':paidamount', $_POST['payment']);

    $statementUpdateLoan->execute();
    
    header('Location: ../borrowers/index.php');


    // $statement2 = $conn->prepare("INSERT INTO jai_db.loans (b_id, amount, payable, balance, mode, term,
    //                                                         interestrate, amortization, releasedate, duedate, status)
    //                                                 VALUES (:b_id, :amount, :payable, :balance, :mode, :term,
    //                                                         :interestrate, :amortization, :releasedate, :duedate, :status)");

    // $statement2->bindValue(':b_id', $_POST['borrower']);
    // $statement2->bindValue(':amount', $rate['amount']);
    // $statement2->bindValue(':payable', $rate['payable']);
    // $statement2->bindValue(':balance', $rate['payable']);
    // $statement2->bindValue(':mode', $rate['mode']);
    // $statement2->bindValue(':term', $rate['term']);
    // $statement2->bindValue(':interestrate', $rate['interestrate']);
    // $statement2->bindValue(':amortization', $rate['amortization']);
    // $statement2->bindValue(':releasedate', 'TEST');
    // $statement2->bindValue(':duedate', 'TEST');
    // $statement2->bindValue(':status', 'Active');

    // $statement2->execute();


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


  <?php include_once "../../views/payments/form.php" ?>

  </body>

  </html>