<?php

require_once "../../views/includes/dbconn.php";
require "../../views/includes/fpdf.php";

$loanID = $_GET['loanID'];

$statementLoan = $conn->prepare("SELECT b.b_id, l.l_id, CONCAT(b.lastname, ', ', b.firstname, ' ', b.middlename) as name, l.amortization, l.mode, l.term, l.activeloan,
                                        l.amount as loanamount, l.payable, l.releasedate, l.duedate, l.status, p.amount as paymentamount, p.type, p.date
                                 FROM jai_db.payments as p
                                 INNER JOIN jai_db.loans as l
                                 ON p.l_id = l.l_id
                                 INNER JOIN jai_db.borrowers as b
                                 ON p.b_id = b.b_id
                                 WHERE p.l_id = :loanID
                                 ORDER BY p.date ASC, p.p_id ASC");
$statementLoan->bindValue(':loanID', $loanID);
$statementLoan->execute();
$payments = $statementLoan->fetchAll(PDO::FETCH_ASSOC);

$statementPaymentCount = $conn->prepare("SELECT COUNT(*) as paymentcount
                                          FROM jai_db.payments as p
                                          WHERE p.l_id = :loanID");
$statementPaymentCount->bindValue(':loanID', $loanID);
$statementPaymentCount->execute();
$paymentCount = $statementPaymentCount->fetch(PDO::FETCH_ASSOC);

$statementSumOfPayments = $conn->prepare("SELECT SUM(p.amount) as sumofpayments
                                          FROM jai_db.payments as p
                                          WHERE p.l_id = :loanID");
$statementSumOfPayments->bindValue(':loanID', $loanID);
$statementSumOfPayments->execute();
$sumOfPayments = $statementSumOfPayments->fetch(PDO::FETCH_ASSOC);

if ($payments) {
    /* ----- CALCULATE Supposed Current Balance & Arrears ----- */

    if ($payments[0]['payable'] - $sumOfPayments['sumofpayments'] <= 0) {
        $SCB = 0;
        $arrears = 0;
    } else {
        // CHECK IF LOAN IS PAST DUE TO ASSIGN CORRECT ARREARS / SCB
        if (date('Y-m-d') > date_format(date_create($payments[0]['duedate']), 'Y-m-d') && $payments[0]['activeloan'] == 1) {
            $SCB = 0;
            $arrears = ($payments[0]['payable'] - $sumOfPayments['sumofpayments']) - $SCB;
        } else {
            $SCB = $payments[0]['payable'] - ($payments[0]['amortization'] * $paymentCount['paymentcount']);
            if ($SCB < 0) {
                $SCB = 0;
            }
            $arrears = ($payments[0]['payable'] - $sumOfPayments['sumofpayments']) - $SCB;
        }
    }
}

// LETTER PAPER SIZE = 215.9mm x 279.4mm
// LEGAL PAPER SIZE = 215.9mm x 355.6mm
// MARGIN PER SIDE = 10mm
// PRINTABLE AREA 215.9 - (10 * 2) = 195.9mm
// EXACT SIZE USED BELOW = 8.5 x 13 inches / 215.9 x 330.2 millimeters

class PDF extends FPDF
{
    function Header()
    {
        global $payments;
        global $paymentCount;
        global $SCB;
        global $arrears;

        if ($payments) {

            $this->SetFont('Courier', '', 10);
            $this->Cell(65.3, 6, 'Page ' . $this->PageNo() . " of {pages}", 0, 0, 'L');

            $this->SetFont('Courier', 'B', 14);
            $this->Cell(65.3, 6, 'JAI FAIR LOAN', 0, 0, 'C');

            $this->SetFont('Courier', '', 10);
            $this->Cell(65.3, 6, 'Date: ' . date('Y-m-d'), 0, 1, 'R');


            $this->SetFont('Courier', '', 10);
            $this->Cell(65.3, 6, '#' . $payments[0]['b_id'], 0, 0);
            $this->SetFont('Courier', '', 14);
            $this->Cell(65.3, 6, 'Ledger', 0, 0, 'C');
            $this->SetFont('Courier', '', 10);
            $this->Cell(65.3, 6, 'Time: ' . date('g:i:s A'), 0, 1, 'R');

            $this->SetFont('Courier', '', 10);
            // $this->Cell(195.9, 6, 'Borrower No.: ' . $payments[0]['b_id'], 0, 1);
            $this->Cell(97.95, 6, 'Name: ' . ucwords(strtolower($payments[0]['name'])), 0, 1);

            // $this->Cell(32.65, 6, 'Loan Amount:', 0, 0);
            // $this->Cell(32.65, 6, number_format($payments[0]['loanamount'], 2), 0, 0);
            // $this->Cell(32.65, 6, '   Payable:', 0, 0, 'L');
            // $this->Cell(32.65, 6, number_format($payments[0]['payable'], 2), 0, 0, 'L');
            // $this->Cell(32.65, 6, 'Amortization:', 0, 0, 'l');
            // $this->Cell(32.65, 6, number_format($payments[0]['amortization'], 2), 0, 1, 'L');
            // $this->Cell(32.65, 6, 'Mode & Term:', 0, 0);
            // $this->Cell(32.65, 6, ucwords(strtolower($payments[0]['mode'])) . '/' . ucwords(strtolower($payments[0]['term'])), 0, 0);
            // $this->Cell(32.65, 6, '   Rel. Date:', 0, 0, 'L');
            // $this->Cell(32.65, 6, $payments[0]['releasedate'], 0, 0, 'L');
            // $this->Cell(32.65, 6, 'Due Date:', 0, 0, 'L');
            // $this->Cell(32.65, 6, $payments[0]['duedate'], 0, 1, 'L');
            // $this->Cell(32.65, 6, 'SCB:', 0, 0, 'L');
            // $this->Cell(32.65, 6, number_format($SCB, 2), 0, 0, 'L');
            // $this->Cell(32.65, 6, '   Arrears:', 0, 0, 'L');
            // $this->Cell(32.65, 6, number_format($arrears, 2), 0, 0, 'L');
            // $this->Cell(32.65, 6, 'Loan Status:', 0, 0, 'L');
            // $this->Cell(32.65, 6, $payments[0]['status'], 0, 1, 'L');

            $this->Cell(65.3, 6, 'Loan Amount: ' . number_format($payments[0]['loanamount'], 2), 0, 0);
            $this->Cell(65.3, 6, '      Payable: ' . number_format($payments[0]['payable'], 2), 0, 0, 'L');
            $this->Cell(65.3, 6, 'Amortization: ' . number_format($payments[0]['amortization'], 2), 0, 1, 'R');
            $this->Cell(65.3, 6, 'Mode & Term: ' . ucwords(strtolower($payments[0]['mode'])) . ', ' . ucwords(strtolower($payments[0]['term'])), 0, 0);
            $this->Cell(65.3, 6, '      Release Date: ' . $payments[0]['releasedate'], 0, 0, 'L');
            $this->Cell(65.3, 6, 'Due Date: ' . $payments[0]['duedate'], 0, 1, 'R');
            $this->Cell(65.3, 6, 'SCB: ' . number_format($SCB, 2), 0, 0, 'L');
            $this->Cell(65.3, 6, '      Arrears: ' . number_format($arrears, 2), 0, 0, 'L');

            // IF LOAN IS PAST DUE (LOAN STATUS)
            if (date('Y-m-d') > date_format(date_create($payments[0]['duedate']), 'Y-m-d') && $payments[0]['activeloan'] == 1) {
                $this->Cell(46.65, 6, 'Loan Status:', 0, 0, 'R');
                $this->SetFont('Courier', 'B', 10);
                $this->SetTextColor(204, 0, 0); //RED
                $this->Cell(18.65, 6, 'PAST DUE', 0, 1, 'R');
                $this->SetFont('Courier', '', 10);
                $this->SetTextColor(0, 0, 0); //BLACK
            } else {
                $this->Cell(65.3, 6, 'Loan Status: ' . $payments[0]['status'], 0, 1, 'R');
            }

            $this->Cell(195.9, 5, '', 0, 1);

            $this->Cell(48.975, 6, 'Date (Y-M-D)', 1, 0);
            $this->Cell(48.975, 6, 'Particulars', 1, 0);
            $this->Cell(48.975, 6, 'Amount', 1, 0, 'R');
            $this->Cell(48.975, 6, 'Balance', 1, 1, 'R');
        } else {
            $this->SetFont('Courier', '', 11);
            $this->Cell(65.3, 6, 'Page ' . $this->PageNo() . " of {pages}", 0, 0, 'L');
            $this->SetFont('Courier', 'B', 14);
            $this->Cell(65.3, 6, 'JAI Fair Loan', 0, 0, 'C');

            $this->SetFont('Courier', '', 11);
            $this->Cell(65.3, 6, 'Date: ' . date('Y-m-d'), 0, 1, 'R');

            $this->SetFont('Courier', '', 14);
            $this->Cell(195.9, 6, '', 0, 1, 'C');
            $this->Cell(195.9, 30, '', 0, 1, 'C');

            $this->SetFont('Courier', 'B', 22);
            $this->Cell(195.9, 20, 'INVALID LOAN ID / NO PAYMENTS ON RECORD.', 0, 1, 'C');
            $this->Cell(195.9, 20, 'LEDGER UNAVAILABLE.', 0, 1, 'C');
        }
    }

    function Footer()
    {

        $this->SetY(-25.5);
        $this->SetFont('Courier', '', 10);
        // $this->Cell(195.9, 0, '', 'T', 1);
        // $this->Cell(0, 10, 'Page ' . $this->PageNo() . " of {pages}", 0, 0, 'C');
    }
}

// echo "<pre>";
// var_dump($payments);
// exit;

// LETTER PAPER SIZE = 215.9mm x 279.4mm
// LEGAL PAPER SIZE = 215.9mm x 355.6mm
// MARGIN PER SIDE = 10mm
// PRINTABLE AREA 215.9 - (10 * 2) = 195.9mm
// EXACT SIZE USED BELOW = 8.5 x 13 inches / 215.9 x 330.2 millimeters

$pdf = new PDF('P', 'mm', array(215.9, 330.2));

// Define alias for total no. of pages
$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

if ($payments) {
    $pdf->SetTitle('JAI Ledger B' . $payments[0]['b_id'] . ' L' . $payments[0]['l_id'] . ' (' . $payments[0]['status'] . ')');

    // $pdf->Image('../assets/watermark/New-Project.png',10,10,195.9);

    $pdf->SetFont('Courier', '', 10);

    $pdf->Cell(48.975, 5.5, $payments[0]['releasedate'], 'LR', 0);
    $pdf->Cell(48.975, 5.5, 'LOAN RELEASE', 'LR', 0);
    $pdf->Cell(48.975, 5.5, '--->', 'LR', 0, 'R');
    $pdf->Cell(48.975, 5.5, number_format($payments[0]['payable'], 2), 'LR', 1, 'R');

    $payable = $payments[0]['payable'];
    foreach ($payments as $i => $payment) {
        $pdf->Cell(48.975, 5.5, $payment['date'], 'L', 0);
        $pdf->Cell(48.975, 5.5, $payment['type'] == 'Pass' ? $payment['type'] : ($payment['type'] == 'GCash' ? $payment['type'] . ' Payment' : 'Payment'), 'L', 0);
        $pdf->Cell(48.975, 5.5, number_format($payment['paymentamount'], 2), 'L', 0, 'R');
        $pdf->Cell(48.975, 5.5, number_format($payable -= $payment['paymentamount'], 2), 'LR', 1, 'R');
    }
    $pdf->Cell(195.9, 7, '', 'T', 1, 'C');

    $pdf->Cell(195.9, 0, '', 0, 1, 'C');
    $pdf->Cell(195.9, 3, '--------------------------------    NOTHING FOLLOWS    --------------------------------', 0, 0, 'C');
} else {
    $pdf->SetTitle('JAI Invalid Ledger');
}


$totalPages = $pdf->PageNo();

$pdf->SetCreator('JAI Fair Loan');
$pdf->SetAuthor('JAI Fair Loan');
if ($payments) {
    $pdf->SetSubject('JAI Ledger_#' . $payments[0]['b_id'] . '_' . $payments[0]['name'] . '_' . date('Y-m-d_giA'));
} else {
    $pdf->SetSubject('JAI Invalid Ledger');
}

if ($payments) {
    $pdf->Output('I', 'JAI Ledger_#' . $payments[0]['b_id'] . '_' . $payments[0]['name'] . '_' . date('Y-m-d_giA') . '.pdf');
} else {
    $pdf->Output('I', 'JAI Invalid Ledger.pdf');
}
