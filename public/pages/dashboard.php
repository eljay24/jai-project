<?php
require_once "../../views/includes/dbconn.php";
require_once "../../views/partials/header.php";

/*      DATES      */
$dateToday = date('Y-m-d');
$firstOfThisMonth = date('Y-m-01'); // hard-coded '01' for first day
$lastOfThisMonth  = date('Y-m-t'); // t = number of days in given month

$datestring = $dateToday . 'first day of last month';
$firstOfLastMonth = date_create($datestring);
$firstOfLastMonth = $firstOfLastMonth->format('Y-m-01'); //2011-02

$datestring = $dateToday . 'last day of last month';
$lastOfLastMonth = date_create($datestring);
$lastOfLastMonth = $lastOfLastMonth->format('Y-m-t');

$datestring = $dateToday . 'first day of last month';
$lastMonth = date_create($datestring);
$lastMonth = $lastMonth->format('F Y');
$thisMonth = date('F Y');


?>

<body>
  <div class="content-container">
    <?php
    /*                                                               */
    /*                                                               */
    /*                          CHART VALUES                         */
    /*                                                               */
    /*                                                               */

    /*                                                                   */
    /*                                                                   */
    /*       QUERIES TOTAL COLLECTION LAST MONTH PER COLLECTOR           */
    /*                                                                   */
    /*                                                                   */

    /*      -----     CASH KING      -----     */
    $queryTotalCashCollectionLastMonthKing = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'Cash' AND (date BETWEEN :firstoflastmonth AND :lastoflastmonth) AND c_id = 1");
    $queryTotalCashCollectionLastMonthKing->bindValue(':firstoflastmonth', $firstOfLastMonth);
    $queryTotalCashCollectionLastMonthKing->bindValue(':lastoflastmonth', $lastOfLastMonth);
    $queryTotalCashCollectionLastMonthKing->execute();
    $totalCashCollectionLastMonthKing = $queryTotalCashCollectionLastMonthKing->fetch(PDO::FETCH_ASSOC);

    /*      -----     CASH CARL      -----     */
    $queryTotalCashCollectionLastMonthCarl = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'Cash' AND (date BETWEEN :firstoflastmonth AND :lastoflastmonth) AND c_id = 2");
    $queryTotalCashCollectionLastMonthCarl->bindValue(':firstoflastmonth', $firstOfLastMonth);
    $queryTotalCashCollectionLastMonthCarl->bindValue(':lastoflastmonth', $lastOfLastMonth);
    $queryTotalCashCollectionLastMonthCarl->execute();
    $totalCashCollectionLastMonthCarl = $queryTotalCashCollectionLastMonthCarl->fetch(PDO::FETCH_ASSOC);

    /*      -----     GCASH KING      -----     */
    $queryTotalGCashCollectionLastMonthKing = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'GCash' AND (date BETWEEN :firstoflastmonth AND :lastoflastmonth) AND c_id = 1");
    $queryTotalGCashCollectionLastMonthKing->bindValue(':firstoflastmonth', $firstOfLastMonth);
    $queryTotalGCashCollectionLastMonthKing->bindValue(':lastoflastmonth', $lastOfLastMonth);
    $queryTotalGCashCollectionLastMonthKing->execute();
    $totalGCashCollectionLastMonthKing = $queryTotalGCashCollectionLastMonthKing->fetch(PDO::FETCH_ASSOC);

    /*      -----     GCASH CARL      -----     */
    $queryTotalGCashCollectionLastMonthCarl = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'GCash' AND (date BETWEEN :firstoflastmonth AND :lastoflastmonth) AND c_id = 2");
    $queryTotalGCashCollectionLastMonthCarl->bindValue(':firstoflastmonth', $firstOfLastMonth);
    $queryTotalGCashCollectionLastMonthCarl->bindValue(':lastoflastmonth', $lastOfLastMonth);
    $queryTotalGCashCollectionLastMonthCarl->execute();
    $totalGCashCollectionLastMonthCarl = $queryTotalGCashCollectionLastMonthCarl->fetch(PDO::FETCH_ASSOC);

    $totalCollectionLastMonth = ($totalCashCollectionLastMonthKing['sum'] + $totalGCashCollectionLastMonthKing['sum']) + ($totalCashCollectionLastMonthCarl['sum'] + $totalGCashCollectionLastMonthCarl['sum']);

    /*                                                                   */
    /*                                                                   */
    /*       END - QUERIES TOTAL COLLECTION LAST MONTH PER COLLECTOR     */
    /*                                                                   */
    /*                                                                   */

    /*                                                                   */
    /*                                                                   */
    /*       QUERIES TOTAL COLLECTION THIS MONTH PER COLLECTOR           */
    /*                                                                   */
    /*                                                                   */

    /*      -----     CASH KING      -----     */
    $queryTotalCashCollectionThisMonthKing = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'Cash' AND (date BETWEEN :firstofthismonth AND :lastofthismonth) AND c_id = 1");
    $queryTotalCashCollectionThisMonthKing->bindValue(':firstofthismonth', $firstOfThisMonth);
    $queryTotalCashCollectionThisMonthKing->bindValue(':lastofthismonth', $lastOfThisMonth);
    $queryTotalCashCollectionThisMonthKing->execute();
    $totalCashCollectionThisMonthKing = $queryTotalCashCollectionThisMonthKing->fetch(PDO::FETCH_ASSOC);

    /*      -----     CASH CARL      -----     */
    $queryTotalCashCollectionThisMonthCarl = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'Cash' AND (date BETWEEN :firstofthismonth AND :lastofthismonth) AND c_id = 2");
    $queryTotalCashCollectionThisMonthCarl->bindValue(':firstofthismonth', $firstOfThisMonth);
    $queryTotalCashCollectionThisMonthCarl->bindValue(':lastofthismonth', $lastOfThisMonth);
    $queryTotalCashCollectionThisMonthCarl->execute();
    $totalCashCollectionThisMonthCarl = $queryTotalCashCollectionThisMonthCarl->fetch(PDO::FETCH_ASSOC);

    /*      -----     GCASH KING      -----     */
    $queryTotalGCashCollectionThisMonthKing = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'GCash' AND (date BETWEEN :firstofthismonth AND :lastofthismonth) AND c_id = 1");
    $queryTotalGCashCollectionThisMonthKing->bindValue(':firstofthismonth', $firstOfThisMonth);
    $queryTotalGCashCollectionThisMonthKing->bindValue(':lastofthismonth', $lastOfThisMonth);
    $queryTotalGCashCollectionThisMonthKing->execute();
    $totalGCashCollectionThisMonthKing = $queryTotalGCashCollectionThisMonthKing->fetch(PDO::FETCH_ASSOC);

    /*      -----     GCASH CARL      -----     */
    $queryTotalGCashCollectionThisMonthCarl = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'GCash' AND (date BETWEEN :firstofthismonth AND :lastofthismonth) AND c_id = 2");
    $queryTotalGCashCollectionThisMonthCarl->bindValue(':firstofthismonth', $firstOfThisMonth);
    $queryTotalGCashCollectionThisMonthCarl->bindValue(':lastofthismonth', $lastOfThisMonth);
    $queryTotalGCashCollectionThisMonthCarl->execute();
    $totalGCashCollectionThisMonthCarl = $queryTotalGCashCollectionThisMonthCarl->fetch(PDO::FETCH_ASSOC);

    $totalCollectionThisMonth = ($totalCashCollectionThisMonthKing['sum'] + $totalGCashCollectionThisMonthKing['sum']) + ($totalCashCollectionThisMonthCarl['sum'] + $totalGCashCollectionThisMonthCarl['sum']);

    /*                                                                   */
    /*                                                                   */
    /*       END - QUERIES TOTAL COLLECTION THIS MONTH PER COLLECTOR     */
    /*                                                                   */
    /*                                                                   */

    /*                                                               */
    /*                                                               */
    /*       QUERIES TOTAL COLLECTION TODAY PER COLLECTOR            */
    /*                                                               */
    /*                                                               */

    /*      -----     CASH KING      -----     */
    $queryTotalCashCollectionTodayKing = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'Cash' AND date = :datetoday AND c_id = 1");
    $queryTotalCashCollectionTodayKing->bindValue(':datetoday', $dateToday);
    $queryTotalCashCollectionTodayKing->execute();
    $totalCashCollectionTodayKing = $queryTotalCashCollectionTodayKing->fetch(PDO::FETCH_ASSOC);

    /*      -----     CASH CARL      -----     */
    $queryTotalCashCollectionTodayCarl = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'Cash' AND date = :datetoday AND c_id = 2");
    $queryTotalCashCollectionTodayCarl->bindValue(':datetoday', $dateToday);
    $queryTotalCashCollectionTodayCarl->execute();
    $totalCashCollectionTodayCarl = $queryTotalCashCollectionTodayCarl->fetch(PDO::FETCH_ASSOC);

    /*      -----     GCASH KING      -----     */
    $queryTotalGCashCollectionTodayKing = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'GCash' AND date = :datetoday AND c_id = 1");
    $queryTotalGCashCollectionTodayKing->bindValue(':datetoday', $dateToday);
    $queryTotalGCashCollectionTodayKing->execute();
    $totalGCashCollectionTodayKing = $queryTotalGCashCollectionTodayKing->fetch(PDO::FETCH_ASSOC);

    /*      -----     GCASH CARL      -----     */
    $queryTotalGCashCollectionTodayCarl = $conn->prepare("SELECT SUM(amount) as sum
                                                  FROM jai_db.payments as p
                                                  WHERE type = 'GCash' AND date = :datetoday AND c_id = 2");
    $queryTotalGCashCollectionTodayCarl->bindValue(':datetoday', $dateToday);
    $queryTotalGCashCollectionTodayCarl->execute();
    $totalGCashCollectionTodayCarl = $queryTotalGCashCollectionTodayCarl->fetch(PDO::FETCH_ASSOC);

    /* TOTAL (ALL) */
    $totalCollectionToday = ($totalCashCollectionTodayKing['sum'] + $totalGCashCollectionTodayKing['sum']) + ($totalCashCollectionTodayCarl['sum'] + $totalGCashCollectionTodayCarl['sum']);

    /*                                                                   */
    /*                                                                   */
    /*      END - QUERIES TOTAL COLLECTION TODAY PER COLLECTOR           */
    /*                                                                   */
    /*                                                                   */

    /*                                    */
    /*                                    */
    /*           END - CHART VALUES       */
    /*                                    */
    /*                                    */
    ?>
    <div class="chart-div d-flex">
      <canvas id="chartTotalCollectionLastMonth"></canvas>
      <?= 'Total collection last month (' . $lastMonth . '): ₱' . number_format($totalCollectionLastMonth, 2) ?>

      <canvas id="chartTotalCollectionThisMonth"></canvas>
      <?= 'Total collection this month (' . $thisMonth . '): ₱' . number_format($totalCollectionThisMonth, 2) ?>

      <canvas id="chartTotalCollectionToday"></canvas>
      <?= 'Total collection today (' . date('F d, Y') . '): ₱' . number_format($totalCollectionToday, 2) ?>
    </div>

    <?php
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

    /* ----- TOTAL RELEASED & PAYABLES THIS MONTH ----- */
    $statementLoans = $conn->prepare("SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
                                        l.l_id, l.amount, l.payable, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id, CONCAT(c.firstname, ' ', c.lastname) as collector
                                  FROM jai_db.borrowers as b
                                  INNER JOIN jai_db.loans as l
                                  ON b.b_id = l.b_id
                                  INNER JOIN jai_db.collectors as c
                                  ON l.c_id = c.c_id
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
                                        l.l_id, l.amount, l.payable, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id
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

    /* ----- GET COLLECTOR DATA (FOR ACCOUNTSLIST.PHP) ----- */
    $statementCollectors = $conn->prepare("SELECT c_id, CONCAT(c.firstname, ' ', c.lastname) as name
                                           FROM jai_db.collectors as c
                                           ORDER BY c_id ASC
                                          ");
    $statementCollectors->execute();
    $collectors = $statementCollectors->fetchAll(PDO::FETCH_ASSOC);
    /* ----- END - GET COLLECTOR DATA (FOR ACCOUNTSLIST.PHP) ----- */

    /* TEST */
    $statementPaymentsAug3 = $conn->prepare("SELECT p.amount, l.amortization
                                              FROM jai_db.payments as p
                                              INNER JOIN jai_db.loans as l
                                              ON p.l_id = l.l_id
                                              WHERE p.date = '2022-08-03'");
    $statementPaymentsAug3->execute();
    $paymentsAug3 = $statementPaymentsAug3->fetchAll(PDO::FETCH_ASSOC);
    $expectedPaymentsAug3 = (float)0;
    $actualPaymentsAug3 = (float)0;
    foreach ($paymentsAug3 as $i => $paymentAug3) {
      $expectedPaymentsAug3 += $paymentAug3['amortization'];
      $actualPaymentsAug3 += $paymentAug3['amount'];
    }

    // $testFirstOfJuly = date('Y-m-01');
    // $testLastOfJuly = date('Y-m-t');
    // $date1 = new DateTime($testFirstOfJuly);
    // $date2 = new DateTime($testLastOfJuly);
    // $interval = $date1->diff($date2);

    // echo $testFirstOfJuly . ' - ' . $testLastOfJuly;
    // echo "<br>";
    // echo "difference " . $interval->y . " years, " . $interval->m." months, ".($interval->d + 1)." days "; 
    // echo "<br>";

    // $count = 0;
    // while ($count <= $interval->d + 1) {
    //   echo $count;
    //   echo '<br>';
    //   $count++;

    // }

    $date = strtotime("+1 day", strtotime("2007-02-28"));
    echo date("Y-m-d", $date);
    echo "<br>";
    echo "(test)Expected Payments on Aug 3: " . number_format($expectedPaymentsAug3, 2);
    echo "<br>";
    echo "(test)Total Payments on Aug 3: " . number_format($actualPaymentsAug3, 2);
    /* END - TEST */


    /*                                                                */
    /*                                                                */
    /*                   END - PREV MONTHS & MISC QUERIES             */
    /*                                                                */
    /*                                                                */





    // echo "<pre>";
    // var_dump($activeLoans);
    // exit;
    ?>
    <br>
    <br>
    Accounts Listtttttttttt test
    <form method="get" action="accountslist" target="_blank">
      <select name="c_id">
        <option value="" selected disabled>Select collector</option>
        <?php
        foreach ($collectors as $i => $collector) {
          echo '<option value="' . $collector['c_id'] . '">' . $collector['name'] . '</option>';
        }
        ?>
      </select>
      <button title="View accounts list" class="btn-primary" type="submit">View accounts list</button>
      <!-- <input title="View ledger" type="submit" name="loanID" class="btn btn-primary btn-sm ledger-btn" value="<?= $payment['l_id'] ?>" <?= ($payment['paymentsmade'] || $payment['passes']) == 0 ? 'disabled' : '' ?>></input> -->
    </form>
    <br>
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

    // echo '<pre>';
    // var_dump($totalCashCollectionTodayKing);
    // exit;

    ?>

    <script>
      var fullDate = {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      };

      var monthYear = {
        month: 'long',
        year: 'numeric'
      };

      var today = new Date();

      function getFirstOfLastMonth() {
        const date = new Date();
        const lastMonth = new Date(date.getFullYear(), date.getMonth() - 1, 1);
        return lastMonth.toLocaleDateString("en-US", monthYear);
      }

      /*     NUMBER FORMAT (2 decimals with comma)     */
      const numberFormat = {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      };




      /*                                               */
      /*                                               */
      /*    CHART DATA TOTAL COLLECTIONS LAST MONTH    */
      /*                                               */
      /*                                               */

      const totalCollectionLastMonth = <?= $totalCollectionLastMonth ?>

      /* Total Cash Collection Per Collector */
      const totalCashCollectionLastMonthKing = <?php
                                                echo json_encode($totalCashCollectionLastMonthKing['sum']);
                                                ?>;

      const totalCashCollectionLastMonthCarl = <?php
                                                echo json_encode($totalCashCollectionLastMonthCarl['sum']);
                                                ?>;

      /* Total G-Cash Collection Per Collector */
      const totalGCashCollectionLastMonthKing = <?php
                                                echo json_encode($totalGCashCollectionLastMonthKing['sum']);
                                                ?>;

      const totalGCashCollectionLastMonthCarl = <?php
                                                echo json_encode($totalGCashCollectionLastMonthCarl['sum']);
                                                ?>;

      // SETUP BLOCK
      const dataLastMonth = {
        labels: ['King Cruz (Cash)', 'King Cruz (GCash)', 'Carl Corpuz (Cash)', 'Carl Corpuz (GCash)'],
        datasets: [{
          label: 'Total Collection Last Month',
          data: [totalCashCollectionLastMonthKing, totalGCashCollectionLastMonthKing, totalCashCollectionLastMonthCarl, totalGCashCollectionLastMonthCarl],
          backgroundColor: [
            'rgba(32, 96, 229, 1)',
            'rgba(72, 121, 223, 1)',
            'rgba(185, 36, 36, 1)',
            'rgba(218, 81, 81, 1)'
          ],
          borderColor: [
            'rgba(32, 96, 229, 1)',
            'rgba(72, 121, 223, 1)',
            'rgba(185, 36, 36, 1)',
            'rgba(218, 81, 81, 1)'
          ],
          borderWidth: 2,
          hoverOffset: 5
        }]
      };

      // CONFIG BLOCK
      const configLastMonth = {
        type: 'pie',
        data: dataLastMonth,
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: true,
              text: ['Total Collection Last Month ' + getFirstOfLastMonth(), '₱ ' + totalCollectionLastMonth.toLocaleString('en', numberFormat)],
              font: {
                size: 14
              }
            }
          }

        }
      };

      // RENDER BLOCK
      const myChartLastMonth = new Chart(
        document.getElementById('chartTotalCollectionLastMonth'),
        configLastMonth
      );

      /*                                                     */
      /*                                                     */
      /*    END - CHART DATA TOTAL COLLECTIONS LAST MONTH    */
      /*                                                     */
      /*                                                     */

      /*                                               */
      /*                                               */
      /*    CHART DATA TOTAL COLLECTIONS THIS MONTH    */
      /*                                               */
      /*                                               */

      const totalCollectionThisMonth = <?= $totalCollectionThisMonth ?>

      /* Total Cash Collection Per Collector */
      const totalCashCollectionThisMonthKing = <?php
                                                echo json_encode($totalCashCollectionThisMonthKing['sum']);
                                                ?>;

      const totalCashCollectionThisMonthCarl = <?php
                                                echo json_encode($totalCashCollectionThisMonthCarl['sum']);
                                                ?>;

      /* Total G-Cash Collection Per Collector */
      const totalGCashCollectionThisMonthKing = <?php
                                                echo json_encode($totalGCashCollectionThisMonthKing['sum']);
                                                ?>;

      const totalGCashCollectionThisMonthCarl = <?php
                                                echo json_encode($totalGCashCollectionThisMonthCarl['sum']);
                                                ?>;

      // SETUP BLOCK
      const dataThisMonth = {
        labels: ['King Cruz (Cash)', 'King Cruz (GCash)', 'Carl Corpuz (Cash)', 'Carl Corpuz (GCash)'],
        datasets: [{
          label: 'Total Collection This Month',
          data: [totalCashCollectionThisMonthKing, totalGCashCollectionThisMonthKing, totalCashCollectionThisMonthCarl, totalGCashCollectionThisMonthCarl],
          backgroundColor: [
            'rgba(32, 96, 229, 1)',
            'rgba(72, 121, 223, 1)',
            'rgba(185, 36, 36, 1)',
            'rgba(218, 81, 81, 1)'
          ],
          borderColor: [
            'rgba(32, 96, 229, 1)',
            'rgba(72, 121, 223, 1)',
            'rgba(185, 36, 36, 1)',
            'rgba(218, 81, 81, 1)'
          ],
          borderWidth: 2,
          hoverOffset: 5
        }]
      };

      // CONFIG BLOCK
      const configThisMonth = {
        type: 'pie',
        data: dataThisMonth,
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: true,
              text: ['Total Collection This Month ' + today.toLocaleDateString("en-US", monthYear), '₱ ' + totalCollectionThisMonth.toLocaleString('en', numberFormat)],
              font: {
                size: 14
              }
            }
          }

        }
      };

      // RENDER BLOCK
      const myChartThisMonth = new Chart(
        document.getElementById('chartTotalCollectionThisMonth'),
        configThisMonth
      );

      /*                                                     */
      /*                                                     */
      /*    END - CHART DATA TOTAL COLLECTIONS THIS MONTH    */
      /*                                                     */
      /*                                                     */


      /*                                          */
      /*                                          */
      /*    CHART DATA TOTAL COLLECTIONS TODAY    */
      /*                                          */
      /*                                          */

      const totalCollectionToday = <?= $totalCollectionToday ?>

      /* Total Cash Collection Per Collector */
      const totalCashCollectionTodayKing = <?php
                                            echo json_encode($totalCashCollectionTodayKing['sum']);
                                            ?>;

      const totalCashCollectionTodayCarl = <?php
                                            echo json_encode($totalCashCollectionTodayCarl['sum']);
                                            ?>;

      /* Total G-Cash Collection Per Collector */
      const totalGCashCollectionTodayKing = <?php
                                            echo json_encode($totalGCashCollectionTodayKing['sum']);
                                            ?>;

      const totalGCashCollectionTodayCarl = <?php
                                            echo json_encode($totalGCashCollectionTodayCarl['sum']);
                                            ?>;

      // SETUP BLOCK
      const data = {
        labels: ['King Cruz (Cash)', 'King Cruz (GCash)', 'Carl Corpuz (Cash)', 'Carl Corpuz (GCash)'],
        datasets: [{
          label: 'Total Collection Today',
          data: [totalCashCollectionTodayKing, totalGCashCollectionTodayKing, totalCashCollectionTodayCarl, totalGCashCollectionTodayCarl],
          backgroundColor: [
            'rgba(32, 96, 229, 1)',
            'rgba(72, 121, 223, 1)',
            'rgba(185, 36, 36, 1)',
            'rgba(218, 81, 81, 1)'
          ],
          borderColor: [
            'rgba(32, 96, 229, 1)',
            'rgba(72, 121, 223, 1)',
            'rgba(185, 36, 36, 1)',
            'rgba(218, 81, 81, 1)'
          ],
          borderWidth: 2,
          hoverOffset: 5
        }]
      };

      // CONFIG BLOCK
      const config = {
        type: 'pie',
        data: data,
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            },
            title: {
              display: true,
              align: 'left',
              text: ['Collection Today ' + today.toLocaleDateString("en-US", fullDate), '₱ ' + totalCollectionToday.toLocaleString('en', numberFormat)],
              font: {
                size: 14
              }
            }
          }

        }
      };

      // RENDER BLOCK
      const myChart = new Chart(
        document.getElementById('chartTotalCollectionToday'),
        config
      );

      /*                                               */
      /*                                               */
      /*   END - CHART DATA TOTAL COLLECTIONS TODAY    */
      /*                                               */
      /*                                               */
    </script>

    <?php

    ?>

  </div>