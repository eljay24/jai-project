<?php

require "../../dbconn.php";


if (isset($_POST['b_id'])) {

    $b_id = $_POST['b_id'];

    $statement = $conn->prepare("SELECT 
                                b.b_id, b.firstname, b.middlename, b.lastname, l.l_id, l.amount, l.payable, l.balance, l.amortization, l.mode, l.term
                                FROM jai_db.borrowers as b
                                INNER JOIN jai_db.loans as l
                                ON b.b_id = l.b_id 
                                WHERE b.b_id = :b_id AND (b.isdeleted = 0) AND (l.amount IS NOT NULL)");

    $statement->bindValue(':b_id', $b_id);
    $statement->execute();
    $borrower = $statement->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($borrower);
}
