<?php

require_once "../../views/includes/dbconn.php";
require_once "../../views/partials/header.php";

$mon = date('Y-m-d', strtotime('monday this week'));
$tue = date('Y-m-d', strtotime('tuesday this week'));
$wed = date('Y-m-d', strtotime('wednesday this week'));
$thu = date('Y-m-d', strtotime('thursday this week'));
$fri = date('Y-m-d', strtotime('friday this week'));
$sat = date('Y-m-d', strtotime('saturday this week'));

$activeLoansQuery = $conn->prepare("SELECT DISTINCT l.l_id, l.b_id, l.c_id, l.mode, l.amortization, l.releasedate, (SELECT MAX(date)
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

//PUSH TO DAILY OR WEEKLY

foreach ($activeLoans as $i => $activeLoan) {

    if ($activeLoan['mode'] == 'Daily') {
        //Check if no payment today AND if pass already entered AND if last transaction is not null (Null = new loan/no payments yet)
        if (($activeLoan['lasttransaction'] != date('Y-m-d') && $activeLoan['lastpass'] != date('Y-m-d') /* && !is_null($activeLoan['lasttransaction']) */ && $activeLoan['releasedate'] != date('Y-m-d'))
            ||
            //Below condition checks if new loan AND past the release date
            (is_null($activeLoan['lasttransaction']) && is_null($activeLoan['lastpass']) && $activeLoan['releasedate'] != date('Y-m-d'))
        ) {
            array_push($dailyPass, $activeLoans[$i]);
        }
    } elseif ($activeLoan['mode'] == 'Weekly') {
        //Check that no payments and passes entered this week
        if (
            (($activeLoan['lasttransaction'] != $mon && $activeLoan['lasttransaction'] != $tue && $activeLoan['lasttransaction'] != $wed && $activeLoan['lasttransaction'] != $thu && $activeLoan['lasttransaction'] != $fri && $activeLoan['lasttransaction'] != $sat)
                &&
                ($activeLoan['lastpass'] != $mon && $activeLoan['lastpass'] != $tue && $activeLoan['lastpass'] != $wed && $activeLoan['lastpass'] != $thu && $activeLoan['lastpass'] != $fri && $activeLoan['lastpass'] != $sat)
                /* &&
            (is_null($activeLoan['lasttransaction'])) */)
            ||
            // Below condition checks if new loan AND past the release date
            (is_null($activeLoan['lasttransaction']) && is_null($activeLoan['lastpass']) && $activeLoan['releasedate'] != date('Y-m-d'))
        ) {
            array_push($weeklyPass, $activeLoans[$i]);
        }
    }
}

/*               */
/*     DAILY     */
/*               */

//INSERT PASSES FOR TODAY (If past 5:30PM)
//Will run only Monday - Saturday
if ((date('D') != 'Sun') && (date('H:i:s') > date('17:30:00'))) {

    foreach ($dailyPass as $i => $dailyP) {
        $newPassQuery = $conn->prepare("INSERT INTO jai_db.payments
                                    (b_id, l_id, c_id, amount, passamount, type, date)
                                    VALUES
                                    (:b_id, :l_id, :c_id, :amount, :passamount, :type, :date)");

        $newPassQuery->bindValue(':b_id', $dailyP['b_id']);
        $newPassQuery->bindValue(':l_id', $dailyP['l_id']);
        $newPassQuery->bindValue(':c_id', $dailyP['c_id']);
        $newPassQuery->bindValue(':amount', 0);
        $newPassQuery->bindValue(':passamount', $dailyP['amortization']);
        $newPassQuery->bindValue(':type', 'Pass');
        $newPassQuery->bindValue(':date', date('Y-m-d'));

        $newPassQuery->execute();
    }
}

/*                */
/*     WEEKLY     */
/*                */

// INSERT PASS FOR THIS WEEK (If past 5:30PM)
// Will run only on Saturdays
if ((date('D') == 'Sat') && (date('H:i:s') > date('17:30:00'))) {

    foreach ($weeklyPass as $i => $weeklyP) {
        $newPassQuery = $conn->prepare("INSERT INTO jai_db.payments
                                        (b_id, l_id, c_id, amount, passamount, type, date)
                                        VALUES
                                        (:b_id, :l_id, :c_id, :amount, :passamount, :type, :date)");

        $newPassQuery->bindValue(':b_id', $weeklyP['b_id']);
        $newPassQuery->bindValue(':l_id', $weeklyP['l_id']);
        $newPassQuery->bindValue(':c_id', $weeklyP['c_id']);
        $newPassQuery->bindValue(':amount', 0);
        $newPassQuery->bindValue(':passamount', $weeklyP['amortization']);
        $newPassQuery->bindValue(':type', 'Pass');
        $newPassQuery->bindValue(':date', date('Y-m-d'));

        $newPassQuery->execute();
    }
}

?>


<div class="content-container">

    <?php
    echo '<pre>';
    echo 'Passes today';
    echo '<br>';
    echo 'daily: ';
    echo '<br>';
    echo count($dailyPass);
    echo '<br>';
    echo 'weekly: ';
    echo '<br>';
    echo count($weeklyPass);
    echo '<br>';
    echo '<br>';
    echo '<br>';

    echo '<pre>';
    var_dump($dailyPass);
    echo '<br>';
    echo '<br>';
    var_dump($weeklyPass);

    ?>

</div>