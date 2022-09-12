<?php

try {
  /** @var $conn \PDO */
  require_once "../../views/includes/dbconn.php";
  require_once "../../views/includes/loanform.php";

  $search = $_GET['search'] ?? '';

  //#region <PAGINATION>

  // PAGE NUMBER
  if (isset($_GET['page'])) {
    $pageNum = $_GET['page'];
  } else {
    $pageNum = 1;
  }

  $numOfRowsPerPage = 10;

  $offset = ($pageNum - 1) * $numOfRowsPerPage;
  $previousPage = $pageNum - 1;
  $nextPage = $pageNum + 1;
  $adjacents = "2";

  if ($search) {
    $statementTotalRows = $conn->prepare("SELECT COUNT(*) as count
                                          FROM jai_db.loans as l
                                          INNER JOIN jai_db.borrowers as b
                                          ON l.b_id = b.b_id
                                          WHERE (b.b_id LIKE :search OR b.firstname LIKE :search OR b.middlename LIKE :search OR b.lastname LIKE :search OR l.status LIKE :search
                                                 OR CONCAT(b.firstname, ' ', b.middlename, ' ', b.lastname) LIKE :search
                                                 OR CONCAT(b.firstname, ' ', b.lastname) LIKE :search
                                                 OR CONCAT(b.lastname, ' ', b.firstname) LIKE :search
                                                 OR CONCAT('l', l.l_id) LIKE :search
                                                 OR CONCAT('b', b.b_id) LIKE :search)
                                          ORDER BY l.activeloan DESC, l.releasedate DESC
                                          ");
    $statementTotalRows->bindValue(':search', "%$search%");
  } else {
    $statementTotalRows = $conn->prepare("SELECT COUNT(*) as count FROM jai_db.loans");
  }
  $statementTotalRows->execute();

  $totalRows = $statementTotalRows->fetchAll(PDO::FETCH_ASSOC);
  $totalPages = ceil($totalRows[0]["count"] / $numOfRowsPerPage);
  $secondLast = $totalPages - 1;

  // echo "<pre>";
  // var_dump($totalRows);
  // echo $totalPages;
  // echo "<br>";
  // echo $pageNum;
  // echo "<br>";
  // echo $offset;
  // exit;

  //#endregion

  if ($search) {
    $statement = $conn->prepare("SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated, b.isdeleted,
                                        l.l_id, l.amount, l.payable, l.mode, l.term, l.amortization, l.activeloan,
                                        l.releasedate, l.duedate, l.status, l.c_id, CONCAT(c.firstname, ' ', c.lastname) as collector
                                 FROM jai_db.borrowers AS b
                                 INNER JOIN jai_db.loans AS l
                                 ON b.b_id = l.b_id
                                 INNER JOIN jai_db.collectors as c
                                 ON l.c_id = c.c_id
                                 WHERE (b.b_id LIKE :search OR b.firstname LIKE :search OR b.middlename LIKE :search OR b.lastname LIKE :search OR l.status LIKE :search
                                        OR CONCAT(b.firstname, ' ', b.middlename, ' ', b.lastname) LIKE :search
                                        OR CONCAT(b.firstname, ' ', b.lastname) LIKE :search
                                        OR CONCAT(b.lastname, ' ', b.firstname) LIKE :search
                                        OR CONCAT('l', l.l_id) LIKE :search
                                        OR CONCAT('b', b.b_id) LIKE :search)
                                 ORDER BY l.activeloan DESC, l.releasedate DESC
                                 LIMIT :offset, :numOfRowsPerPage");
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated, b.isdeleted,
                                        l.l_id, l.amount, l.payable, l.mode, l.term, l.amortization, l.activeloan,
                                        l.releasedate, l.duedate, l.status, l.c_id, CONCAT(c.firstname, ' ', c.lastname) as collector
                                  FROM jai_db.borrowers as b
                                  INNER JOIN jai_db.loans as l
                                  ON b.b_id = l.b_id
                                  INNER JOIN jai_db.collectors as c
                                  ON l.c_id = c.c_id
                                  ORDER BY l.activeloan DESC, l.releasedate DESC
                                  LIMIT :offset, :numOfRowsPerPage");
  }

  $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
  $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL

  $statement->execute();
  $loans = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

// echo "<pre>";
// var_dump($borrowers);
// echo '</pre>';

?>

<?php include_once "../../views/partials/header.php"; ?>

<div class="content-container">
  <div class="page-name">
    <div class="title-container d-flex">
      <img src="../assets/icons/book_dark.svg">
      <h1>Loans</h1>
    </div>
    <div class="actions-container">
      <a href="#" type="button" class="btn btn-new-loan create-btn"><img src="../assets/icons/plus.svg"> New Loan</a>

      <form class="table-search">
        <div class="input-group search-group">
          <input type="text" class="form-control search-input" placeholder="Search..." name="search" value="<?php echo $search; ?>" autofocus onfocus="this.select()">
          <button class="btn search-btn" type="submit">Search</button>
        </div>
      </form>
    </div>
  </div>

  <div class="neumorph-container">
    <div class="table-wrapper">
      <div class="jai-table table-container loans-table">
        <div class="row table-header">
          <div class="jai-col-ID">ID</div>
          <div class="col">Borrower</div>
          <div class="col">Loan Details</div>
          <div class="col">Payment Details</div>
          <div class="col-1 text-center">Action</div>
        </div>
        <?php
        $count = 1;
        foreach ($loans as $i => $loan) {

          $releaseDate = date_create($loan['releasedate']);
          $dueDate = date_create($loan['duedate']);

          /* ----- SELECT LAST PAYMENT OF LOAN ----- */
          $statementLastPayment = $conn->prepare("SELECT l_id, amount, type, date
                                              FROM jai_db.payments
                                              WHERE l_id = :loanid AND date = (SELECT MAX(date)
                                                                               FROM jai_db.payments
                                                                               WHERE l_id = :loanid)");
          $statementLastPayment->bindValue(":loanid", $loan['l_id']);
          $statementLastPayment->execute();
          $lastPayment = $statementLastPayment->fetch(PDO::FETCH_ASSOC);
          /* ----- END - SELECT LAST PAYMENT OF LOAN ----- */

          /* ----- GET MONTHLY & TOTAL INTEREST RATE ----- */
          $loanID = $loan['l_id'];

          $loanPayable = $loan['payable'];
          $loanAmount = $loan['amount'];
          $loanDuration = (int)substr($loan['term'], 0, 1);

          $interestRate = ($loanPayable / $loanAmount) - 1;
          $monthlyInterestRate = $interestRate / $loanDuration;
          /* ----- END - GET MONTHLY & TOTAL INTEREST RATE ----- */

          /* ----- GET TOTAL AMOUNT PAID ----- */
          $statementPayment = $conn->prepare("SELECT sum(amount) as amount
                                          FROM jai_db.payments
                                          WHERE l_id = :l_id");
          $statementPayment->bindValue(':l_id', $loanID);
          $statementPayment->execute();
          $amountPaid = $statementPayment->fetch(PDO::FETCH_ASSOC);
          $amount = $amountPaid['amount'];
          /* ----- END - GET TOTAL AMOUNT PAID ----- */

          /* ----- GET TOTAL PAYMENTS MADE ----- */
          $statementTotalPayments = $conn->prepare("SELECT COUNT(amount) as totalpayments
                                                FROM jai_db.payments as p
                                                WHERE l_id = :l_id AND (p.type = 'Cash' OR p.type = 'GCash')");
          $statementTotalPayments->bindValue(':l_id', $loanID);
          $statementTotalPayments->execute();
          $totalPayments = $statementTotalPayments->fetch(PDO::FETCH_ASSOC);
          $totalPayment = $totalPayments['totalpayments'];
          /* ----- END - GET TOTAL PAYMENTS MADE ----- */

          /* ----- GET TOTAL PASSES ----- */
          $statementTotalPasses = $conn->prepare("SELECT COUNT(amount) as totalpasses
                                                FROM jai_db.payments as p
                                                WHERE l_id = :l_id AND (p.type = 'Pass')");
          $statementTotalPasses->bindValue(':l_id', $loanID);
          $statementTotalPasses->execute();
          $totalPasses = $statementTotalPasses->fetch(PDO::FETCH_ASSOC);
          $totalPass = $totalPasses['totalpasses'];
          /* ----- END - GET TOTAL PASSES ----- */

          /* ----- GET EST. PASS AMOUNT ----- */
          $passAmount = $loan['amortization'] * $totalPass;
          /* ----- END - GET EST. PASS AMOUNT ----- */



          /*                                                                   */
          /*       Count number of days                                        */
          /*       from release date to due date (inclusive of both)           */
          /*       excluding Sundays                                           */
          /*                                                                   */
          $start = new DateTime(date_format($releaseDate, 'Y-m-d'));
          $end = new DateTime(date_format($dueDate, 'Y-m-d'));

          // otherwise the  end date is excluded (bug?)
          $end->modify('+1 day');

          $interval = $end->diff($start);

          // total days
          $days = $interval->days;

          // create an iterateable period of date (P1D equates to 1 day)
          $period = new DatePeriod($start, new DateInterval('P1D'), $end);

          // best stored as array, so you can add more than one
          $holidays = array('2012-09-07');

          foreach ($period as $dt) {
            $curr = $dt->format('D');

            // substract if Saturday or Sunday
            if ($curr == 'Sun') {
              $days--;
            }

            // (optional) for the updated question
            elseif (in_array($dt->format('Y-m-d'), $holidays)) {
              $days--;
            }
          }
          /*                                                                   */
          /*       END - Count number of days                                  */
          /*       from release date to due date (inclusive of both)           */
          /*       excluding Sundays                                           */
          /*                                                                   */


          /*                                                         */
          /*       CALCULATE PROFIT (PER PAYMENT / PER LOAN)         */
          /*                                                         */

          $profit = $loan['payable'] - $loan['amount'];
          $paymentsToCloseLoan = $loan['payable'] / $loan['amortization'];
          $profitPerPayment = $profit / $paymentsToCloseLoan;

          /*    END - CALCULATE PROFIT (PER PAYMENT / PER LOAN)      */

          // echo "<pre>";
          // echo $loanID;

          // var_dump($loan);
          // var_dump($amountPaid);
          // exit; 

        ?>
          <div class="row jai-data-row">

            <div class="jai-col-ID"><?php echo $loan['b_id'] ?></div>
            <div class="col">
              <div class="row">
                <div class="jai-image-col">
                  <div class="jai-picture zoom">
                    <img src="/<?= 'JAI/public/' . $loan['picture']; ?>" class="thumb-image2">
                  </div>
                </div>
                <div class="col">
                  <p class="jai-table-name primary-font <?= $totalPass >= 5 ? 'red' : ''; ?>
                                              <?= $totalPass < 5 ? 'green' : '' ?>"><span class="jai-table-label"></span> <?= ucwords(strtolower($loan['firstname'])) . ' ' . ucwords(strtolower(substr($loan['middlename'], 0, 1))) . '. ' . ucwords(strtolower($loan['lastname'])) ?></p>
                  <p class="jai-table-name primary-font"><?= $loan['status'] ?></p>
                  <?php
                  if ($loan['isdeleted'] == 1) {
                    echo '<p class="sub-font">Borrower deleted</p>';
                  }
                  ?>
                </div>
              </div>

            </div>
            <div class="col">
              <div class="row">
                <p class="jai-table-name primary-font"><span class="jai-table-label">Loan Reference #<?= $loan['l_id'] ?></span></p>
                <?= (date('Y-m-d') > $dueDate->format('Y-m-d') && $loan['activeloan'] == 1 ? '<p class="jai-table-name primary-font red">(PAST DUE)</p>' : '') ?>
              </div>
              <div class="row">
                <div class="col">
                  <p class="jai-table-contact sub-font"> <span class="jai-table-label">Amount: </span><?= "₱ " . number_format($loan['amount'], 2) ?></p>
                  <p class="jai-table-address sub-font"> <span class="jai-table-label">Payable: </span><?= "₱ " . number_format($loan['payable'], 2) ?></p>
                  <!-- <p class="jai-table-address sub-font"> <span class="jai-table-label">Balance: </span><?= "₱ " . number_format($loan['balance'], 2) ?></p> Hard coded balance (From loans table) -->
                  <p class="jai-table-address sub-font"> <span class="jai-table-label">Amortization: </span><?= "₱ " . number_format($loan['amortization'], 2) ?></p>
                </div>
                <div class="col">
                  <p class="jai-table-address sub-font"> <?= ucwords(strtolower($loan['term'])) . ', ' . ucwords(strtolower($loan['mode'])) ?></p>
                  <p class="jai-table-release sub-font"> <span class="jai-table-label">Rel. Date: </span> <?= date_format($releaseDate, 'M-d-Y') ?></p>
                  <p class="jai-table-due sub-font"> <span class="jai-table-label">Due Date: </span> <?= date_format($dueDate, 'M-d-Y') ?></p>
                  <p class="jai-table-address sub-font">Interest: <?= number_format($interestRate * 100, 2) . '%' ?></p>
                  <p class="jai-table-address sub-font">Monthly interest: <?= number_format($monthlyInterestRate * 100, 2) . '%' ?></p>
                </div>
              </div>
              <br>
              <div class="row">
                <p class="sub-font">Collector: <?= $loan['collector'] ?></p>
                <br>
                <p class="sub-font">Loan duration: <?= $days ?> days</p>
                <!-- <p class="sub-font">(test)Daily profit:
              <?php

              /*                                                                   */
              /*       Count number of days                                        */
              /*       from release date to due date (inclusive of both)           */
              /*       excluding Sundays                                           */
              /*                                                                   */
              $start = new DateTime(date_format($releaseDate, 'Y-m-d'));
              $end = new DateTime(date_format($dueDate, 'Y-m-d'));


              ?>
            </p> -->
                <p class="sub-font">Number of payments to close loan: <?= number_format($paymentsToCloseLoan, 4) ?></p>
                <br>
                <p class="sub-font">Payment breakdown (based on amortization):</p>
                <p class="sub-font">Principal amount: <?= '₱ ' . number_format(($loan['amortization'] - $profitPerPayment), 4) ?></p>
                <p class="sub-font">Interest amount: <?= '₱ ' . number_format($profitPerPayment, 4) ?></p>
                <p class="sub-font">Interest per payment: <?= number_format((($profitPerPayment / $loan['amortization']) * 100), 4) . '%' ?></p>
              </div>
            </div>
            <div class="col position-relative">
              <div class="row">
                <p class="primary-font"> <span class="jai-table-label">Balance: </span><?= "₱ " . number_format($loan['payable'] - $amount, 2) ?></p> <!-- Computed balance (Payable - Total payments) -->
              </div>
              <div class="row">
                <div class="col">
                  <!-- <p class="jai-table-amount primary-font"><span class="jai-table-label">Payments made:</span> <?php echo $loan['paymentsmade'] ?></p> hard coded payments made from loans table -->
                  <p class="jai-table-amount primary-font"><span class="jai-table-label">Payments made:</span> <?php echo $totalPayment ?></p> <!-- select query total payments from payments table  -->
                </div>
                <div class="col">
                  <!-- <p class="jai-table-payable primary-font"> <span class="jai-table-label">Passes: </span> <?php echo $loan['passes'] ?></p> hard coded passes from loans table -->
                  <p class="jai-table-payable primary-font"> <span class="jai-table-label">Passes: </span> <?php echo $totalPass ?></p> <!-- select query total passes from payments table -->
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <p class="jai-table-payment-made sub-font"> <span class="jai-table-label">Total Paid: </span> <?= "₱ " . number_format($amount, 2) ?> </p>
                </div>
                <div class="col">
                  <p class="jai-table-release sub-font"> <span class="jai-table-label">Est. Loss: </span> <?= "₱ " . number_format($passAmount, 2) ?> </p>
                </div>
              </div>
              <?php if ($lastPayment != 0) { ?>
                <div class="row">
                  <p class="primary-font"><?= ($loan['status'] == 'Active') ? 'Latest Payment' : 'Final Payment' ?></p>
                  <p class="sub-font"> <span class="jai-table-label">Date: </span> <?= date_format(date_create($lastPayment['date']), 'M-d-Y') ?></p>
                  <p class="sub-font"> <span class="jai-table-label">Type: </span> <?= $lastPayment['type'] ?></p>
                  <p class="sub-font"> <span class="jai-table-label">Amount: </span> <?= number_format($lastPayment['amount'], 2) ?></p>
                </div>
              <?php } ?>
              <!-- <textarea class="jai-table-input" type="text"></textarea> -->
            </div>
            <div class="col-1 d-flex align-items-start justify-content-around">
              <form method="get" action="ledger" target="_blank">
                <input title="View ledger" type="submit" name="loanID" class="btn ledger-btn" value="<?= $loan['l_id'] ?>" <?= ($totalPayment || $totalPass) == 0 ? 'disabled' : '' ?>></input>
              </form>
              <button title="Delete" type="button" class="btn delete-btn delete-borrower" data-toggle="modal" data-target="#deleteBorrower" <?= ($totalPayment || $totalPass) != 0 ? 'disabled' : '' ?>>Delete</button>

            </div>
            <div class="d-none hidden-field">
              <form id="hidden-form-<?= $count; ?>" class="hidden-form" action="">
                <input type="hidden" name="data-row" value='row-<?= $loan['b_id'] ?>'>
                <input type="hidden" name="b_id" value="<?= ucwords(strtolower($loan['b_id'])) ?>">
                <input type="hidden" name="borrower" value="<?= ucwords(strtolower($loan['b_id'])) ?>">
                <input type="hidden" name="amount" value="<?= ucwords(strtolower($loan['amount'])) ?>">
                <input type="hidden" name="mode" value="<?= ucwords(strtolower($loan['mode'])) ?>">
                <input type="hidden" name="term" value="<?= ucwords(strtolower($loan['term'])) ?>">
              </form>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
    <div class="table-padding">
    </div>
  </div>

  <!-- PAGE NAVIGATION -->
  <div class="pagination-container ">

    <ul class="pagination justify-content-center">
      <?php if ($pageNum > 1) {
        if (!$search) {
          echo "<li class='page-item first-page'><a class='page-link' data-pagecount='1' href='?page=1'><img src='../assets/icons/chevrons-left.svg'></a></li>";
        } else {
          echo "<li class='page-item first-page'><a class='page-link' data-pagecount='1' href='?page=1&search=$search'><img src='../assets/icons/chevrons-left.svg'></a></li>";
        }
      } ?>

      <li <?php if ($pageNum <= 1) {
            echo "class='page-item disabled prev-page'";
          } else {
            echo "class='page-item prev-page'";
          } ?>>
        <a <?php if ($pageNum > 1) {
              if (!$search) {
                echo "class='page-link' data-pagecount='$previousPage' href='?page=$previousPage'";
              } else {
                echo "class='page-link' data-pagecount='$previousPage' href='?page=$previousPage&search=$search'";
              }
            } else {
              echo "class='page-link' data-pagecount='$previousPage' href='?page=$previousPage'";
            } ?>><img src='../assets/icons/chevron-left.svg'></a>
      </li>
      <div class="numbers-container">

        <?php
        if ($totalPages <= 10) {
          for ($counter = 1; $counter <= $totalPages; $counter++) {
            if ($counter == $pageNum) {
              echo "<li class='page-item is-number active'><a data-pagecount='$counter' class='page-link active'>$counter</a></li>";
            } else {
              if (!$search) {
                echo "<li class='page-item is-number'><a class='page-link' data-pagecount='$counter' href='?page=$counter'>$counter</a></li>";
              } else {
                echo "<li class='page-item is-number'><a class='page-link' data-pagecount='$counter' href='?page=$counter&search=$search'>$counter</a></li>";
              }
            }
          }
        } elseif ($totalPages > 10) {
          if ($pageNum <= 4) {
            for ($counter = 1; $counter < 8; $counter++) {
              if ($counter == $pageNum) {
                echo "<li class='page-item is-number active'><a data-pagecount='$counter' class='page-link active'>$counter</a></li>";
              } else {
                if (!$search) {
                  echo "<li class='page-item is-number'><a class='page-link' data-pagecount='$counter' href='?page=$counter'>$counter</a></li>";
                } else {
                  echo "<li class='page-item is-number'><a class='page-link' data-pagecount='$counter' href='?page=$counter&search=$search'>$counter</a></li>";
                }
              }
            }
            echo "<li class='page-item is-number'><a class='page-link'>...</a></li>";
            if (!$search) {
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='$secondLast' href='?page=$secondLast'>$secondLast</a></li>";
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='$totalPages' href='?page=$totalPages'>$totalPages</a></li>";
            } else {
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='$secondLast' href='?page=$secondLast&search=$search'>$secondLast</a></li>";
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='$totalPages' href='?page=$totalPages&search=$search'>$totalPages</a></li>";
            }
          } elseif ($pageNum > 4 && $pageNum < $totalPages - 4) {
            if (!$search) {
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='1' href='?page=1'>1</a></li>";
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='2' href='?page=2'>2</a></li>";
            } else {
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='1' href='?page=1&search=$search'>1</a></li>";
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='2' href='?page=2&search=$search'>2</a></li>";
            }


            echo "<li class='page-item is-number'><a class='page-link'>...</a></li>";
            for (
              $counter = $pageNum - $adjacents;
              $counter <= $pageNum + $adjacents;
              $counter++
            ) {
              if ($counter == $pageNum) {
                echo "<li class='page-item is-number active'><a class='page-link active' data-pagecount='" . $counter . "'>$counter</a></li>";
              } else {
                if (!$search) {
                  echo "<li class='page-item is-number'><a class='page-link' data-pagecount='" . $counter . "' href='?page=$counter'>$counter</a></li>";
                } else {
                  echo "<li class='page-item is-number'><a class='page-link' data-pagecount='" . $counter . "' href='?page=$counter&search=$search'>$counter</a></li>";
                }
              }
            }
            echo "<li class='page-item is-number'><a class='page-link'>...</a></li>";
            if (!$search) {
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='" . $secondLast . "' href='?page=$secondLast'>$secondLast</a></li>";
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='" . $totalPages . "' href='?page=$totalPages'>$totalPages</a></li>";
            } else {
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='" . $secondLast . "' href='?page=$secondLast&search=$search'>$secondLast</a></li>";
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='" . $totalPages . "' href='?page=$totalPages&search=$search'>$totalPages</a></li>";
            }
          } else {
            if (!$search) {
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='1' href='?page=1'>1</a></li>";
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='2' href='?page=2'>2</a></li>";
            } else {
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='1' href='?page=1&search=$search'>1</a></li>";
              echo "<li class='page-item is-number'><a class='page-link' data-pagecount='2' href='?page=2&search=$search'>2</a></li>";
            }
            echo "<li class='page-item is-number'><a class='page-link'>...</a></li>";
            for (
              $counter = $totalPages - 6;
              $counter <= $totalPages;
              $counter++
            ) {
              if ($counter == $pageNum) {
                echo "<li class='page-item is-number active'><a class='page-link active'>$counter</a></li>";
              } else {
                if (!$search) {
                  echo "<li class='page-item is-number'><a class='page-link' data-pagecount='$counter' href='?page=$counter'>$counter</a></li>";
                } else {
                  echo "<li class='page-item is-number'><a class='page-link' data-pagecount='$counter' href='?page=$counter&search=$search'>$counter</a></li>";
                }
              }
            }
          }
        }
        ?>
      </div>


      <li <?php if ($pageNum >= $totalPages) {
            echo "class='page-item next-page disabled'";
          } else {
            echo "class='page-item next-page'";
          } ?>>
        <a <?php if ($pageNum < $totalPages) {
              if (!$search) {
                echo "class='page-link' data-pagecount='$nextPage' href='?page=$nextPage'";
              } else {
                echo "class='page-link' data-pagecount='$nextPage href='?page=$nextPage&search=$search'";
              }
            } ?>><img src='../assets/icons/chevron-right.svg'></a>
      </li>

      <?php if ($pageNum < $totalPages) {
        if (!$search) {
          echo "<li class='page-item last-page'><a class='page-link' data-pagecount='$totalPages' href='?page=$totalPages'><img src='../assets/icons/chevrons-right.svg'></a></li>";
        } else {
          echo "<li class='page-item last-page'><a class='page-link' data-pagecount='$totalPages' href='?page=$totalPages&search=$search'><img src='../assets/icons/chevrons-right.svg'></a></li>";
        }
      } ?>
    </ul>
  </div>

  <!-- END - PAGE NAVIGATION -->

</div>



<div class="modal fade" id="deleteBorrower" tabindex="-1" role="dialog" aria-labelledby="deleteBorrowerLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm close-modal" data-dismiss="modal">Cancel</button>
        <form class="delete-form" style="display: inline-block" method="post" action="delete.php">
          <input type="hidden" name="id" value="">
          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade form-modal" data-loan="1" id="createloan" tabindex="-1" role="dialog" aria-labelledby="createloanLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Loan</h5>
      </div>
      <div class="modal-body">
        <form class="action-form" autocomplete="off" action="create-loan" method="post" enctype="multipart/form-data">
          <input type="hidden" class="d-none" name="b_id" value="">
          <input name="data-row" type="hidden" class="d-none" value=''>

          <div class="container">
            <div class="row">
              <div class="col">
                <h5 class="modal-body-label">Loan</h5>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="jai-mb-2 autocomplete">
                  <!-- <select name="borrower" id="borrower" class="form-control" required>
                    <option value="" disabled selected>Select borrower</option>
                    <?php
                    //foreach ($borrowers as $i => $borrower) {
                    //echo '<option value="' . $borrower['b_id'] . '">#' . $borrower['b_id'] . ' ' . ucwords(strtolower($borrower['firstname'])) . ' ' . ucwords(strtolower($borrower['middlename'])) . ' ' . ucwords(strtolower($borrower['lastname'])) . '</option>';
                    //}
                    ?>
                  </select> -->
                  <input type="hidden" class="borrower-id" name="borrower" placeholder="Search for borrowers..." autofocus>
                  <input required type="text" name="borrower-name" id="newloansearch" class="autocomplete-input form-control" placeholder="Search for borrowers..." onclick="this.select()" autofocus>
                  <div class="suggestions-container">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">

              <div class="col">
                <div class="jai-mb-2">
                  <select placeholder="Amount" class="form-control" name="amount" required>
                    <option value="" disabled selected>Amount</option>
                    <?php
                    foreach ($amounts as $amount) {
                      echo "<option value='" . $amount['amount'] . "'> ₱ " . number_format($amount['amount'], 2) . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col">
                <div class="jai-mb-2">
                  <select placeholder="Mode" type="text" class="form-control" name="mode" value="" required>
                    <option value="" disabled selected>Mode</option>
                    <?php
                    foreach ($modes as $mode) {
                      echo "<option>" . ucwords(strtolower($mode['mode']))  . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col">
                <div class="jai-mb-2">
                  <select class="form-control" name="term" required>
                    <option value="" disabled selected>Term</option>
                    <?php
                    foreach ($terms as $term) {
                      echo "<option value='" . ucwords(strtolower($term['term'])) . "'>" . ucwords(strtolower($term['term']))  . "</option>";
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="jai-mb-2">
                  <select class="form-control" name="collector" required>
                    <option value="" disabled selected>Collector</option>
                    <?php
                    foreach ($collectors as $collector) {
                      echo '<option value="' . $collector['c_id'] . '">' . ucwords(strtolower($collector['firstname'])) . ' ' . ucwords(strtolower($collector['middlename'])) . ' ' . ucwords(strtolower($collector['lastname'])) . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col">
                <div class="jai-mb-2">
                  <input placeholder="Release Date" type="text" class="form-control datepicker no-limit" name="release-date" value="" readonly required>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="jai-mb-2">
                  <!-- <input placeholder="Due Date" type="text" class="form-control" name="due-date" value="" readonly> -->
                </div>
              </div>
            </div>

          </div>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm close-modal" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm btn-action">Submit</button>
      </div>
    </div>
    <div class="success-message" style="display: none;">
      <div class="close-container">
        <div class="close-button"></div>
      </div>
      <h3 class="success-content">

      </h3>
    </div>
  </div>
</div>

</body>

</html>