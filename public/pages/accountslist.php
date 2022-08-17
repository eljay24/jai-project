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
                                             WHERE p1.l_id = l.l_id) as lasttransaction,

                                             CONCAT(c.lastname, ', ', c.firstname) as collector

                             FROM jai_db.loans as l
                             INNER JOIN jai_db.borrowers as b
                             ON b.b_id = l.b_id
                             INNER JOIN jai_db.payments as p
                             ON l.l_id = p.l_id
                             INNER JOIN jai_db.collectors as c
                             ON l.c_id = c.c_id
                             WHERE l.c_id = :c_id AND l.activeloan = 1
                             ORDER BY name ASC");
$statement->bindValue(':c_id', $c_id);
$statement->execute();
$accounts = $statement->fetchAll(PDO::FETCH_ASSOC); //13 columns

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

// MARGIN PER SIDE = 10mm
// EXACT SIZE USED BELOW = 13 x 8.5 inches /330.2 x 215.9 millimeters (LANDSCAPE)
// PRINTABLE AREA 330.2 - (10 * 2) = 310.2mm

class PDF extends FPDF
{
    function Header()
    {
        global $accounts;

        if ($accounts) {
            $this->SetFont('Courier', '', 10);
            $this->Cell(103.4, 4, 'Page ' . $this->PageNo() . " of {pages}", 0, 0, 'L');
            $this->SetFont('Courier', 'B', 14);
            $this->Cell(103.4, 4, 'JAI FAIR LOAN', 0, 0, 'C');
            $this->SetFont('Courier', '', 10);
            $this->Cell(103.4, 4, 'Date: ' . date('Y-m-d'), 0, 1, 'R');
            $this->Cell(103.4, 8, '', 0, 0, 'C');
            $this->Cell(103.4, 8, 'List of Active Loans by Collector', 0, 0, 'C');
            $this->Cell(103.4, 8, 'Time: ' . date('g:i:s A'), 0, 1, 'R');
            $this->Cell(310.2, 4, 'Collector: ' . $accounts[0]['collector'], 0, 1, 'L');
            $this->Cell(310.2, 4, '', 0, 1);

            $this->SetFont('Arial', 'B', 8);
            $this->Cell(10.86, 6, '#', 'LTB', 0);
            $this->Cell(42.86, 6, 'Name', 'TB', 0);
            $this->Cell(23.86, 6, 'Release Date', 'TB', 0);
            $this->Cell(20.86, 6, 'Due Date', 'TB', 0);
            $this->Cell(20.86, 6, 'Amount', 'TB', 0, 'R');
            $this->Cell(23.86, 6, 'Payable', 'TB', 0, 'R');
            $this->Cell(23.86, 6, 'Amortization', 'TB', 0, 'R');
            $this->Cell(23.86, 6, 'Term', 'TB', 0, 'R');
            $this->Cell(23.86, 6, 'Mode', 'TB', 0, 'R');
            $this->Cell(23.86, 6, 'Outstanding Bal.', 'TB', 0, 'R');
            $this->Cell(23.86, 6, 'SCB', 'TB', 0, 'R');
            $this->Cell(23.86, 6, 'Arrears', 'TB', 0, 'R');
            $this->Cell(23.86, 6, 'Last Transaction', 'RTB', 1, 'R');
        } else {
            $this->SetFont('Courier', '', 10);
            $this->Cell(103.4, 4, 'Page ' . $this->PageNo() . " of {pages}", 0, 0, 'L');
            $this->SetFont('Courier', 'B', 14);
            $this->Cell(103.4, 4, 'JAI FAIR LOAN', 0, 0, 'C');
            $this->SetFont('Courier', '', 10);
            $this->Cell(103.4, 4, 'Date: ' . date('Y-m-d'), 0, 1, 'R');
            $this->Cell(103.4, 8, '', 0, 0, 'C');
            $this->Cell(103.4, 8, 'List of Accounts by Collector', 0, 0, 'C');
            $this->Cell(103.4, 8, 'Time: ' . date('g:i:s A'), 0, 1, 'R');
        }
    }
    function Footer()
    {
    }
}

$pdf = new PDF('L', 'mm', array(330.2, 215.9));
if ($accounts) {
    $pdf->SetTitle('JAI Accounts List - ' . $accounts[0]['collector']);
} else {
    $pdf->SetTitle('JAI Invalid Accounts List');
}
$pdf->AliasNbPages('{pages}');
$pdf->AddPage();

