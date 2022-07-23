<?php

require "../../dbconn.php";


if (isset($_POST['b_id'])) {

    $b_id = $_POST['b_id'];

    $statement = $conn->prepare("SELECT 
                                b.b_id, b.firstname as bfname, b.middlename as bmname, b.lastname as blname, l.l_id, l.amount, l.payable, l.balance, l.amortization, l.mode, l.term, l.c_id,
                                c.firstname as cfname, c.middlename as cmname, c.lastname as clname
                                FROM jai_db.borrowers as b
                                INNER JOIN jai_db.loans as l
                                ON b.b_id = l.b_id 
                                INNER JOIN jai_db.collectors as c
                                ON l.c_id = c.c_id
                                WHERE b.b_id = :b_id AND (b.isdeleted = 0) AND (l.amount IS NOT NULL)");

    $statement->bindValue(':b_id', $b_id);
    $statement->execute();
    $borrower = $statement->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($borrower);
}
