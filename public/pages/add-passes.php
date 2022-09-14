<?php

require_once "../../views/includes/dbconn.php";
require_once "../../views/partials/header.php";

$mon = date('Y-m-d', strtotime('monday this week'));
$tue = date('Y-m-d', strtotime('tuesday this week'));
$wed = date('Y-m-d', strtotime('wednesday this week'));
$thu = date('Y-m-d', strtotime('thursday this week'));
$fri = date('Y-m-d', strtotime('friday this week'));
$sat = date('Y-m-d', strtotime('saturday this week'));

$activeLoansQuery = $conn->prepare("SELECT DISTINCT l.l_id, l.b_id, l.c_id, l.mode, l.amortization, (SELECT MAX(date)
                                                                     FROM jai_db.payments as p1
                                                                     WHERE (p1.l_id = l.l_id) AND (p1.type = 'Cash' OR p1.type = 'GCash')) as lasttransaction,
                                                                     (SELECT MAX(date)
                                                                     FROM jai_db.payments as p2
                                                                     WHERE (p2.l_id = l.l_id) AND (p2.type = 'Pass')) as lastpass
                                    FROM jai_db.loans as l
                                    INNER JOIN jai_db.borrowers as b
                                    ON b.b_id = l.b_id
                                    LEFT JOIN jai_db.payments as p
                                    ON l.l_id = p.l_id
                                    INNER JOIN jai_db.collectors as c
                                    ON l.c_id = c.c_id
                                    WHERE l.activeloan = 1
                                    ORDER BY b.b_id ASC");
$activeLoansQuery->execute();
$activeLoans = $activeLoansQuery->fetchAll(PDO::FETCH_ASSOC);

// echo '<pre>';
// var_dump($activeLoans);

$dailyPass = [];
$weeklyPass = [];

foreach ($activeLoans as $i => $activeLoan) {
    //Will run only on Weekdays and Saturdays
    if (date('D') != 'Sun') {
        /*               */
        /*     DAILY     */
        /*               */
        if ($activeLoan['mode'] == 'Daily') {
            //Check if no payment today AND if pass already entered AND if last transaction is not null (Null = new loan/no payments yet)
            if ($activeLoan['lasttransaction'] != date('Y-m-d') && $activeLoan['lastpass'] != date('Y-m-d') && !is_null($activeLoan['lasttransaction'])) {
                array_push($dailyPass, $activeLoans[$i]);
            }
        }
    }
    //Will run only on Saturdays
    if (date('D') != 'Sat') { //TESTING (Set condition to == for actual use)
        /*                */
        /*     WEEKLY     */
        /*                */
        if ($activeLoan['mode'] == 'Weekly') {
            //Check if no payment this week AND if pass already entered
            if (
                ($activeLoan['lasttransaction'] != $mon || $activeLoan['lasttransaction'] != $tue || $activeLoan['lasttransaction'] != $wed || $activeLoan['lasttransaction'] != $thu || $activeLoan['lasttransaction'] != $fri || $activeLoan['lasttransaction'] != $sat)
                &&
                ($activeLoan['lastpass'] != $mon || $activeLoan['lastpass'] != $tue || $activeLoan['lastpass'] != $wed || $activeLoan['lastpass'] != $thu || $activeLoan['lastpass'] != $fri || $activeLoan['lastpass'] != $sat)
                && 
                (!is_null($activeLoan['lasttransaction']))
            ) {
                array_push($weeklyPass, $activeLoans[$i]);
            }
        }
    }
}

//INSERT PASS FOR TODAY TO INDIVIDUAL LOANS
foreach ($dailyPass as $i => $dailyP) {

}

//INSERT PASS FOR THIS WEEK TO INDIVIDUAL LOANS
foreach ($weeklyPass as $i => $weeklyP) {

}

?>


<div class="content-container">

    <?php
    echo '<pre>';
    var_dump($dailyPass);
    // echo '<br>';
    // var_dump($weeklyPass);

    // echo 'Passes today (From Daily): ' . count($dailyPass);
    // echo '<br>';
    // echo 'Passes today (From Weekly): ' . count($weeklyPass);
    ?>

</div>