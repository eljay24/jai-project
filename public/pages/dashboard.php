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
    //////////////////////////////// END - COUNT ACTIVE LOANS



    /*                                                                */
    /*                                                                */
    /*                         THIS MONTH QUERIES                     */
    /*                                                                */
    /*                                                                */

    $firstOfThisMonth = date('Y-m-01'); // hard-coded '01' for first day
    $lastOfThisMonth  = date('Y-m-t');

    /* ----- TOTAL RELEASED & PAYABLES THIS MONTH ----- */
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
    /* ----- END - TOTAL RELEASED & PAYABLES THIS MONTH ----- */

    /* ----- TOTAL PAYMENTS FOR LOANS THIS MONTH ----- */
    $statementPaymentsThisMonth = $conn->prepare("SELECT p.amount as amount, p.date as date, l.releasedate
                                         FROM jai_db.payments as p
                                         INNER JOIN jai_db.loans as l
                                         ON p.l_id = l.l_id
                                         WHERE l.releasedate BETWEEN :firstOfThisMonth AND :lastOfThisMonth
                                         ORDER BY date ASC");
    $statementPaymentsThisMonth->bindValue(':firstOfThisMonth', $firstOfThisMonth);
    $statementPaymentsThisMonth->bindValue(':lastOfThisMonth', $lastOfThisMonth);
    $statementPaymentsThisMonth->execute();
    $paymentsThisMonth = $statementPaymentsThisMonth->fetchAll(PDO::FETCH_ASSOC);

    $totalPaymentsThisMonth = (float)0;
    foreach ($paymentsThisMonth as $i => $paymentThisMonth) {
      $totalPaymentsThisMonth += $paymentThisMonth['amount'];
    }
    /* ----- END - TOTAL PAYMENTS FOR LOANS THIS MONTH ----- */

    /*                                                                */
    /*                                                                */
    /*                      END - THIS MONTH QUERIES                  */
    /*                                                                */
    /*                                                                */

    /*                                                                */
    /*                                                                */
    /*                        PREV MONTHS & MISC QUERIES              */
    /*                                                                */
    /*                                                                */

    /* ----- TOTAL PAYMENTS FOR LOANS FROM PREVIOUS MONTHS ----- */
    $statementPaymentsPrevMonths = $conn->prepare("SELECT p.amount as amount, p.date as date, l.releasedate
                                                   FROM jai_db.payments as p
                                                   INNER JOIN jai_db.loans as l
                                                   ON p.l_id = l.l_id
                                                   WHERE l.releasedate < :firstOfThisMonth
                                                   ORDER BY date ASC");
    $statementPaymentsPrevMonths->bindValue(':firstOfThisMonth', $firstOfThisMonth);
    $statementPaymentsPrevMonths->execute();
    $paymentsPrevMonths = $statementPaymentsPrevMonths->fetchAll(PDO::FETCH_ASSOC);

    $totalPaymentsPrevMonths = (float)0;
    foreach ($paymentsPrevMonths as $i => $paymentPrevMonths) {
      $totalPaymentsPrevMonths += $paymentPrevMonths['amount'];
    }
    /* ----- END - TOTAL PAYMENTS FOR LOANS FROM PREVIOUS MONTHS ----- */

    /* ----- TOTAL RELEASED & PAYABLES PREV MONTHS ----- */
    $statementLoansPrevMonths = $conn->prepare("SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
                                        l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id, l.paymentsmade, l.passes
                                  FROM jai_db.borrowers as b
                                  INNER JOIN jai_db.loans as l
                                  ON b.b_id = l.b_id
                                  WHERE b.isdeleted = 0 AND l.activeloan = 1 AND (l.releasedate < :firstday)
                                  ");
    $statementLoansPrevMonths->bindValue(':firstday', $firstOfThisMonth);
    $statementLoansPrevMonths->execute();
    $loansPrevMonths = $statementLoansPrevMonths->fetchAll(PDO::FETCH_ASSOC);

    $totalReleasedPrevMonths = (float)0;
    $totalPayablesPrevMonths = (float)0;

    foreach ($loansPrevMonths as $i => $loanPrevMonths) {
      $totalReleasedPrevMonths += $loanPrevMonths['amount'];
      $totalPayablesPrevMonths += $loanPrevMonths['payable'];
    }
    /* ----- END - TOTAL RELEASED & PAYABLES PREV MONTHS ----- */

    /* ----- CALCULATE PROFIT ----- */
    $statementProfits = $conn->prepare("SELECT *
                                        FROM jai_db.loans
                                        WHERE activeloan = 1");
    $statementProfits->execute();
    $profits = $statementProfits->fetchAll(PDO::FETCH_ASSOC);

    $totalDailyProfit = (float)0;
    $expectedTotalCollection = (float)0;
    foreach ($profits as $i => $profit) {
      $loanNumberOfDays = $profit['payable'] / $profit['amortization'];
      $dailyProfit = ($profit['payable'] - $profit['amount']) / $loanNumberOfDays;
      $totalDailyProfit += $dailyProfit;
      $expectedTotalCollection += $profit['amortization'];
    }
    /* ----- END - CALCULATE PROFIT ----- */

    /* ----- GET COLLECTOR DATA (FOR ACCOUNT LIST) ----- */
    $statementCollectors = $conn->prepare("SELECT c_id, CONCAT(c.firstname, ' ', c.lastname) as name
                                           FROM jai_db.collectors as c
                                          ");
    $statementCollectors->execute();
    $collectors = $statementCollectors->fetchAll(PDO::FETCH_ASSOC);
    /* ----- END - GET COLLECTOR DATA (FOR ACCOUNT LIST) ----- */


    /*                                                                */
    /*                                                                */
    /*                   END - PREV MONTHS & MISC QUERIES             */
    /*                                                                */
    /*                                                                */





    // echo "<pre>";
    // var_dump($activeLoans);
    // exit;
    ?>
    SELECT COLELCTOR
    <form method="get" action="accountslist.php" target="_blank">
      <select name="c_id">
        <?php
        foreach ($collectors as $i => $collector) {
          echo '<option value="' . $collector['c_id'] . '">' . $collector['name'] . '</option>';
        }
        ?>
      </select>
      <input title="View accounts list" type="submit" />
      <!-- <input title="View ledger" type="submit" name="loanID" class="btn btn-primary btn-sm ledger-btn" value="<?= $payment['l_id'] ?>" <?= ($payment['paymentsmade'] || $payment['passes']) == 0 ? 'disabled' : '' ?>></input> -->
    </form>

    <?php
    echo 'Active Loans: ' . $activeLoans['count'];
    echo "<br>";
    echo "<br>";
    echo "PREVIOUS MONTHS";
    echo "<br>";
    echo 'Total released: ' . number_format($totalReleasedPrevMonths, 2);
    echo "<br>";
    echo 'Total collectibles: ' . number_format($totalPayablesPrevMonths, 2);
    echo "<br>";
    echo 'Expected total profit: ' . number_format($totalPayablesPrevMonths - $totalReleasedPrevMonths, 2) . ' (' . number_format(((($totalPayablesPrevMonths - $totalReleasedPrevMonths) / $totalPayablesPrevMonths) * 100), 2) . '%)';
    echo "<br>";
    echo 'Total collection from previous months: ' . number_format($totalPaymentsPrevMonths, 2);
    echo "<br>";
    echo 'Remaining Collectibles: ' . number_format($totalPayablesPrevMonths - $totalPaymentsPrevMonths, 2);

    echo "<br>";
    echo "<br>";
    $thisMonth = date('F Y');
    echo "THIS MONTH ($thisMonth)";
    echo "<br>";
    echo 'Total released: ' . number_format($totalReleasedThisMonth, 2);
    echo "<br>";
    echo 'Total collectibles: ' . number_format($totalPayablesThisMonth, 2);
    echo "<br>";
    echo 'Total collection for loans this month: ' . number_format($totalPaymentsThisMonth, 2);
    echo "<br>";
    echo 'Remaining Collectibles: ' . number_format($totalPayablesThisMonth - $totalPaymentsThisMonth, 2);

    echo "<br>";
    echo "<br>";
    echo 'Expected profits today: ' . number_format($totalDailyProfit, 2);
    echo "<br>";
    echo 'Expected total collection today: ' . number_format($expectedTotalCollection, 2);
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