<?php
require_once "../../views/includes/dbconn.php";
require_once "../../views/partials/header.php";

/*                           */
/*                           */
/*           DATES           */
/*                           */
/*                           */

$dateToday = date('Y-m-d');

/*      FIRST AND LAST OF THIS MONTH      */
$firstOfThisMonth = date('Y-m-01'); // hard-coded '01' for first day
$lastOfThisMonth  = date('Y-m-t'); // t = number of days in given month

/*      FIRST AND LAST OF LAST MONTH      */
$datestring = $dateToday . 'first day of last month';
$firstOfLastMonth = date_create($datestring);
$firstOfLastMonth = $firstOfLastMonth->format('Y-m-01'); //2011-02
$datestring = $dateToday . 'last day of last month';
$lastOfLastMonth = date_create($datestring);
$lastOfLastMonth = $lastOfLastMonth->format('Y-m-t');

/*               LAST MONTH               */
$datestring = $dateToday . 'first day of last month';
$lastMonth = date_create($datestring);
$lastMonth = $lastMonth->format('F Y');

/*               THIS MONTH               */
$thisMonth = date('F Y');

/*        MON & SAT OF CURRENT WEEK       */
$startOfWeekDay = date_create('monday this week');
$endOfWeekDay = date_create('saturday this week');

$mon = date_create('monday this week');
$tue = date_create('tuesday this week');
$wed = date_create('wednesday this week');
$thu = date_create('thursday this week');
$fri = date_create('friday this week');
$sat = date_create('saturday this week');

// echo date_format(date_create('2022-06-25'), 'Y-m-d');
// exit;

// while ($startOfWeekDay <= $endOfWeekDay) {
//   echo $startOfWeekDay->format('D');
//   echo '<br>';
//   $startOfWeekDay->modify('+1 day');
// }
// exit;


/*                                */
/*                                */
/*          END - DATES           */
/*                                */
/*                                */

/*                                                */
/*                                                */
/*                GET PROFIT QUERY                */
/*                                                */
/*                                                */

