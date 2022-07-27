<?php

try {
  /** @var $conn \PDO */
  require_once "../../dbconn.php";

  // echo "DB connected successfully";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

$statementBorrowers = $conn->prepare("SELECT b.b_id, b.firstname, b.middlename, b.lastname
                                FROM jai_db.borrowers as b
                                LEFT JOIN jai_db.loans as l
                                ON b.b_id = l.b_id 
                                WHERE (b.isdeleted = 0 AND l.status = 'Active' AND l.amount IS NOT NULL)
                                ORDER BY b.b_id ASC");
$statementBorrowers->execute();
$borrowers = $statementBorrowers->fetchAll(PDO::FETCH_ASSOC);

$statementCollectors = $conn->prepare("SELECT *
                                       FROM jai_db.collectors
                                       ORDER BY c_id ASC");
$statementCollectors->execute();
$collectors = $statementCollectors->fetchAll(PDO::FETCH_ASSOC);

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

?>

<?php include_once "../../views/partials/header.php"; ?>

<div class="content-container">
  <p>
    <a href="index.php" class="btn btn-secondary">Go back</a>
  </p>
  <h1>Add new payment</h1>

  <?php

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    // ----------------------------------- TEST -------------------------------------------------

    // echo "<pre>";
    // var_dump($_POST);
    // echo "</pre>";
    // exit;

    // echo "TEST <br>";
    // echo $_POST['borrower'] . '<br>';
    // echo 'Amount: ' . $_POST['loanamount'] . '<br>';
    // echo 'Payable: ' . number_format($_POST['payable'], 2) . '<br>';
    // echo 'Balance: ' . number_format($_POST['remainingbalance'], 2) . '<br>';
    // echo 'Amortization: ' . number_format($_POST['amortization']) . '<br>';
    // echo 'Payment: ' . number_format($_POST['payment'], 2) . '<br>';
    // echo 'Type of Payment: ' . $_POST['type'] . '<br>';
    // echo 'Loan ID: ' . $_POST['loanid'] . '<br>';
    // echo 'Collector ID: ' . $_POST['collectorid'] . '<br>';
    // echo ucwords(strtolower($_POST['mode'])) . ', ' . ucwords(strtolower($_POST['term'])) . '<br>';

    // ----------------------------------- END TEST ---------------------------------------------

    // CHECK IF PASS
    if ($_POST['type'] == 'Pass') {
      $pass = 1;
      $paymentsMade = 0;
      $passAmount = $_POST['amortization'];
    } else {
      $pass = 0;
      $paymentsMade = 1;
      $passAmount = 0;
    }
    // END - CHECK IF PASS

    // ADD PAYMENT TO PAYMENTS TABLE
    $statementPayment = $conn->prepare("INSERT INTO jai_db.payments
                                  (b_id, l_id, c_id, amount, passamount, type, date)
                                  VALUES
                                  (:b_id, :l_id, :c_id, :amount, :passamount, :type, :date)");

    $statementPayment->bindValue(':b_id', $_POST['borrower']);
    $statementPayment->bindValue(':l_id', $_POST['loanid']);
    $statementPayment->bindValue(':c_id', $_POST['collectorid']);
    $statementPayment->bindValue(':amount', $_POST['payment']);
    $statementPayment->bindValue(':passamount', $passAmount);
    $statementPayment->bindValue(':type', $_POST['type']);
    $statementPayment->bindValue(':date', $_POST['date']);

    $statementPayment->execute();
    // END - ADD PAYMENT TO PAYMENTS TABLE



    // UPDATE BALANCE ON LOANS TABLE
    $statementUpdateLoan = $conn->prepare("UPDATE jai_db.loans
                                            SET balance = balance - :paidamount, paymentsmade = paymentsmade + :paymentsmade, passes = passes + :passes
                                            WHERE b_id = :b_id AND l_id = :l_id");

    $statementUpdateLoan->bindValue(':b_id', $_POST['borrower']);
    $statementUpdateLoan->bindValue(':l_id', $_POST['loanid']);
    $statementUpdateLoan->bindValue(':paidamount', $_POST['payment']);
    $statementUpdateLoan->bindValue(':paymentsmade', $paymentsMade);
    $statementUpdateLoan->bindValue(':passes', $pass);

    $statementUpdateLoan->execute();
    // END - UPDATE BALANCE ON LOANS TABLE

    // CHECK IF LOAN FINISHED
    $statementCheckFinished = $conn->prepare("UPDATE jai_db.loans as l
                                              INNER JOIN jai_db.borrowers as b
                                              ON l.b_id = b.b_id
                                              SET l.status = 'Finished', l.activeloan= 0, b.activeloan = 0
                                              WHERE (l.b_id = :b_id AND l.l_id = :l_id AND balance <= 0)
    ");

    $statementCheckFinished->bindValue(':b_id', $_POST['borrower']);
    $statementCheckFinished->bindValue(':l_id', $_POST['loanid']);
    $statementCheckFinished->execute();
    // END - CHECK IF LOAN FINISHED

    // UPDATE BORROWER ACTIVE LOAN

    // END - UPDATE BORROWER ACTIVE LOAN




    header('Location: ../payments/index.php');
  }

  ?>

  <br>

  <?php include_once "../../views/payments/form.php" ?>

  </body>

  </html>