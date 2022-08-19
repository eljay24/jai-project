<?php

require "../../views/includes/dbconn.php";


if (isset($_POST['action'])) {

    $b_id = $_POST['b_id'];

    $statementPayable = $conn->prepare("SELECT l.payable, l.l_id
                                        FROM jai_db.borrowers as b
                                        INNER JOIN jai_db.loans as l
                                        ON b.b_id = l.b_id 
                                        INNER JOIN jai_db.collectors as c
                                        ON l.c_id = c.c_id
                                        WHERE b.b_id = :b_id AND (b.isdeleted = 0) AND (l.amount IS NOT NULL) AND (l.status = 'Active')");
    $statementPayable->bindValue(':b_id', $b_id);
    $statementPayable->execute();
    $payable = $statementPayable->fetch(PDO::FETCH_ASSOC);

    $statementPayments = $conn->prepare("SELECT SUM(p.amount) as paymentsum
                                FROM jai_db.payments as p
                                WHERE p.l_id = :l_id");
    $statementPayments->bindValue(':l_id', $payable['l_id']);
    $statementPayments->execute();
    $payments = $statementPayments->fetch(PDO::FETCH_ASSOC);

    $remainingBalance = $payable['payable'] - $payments['paymentsum'];

    $statement = $conn->prepare("SELECT 
                                b.b_id, b.firstname as bfname, b.middlename as bmname, b.lastname as blname, l.l_id, l.amount, l.payable, (:remainingbalance) as balance, l.amortization, l.mode, l.term, l.c_id,
                                c.firstname as cfname, c.middlename as cmname, c.lastname as clname
                                FROM jai_db.borrowers as b
                                INNER JOIN jai_db.loans as l
                                ON b.b_id = l.b_id 
                                INNER JOIN jai_db.collectors as c
                                ON l.c_id = c.c_id
                                WHERE b.b_id = :b_id AND (b.isdeleted = 0) AND (l.amount IS NOT NULL) AND (l.status = 'Active')");

    $statement->bindValue(':remainingbalance', $remainingBalance);
    $statement->bindValue(':b_id', $b_id);
    $statement->execute();
    $borrower = $statement->fetchAll(PDO::FETCH_ASSOC);

    

    echo json_encode($borrower);
}
