<?php
require_once "../../views/includes/dbconn.php";
require_once "../../views/partials/header.php";

?>

<body>
  <div class="content-container">
    <?= "Hello" ?>
    <div class="chart-div">
      <canvas id="testChart"></canvas>
    </div>

    <?php
    //////////////////////////////// CHART DUMMY VALUES
    $statementTest = $conn->prepare("SELECT CONCAT(b.firstname, ' ', b.lastname) as bname, p.l_id, p.l_id, sum(p.amount) as amount
                                     FROM jai_db.payments as p
                                     INNER JOIN jai_db.borrowers as b
                                     ON b.b_id = p.b_id
                                     GROUP BY l_id");
    $statementTest->execute();
    $sumOfAmountsTest = $statementTest->fetchAll(PDO::FETCH_ASSOC);

    //////////////////////////////// COUNT ACTIVE LOANS
    $statActiveLoans = $conn->prepare("SELECT COUNT(*) as count
                                       FROM jai_db.loans as l
                                       WHERE activeloan = 1
    ");
    $statActiveLoans->execute();
    $activeLoans = $statActiveLoans->fetch(PDO::FETCH_ASSOC);

    //////////////////////////////// TOTAL RELEASED & PAYABLES THIS MONTH
    $firstOfThisMonth = date('Y-m-01'); // hard-coded '01' for first day
    $lastOfThisMonth  = date('Y-m-t');

    $statementLoans = $conn->prepare("SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
                                        l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id, l.paymentsmade, l.passes
                                  FROM jai_db.borrowers as b
                                  INNER JOIN jai_db.loans as l
                                  ON b.b_id = l.b_id
                                  WHERE b.isdeleted = 0 AND l.activeloan = 1 AND (l.releasedate BETWEEN :firstday AND :lastday)
                                  ");
    $statementLoans->bindValue(':firstday', $firstOfThisMonth);
    $statementLoans->bindValue(':lastday', $lastOfThisMonth);
    $statementLoans->execute();
    $loans = $statementLoans->fetchAll(PDO::FETCH_ASSOC);

    $totalReleasedThisMonth = (float)0;
    $totalPayablesThisMonth = (float)0;

    foreach ($loans as $i => $loan) {
      $totalReleasedThisMonth += $loan['amount'];
      $totalPayablesThisMonth += $loan['payable'];
    }

    //////////////////////////////// TOTAL PAYMENTS FOR LOANS THIS MONTH
    $statementPayments = $conn->prepare("SELECT p.amount as amount, p.date as date, l.releasedate
                                         FROM jai_db.payments as p
                                         INNER JOIN jai_db.loans as l
                                         ON p.l_id = l.l_id
                                         WHERE l.releasedate BETWEEN :firstOfThisMonth AND :lastOfThisMonth
                                         ORDER BY date ASC");
    $statementPayments->bindValue(':firstOfThisMonth', $firstOfThisMonth);
    $statementPayments->bindValue(':lastOfThisMonth', $lastOfThisMonth);
    $statementPayments->execute();
    $payments = $statementPayments->fetchAll(PDO::FETCH_ASSOC);

    $totalPaymentsThisMonth = (float)0;
    foreach ($payments as $i => $payment) {
      $totalPaymentsThisMonth += $payment['amount'];
    }

    // echo "<pre>";
    // var_dump($activeLoans);
    // exit;
    echo 'Active Loans: ' . $activeLoans['count'];
    echo "<br>";
    echo "<br>";
    $thisMonth = date('F Y');
    echo "THIS MONTH ($thisMonth)";
    echo "<br>";
    echo 'Total released: ' . number_format($totalReleasedThisMonth, 2);
    echo "<br>";
    echo 'Total collectibles: ' . number_format($totalPayablesThisMonth, 2);
    echo "<br>";
    echo 'Total payments for loans this month: ' . number_format($totalPaymentsThisMonth, 2);
    echo "<br>";
    echo 'Remaining Collectibles: ' . number_format($totalPayablesThisMonth - $totalPaymentsThisMonth, 2);

    echo "<br>";
    echo "<br>";
    


    exit;

    ?>

    <script>
      const borrower = <?php foreach ($sumOfAmountsTest as $i => $l_id) {
                          $borrower[] = $l_id['bname'] . " Loan#" . $l_id['l_id'];
                        }
                        echo json_encode($borrower);
                        ?>;

      const amount = <?php foreach ($sumOfAmountsTest as $i => $l_id) {
                        $amount[] = $l_id['amount'];
                      }
                      echo json_encode($amount);
                      ?>;

      console.log(borrower);

      // SETUP BLOCK
      const data = {
        labels: borrower,
        datasets: [{
          label: 'Total Payments',
          data: amount,
          backgroundColor: [
            'rgba(255, 99, 132, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(255, 205, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(201, 203, 207, 0.8)'
          ],
          borderColor: [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
          ],
          borderWidth: 2
        }]
      };

      // CONFIG BLOCK
      const config = {
        type: 'bar',
        data: data,
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      };

      // RENDER BLOCK
      const myChart = new Chart(
        document.getElementById('testChart'),
        config
      );
    </script>

    <?php

    ?>

  </div>