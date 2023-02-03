<?php
require_once "../../views/includes/dbconn.php";
require_once "../../views/partials/header.php";

/*                           */
/*           DATES           */
/*                           */

//Date TODAY
$dateToday = date('Y-m-d');

//Days of CURRENT WEEK
$monCurrentWeek = date('Y-m-d', strtotime('monday this week'));
$tueCurrentWeek = date('Y-m-d', strtotime('tuesday this week'));
$wedCurrentWeek = date('Y-m-d', strtotime('wednesday this week'));
$thuCurrentWeek = date('Y-m-d', strtotime('thursday this week'));
$friCurrentWeek = date('Y-m-d', strtotime('friday this week'));
$satCurrentWeek = date('Y-m-d', strtotime('saturday this week'));

//First and last day of LAST MONTH
$firstOfLastMonth = date('Y-m-01', strtotime('last month'));
$lastOfLastMonth = date('Y-m-t', strtotime('last month'));

//First and last day of CURRENT MONTH
$firstOfCurrentMonth = date('Y-m-01'); // hard-coded '01' for first day
$lastOfCurrentMonth  = date('Y-m-t'); // t = number of days in given month

//Start and end of CURRENT YEAR
$startOfCurrentYear = date('Y-m-d', strtotime('this year January 1st'));
$endOfCurrentYear = date('Y-m-d', strtotime('this year December 31st'));

/*                                            */
/*           QUERY FOR ALL PAYMENTS           */
/*                                            */

