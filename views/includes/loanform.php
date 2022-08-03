<?php

//// SELECT BORROWERS WITHOUT ACTIVE LOAN
$statementBorrower = $conn->prepare("SELECT b_id, firstname, middlename, lastname
                                     FROM jai_db.borrowers as b
                                     WHERE activeloan = 0
                                     ORDER BY b_id ASC;");
$statementBorrower->execute();
$borrowers = $statementBorrower->fetchAll(PDO::FETCH_ASSOC);

//// SELECT AMOUNTS FROM RATES TABLE
$statementAmount = $conn->prepare("SELECT 
                             DISTINCT amount
                             FROM jai_db.rates as r
                             ORDER BY amount ASC");
$statementAmount->execute();
$amounts = $statementAmount->fetchAll(PDO::FETCH_ASSOC);

//// SELECT MODES FROM RATES TABLE
$statementMode = $conn->prepare("SELECT 
                             DISTINCT mode
                             FROM jai_db.rates as r");
$statementMode->execute();
$modes = $statementMode->fetchAll(PDO::FETCH_ASSOC);

//// SELECT TERMS FROM RATES TABLE
$statementTerm = $conn->prepare("SELECT 
                             DISTINCT term
                             FROM jai_db.rates as r");
$statementTerm->execute();
$terms = $statementTerm->fetchAll(PDO::FETCH_ASSOC);

//// SELECT COLLECTORS FROM COLLECTORS TABLE
$statementCollector = $conn->prepare("SELECT * FROM jai_db.collectors as c");
$statementCollector->execute();
$collectors = $statementCollector->fetchAll(PDO::FETCH_ASSOC);