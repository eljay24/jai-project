<?php

require "../../views/includes/dbconn.php";

if (isset($_POST['borrower-name'])) {



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

  $statementPayment->bindValue(':b_id', $_POST['borrower-id']);
  $statementPayment->bindValue(':l_id', $_POST['loanid']);
  $statementPayment->bindValue(':c_id', $_POST['collector-id']);
  $statementPayment->bindValue(':amount', $_POST['payment']);
  $statementPayment->bindValue(':passamount', $passAmount);
  $statementPayment->bindValue(':type', $_POST['type']);
  $statementPayment->bindValue(':date', $_POST['date']);

  $statementPayment->execute();
  // END - ADD PAYMENT TO PAYMENTS TABLE



  // UPDATE BALANCE & ARREARS? ON LOANS TABLE
  $statementUpdateLoan = $conn->prepare("UPDATE jai_db.loans
                                          SET balance = balance - :paidamount, paymentsmade = paymentsmade + :paymentsmade, passes = passes + :passes
                                          WHERE b_id = :b_id AND l_id = :l_id");

  $statementUpdateLoan->bindValue(':b_id', $_POST['borrower']);
  $statementUpdateLoan->bindValue(':l_id', $_POST['loanid']);
  $statementUpdateLoan->bindValue(':paidamount', $_POST['payment']);
  $statementUpdateLoan->bindValue(':paymentsmade', $paymentsMade);
  $statementUpdateLoan->bindValue(':passes', $pass);

  $statementUpdateLoan->execute();
  // END - UPDATE BALANCE & ARREARS? ON LOANS TABLE

  // CHECK IF LOAN CLOSED
  $statementCheckClosed = $conn->prepare("UPDATE jai_db.loans as l
                                            INNER JOIN jai_db.borrowers as b
                                            ON l.b_id = b.b_id
                                            SET l.status = 'Closed', l.activeloan= 0, b.activeloan = 0
                                            WHERE (l.b_id = :b_id AND l.l_id = :l_id AND balance <= 0)
  ");

  $statementCheckClosed->bindValue(':b_id', $_POST['borrower']);
  $statementCheckClosed->bindValue(':l_id', $_POST['loanid']);
  $statementCheckClosed->execute();
  // END - CHECK IF LOAN CLOSED

  // UPDATE BORROWER ACTIVE LOAN

  // END - UPDATE BORROWER ACTIVE LOAN

  echo json_encode('working');


  // header('Location: ../payments/index.php');
}
