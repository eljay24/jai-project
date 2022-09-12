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

$queryAllPayments = $conn->prepare("SELECT p.*, l.activeloan
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

$queryAllLoans = $conn->prepare("SELECT *
                                 FROM jai_db.loans as l");
$queryAllLoans->execute();
$allLoans = $queryAllLoans->fetchAll(PDO::FETCH_ASSOC);

/*                                                 */
/*        PUSH PAYMENTS TO DIFFERENT ARRAYS        */
/*                                                 */

//Today array and collection
$todaysCollectionArray = [];

$todaysCollection = (float)0;

//Current week per day arrays and per day collection
$monCurrentWeekCollectionArray = [];
$tueCurrentWeekCollectionArray = [];
$wedCurrentWeekCollectionArray = [];
$thuCurrentWeekCollectionArray = [];
$friCurrentWeekCollectionArray = [];
$satCurrentWeekCollectionArray = [];

$monCurrentWeekCollection = (float)0;
$tueCurrentWeekCollection = (float)0;
$wedCurrentWeekCollection = (float)0;
$thuCurrentWeekCollection = (float)0;
$friCurrentWeekCollection = (float)0;
$satCurrentWeekCollection = (float)0;

//Last month array and total collection
$lastMonthCollectionArray = [];

$lastMonthCollection = (float)0;

//This month array and total collection
$currentMonthCollectionArray = [];

$currentMonthCollection = (float)0;

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

foreach ($allPayments as $i => $payment) {

    /*                            */
    /*     TODAY's collection     */
    /*                            */
    if ($payment['date'] == $dateToday) {
        array_push($todaysCollectionArray, $allPayments[$i]);
        $todaysCollection += $payment['amount'];
    }

    /*                                     */
    /*       Per day of CURRENT WEEK       */
    /*                                     */

    //Monday of current week
    if (($payment['date'] == $monCurrentWeek)) {
        array_push($monCurrentWeekCollectionArray, $allPayments[$i]);
        $monCurrentWeekCollection += $payment['amount'];
    }

    //Tuesday of current week
    if (($payment['date'] == $tueCurrentWeek)) {
        array_push($tueCurrentWeekCollectionArray, $allPayments[$i]);
        $tueCurrentWeekCollection += $payment['amount'];
    }

    //Wednesday of current week
    if (($payment['date'] == $wedCurrentWeek)) {
        array_push($wedCurrentWeekCollectionArray, $allPayments[$i]);
        $wedCurrentWeekCollection += $payment['amount'];
    }

    //Thursday of current week
    if (($payment['date'] == $thuCurrentWeek)) {
        array_push($thuCurrentWeekCollectionArray, $allPayments[$i]);
        $thuCurrentWeekCollection += $payment['amount'];
    }

    //Friday of current week
    if (($payment['date'] == $friCurrentWeek)) {
        array_push($friCurrentWeekCollectionArray, $allPayments[$i]);
        $friCurrentWeekCollection += $payment['amount'];
    }

    //Saturday of current week
    if (($payment['date'] == $satCurrentWeek)) {
        array_push($satCurrentWeekCollectionArray, $allPayments[$i]);
        $satCurrentWeekCollection += $payment['amount'];
    }

    /*                                   */
    /*      LAST MONTH's collection      */
    /*                                   */

    if (($payment['date'] >= $firstOfLastMonth) && ($payment['date'] <= $lastOfLastMonth)) {
        array_push($lastMonthCollectionArray, $allPayments[$i]);
        $lastMonthCollection += $payment['amount'];
    }

    /*                                   */
    /*      THIS MONTH's collection      */
    /*                                   */

    if (($payment['date'] >= $firstOfCurrentMonth) && ($payment['date'] <= $lastOfCurrentMonth)) {
        array_push($currentMonthCollectionArray, $allPayments[$i]);
        $currentMonthCollection += $payment['amount'];
    }

    /*                                   */
    /*     Per month of CURRENT YEAR     */
    /*                                   */

    //All January payments
    if (($payment['date'] >= date('Y-01-01')) && ($payment['date'] <= date('Y-m-t', strtotime('January')))) {
        array_push($janCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $janCollection += $payment['amount'];
    }
    //All February payments
    if (($payment['date'] >= date('Y-02-01')) && ($payment['date'] <= date('Y-m-t', strtotime('February')))) {
        array_push($febCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $febCollection += $payment['amount'];
    }
    //All March payments
    if (($payment['date'] >= date('Y-03-01')) && ($payment['date'] <= date('Y-m-t', strtotime('March')))) {
        array_push($marCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $marCollection += $payment['amount'];
    }
    //All April payments
    if (($payment['date'] >= date('Y-04-01')) && ($payment['date'] <= date('Y-m-t', strtotime('April')))) {
        array_push($aprCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $aprCollection += $payment['amount'];
    }
    //All May payments
    if (($payment['date'] >= date('Y-05-01')) && ($payment['date'] <= date('Y-m-t', strtotime('May')))) {
        array_push($mayCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $mayCollection += $payment['amount'];
    }
    //All June payments
    if (($payment['date'] >= date('Y-06-01')) && ($payment['date'] <= date('Y-m-t', strtotime('June')))) {
        array_push($junCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $junCollection += $payment['amount'];
    }
    //All July payments
    if (($payment['date'] >= date('Y-07-01')) && ($payment['date'] <= date('Y-m-t', strtotime('July')))) {
        array_push($julCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $julCollection += $payment['amount'];
    }
    //All August payments
    if (($payment['date'] >= date('Y-08-01')) && ($payment['date'] <= date('Y-m-t', strtotime('August')))) {
        array_push($augCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $augCollection += $payment['amount'];
    }
    //All September payments
    if (($payment['date'] >= date('Y-09-01')) && ($payment['date'] <= date('Y-m-t', strtotime('September')))) {
        array_push($sepCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $sepCollection += $payment['amount'];
    }
    //All October payments
    if (($payment['date'] >= date('Y-10-01')) && ($payment['date'] <= date('Y-m-t', strtotime('October')))) {
        array_push($octCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $octCollection += $payment['amount'];
    }
    //All November payments
    if (($payment['date'] >= date('Y-11-01')) && ($payment['date'] <= date('Y-m-t', strtotime('November')))) {
        array_push($novCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $novCollection += $payment['amount'];
    }
    //All December payments
    if (($payment['date'] >= date('Y-12-01')) && ($payment['date'] <= date('Y-m-t', strtotime('December')))) {
        array_push($decCollectionArray, $allPayments[$i]);
        array_push($currentYearCollectionArray, $allPayments[$i]);
        $decCollection += $payment['amount'];
    }

    //Current year collection
    $currentYearCollection = $janCollection + $febCollection + $marCollection + $aprCollection + $mayCollection + $junCollection + $julCollection + $augCollection + $sepCollection + $octCollection + $novCollection + $decCollection;
}

/*                                */
/*      For each active loan      */
/*                                */

$totalReleased = (float)0;
$totalPayable = (float)0;
$activeLoans = (int)0;

foreach ($allLoans as $i => $loan) {
    if ($loan['activeloan'] == 1) {
        $totalReleased += $loan['amount'];
        $totalPayable += $loan['payable'];
        $activeLoans++;
    }
}

?>

<div class="content-container">

    <?php
    echo 'Overview for ' . date('Y');
    echo '<br>';
    echo 'Active loans: ' . $activeLoans;
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
    echo 'total released (active): ' . number_format($totalReleased, 2);
    echo '<br>';
    echo 'total collection this year: ' . number_format($currentYearCollection, 2);
    echo '<br>';
    echo 'total payable remaining: ' . number_format($totalPayable - $currentYearCollection, 2);
    echo '<br>';
    echo 'total collection last month: ' . number_format($lastMonthCollection, 2);
    echo '<br>';
    echo 'total collection this month: ' . number_format($currentMonthCollection, 2);
    echo '<br>';
    echo 'total payments this year: ' . count($currentYearCollectionArray);
    ?>

</div>