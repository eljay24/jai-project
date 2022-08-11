<?php

require_once "../../views/includes/dbconn.php";
require "../../views/includes/fpdf.php";

$loanID = $_GET['loanID'];

$statementLoan = $conn->prepare("SELECT b.b_id, CONCAT(b.firstname, ' ', b.middlename, ' ', b.lastname) as name, l.amortization, l.mode, l.term,
                                        l.amount as loanamount, l.payable, l.releasedate, l.duedate, l.status, p.amount as paymentamount, p.type, p.date
                                 FROM jai_db.payments as p
                                 INNER JOIN jai_db.loans as l
                                 ON p.l_id = l.l_id
                                 INNER JOIN jai_db.borrowers as b
                                 ON p.b_id = b.b_id
                                 WHERE p.l_id = :loanID
                                 ORDER BY p.date ASC
");
$statementLoan->bindValue(':loanID', $loanID);
$statementLoan->execute();

$payments = $statementLoan->fetchAll(PDO::FETCH_ASSOC);

class PDF extends FPDF
{
    function Header()
    {
        global $payments;



        if ($payments) {

            $this->SetFont('Courier', 'B', 14);
            $this->Cell(118.54, 6, 'JAI FAIR LOAN', 0, 0, 'R');

            $this->SetFont('Courier', '', 11);
            $this->Cell(77.36, 6, 'Date: ' . date('Y-m-d'), 0, 1, 'R');


            $this->SetFont('Courier', '', 14);
            $this->Cell(195.9, 6, 'Ledger', 0, 1, 'C');


            $this->SetFont('Courier', '', 11);
            $this->Cell(97.95, 6, 'Borrower No.: ' . $payments[0]['b_id'], 0, 1);
            $this->Cell(97.95, 6, 'Name: ' . ucwords(strtolower($payments[0]['name'])), 0, 0);
            $this->Cell(97.95, 6, 'Loan Status: ' . $payments[0]['status'], 0, 1, 'R');


            $this->Cell(65.3, 6, 'Loan Amount: ' . number_format($payments[0]['loanamount'], 2), 0, 0);
            $this->Cell(65.3, 6, 'Payable: ' . number_format($payments[0]['payable'], 2), 0, 0, 'R');
            $this->Cell(65.3, 6, 'Amortization: ' . number_format($payments[0]['amortization'], 2), 0, 1, 'R');
            $this->Cell(65.3, 6, 'Mode & Term: ' . ucwords(strtolower($payments[0]['mode'])) . '/' . ucwords(strtolower($payments[0]['term'])), 0, 0);
            $this->Cell(65.3, 6, 'Release Date: ' . $payments[0]['releasedate'], 0, 0, 'R');
            $this->Cell(65.3, 6, 'Due Date: ' . $payments[0]['duedate'], 0, 1, 'R');

            $this->SetFont('Courier', '', 10);

            $this->Cell(195.9, 5, '', 0, 1);

            $this->Cell(48.975, 6, 'Date (Y-M-D)', 1, 0);
            $this->Cell(48.975, 6, 'Type', 1, 0);
            $this->Cell(48.975, 6, 'Amount', 1, 0, 'R');
            $this->Cell(48.975, 6, 'Balance', 1, 1, 'R');
        } else {
            $this->SetFont('Courier', 'B', 14);
            $this->Cell(117.54, 6, 'JAI Fair Loan', 0, 0, 'R');

            $this->SetFont('Courier', '', 11);
            $this->Cell(78.36, 6, 'Date: ' . date('Y-m-d'), 0, 1, 'R');


            $this->SetFont('Courier', '', 14);
            $this->Cell(195.9, 6, 'Ledger', 0, 1, 'C');
            
            $this->SetFont('Courier', '', 22);
            $this->Cell(195.9, 50, 'NO PAYMENTS ON RECORD', 0, 1, 'C');

        }
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Courier', '', 10);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . " of {pages}", 0, 0, 'C');
    }
}

// echo "<pre>";
// var_dump($payments);
// exit;

// LETTER PAPER SIZE = 215.9mm x 279.4mm
// MARGIN PER SIDE = 10mm
// PRINTABLE AREA 215.9 - (10 * 2) = 195.9mm

$pdf = new PDF('P', 'mm', 'Letter');

// Define alias for total no. of pages
$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

if ($payments) {

    // $pdf->Image('../assets/watermark/New-Project.png',10,10,195.9);

    $pdf->SetFont('Courier', '', 10);

    $pdf->Cell(48.975, 7, $payments[0]['releasedate'], 'LR', 0);
    $pdf->Cell(48.975, 7, 'LOAN RELEASE DATE', 'LR', 0);
    $pdf->Cell(48.975, 7, '', 'LR', 0, 'R');
    $pdf->Cell(48.975, 7, number_format($payments[0]['payable'], 2), 'LR', 1, 'R');

    $payable = $payments[0]['payable'];
    foreach ($payments as $i => $payment) {
        $pdf->Cell(48.975, 7, $payment['date'], 'LR', 0);
        $pdf->Cell(48.975, 7, $payment['type'], 'LR', 0);
        $pdf->Cell(48.975, 7, number_format($payment['paymentamount'], 2), 'LR', 0, 'R');
        $pdf->Cell(48.975, 7, number_format($payable -= $payment['paymentamount'], 2), 'LR', 1, 'R');
    }
    $pdf->Cell(195.9, 7, '', 'T', 1, 'C');

    $pdf->Cell(195.9, 0, '', 0, 1, 'C');
    $pdf->Cell(195.9, 3, '------------------------------- NOTHING FOLLOWS -------------------------------', 0, 0, 'C');

   
}

$pdf->Output('I','#'.$payments[0]['b_id'].'-'.$payments[0]['name'].'-LEDGER-'.date('Y-m-d'));
 ?>