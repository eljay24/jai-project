<?php
require_once "../../views/includes/dbconn.php";
require_once "../../views/partials/header.php";

/*                           */
/*                           */
/*           DATES           */
/*                           */
/*                           */

$dateToday = date('Y-m-d');

/*      Start and end of year       */
$startOfYear = date('Y-m-d', strtotime('this year January 1st'));
$endOfYear = date('Y-m-d', strtotime('this year December 31st'));

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



?>

<body>
  <div class="content-container">
    <?php

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

    /*                                                                   */
    /*                                                                   */
    /*                            CHART VALUES                           */
    /*                                                                   */
    /*                                                                   */

    /*                                                                   */
    /*                                                                   */
    /*                    QUERIES OVERVIEW THIS YEAR                     */
    /*                                                                   */
    /*                                                                   */

    $statementLoans = $conn->prepare("SELECT * FROM jai_db.loans as l WHERE l.releasedate BETWEEN :startofyear AND :endofyear");
    $statementLoans->bindValue(':startofyear', $startOfYear);
    $statementLoans->bindValue(':endofyear', $endOfYear);
    $statementLoans->execute();
    $loans = $statementLoans->fetchAll(PDO::FETCH_ASSOC);

    $statementPayments = $conn->prepare("SELECT * FROM jai_db.payments as p WHERE p.date BETWEEN :startofyear AND :endofyear");
    $statementPayments->bindValue(':startofyear', $startOfYear);
    $statementPayments->bindValue(':endofyear', $endOfYear);
    $statementPayments->execute();
    $payments = $statementPayments->fetchAll(PDO::FETCH_ASSOC);

    $janRelease = (float)0;
    $febRelease = (float)0;
    $marRelease = (float)0;
    $aprRelease = (float)0;
    $mayRelease = (float)0;
    $junRelease = (float)0;
    $julRelease = (float)0;
    $augRelease = (float)0;
    $sepRelease = (float)0;
    $octRelease = (float)0;
    $novRelease = (float)0;
    $decRelease = (float)0;

    $janPayable = (float)0;
    $febPayable = (float)0;
    $marPayable = (float)0;
    $aprPayable = (float)0;
    $mayPayable = (float)0;
    $junPayable = (float)0;
    $julPayable = (float)0;
    $augPayable = (float)0;
    $sepPayable = (float)0;
    $octPayable = (float)0;
    $novPayable = (float)0;
    $decPayable = (float)0;

    $janCollection = (float)0;
    $febCollection = (float)0;
    $marCollection = (float)0;
    $aprCollection = (float)0;
    $mayCollection = (float)0;
    $junCollection = (float)0;
    $julCollection = (float)0;
    $augCollection = (float)0;
    $sepCollection = (float)0;
    $octCollection = (float)0;
    $novCollection = (float)0;
    $decCollection = (float)0;

    foreach ($loans as $i => $loan) {
      if (date_format(date_create($loan['releasedate']), 'M') == 'Jan') {
        $janRelease += $loan['amount'];
        $janPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'Feb') {
        $febRelease += $loan['amount'];
        $febPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'Mar') {
        $marRelease += $loan['amount'];
        $marPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'Apr') {
        $aprRelease += $loan['amount'];
        $aprPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'May') {
        $mayRelease += $loan['amount'];
        $mayPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'Jun') {
        $junRelease += $loan['amount'];
        $junPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'Jul') {
        $julRelease += $loan['amount'];
        $julPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'Aug') {
        $augRelease += $loan['amount'];
        $augPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'Sep') {
        $sepRelease += $loan['amount'];
        $sepPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'Oct') {
        $octRelease += $loan['amount'];
        $octPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'Nov') {
        $novRelease += $loan['amount'];
        $novPayable += $loan['payable'];
      } else if (date_format(date_create($loan['releasedate']), 'M') == 'Dec') {
        $decRelease += $loan['amount'];
        $decPayable += $loan['payable'];
      }
    }

    foreach ($payments as $i => $payment) {
      if (date_format(date_create($payment['date']), 'M') == 'Jan') {
        $janCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'Feb') {
        $febCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'Mar') {
        $marCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'Apr') {
        $aprCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'May') {
        $mayCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'Jun') {
        $junCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'Jul') {
        $julCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'Aug') {
        $augCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'Sep') {
        $sepCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'Oct') {
        $octCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'Nov') {
        $novCollection += $payment['amount'];
      } else if (date_format(date_create($payment['date']), 'M') == 'Dec') {
        $decCollection += $payment['amount'];
      }
    }


    /*

    echo 'Jan Release: '.number_format($janRelease, 2);
    echo '<br>';
    echo 'Feb Release: '.number_format($febRelease, 2);
    echo '<br>';
    echo 'Mar Release: '.number_format($marRelease, 2);
    echo '<br>';
    echo 'Apr Release: '.number_format($aprRelease, 2);
    echo '<br>';
    echo 'May Release: '.number_format($mayRelease, 2);
    echo '<br>';
    echo 'Jun Release: '.number_format($junRelease, 2);
    echo '<br>';
    echo 'Jul Release: '.number_format($julRelease, 2);
    echo '<br>';
    echo 'Aug Release: '.number_format($augRelease, 2);
    echo '<br>';
    echo 'Sep Release: '.number_format($sepRelease, 2);
    echo '<br>';
    echo 'Oct Release: '.number_format($octRelease, 2);
    echo '<br>';
    echo 'Nov Release: '.number_format($novRelease, 2);
    echo '<br>';
    echo 'Dec Release: '.number_format($decRelease, 2);
    echo '<br>';

    */


    /*                                                                   */
    /*                                                                   */
    /*                 END - QUERIES OVERVIEW THIS YEAR                  */
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

    $statementCollectors = $conn->prepare("SELECT c.c_id, CONCAT(c.lastname, ', ', c.firstname) as name
                                           FROM jai_db.collectors as c");
    $statementCollectors->execute();
    $collectors = $statementCollectors->fetchAll(PDO::FETCH_ASSOC);


    ?>

    <!--                                 -->
    <!--                                 -->
    <!--           DRAW CHARTS           -->
    <!--                                 -->
    <!--                                 -->
    <div class="overview-chart-div">
      <canvas id="chartOverview"></canvas>
    </div>
    <div class="chart-div d-flex">
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

    <br>
    <br>
    Accounts List
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

    echo 'Expected profits today: ' . number_format($totalProfitPerPayment, 4);
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

      var yearOnly = {
        year: 'numeric'
      }

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
            'rgba(30, 139, 195, 1)',
            'rgba(30, 139, 195, 1)',
            'rgba(196, 77, 86, 1)',
            'rgba(196, 77, 86, 1)'
          ],
          borderColor: [
            'rgba(0, 0, 0, 1)',
            'rgba(0, 0, 0, 1)',
            'rgba(0, 0, 0, 1)',
            'rgba(0, 0, 0, 1)'
          ],
          borderWidth: 1,
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
            'rgba(30, 139, 195, 1)',
            'rgba(30, 139, 195, 1)',
            'rgba(196, 77, 86, 1)',
            'rgba(196, 77, 86, 1)'
          ],
          borderColor: [
            'rgba(0, 0, 0, 1)',
            'rgba(0, 0, 0, 1)',
            'rgba(0, 0, 0, 1)',
            'rgba(0, 0, 0, 1)'
          ],
          borderWidth: 1,
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
            'rgba(30, 139, 195, 1)',
            'rgba(30, 139, 195, 1)',
            'rgba(196, 77, 86, 1)',
            'rgba(196, 77, 86, 1)'
          ],
          borderColor: [
            'rgba(0, 0, 0, 1)',
            'rgba(0, 0, 0, 1)',
            'rgba(0, 0, 0, 1)',
            'rgba(0, 0, 0, 1)'
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
            'rgba(30, 139, 195, 1)'
          ],
          borderColor: [
            'rgba(0, 0, 0, 1)'
          ],
          borderWidth: 1,
          borderRadius: 1
        }, {
          barPercentage: 0.7,
          label: 'Carl Corpuz',
          data: [monCollectionCarl, tueCollectionCarl, wedCollectionCarl, thuCollectionCarl, friCollectionCarl, satCollectionCarl],
          backgroundColor: [
            'rgba(196, 77, 86, 1)'

          ],
          borderColor: [
            'rgba(0, 0, 0, 1)'
          ],
          borderWidth: 1,
          borderRadius: 1
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
                usePointStyle: false
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

      /*                                                    */
      /*                                                    */
      /*                 BAR CHART OVERVIEW                 */
      /*                                                    */
      /*                                                    */

      const janRelease = <?= json_encode($janRelease) ?>;
      const febRelease = <?= json_encode($febRelease) ?>;
      const marRelease = <?= json_encode($marRelease) ?>;
      const aprRelease = <?= json_encode($aprRelease) ?>;
      const mayRelease = <?= json_encode($mayRelease) ?>;
      const junRelease = <?= json_encode($junRelease) ?>;
      const julRelease = <?= json_encode($julRelease) ?>;
      const augRelease = <?= json_encode($augRelease) ?>;
      const sepRelease = <?= json_encode($sepRelease) ?>;
      const octRelease = <?= json_encode($octRelease) ?>;
      const novRelease = <?= json_encode($novRelease) ?>;
      const decRelease = <?= json_encode($decRelease) ?>;

      const janPayable = <?= json_encode($janPayable) ?>;
      const febPayable = <?= json_encode($febPayable) ?>;
      const marPayable = <?= json_encode($marPayable) ?>;
      const aprPayable = <?= json_encode($aprPayable) ?>;
      const mayPayable = <?= json_encode($mayPayable) ?>;
      const junPayable = <?= json_encode($junPayable) ?>;
      const julPayable = <?= json_encode($julPayable) ?>;
      const augPayable = <?= json_encode($augPayable) ?>;
      const sepPayable = <?= json_encode($sepPayable) ?>;
      const octPayable = <?= json_encode($octPayable) ?>;
      const novPayable = <?= json_encode($novPayable) ?>;
      const decPayable = <?= json_encode($decPayable) ?>;

      const janCollection = <?= json_encode($janCollection) ?>;
      const febCollection = <?= json_encode($febCollection) ?>;
      const marCollection = <?= json_encode($marCollection) ?>;
      const aprCollection = <?= json_encode($aprCollection) ?>;
      const mayCollection = <?= json_encode($mayCollection) ?>;
      const junCollection = <?= json_encode($junCollection) ?>;
      const julCollection = <?= json_encode($julCollection) ?>;
      const augCollection = <?= json_encode($augCollection) ?>;
      const sepCollection = <?= json_encode($sepCollection) ?>;
      const octCollection = <?= json_encode($octCollection) ?>;
      const novCollection = <?= json_encode($novCollection) ?>;
      const decCollection = <?= json_encode($decCollection) ?>;

      //SETUP BLOCK
      const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      const dataOverview = {
        labels: months,
        datasets: [{
          barPercentage: 0.85,
          label: 'Released',
          data: [janRelease, febRelease, marRelease, aprRelease, mayRelease, junRelease, julRelease, augRelease, sepRelease, octRelease, novRelease, decRelease],
          backgroundColor: [
            'rgba(3, 201, 169, 1)'
          ],
          borderColor: [
            'rgb(0, 0, 0)'
          ],
          borderWidth: 1
        }, {
          barPercentage: 0.85,
          label: 'Payable',
          data: [janPayable, febPayable, marPayable, aprPayable, mayPayable, junPayable, julPayable, augPayable, sepPayable, octPayable, novPayable, decPayable],
          backgroundColor: [
            'rgba(230, 126, 34, 1)'
          ],
          borderColor: [
            'rgb(0, 0, 0)'
          ],
          borderWidth: 1
        }, {
          barPercentage: 0.85,
          label: 'Collection',
          data: [janCollection, febCollection, marCollection, aprCollection, mayCollection, junCollection, julCollection, augCollection, sepCollection, octCollection, novCollection, decCollection],
          backgroundColor: [
            'rgba(30, 139, 195, 1)'
          ],
          borderColor: [
            'rgb(0, 0, 0)'
          ],
          borderWidth: 1
        }]
      };

      //CONFIG BLOCK
      const configOverview = {
        type: 'bar',
        data: dataOverview,
        options: {
          // animation: {
          //   onComplete: () => {
          //     delayed = true;
          //   },
          //   delay: (context) => {
          //     let delay = 0;
          //     if (context.type === 'data' && context.mode === 'default' && !delayed) {
          //       delay = context.dataIndex * 300 + context.datasetIndex * 100;
          //     }
          //     return delay;
          //   }
          // },
          scales: {
            y: {
              beginAtZero: true
            }
          },
          plugins: {
            title: {
              display: true,
              text: ['Overview for ' + today.toLocaleDateString("en-US", yearOnly)],
              font: {
                size: 14
              }
            }
          }
        },
      };

      //RENDER BLOCK
      const overviewChart = new Chart(
        document.getElementById('chartOverview'),
        configOverview
      );


      /*                                                    */
      /*                                                    */
      /*              END - BAR CHART OVERVIEW              */
      /*                                                    */
      /*                                                    */
    </script>

    <?php

    ?>

  </div>