if ($accounts) {
    $pdf->SetFont('Arial', '', 8);
    /* ----- UPDATED ACCOUNTS ----- */
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(310.2, 6, 'UPDATED ACCOUNTS', 'LR', 1);
    $updatedAccsTotalOutBal = (float)0;
    $updatedAccsTotalSCB = (float)0;
    $updatedAccsTotalArrears = (float)0;
    $pdf->SetFont('Arial', '', 8);
    foreach ($updatedAccs as $i => $updatedAcc) {
        $pdf->Cell(10.86, 5, $updatedAcc['l_id'], 'LB', 0);
        $pdf->Cell(42.86, 5, $updatedAcc['name'], 'B', 0);
        $pdf->Cell(23.86, 5, $updatedAcc['releasedate'], 'B', 0);
        $pdf->Cell(20.86, 5, $updatedAcc['duedate'], 'B', 0);
        $pdf->Cell(20.86, 5, number_format($updatedAcc['amount'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, number_format($updatedAcc['payable'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, number_format($updatedAcc['amortization'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, $updatedAcc['term'], 'B', 0, 'R');
        $pdf->Cell(23.86, 5, $updatedAcc['mode'], 'B', 0, 'R');
        $pdf->Cell(23.86, 5, number_format($updatedAcc['outstandingbalance'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, number_format($updatedAcc['SCB'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, number_format($updatedAcc['arrears'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, $updatedAcc['lasttransaction'], 'RB', 1, 'R');

        $updatedAccsTotalOutBal += $updatedAcc['outstandingbalance'];
        $updatedAccsTotalSCB += $updatedAcc['SCB'];
        $updatedAccsTotalArrears += $updatedAcc['arrears'];
    }

    /* ----- UPDATED ACCOUNTS SUMMARY ----- */
    $pdf->Cell(310.2, 2, '', 0, 1);
    $pdf->Cell(50.4, 6, 'In Arrears Accounts Summary', 'B', 0, 'C');
    $pdf->Cell(31.2, 6, 'Total Accounts:', 'B', 0, 'R');
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(31.2, 6, count($updatedAccs), 'B', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(37.2, 6, 'Total Outstanding Balance:', 'B', 0);
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(37.2, 6, number_format($updatedAccsTotalOutBal, 2), 'B', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(17.2, 6, 'Total SCB:', 'B', 0);
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(45.2, 6, number_format($updatedAccsTotalSCB, 2), 'B', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(20.2, 6, 'Total Arrears:', 'B', 0);
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(42.2, 6, number_format($updatedAccsTotalArrears, 2), 'B', 1);
    $pdf->Cell(310.2, 5, '', 0, 1);


    /* ----- IN ARREARS ACCOUNTS ----- */
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(310.2, 6, 'IN ARREARS ACCOUNTS', 'LR', 1);
    $inArrearsAccsTotalOutBal = (float)0;
    $inArrearsAccsTotalSCB = (float)0;
    $inArrearsAccsTotalArrears = (float)0;
    $pdf->SetFont('Arial', '', 8);
    foreach ($inArrearsAccs as $i => $inArrearsAcc) {
        $pdf->Cell(10.86, 5, $inArrearsAcc['l_id'], 'LB', 0);
        $pdf->Cell(42.86, 5, $inArrearsAcc['name'], 'B', 0);
        $pdf->Cell(23.86, 5, $inArrearsAcc['releasedate'], 'B', 0);
        $pdf->Cell(20.86, 5, $inArrearsAcc['duedate'], 'B', 0);
        $pdf->Cell(20.86, 5, number_format($inArrearsAcc['amount'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, number_format($inArrearsAcc['payable'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, number_format($inArrearsAcc['amortization'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, $inArrearsAcc['term'], 'B', 0, 'R');
        $pdf->Cell(23.86, 5, $inArrearsAcc['mode'], 'B', 0, 'R');
        $pdf->Cell(23.86, 5, number_format($inArrearsAcc['outstandingbalance'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, number_format($inArrearsAcc['SCB'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, number_format($inArrearsAcc['arrears'], 2), 'B', 0, 'R');
        $pdf->Cell(23.86, 5, $inArrearsAcc['lasttransaction'], 'RB', 1, 'R');

        $inArrearsAccsTotalOutBal += $inArrearsAcc['outstandingbalance'];
        $inArrearsAccsTotalSCB += $inArrearsAcc['SCB'];
        $inArrearsAccsTotalArrears += $inArrearsAcc['arrears'];
    }

    /* ----- IN ARREARS ACCOUNTS SUMMARY ----- */
    $pdf->Cell(310.2, 2, '', 0, 1);
    $pdf->Cell(50.4, 6, 'In Arrears Accounts Summary', 'B', 0, 'C');
    $pdf->Cell(31.2, 6, 'Total Accounts:', 'B', 0, 'R');
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(31.2, 6, count($inArrearsAccs), 'B', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(37.2, 6, 'Total Outstanding Balance:', 'B', 0);
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(37.2, 6, number_format($inArrearsAccsTotalOutBal, 2), 'B', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(17.2, 6, 'Total SCB:', 'B', 0);
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(45.2, 6, number_format($inArrearsAccsTotalSCB, 2), 'B', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(20.2, 6, 'Total Arrears:', 'B', 0);
    $pdf->SetFont('Arial', 'B', 8.5);
    $pdf->Cell(42.2, 6, number_format($inArrearsAccsTotalArrears, 2), 'B', 1);
    $pdf->Cell(310.2, 5, '', 0, 1);
} else {
    $pdf->SetFont('Courier', 'B', 22);
    $pdf->Cell(310.2, 50, '', 0, 1, 'C');
    $pdf->Cell(310.2, 20, 'INVALID COLLECTOR ID / NO DATA RETRIEVED', 0, 1, 'C');
    $pdf->Cell(310.2, 6, 'ACCOUNTS LIST UNAVAILABLE', 0, 1, 'C');
}

if ($accounts) {
    $pdf->Output('I', 'JAI Accounts List_' . $accounts[0]['collector'] . '_' . date('Y-m-d') . '_' . date('giA') .'.pdf');
} else {
    $pdf->Output('I', 'JAI Invalid Accounts List.pdf');
}
