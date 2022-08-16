<?php

require_once "../../views/includes/dbconn.php";
require "../../views/includes/fpdf.php";

$c_id = $_GET['c_id'];

$statement = $conn->prepare("SELECT DISTINCT l.l_id, CONCAT(b.lastname, ', ', b.firstname, ' ', b.middlename) as name,
                                             l.releasedate, l.duedate, l.amount, l.payable, l.amortization, l.term, l.mode,
 
                                             (l.payable - (SELECT SUM(p2.amount) as totalpayment
                                             FROM jai_db.payments as p2
                                             WHERE p2.l_id = l.l_id)) as outstandingbalance,
                                             
                                             (l.payable - ((SELECT COUNT(*)
                                             FROM jai_db.payments as p3
                                             WHERE p3.l_id = l.l_id) * l.amortization)) as SCB,
                                             
                                             ((l.payable - (SELECT SUM(p2.amount) as totalpayment
                                             FROM jai_db.payments as p2
                                             WHERE p2.l_id = l.l_id )) - (l.payable - ((SELECT COUNT(*)
                                             FROM jai_db.payments as p3
                                             WHERE p3.l_id = l.l_id) * l.amortization))) as arrears,
                                             
                                             (SELECT MAX(date)
                                             FROM jai_db.payments as p1
                                             WHERE p1.l_id = l.l_id) as lasttransaction

                             FROM jai_db.loans as l
                             INNER JOIN jai_db.borrowers as b
                             ON b.b_id = l.b_id
                             INNER JOIN jai_db.payments as p
                             ON l.l_id = p.l_id
                             WHERE l.c_id = :c_id
                             ORDER BY name ASC");
$statement->bindValue(':c_id', $c_id);
$statement->execute();
$accounts = $statement->fetchAll(PDO::FETCH_ASSOC);

$updatedAccs = [];
$inArrearsAccs = [];
$pastDueAccs = [];

foreach ($accounts as $i => $account) {
    if ($account['arrears'] < 0) {
        array_push($updatedAccs, $accounts[$i]);
    } elseif ($account['arrears'] >= 0) {
        array_push($inArrearsAccs, $accounts[$i]);
    }
}

// echo '<pre>';
// var_dump($updatedAccs);