$statementActiveLoans = $conn->prepare("SELECT *
                                    FROM jai_db.loans
                                    WHERE activeloan = 1");
$statementActiveLoans->execute();
$activeLoans = $statementActiveLoans->fetchAll(PDO::FETCH_ASSOC);

$totalProfitPerPayment = (float)0;
$totalDailyProfit = (float)0;
$expectedTotalCollection = (float)0;

foreach ($activeLoans as $i => $activeLoan) {

  $releaseDate = date_create($activeLoan['releasedate']);
  $dueDate = date_create($activeLoan['duedate']);

  /*                                                                   */
  /*       Count number of days                                        */
  /*       from release date to due date (inclusive of both)           */
  /*       excluding Sundays                                           */
  /*                                                                   */

  $start = new DateTime(date_format($releaseDate, 'Y-m-d'));
  $end = new DateTime(date_format($dueDate, 'Y-m-d'));

  // otherwise the  end date is excluded (bug?)
  $end->modify('+1 day');

  $interval = $end->diff($start);

  // total days
  $days = $interval->days;

  // create an iterateable period of date (P1D equates to 1 day)
  $period = new DatePeriod($start, new DateInterval('P1D'), $end);

  // best stored as array, so you can add more than one
  $holidays = array('2012-09-07');

  foreach ($period as $dt) {
    $curr = $dt->format('D');

    // substract if Saturday or Sunday
    if ($curr == 'Sun') {
      $days--;
    }

    // (optional) for the updated question
    elseif (in_array($dt->format('Y-m-d'), $holidays)) {
      $days--;
    }
  }

  /*                                        */
  /*                                        */
  /*       END - Count number of days       */
  /*                                        */
  /*                                        */

  $profit = $activeLoan['payable'] - $activeLoan['amount'];
  $paymentsToCloseLoan = $activeLoan['payable'] / $activeLoan['amortization'];
  $profitPerPayment = $profit / $paymentsToCloseLoan;

  $loanNumberOfDays = $activeLoan['payable'] / $activeLoan['amortization'];
  $dailyProfit = ($activeLoan['payable'] - $activeLoan['amount']) / $days;
  $totalProfitPerPayment += $profitPerPayment;
  $totalDailyProfit += $dailyProfit;
  $expectedTotalCollection += $activeLoan['amortization'];
}

/*                                                */
/*                                                */
/*            END - GET PROFIT QUERY              */
/*                                                */
/*                                                */

//////////////////////////////// COUNT ACTIVE LOANS



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


/* ----- GET COLLECTOR DATA (FOR ACCOUNTSLIST.PHP) ----- */
$statementCollectors = $conn->prepare("SELECT c_id, CONCAT(c.firstname, ' ', c.lastname) as name
                                       FROM jai_db.collectors as c
                                       ORDER BY c_id ASC
                                      ");
$statementCollectors->execute();
$collectors = $statementCollectors->fetchAll(PDO::FETCH_ASSOC);
/* ----- END - GET COLLECTOR DATA (FOR ACCOUNTSLIST.PHP) ----- */

/*                                                                */
/*                                                                */
/*                   END - PREV MONTHS & MISC QUERIES             */
/*                                                                */
/*                                                                */

?>

<body>
  <div class="content-container">
    <?php
    /*                                                                   */
    /*                                                                   */
    /*                            CHART VALUES                           */
    /*                                                                   */
    /*                                                                   */

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

    /*                                                     */
    /*                                                     */
    /*         QUERIES COLLECTION FOR CURRENT WEEK         */
    /*                                                     */
    /*                                                     */

    $queryCollectionThisWeek = $conn->prepare("SELECT p.c_id, CONCAT(c.lastname, ', ', c.firstname) as collectorname, p.type, p.amount, p.date
                                               FROM jai_db.payments as p
                                               INNER JOIN jai_db.loans as l
                                               ON p.l_id = l.l_id
                                               INNER JOIN jai_db.collectors as c
                                               on p.c_id = c.c_id
                                               WHERE date BETWEEN :startofweekday AND :endofweek
                                               ORDER BY p.date ASC, p.c_id ASC");
    $queryCollectionThisWeek->bindValue(':startofweekday', $startOfWeekDay->format('Y-m-d'));
    $queryCollectionThisWeek->bindValue(':endofweek', $endOfWeekDay->format('Y-m-d'));
    $queryCollectionThisWeek->execute();
    $collectionThisWeek = $queryCollectionThisWeek->fetchAll(PDO::FETCH_ASSOC);

    $monCollectionKing = (float)0;
    $tueCollectionKing = (float)0;
    $wedCollectionKing = (float)0;
    $thuCollectionKing = (float)0;
    $friCollectionKing = (float)0;
    $satCollectionKing = (float)0;

    $monCollectionCarl = (float)0;
    $tueCollectionCarl = (float)0;
    $wedCollectionCarl = (float)0;
    $thuCollectionCarl = (float)0;
    $friCollectionCarl = (float)0;
    $satCollectionCarl = (float)0;

    $totalCollectionThisWeek = (float)0;

    foreach ($collectionThisWeek as $i => $collection) {

      if ($collection['c_id'] == 1) {
        if (date_format(date_create($collection['date']), 'D') == 'Mon') {
          $monCollectionKing += $collection['amount'];
        } elseif (date_format(date_create($collection['date']), 'D') == 'Tue') {
          $tueCollectionKing += $collection['amount'];
        } elseif (date_format(date_create($collection['date']), 'D') == 'Wed') {
          $wedCollectionKing += $collection['amount'];
        } elseif (date_format(date_create($collection['date']), 'D') == 'Thu') {
          $thuCollectionKing += $collection['amount'];
        } elseif (date_format(date_create($collection['date']), 'D') == 'Fri') {
          $friCollectionKing += $collection['amount'];
        } elseif (date_format(date_create($collection['date']), 'D') == 'Sat') {
          $satCollectionKing += $collection['amount'];
        }
      } elseif ($collection['c_id'] == 2) {
        if (date_format(date_create($collection['date']), 'D') == 'Mon') {
          $monCollectionCarl += $collection['amount'];
        } elseif (date_format(date_create($collection['date']), 'D') == 'Tue') {
          $tueCollectionCarl += $collection['amount'];
        } elseif (date_format(date_create($collection['date']), 'D') == 'Wed') {
          $wedCollectionCarl += $collection['amount'];
        } elseif (date_format(date_create($collection['date']), 'D') == 'Thu') {
          $thuCollectionCarl += $collection['amount'];
        } elseif (date_format(date_create($collection['date']), 'D') == 'Fri') {
          $friCollectionCarl += $collection['amount'];
        } elseif (date_format(date_create($collection['date']), 'D') == 'Sat') {
          $satCollectionCarl += $collection['amount'];
        }
      }
    }

    $totalCollectionThisWeek = $monCollectionKing + $tueCollectionKing + $wedCollectionKing + $thuCollectionKing + $friCollectionKing + $satCollectionKing
      + $monCollectionCarl + $tueCollectionCarl + $wedCollectionCarl + $thuCollectionCarl + $friCollectionCarl + $satCollectionCarl;

    // echo 'KING<br>';
    // echo 'Monday Collection: ' . $monCollectionKing;
    // echo '<br>';
    // echo 'Tuesday Collection: ' . $tueCollectionKing;
    // echo '<br>';
    // echo 'Wednesday Collection: ' . $wedCollectionKing;
    // echo '<br>';
    // echo 'Thursday Collection: ' . $thuCollectionKing;
    // echo '<br>';
    // echo 'Friday Collection: ' . $friCollectionKing;
    // echo '<br>';
    // echo 'Saturday Collection: ' . $satCollectionKing;

    // echo '<br>';

    // echo 'CARL<br>';
    // echo 'Monday Collection: ' . $monCollectionCarl;
    // echo '<br>';
    // echo 'Tuesday Collection: ' . $tueCollectionCarl;
    // echo '<br>';
    // echo 'Wednesday Collection: ' . $wedCollectionCarl;
    // echo '<br>';
    // echo 'Thursday Collection: ' . $thuCollectionCarl;
    // echo '<br>';
    // echo 'Friday Collection: ' . $friCollectionCarl;
    // echo '<br>';
    // echo 'Saturday Collection: ' . $satCollectionCarl;

    // exit;

    /*                                                     */
    /*                                                     */
    /*      END - QUERIES COLLECTION FOR CURRENT WEEK      */
    /*                                                     */
    /*                                                     */

    /*                                    */
    /*                                    */
    /*           END - CHART VALUES       */
    /*                                    */
    /*                                    */
    ?>
    <div class="chart-div d-flex">

      <!--                                 -->
      <!--                                 -->
      <!--           DRAW CHARTS           -->
      <!--                                 -->
      <!--                                 -->

      <canvas id="chartTotalCollectionLastMonth"></canvas>
      <canvas id="chartTotalCollectionThisMonth"></canvas>
      <canvas id="chartTotalCollectionToday"></canvas>
      <div class="no-collections">
        <?= $totalCollectionToday == 0 ? '<span>No collections today</span>' : '' ?>
      </div>
    </div>
    <div class="bar-chart-div">
      <canvas id="chartCollectionThisWeek"></canvas>
    </div>


    <!--                                       -->
    <!--                                       -->
    <!--           END - DRAW CHARTS           -->
    <!--                                       -->
    <!--                                       -->

    <?php


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
    /* END - TEST */








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
    echo 'Active Loans: ' . count($activeLoans);
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
    echo 'Expected profits today: ' . number_format($totalDailyProfit, 4);
    echo "<br>";
    echo 'Expected profits per payment today: ' . number_format($totalProfitPerPayment, 4);
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
      const totalCashCollectionLastMonthKing = <?= json_encode($totalCashCollectionLastMonthKing['sum']); ?>;
      const totalCashCollectionLastMonthCarl = <?= json_encode($totalCashCollectionLastMonthCarl['sum']); ?>;

      /* Total G-Cash Collection Per Collector */
      const totalGCashCollectionLastMonthKing = <?= json_encode($totalGCashCollectionLastMonthKing['sum']); ?>;
      const totalGCashCollectionLastMonthCarl = <?= json_encode($totalGCashCollectionLastMonthCarl['sum']); ?>;

      // SETUP BLOCK
      const dataLastMonth = {
        labels: ['King Cruz (Cash)', 'King Cruz (GCash)', 'Carl Corpuz (Cash)', 'Carl Corpuz (GCash)'],
        datasets: [{
          label: 'Collection Last Month',
          data: [totalCashCollectionLastMonthKing, totalGCashCollectionLastMonthKing, totalCashCollectionLastMonthCarl, totalGCashCollectionLastMonthCarl],
          backgroundColor: [
            'rgba(32, 96, 229, 0.95)',
            'rgba(72, 121, 223, 1)',
            'rgba(185, 36, 36, 0.9)',
            'rgba(218, 81, 81, 1)'
          ],
          borderColor: [
            'rgba(32, 96, 229, 1)',
            'rgba(72, 121, 223, 1)',
            'rgba(185, 36, 36, 1)',
            'rgba(218, 81, 81, 1)'
          ],
          borderWidth: 2,
          hoverOffset: 10
        }]
      };

      // CONFIG BLOCK
      const configLastMonth = {
        type: 'pie',
        data: dataLastMonth,
        options: {
          layout: {
            padding: 7
          },
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
              labels: {
                usePointStyle: true
              }
            },
            title: {
              display: true,
              text: ['Collection Last Month ' + getFirstOfLastMonth(), '₱ ' + totalCollectionLastMonth.toLocaleString('en', numberFormat)],
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
      const totalCashCollectionThisMonthKing = <?= json_encode($totalCashCollectionThisMonthKing['sum']); ?>;
      const totalCashCollectionThisMonthCarl = <?= json_encode($totalCashCollectionThisMonthCarl['sum']); ?>;

      /* Total G-Cash Collection Per Collector */
      const totalGCashCollectionThisMonthKing = <?= json_encode($totalGCashCollectionThisMonthKing['sum']); ?>;
      const totalGCashCollectionThisMonthCarl = <?= json_encode($totalGCashCollectionThisMonthCarl['sum']); ?>;

      // SETUP BLOCK
      const dataThisMonth = {
        labels: ['King Cruz (Cash)', 'King Cruz (GCash)', 'Carl Corpuz (Cash)', 'Carl Corpuz (GCash)'],
        datasets: [{
          label: 'Collection This Month',
          data: [totalCashCollectionThisMonthKing, totalGCashCollectionThisMonthKing, totalCashCollectionThisMonthCarl, totalGCashCollectionThisMonthCarl],
          backgroundColor: [
            'rgba(53, 118, 255, 1)',
            'rgba(53, 118, 255, 0.95)',
            'rgba(183, 0, 0, 1)',
            'rgba(183, 0, 0, 0.9)'
          ],
          borderColor: [
            'rgba(53, 118, 255, 1)',
            'rgba(53, 118, 255, 1)',
            'rgba(183, 0, 0, 1)',
            'rgba(183, 0, 0, 1)'
          ],
          borderWidth: 2,
          hoverOffset: 10
        }]
      };

      // CONFIG BLOCK
      const configThisMonth = {
        type: 'pie',
        data: dataThisMonth,
        options: {
          layout: {
            padding: 7
          },
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
              labels: {
                usePointStyle: true
              }
            },
            title: {
              display: true,
              text: ['Collection This Month ' + today.toLocaleDateString("en-US", monthYear), '₱ ' + totalCollectionThisMonth.toLocaleString('en', numberFormat)],
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
      const totalCashCollectionTodayKing = <?= json_encode($totalCashCollectionTodayKing['sum']); ?>;
      const totalCashCollectionTodayCarl = <?= json_encode($totalCashCollectionTodayCarl['sum']); ?>;

      /* Total G-Cash Collection Per Collector */
      const totalGCashCollectionTodayKing = <?= json_encode($totalGCashCollectionTodayKing['sum']); ?>;
      const totalGCashCollectionTodayCarl = <?= json_encode($totalGCashCollectionTodayCarl['sum']); ?>;

      // SETUP BLOCK
      const data = {
        labels: ['King Cruz (Cash)', 'King Cruz (GCash)', 'Carl Corpuz (Cash)', 'Carl Corpuz (GCash)'],
        datasets: [{
          label: 'Total Collection Today',
          data: [totalCashCollectionTodayKing, totalGCashCollectionTodayKing, totalCashCollectionTodayCarl, totalGCashCollectionTodayCarl],
          backgroundColor: [
            'rgba(32, 96, 229, 1)',
            'rgba(72, 121, 223, 0.95)',
            'rgba(185, 36, 36, 1)',
            'rgba(218, 81, 81, 0.9)'
          ],
          borderColor: [
            'rgba(32, 96, 229, 1)',
            'rgba(72, 121, 223, 1)',
            'rgba(185, 36, 36, 1)',
            'rgba(218, 81, 81, 1)'
          ],
          borderWidth: 2,
          hoverOffset: 10
        }]
      };

      // CONFIG BLOCK
      const config = {
        type: 'pie',
        data: data,
        options: {
          layout: {
            padding: 7
          },
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
              labels: {
                usePointStyle: true
              }
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

      /*                                                    */
      /*                                                    */
      /*           BAR CHART COLLECTION THIS WEEK           */
      /*                                                    */
      /*                                                    */

      const monCollectionKing = <?= json_encode($monCollectionKing) ?>;
      const tueCollectionKing = <?= json_encode($tueCollectionKing) ?>;
      const wedCollectionKing = <?= json_encode($wedCollectionKing) ?>;
      const thuCollectionKing = <?= json_encode($thuCollectionKing) ?>;
      const friCollectionKing = <?= json_encode($friCollectionKing) ?>;
      const satCollectionKing = <?= json_encode($satCollectionKing) ?>;

      const monCollectionCarl = <?= json_encode($monCollectionCarl) ?>;
      const tueCollectionCarl = <?= json_encode($tueCollectionCarl) ?>;
      const wedCollectionCarl = <?= json_encode($wedCollectionCarl) ?>;
      const thuCollectionCarl = <?= json_encode($thuCollectionCarl) ?>;
      const friCollectionCarl = <?= json_encode($friCollectionCarl) ?>;
      const satCollectionCarl = <?= json_encode($satCollectionCarl) ?>;

      const mon = <?= json_encode($mon->format('D M. d')) ?>;
      const tue = <?= json_encode($tue->format('D M. d')) ?>;
      const wed = <?= json_encode($wed->format('D M. d')) ?>;
      const thu = <?= json_encode($thu->format('D M. d')) ?>;
      const fri = <?= json_encode($fri->format('D M. d')) ?>;
      const sat = <?= json_encode($sat->format('D M. d')) ?>;

      const totalCollectionThisWeek = <?= json_encode($totalCollectionThisWeek) ?>

      //SETUP BLOCK
      const dataBar = {
        labels: [
          [mon, '₱ ' + (monCollectionKing + monCollectionCarl).toFixed(2)],
          [tue, '₱ ' + (tueCollectionKing + tueCollectionCarl).toFixed(2)],
          [wed, '₱ ' + (wedCollectionKing + wedCollectionCarl).toFixed(2)],
          [thu, '₱ ' + (thuCollectionKing + thuCollectionCarl).toFixed(2)],
          [fri, '₱ ' + (friCollectionKing + friCollectionCarl).toFixed(2)],
          [sat, '₱ ' + (satCollectionKing + satCollectionCarl).toFixed(2)]
        ],
        datasets: [{
          barPercentage: 0.7,
          label: 'King Cruz',
          data: [monCollectionKing, tueCollectionKing, wedCollectionKing, thuCollectionKing, friCollectionKing, satCollectionKing],
          backgroundColor: [
            'rgba(32, 96, 229, 1)'
          ],
          borderColor: [
            'rgba(32, 96, 229, 1)'
          ],
          borderWidth: 0,
          borderRadius: 4
        }, {
          barPercentage: 0.7,
          label: 'Carl Corpuz',
          data: [monCollectionCarl, tueCollectionCarl, wedCollectionCarl, thuCollectionCarl, friCollectionCarl, satCollectionCarl],
          backgroundColor: [
            'rgba(185, 36, 36, 1)'

          ],
          borderColor: [
            'rgba(185, 36, 36, 1)'
          ],
          borderWidth: 0,
          borderRadius: 4
        }]
      };

      //CONFIG BLOCK
      let delayed;
      const configBar = {
        type: 'bar',
        data: dataBar,
        options: {
          animation: {
            onComplete: () => {
              delayed = true;
            },
            delay: (context) => {
              let delay = 0;
              if (context.type === 'data' && context.mode === 'default' && !delayed) {
                delay = context.dataIndex * 300 + context.datasetIndex * 100;
              }
              return delay;
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              stacked: true
            },
            x: {
              stacked: true
            }
          },
          plugins: {
            legend: {
              position: 'top',
              labels: {
                usePointStyle: true
              }
            },
            title: {
              display: true,
              text: ['Collection This Week', '₱ ' + totalCollectionThisWeek.toFixed(2)],
              font: {
                size: 14
              }
            }
          }
        }
      };

      //RENDER BLOCK
      const barChart = new Chart(
        document.getElementById('chartCollectionThisWeek'),
        configBar
      );


      /*                                                    */
      /*                                                    */
      /*        END - BAR CHART COLLECTION THIS WEEK        */
      /*                                                    */
      /*                                                    */
    </script>

    <?php

    ?>

  </div>