$queryAllPayments = $conn->prepare("SELECT p.*, l.activeloan, l.releasedate, l.status
                                    FROM jai_db.payments as p
                                    INNER JOIN jai_db.collectors as c
                                    ON p.c_id = c.c_id
                                    INNER JOIN jai_db.borrowers as b
                                    ON p.b_id = b.b_id
                                    INNER JOIN jai_db.loans as l
                                    ON p.l_id = l.l_id");
$queryAllPayments->execute();
$allPayments = $queryAllPayments->fetchAll(PDO::FETCH_ASSOC);

/*                                  */
/*      QUERY FOR ACTIVE LOANS      */
/*                                  */

$queryAllLoans = $conn->prepare("SELECT DISTINCT l.l_id AS xxx, b.firstname, b.middlename, b.lastname, l.*,
                                                 (SELECT MAX(p2.date)
                                                  FROM jai_db.payments as p2
                                                  WHERE p2.l_id = l.l_id) as latestpayment,
                                                  CONCAT(c.firstname, ' ', c.lastname) as collector
                                 FROM jai_db.loans as l
                                 LEFT JOIN jai_db.payments as p
                                 ON l.l_id = p.l_id
                                 INNER JOIN jai_db.borrowers as b
                                 ON l.b_id = b.b_id
                                 INNER JOIN jai_db.collectors as c
                                 ON l.c_id = c.c_id");
$queryAllLoans->execute();
$allLoans = $queryAllLoans->fetchAll(PDO::FETCH_ASSOC);

/*                                                 */
/*        PUSH PAYMENTS TO DIFFERENT ARRAYS        */
/*              AND DIFFERENT TOTALS               */
/*                                                 */

//New releases array
$newReleasesToday = [];
$newReleasesTomorrow = [];

//Closed loans today
$closedLoansTodayArray = [];

//Today array and collection
$todaysCollectionArray = [];

$todaysCollection = (float)0;

//Today passes and passamount
$todaysPassesArray = [];

$todaysPassesAmount = (float)0;

//Current week per day arrays, per day collection, and total current week collection 
$monCurrentWeekCollectionArray = [];
$tueCurrentWeekCollectionArray = [];
$wedCurrentWeekCollectionArray = [];
$thuCurrentWeekCollectionArray = [];
$friCurrentWeekCollectionArray = [];
$satCurrentWeekCollectionArray = [];

$totalCurrentWeekCollectionArray = [];

$monCurrentWeekCollection = (float)0;
$tueCurrentWeekCollection = (float)0;
$wedCurrentWeekCollection = (float)0;
$thuCurrentWeekCollection = (float)0;
$friCurrentWeekCollection = (float)0;
$satCurrentWeekCollection = (float)0;

$totalCurrentWeekCollection = (float)0;

//Last month array and total collection
$lastMonthCollectionArray = [];

$lastMonthCollection = (float)0;

//This month array and total collection
$currentMonthCollectionArray = [];

$currentMonthCollection = (float)0;

$kingCurrentMonthTotalCollection = (float)0;
$kingCurrentMonthCashCollection = (float)0;
$kingCurrentMonthGCashCollection = (float)0;

$carlCurrentMonthTotalCollection = (float)0;
$carlCurrentMonthCashCollection = (float)0;
$carlCurrentMonthGCashCollection = (float)0;

//Per month arrays and per month total collection of CURRENT YEAR
$janCollectionArray = [];
$febCollectionArray = [];
$marCollectionArray = [];
$aprCollectionArray = [];
$mayCollectionArray = [];
$junCollectionArray = [];
$julCollectionArray = [];
$augCollectionArray = [];
$sepCollectionArray = [];
$octCollectionArray = [];
$novCollectionArray = [];
$decCollectionArray = [];

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

//Current year array and total collection
$currentYearCollectionArray = [];

$currentYearCollection = (float)0;

//Active loan collection array and total
$activeLoansCollectionsArray = [];

$activeLoansCollections = (float)0;

//Collections and arrays per collector


/*                                */
/*      FOREACH ALL PAYMENTS      */
/*                                */
foreach ($allPayments as $i => $payment) {

    /*                            */
    /*     TODAY's collection     */
    /*                            */
    if ($payment['date'] == $dateToday && ($payment['type'] != 'Pass')) {
        array_push($todaysCollectionArray, $allPayments[$i]);
        $todaysCollection += $payment['amount'];
    }

    /*                        */
    /*     TODAY's passes     */
    /*                        */
    if ($payment['date'] == $dateToday && ($payment['type'] == 'Pass') && $payment['releasedate'] != $dateToday) {
        array_push($todaysPassesArray, $allPayments[$i]);
        $todaysPassesAmount += $payment['passamount'];
    }

    /*                                     */
    /*       Per day of CURRENT WEEK       */
    /*                                     */

    //Monday of current week
    if ($payment['date'] == $monCurrentWeek && ($payment['type'] != 'Pass')) {
        array_push($monCurrentWeekCollectionArray, $allPayments[$i]);
        array_push($totalCurrentWeekCollectionArray, $allPayments[$i]);
        $monCurrentWeekCollection += $payment['amount'];
        $totalCurrentWeekCollection += $payment['amount'];
    }

    //Tuesday of current week
    if ($payment['date'] == $tueCurrentWeek && ($payment['type'] != 'Pass')) {
        array_push($tueCurrentWeekCollectionArray, $allPayments[$i]);
        array_push($totalCurrentWeekCollectionArray, $allPayments[$i]);
        $tueCurrentWeekCollection += $payment['amount'];
        $totalCurrentWeekCollection += $payment['amount'];
    }

    //Wednesday of current week
    if ($payment['date'] == $wedCurrentWeek && ($payment['type'] != 'Pass')) {
        array_push($wedCurrentWeekCollectionArray, $allPayments[$i]);
        array_push($totalCurrentWeekCollectionArray, $allPayments[$i]);
        $wedCurrentWeekCollection += $payment['amount'];
        $totalCurrentWeekCollection += $payment['amount'];
    }

    //Thursday of current week
    if ($payment['date'] == $thuCurrentWeek && ($payment['type'] != 'Pass')) {
        array_push($thuCurrentWeekCollectionArray, $allPayments[$i]);
        array_push($totalCurrentWeekCollectionArray, $allPayments[$i]);
        $thuCurrentWeekCollection += $payment['amount'];
        $totalCurrentWeekCollection += $payment['amount'];
    }

    //Friday of current week
    if ($payment['date'] == $friCurrentWeek && ($payment['type'] != 'Pass')) {
        array_push($friCurrentWeekCollectionArray, $allPayments[$i]);
        array_push($totalCurrentWeekCollectionArray, $allPayments[$i]);
        $friCurrentWeekCollection += $payment['amount'];
        $totalCurrentWeekCollection += $payment['amount'];
    }

    //Saturday of current week
    if ($payment['date'] == $satCurrentWeek && ($payment['type'] != 'Pass')) {
        array_push($satCurrentWeekCollectionArray, $allPayments[$i]);
        array_push($totalCurrentWeekCollectionArray, $allPayments[$i]);
        $satCurrentWeekCollection += $payment['amount'];
        $totalCurrentWeekCollection += $payment['amount'];
    }

    /*                                   */
    /*      LAST MONTH's collection      */
    /*                                   */

    if (($payment['date'] >= $firstOfLastMonth) && ($payment['date'] <= $lastOfLastMonth) && ($payment['type'] != 'Pass')) {
        array_push($lastMonthCollectionArray, $allPayments[$i]);
        $lastMonthCollection += $payment['amount'];
    }

    /*                                   */
    /*      THIS MONTH's collection      */
    /*                                   */

    if (($payment['date'] >= $firstOfCurrentMonth) && ($payment['date'] <= $lastOfCurrentMonth) && ($payment['type'] != 'Pass')) {
        array_push($currentMonthCollectionArray, $allPayments[$i]);
        $currentMonthCollection += $payment['amount'];
    }

    //King Collection This Month
    if (($payment['date'] >= $firstOfCurrentMonth && $payment['date'] <= $lastOfCurrentMonth) && ($payment['c_id'] == 1)) {
        $kingCurrentMonthTotalCollection += $payment['amount'];
        if ($payment['type'] == 'Cash') {
            $kingCurrentMonthCashCollection += $payment['amount'];
        } elseif ($payment['type'] == 'GCash') {
            $kingCurrentMonthGCashCollection += $payment['amount'];
        }
    }

    //King Collection Incentives This Month
    $kingCurrentMonthIncentives = $kingCurrentMonthTotalCollection * 0.001;

    //Carl Collection This Month
    if (($payment['date'] >= $firstOfCurrentMonth && $payment['date'] <= $lastOfCurrentMonth) && ($payment['c_id'] == 2)) {
        $carlCurrentMonthTotalCollection += $payment['amount'];
        if ($payment['type'] == 'Cash') {
            $carlCurrentMonthCashCollection += $payment['amount'];
        } elseif ($payment['type'] == 'GCash') {
            $carlCurrentMonthGCashCollection += $payment['amount'];
        }
    }

    //Carl Collection Incentives This Month
    $carlCurrentMonthIncentives = $carlCurrentMonthTotalCollection * 0.001;

    /*                                   */
    /*     Per month of CURRENT YEAR     */
    /*                                   */

    //All January payments
    if (($payment['date'] >= date('Y-01-01')) && ($payment['date'] <= date('Y-m-t', strtotime('January'))) && ($payment['type'] != 'Pass')) {
        array_push($janCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $janCollection += $payment['amount'];
    }
    //All February payments
    if (($payment['date'] >= date('Y-02-01')) && ($payment['date'] <= date('Y-m-t', strtotime('February'))) && ($payment['type'] != 'Pass')) {
        array_push($febCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $febCollection += $payment['amount'];
    }
    //All March payments
    if (($payment['date'] >= date('Y-03-01')) && ($payment['date'] <= date('Y-m-t', strtotime('March'))) && ($payment['type'] != 'Pass')) {
        array_push($marCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $marCollection += $payment['amount'];
    }
    //All April payments
    if (($payment['date'] >= date('Y-04-01')) && ($payment['date'] <= date('Y-m-t', strtotime('April'))) && ($payment['type'] != 'Pass')) {
        array_push($aprCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $aprCollection += $payment['amount'];
    }
    //All May payments
    if (($payment['date'] >= date('Y-05-01')) && ($payment['date'] <= date('Y-m-t', strtotime('May'))) && ($payment['type'] != 'Pass')) {
        array_push($mayCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $mayCollection += $payment['amount'];
    }
    //All June payments
    if (($payment['date'] >= date('Y-06-01')) && ($payment['date'] <= date('Y-m-t', strtotime('June'))) && ($payment['type'] != 'Pass')) {
        array_push($junCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $junCollection += $payment['amount'];
    }
    //All July payments
    if (($payment['date'] >= date('Y-07-01')) && ($payment['date'] <= date('Y-m-t', strtotime('July'))) && ($payment['type'] != 'Pass')) {
        array_push($julCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $julCollection += $payment['amount'];
    }
    //All August payments
    if (($payment['date'] >= date('Y-08-01')) && ($payment['date'] <= date('Y-m-t', strtotime('August'))) && ($payment['type'] != 'Pass')) {
        array_push($augCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $augCollection += $payment['amount'];
    }
    //All September payments
    if (($payment['date'] >= date('Y-09-01')) && ($payment['date'] <= date('Y-m-t', strtotime('September'))) && ($payment['type'] != 'Pass')) {
        array_push($sepCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $sepCollection += $payment['amount'];
    }
    //All October payments
    if (($payment['date'] >= date('Y-10-01')) && ($payment['date'] <= date('Y-m-t', strtotime('October'))) && ($payment['type'] != 'Pass')) {
        array_push($octCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $octCollection += $payment['amount'];
    }
    //All November payments
    if (($payment['date'] >= date('Y-11-01')) && ($payment['date'] <= date('Y-m-t', strtotime('November'))) && ($payment['type'] != 'Pass')) {
        array_push($novCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $novCollection += $payment['amount'];
    }
    //All December payments
    if (($payment['date'] >= date('Y-12-01')) && ($payment['date'] <= date('Y-m-t', strtotime('December'))) && ($payment['type'] != 'Pass')) {
        array_push($decCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $decCollection += $payment['amount'];
    }

    //All active loans payments
    if ($payment['activeloan'] == 1) {
        array_push($activeLoansCollectionsArray, $allPayments[$i]);
        $activeLoansCollections += $payment['amount'];
    }

    //Current year collection
    $currentYearCollection = $janCollection + $febCollection + $marCollection + $aprCollection + $mayCollection + $junCollection + $julCollection + $augCollection + $sepCollection + $octCollection + $novCollection + $decCollection;
}

/*                                */
/*      For each active loan      */
/*                                */

$totalReleased = (float)0;
$totalPayable = (float)0;
$activeLoans = [];
$activeDailyLoans = [];
$activeWeeklyLoans = [];

foreach ($allLoans as $i => $loan) {
    if ($loan['activeloan'] == 1) {
        $totalReleased += $loan['amount'];
        $totalPayable += $loan['payable'];
        array_push($activeLoans, $allLoans[$i]);
        if ($loan['mode'] == 'Daily') {
            array_push($activeDailyLoans, $allLoans[$i]);
        } elseif ($loan['mode'] == 'Weekly') {
            array_push($activeWeeklyLoans, $allLoans[$i]);
        }
    }

    /*                            */
    /*     Closed loans TODAY     */
    /*                            */
    if ($loan['status'] == 'Closed' && $loan['latestpayment'] == $dateToday) {
        array_push($closedLoansTodayArray, $allLoans[$i]);
    }

    /*                            */
    /*        New releases        */
    /*                            */
    if ($loan['releasedate'] == date('Y-m-d')) {
        array_push($newReleasesToday, $allLoans[$i]);
    } elseif ($loan['releasedate'] == date('Y-m-d', strtotime('tomorrow'))) {
        array_push($newReleasesTomorrow, $allLoans[$i]);
    }
}

?>

<div class="content-container">

    <?php
    echo 'Overview for ' . date('Y');
    echo '<br>';
    echo '<br>';
    echo 'Active loans: ' . count($activeLoans);
    echo '<br>';
    echo '(' . count($activeDailyLoans) . ' daily, ' . count($activeWeeklyLoans) . ' weekly)';
    echo '<br>';
    echo '<br>';
    echo 'New release(s) today: ' . count($newReleasesToday);
    echo '<br>';
    foreach ($newReleasesToday as $i => $newReleaseToday) {
        echo '₱ ' . number_format($newReleaseToday['amount'], 2) . ' (' . $newReleaseToday['term'] . ', ' . $newReleaseToday['mode'] . ') - #' . $newReleaseToday['b_id'] . ' ' . $newReleaseToday['firstname'] . ' ' . $newReleaseToday['lastname'] . ' / ' . $newReleaseToday['collector'];
        echo '<br>';
    }
    echo '<br>';
    echo 'New releases tomorrow: ' . count($newReleasesTomorrow);
    echo '<br>';
    echo '<br>';
    echo 'Total collection per month';
    echo '<br>';
    echo 'Jan - ' . number_format($janCollection, 2);
    echo '<br>';
    echo 'Feb - ' . number_format($febCollection, 2);
    echo '<br>';
    echo 'Mar - ' . number_format($marCollection, 2);
    echo '<br>';
    echo 'Apr - ' . number_format($aprCollection, 2);
    echo '<br>';
    echo 'May - ' . number_format($mayCollection, 2);
    echo '<br>';
    echo 'Jun - ' . number_format($junCollection, 2);
    echo '<br>';
    echo 'Jul - ' . number_format($julCollection, 2);
    echo '<br>';
    echo 'Aug - ' . number_format($augCollection, 2);
    echo '<br>';
    echo 'Sep - ' . number_format($sepCollection, 2);
    echo '<br>';
    echo 'Oct - ' . number_format($octCollection, 2);
    echo '<br>';
    echo 'Nov - ' . number_format($novCollection, 2);
    echo '<br>';
    echo 'Dec - ' . number_format($decCollection, 2);
    echo '<br>';
    echo '<br>';
    echo 'total released (active): ' . number_format($totalReleased, 2);
    echo '<br>';
    echo 'total payables (active): ' . number_format($totalPayable, 2);
    echo '<br>';
    echo 'remaining payables (active): ' . number_format($totalPayable - $activeLoansCollections, 2);
    echo '<br>';
    echo 'collection (active): ' . number_format($activeLoansCollections, 2);
    echo '<br>';
    echo '<br>';
    echo 'total collection today: ' . number_format($todaysCollection, 2);
    echo '<br>';
    echo number_format(count($todaysCollectionArray)) . ' payments  and ' . number_format(count($todaysPassesArray)) . ' passes today.';
    echo '<br>';
    echo '<br>';
    echo number_format(count($closedLoansTodayArray)) . ' loan(s) closed today.';
    echo '<br>';
    echo '<br>';
    echo 'total collection this week: ' . number_format($totalCurrentWeekCollection, 2);
    echo '<br>';
    echo '<br>';
    echo 'total collection last month: ' . number_format($lastMonthCollection, 2);
    echo '<br>';
    echo '<br>';
    echo 'total collection this month: ' . number_format($currentMonthCollection, 2);
    echo '<br>';
    echo '<br>';
    echo 'king total collection this month: ' . number_format($kingCurrentMonthTotalCollection, 2);
    echo '<br>';
    echo 'king cash collection this month: ' . number_format($kingCurrentMonthCashCollection, 2);
    echo '<br>';
    echo 'king gcash collection this month: ' . number_format($kingCurrentMonthGCashCollection, 2);
    echo '<br>';
    echo 'king collection incentive this month: ' . number_format($kingCurrentMonthIncentives, 2);
    echo '<br>';
    echo '<br>';
    echo 'carl total collection this month: ' . number_format($carlCurrentMonthTotalCollection, 2);
    echo '<br>';
    echo 'carl cash collection this month: ' . number_format($carlCurrentMonthCashCollection, 2);
    echo '<br>';
    echo 'carl gcash collection this month: ' . number_format($carlCurrentMonthGCashCollection, 2);
    echo '<br>';
    echo 'carl collection incentive this month: ' . number_format($carlCurrentMonthIncentives, 2);
    echo '<br>';
    echo '<br>';
    echo 'total collection this year: ' . number_format($currentYearCollection, 2);
    echo '<br>';
    echo '<br>';

    echo '<br>';
    echo number_format(count($totalCurrentWeekCollectionArray)) . ' payments made this week.';
    echo '<br>';
    echo number_format(count($lastMonthCollectionArray)) . ' payments made last month.';
    echo '<br>';
    echo number_format(count($currentMonthCollectionArray)) . ' payments made this month.';
    echo '<br>';
    echo number_format(count($currentYearCollectionArray)) . ' payments made this year.';
    echo '<br>';
    echo '<br>';
    echo 'monday collection: ' . number_format($monCurrentWeekCollection, 2);
    echo '<br>';
    echo 'tuesday collection: ' . number_format($tueCurrentWeekCollection, 2);
    echo '<br>';
    echo 'wednesday collection: ' . number_format($wedCurrentWeekCollection, 2);
    echo '<br>';
    echo 'thursday collection: ' . number_format($thuCurrentWeekCollection, 2);
    echo '<br>';
    echo 'friday collection: ' . number_format($friCurrentWeekCollection, 2);
    echo '<br>';
    echo 'saturday collection: ' . number_format($satCurrentWeekCollection, 2);
    // echo date('e');
    echo '<br>';

    // echo '<pre>';
    // var_dump($closedLoansTodayArray);

    //TEST
    /* $begin = new DateTime($monCurrentWeek);
    $end = new DateTime($satCurrentWeek);
    $end->setTime(0, 0, 1);

    $dateRange = new DatePeriod($begin, new DateInterval('P1D'), $end);

    foreach ($dateRange as $date) {
        echo '<br>';
        echo $date->format('Y-m-d');
    } */
    //END TEST


    ?>

</div>