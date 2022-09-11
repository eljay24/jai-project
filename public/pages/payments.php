<?php

try {
  /** @var $conn \PDO */
  require_once "../../views/includes/dbconn.php";


  $search = $_GET['search'] ?? '';

  //#region <PAGINATION>

  // PAGE NUMBER
  if (isset($_GET['page'])) {
    $pageNum = $_GET['page'];
  } else {
    $pageNum = 1;
  }

  $numOfRowsPerPage = 20;

  $offset = ($pageNum - 1) * $numOfRowsPerPage;
  $previousPage = $pageNum - 1;
  $nextPage = $pageNum + 1;
  $adjacents = "2";

  if ($search) {
    $statementTotalRows = $conn->prepare("SELECT COUNT(*) as count
                                          FROM jai_db.payments as p
                                          INNER JOIN jai_db.collectors as c
                                          ON p.c_id = c.c_id
                                          INNER JOIN jai_db.loans as l
                                          ON p.l_id = l.l_id
                                          INNER JOIN jai_db.borrowers as b
                                          ON b.b_id = l.b_id
                                          WHERE (b.firstname LIKE :search OR b.middlename LIKE :search OR b.lastname LIKE :search OR b.comaker LIKE :search OR b.b_id LIKE :search
                                                 OR c.firstname LIKE :search OR c.middlename LIKE :search OR c.lastname LIKE :search OR p.type LIKE :search
                                                 OR CONCAT(b.firstname, ' ', b.middlename, ' ', b.lastname) LIKE :search
                                                 OR CONCAT(b.firstname, ' ', b.lastname) LIKE :search
                                                 OR CONCAT(b.lastname, ' ', b.firstname) LIKE :search
                                                 OR CONCAT('l', p.l_id) LIKE :search
                                                 OR CONCAT('b', b.b_id) LIKE :search
                                                 OR CONCAT('p', p.p_id) LIKE :search)
                                          ORDER BY p.date ASC");
    $statementTotalRows->bindValue(':search', "%$search%");
  } else {
    $statementTotalRows = $conn->prepare("SELECT COUNT(*) as count FROM jai_db.payments");
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
    $statement = $conn->prepare("SELECT b.b_id, b.firstname as borrowerfname, b.middlename as borrowermname, b.lastname as borrowerlname, b.picture, b.contactno, l.l_id, l.mode, l.status, l.payable, l.amount as loanamount, l.amortization,
                                        p.p_id, p.amount as amount, p.type, p.date, c.firstname as collectorfname, c.middlename as collectormname, c.lastname as collectorlname
                                 FROM jai_db.payments as p
                                 INNER JOIN jai_db.collectors as c 
                                 ON p.c_id = c.c_id
                                 INNER JOIN jai_db.loans as l
                                 ON p.l_id = l.l_id
                                 INNER JOIN jai_db.borrowers as b 
                                 ON b.b_id = l.b_id
                                 WHERE b.firstname LIKE :search OR b.middlename LIKE :search OR b.lastname LIKE :search OR b.b_id LIKE :search OR c.firstname LIKE :search
                                       OR c.middlename LIKE :search OR c.lastname LIKE :search OR p.type LIKE :search
                                       OR CONCAT(b.firstname, ' ', b.middlename, ' ', b.lastname) LIKE :search
                                       OR CONCAT(b.firstname, ' ', b.lastname) LIKE :search
                                       OR CONCAT(b.lastname, ' ', b.firstname) LIKE :search
                                       OR CONCAT('l', l.l_id) LIKE :search
                                       OR CONCAT('b', b.b_id) LIKE :search
                                       OR CONCAT('p', p.p_id) LIKE :search
                                 ORDER BY p.date DESC, p.p_id DESC
                                 LIMIT :offset, :numOfRowsPerPage");
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT b.b_id, b.firstname as borrowerfname, b.middlename as borrowermname, b.lastname as borrowerlname, b.picture, b.contactno, l.l_id, l.mode, l.status, l.payable, l.amount as loanamount, l.amortization,
                                        p.p_id, p.amount as amount, p.type, p.date, c.firstname as collectorfname, c.middlename as collectormname, c.lastname as collectorlname
                                 FROM jai_db.payments as p
                                 INNER JOIN jai_db.collectors as c 
                                 ON p.c_id = c.c_id
                                 INNER JOIN jai_db.loans as l
                                 ON p.l_id = l.l_id
                                 INNER JOIN jai_db.borrowers as b 
                                 ON b.b_id = l.b_id
                                 ORDER BY p.date DESC, p.p_id DESC
                                 LIMIT :offset, :numOfRowsPerPage");
  }

  $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
  $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL

  $statement->execute();
  $payments = $statement->fetchAll(PDO::FETCH_ASSOC);

  $statementCollectors = $conn->prepare("SELECT *
                                       FROM jai_db.collectors
                                       ORDER BY c_id ASC");
  $statementCollectors->execute();
  $collectors = $statementCollectors->fetchAll(PDO::FETCH_ASSOC);



  // echo "DB connected successfully";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

// echo "<pre>";
// var_dump($payments);
// echo '</pre>';
// exit;

//TOTAL AMOUNT TEST
// $totalAmount = 0;
// $totalPayable = 0;
// $totalBalance = 0;

?>

<?php include_once "../../views/partials/header.php"; ?>

<div class="content-container">
  <div class="page-name">
    <h1>Payments</h1>
    <div class="actions-container">
      <a href="#" type="button" class="btn open-payment-modal create-btn">Add Payment</a>
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
      <div class="jai-table table-container payments-table">
        <div class="row">
          <div class="jai-col-ID">Ref#</div>
          <div class="col">Borrower</div>
          <div class="col">Payment Details</div>
          <div class="col">Collector</div>
          <div class="col-1">Action</div>
        </div>
        <?php
        $count = 1;
        foreach ($payments as $i => $payment) {

          $profit = $payment['payable'] - $payment['loanamount'];
          $paymentsToCloseLoan = $payment['payable'] / $payment['amortization'];
          $profitPerPayment = $profit / $paymentsToCloseLoan;

          // $profitOrLoss = (($payment['amount'] - $payment['amortization']) + $profitPerPayment);
          $profitOrLoss = ($profitPerPayment / $payment['amortization']) * $payment['amount'];

          $date = date_create($payment['date']);
          $loanID = $payment['l_id'];

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

        ?>

          <div data-row-id="<?php echo $payment['p_id'] ?>" class="row jai-data-row">
            <div class="jai-col-ID"><?php echo $payment['p_id'] ?></div>
            <div class="col">
              <div class="row">
                <!-- <div class="jai-image-col">
              <div class="jai-picture">
                <img src="/<?= 'JAI/public/' . $payment['picture']; ?>" class="thumb-image2">
              </div>
            </div> -->
                <div class="col">
                  <p class="jai-table-name primary-font"><span class="jai-table-label"></span> <?= '#' . $payment['b_id'] . ' ' . ucwords(strtolower($payment['borrowerfname'])) . ' ' . ucwords(strtolower(substr($payment['borrowermname'], 0, 1))) . '. ' . ucwords(strtolower($payment['borrowerlname'])) ?></p>
                </div>
                <div class="col-4">
                  <p class="primary-font">Loan Ref #<?= $payment['l_id'] ?></p>
                </div>
              </div>
              <div class="row">
                <p class="sub-font">Contact: <?= $payment['contactno'] ?></p>
              </div>
            </div>
            <div class="col">
              <div class="row">
                <div class="col">
                  <p class="jai-table-comaker primary-font"><span class="jai-table-label">Amount:</span> <?= "₱ " .  number_format($payment['amount'], 2) ?></p>
                  <p class="jai-table-contact sub-font"> <span class="jai-table-label">Type: </span><?php echo $payment['type'] ?></p>
                </div>
                <div class="col">
                  <p class="primary-font">Date: <?= date_format($date, "M-d-Y") ?></p>
                  <p class="sub-font">Mode: <?= ucwords(strtolower($payment['mode'])) ?></p>
                </div>
                <!-- <div class="col">
              <p class="jai-table-amount primary-font"><span class="jai-table-label">Collector: </span><?php echo ucwords(strtolower($payment['collectorfname'])) . ' ' . ucwords(strtolower($payment['collectormname'])) . ' ' . ucwords(strtolower($payment['collectorlname'])) ?></p>
            </div>
            <div class="col">
              <p class="jai-table-payable primary-font"> <span class="jai-table-label">Remaining Bal.: </span> <?php  ?></p>
            </div> -->
              </div>
              <!-- <div class="row">
            <div class="col">
              <p class="jai-table-payment-made sub-font"> <span class="jai-table-label">Payable??: </span> <?php  ?></p>
              <p class="jai-table-mode sub-font"> <span class="jai-table-label">Mode & Term??: </span> <?php ?></p>
              <p class="jai-table-amort sub-font"> <span class="jai-table-label">Amortization???: </span> <?php ?></p>
            </div>
            <div class="col">
              <p class="jai-table-release sub-font"> <span class="jai-table-label">Hmm???: </span> 01/01/22</p>
              <p class="jai-table-due sub-font"> <span class="jai-table-label">Due Date???: </span> 01/01/22</p>
            </div>
          </div> -->
            </div>
            <div class="col position-relative">
              <div class="row">
                <div class="col">
                  <p class="jai-table-address primary-font"> <span class="jai-table-label">Collector: </span><?php echo ucwords(strtolower($payment['collectorfname'])) . ' ' . ucwords(strtolower($payment['collectormname'])) . ' ' . ucwords(strtolower($payment['collectorlname'])) ?></p>
                  <p class="sub-font"><?= ($profitOrLoss > 0) ? '(test)Profit: +' . number_format($profitOrLoss, 2) : '(test)Loss: ' . number_format($profitOrLoss, 2) ?></p>
                  <!-- <textarea class="jai-table-input" type="text"></textarea> -->
                </div>
                <div class="col-4">
                  <p class="primary-font"><?= $payment['status'] ?></p>
                  <p class="sub-font">Amort.: <?= $payment['amortization'] ?></p>
                </div>
              </div>
            </div>
            <div class="col-1 d-flex align-items-center justify-content-around">
              <!-- <a href="update.php?id=<?php //echo $borrower['b_id'] 
                                          ?>" class="btn btn-primary btn-sm edit-btn">Edit</a> -->
              <!-- <a title="Edit" href="#" class="btn btn-primary btn-sm edit-btn">Edit</a> -->
              <form method="get" action="ledger" target="_blank">
                <input title="View ledger" type="submit" name="loanID" class="btn btn-primary btn-sm ledger-btn" value="<?= $loanID ?>" <?= ($totalPayment || $totalPass) == 0 ? 'disabled' : '' ?>></input>
              </form>
              <button title="Delete" type="button" class="btn btn-danger btn-sm delete-borrower delete-btn" data-toggle="modal" data-target="#deleteBorrower" disabled>Delete</button>

            </div>
            <div class="d-none hidden-field">
              <form id="hidden-form-<?= $count; ?>" class="hidden-form" action="">
                <input type="hidden" name="data-row" value='row-<?= $loanID ?>'>
                <input type="hidden" name="b_id" value="<?= ucwords(strtolower($loanID)) ?>">
                <input type="hidden" name="borrower-id" value="<?= $loanID ?>">
                <input type="hidden" name="borrower-name" value="<?= '#' . $payment['b_id'] . ' ' . ucwords(strtolower($payment['borrowerfname'])) . ' ' . ucwords(strtolower($payment['borrowermname'])) . ' ' . ucwords(strtolower($payment['borrowerlname'])) ?>">
                <input type="hidden" name="loan-amount" value="<?= number_format($payment['amount'], 2) ?>">
              </form>
            </div>
          </div>
        <?php $count++;
        } ?>
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

  <!-- DELETE BORROWER START -->

  <div class="modal fade" data-borrower="1" id="deleteBorrower" tabindex="-1" role="dialog" aria-labelledby="deleteBorrowerLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm close-modal" data-dismiss="modal">Cancel</button>
          <form class="delete-form" style="display: inline-block" method="post" action="delete.php">
            <input type="hidden" name="b_id" value="">
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- DELETE BORROWER END -->

  <!-- CREATE/EDIT BORROWER START -->

  <div class="modal fade form-modal payment-modal" data-loan="1" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create Payment</h5>
        </div>
        <div class="modal-body">
          <form class="action-form payment-form" autocomplete="off" action="payment-loan" method="post" enctype="multipart/form-data">
            <input type="hidden" class="d-none" name="b_id" value="">
            <input name="data-row" type="hidden" class="d-none" value=''>
            <div class="container">
              <div class="row">
                <div class="col">
                  <h5 class="modal-body-label">Payment</h5>
                </div>
              </div>
              <div class="row">
                <div class="col-7">
                  <div class="row">
                    <div class="col">
                      <div class="jai-mb-2 autocomplete">
                        <input type="hidden" class="borrower-id" name="borrower-id" placeholder="Search for borrowers...">
                        <input type="text" name="borrower-name" id="namesearch" class="autocomplete-input form-control" placeholder="Search for borrowers..." required>
                        <div class="suggestions-container">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="jai-mb-2">
                        <input id="loanamount" tabindex="-1" name="loan-amount" placeholder="Loan Amount" type="text" class="form-control" readonly>
                      </div>
                    </div>
                    <div class="col">
                      <div class="jai-mb-2">
                        <input id="remainingbalance" tabindex="-1" name="remaining-balance" placeholder="Remaining Balance" type="text" class="form-control" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="jai-mb-2">
                        <input id="mode" tabindex="-1" name="mode" placeholder="Mode" type="text" class="form-control" readonly>
                      </div>
                    </div>
                    <div class="col">
                      <div class="jai-mb-2">
                        <input id="term" tabindex="-1" name="term" placeholder="Term" type="text" class="form-control" readonly>
                      </div>
                    </div>
                    <div class="col">
                      <div class="jai-mb-2">
                        <input id="amortization" tabindex="-1" name="amortization" placeholder="Amortization" type="text" class="form-control" readonly>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col">
                  <div class="row">
                    <div class="col">
                      <div class="jai-mb-2">
                        <input id="payment" name="payment" placeholder="Payment amount" type="text" class="form-control remove-readonly" required>
                      </div>
                    </div>
                    <div class="col">
                      <div class="jai-mb-2">
                        <select id="type" name="type" class="form-control" onchange="setToZero();" required>
                          <option value="" disabled selected>Select type</option>
                          <option value="Cash">Cash</option>
                          <option value="GCash">GCash</option>
                          <option value="Pass">Pass</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col">
                      <div class="jai-mb-2">
                        <input type="hidden" id="collectorid" name="collector-id" class="form-control" value="">
                        <select id="collectorname" name="collector-name" class="form-control" disabled required>
                          <option value="" disabled selected>Collector</option>
                          <?php
                          foreach ($collectors as $i => $collector) {
                            echo '<option value="' . $collector['c_id'] . '">' . $collector['firstname'] . ' ' . $collector['middlename'] . ' ' . $collector['lastname'] . '</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="col">
                      <div class="jai-mb-2">
                        <input id="date" class="datepicker form-control today no-reset set-min-date" name="date" placeholder="Select date of payment" type="text" class="form-control" onkeydown="return false" required>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <input id="loanid" name="loanid" value="" hidden>

            </div>
          </form>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn close-modal" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-action submit-payment">Submit</button>
          <button type="button" class="btn add-new">Submit & Add New</button>
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

  <div class="modal fade" data-borrower="1" id="editBorrower" tabindex="-1" role="dialog" aria-labelledby="editBorrowerLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Borrower</h5>
        </div>
        <div class="modal-body">
          <form action="" method="post" enctype="multipart/form-data">

            <div class="container">
              <div class="row">
                <div class="col">
                  <h5 class="modal-body-label">Borrower</h5>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="First name" type="text" class="form-control" name="firstname" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Middle name" type="text" class="form-control" name="middlename" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Last name" type="text" class="form-control" name="lastname" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Birthday" type="text" class="form-control" name="birthday" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Contact number" type="text" class="form-control" name="contactno" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Address" type="text" class="form-control" name="address" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Occupation" type="text" class="form-control" name="occupation" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Business name" type="text" class="form-control" name="businessname" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <h5 class="modal-body-label">Comaker</h5>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Comaker" type="text" class="form-control" name="comaker" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Comaker Contact Number" type="text" class="form-control" name="comakerno" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-2">
                    <textarea placeholder="Remarks" type="text" class="form-control" name="remarks"></textarea>
                  </div>
                </div>
              </div>
            </div>



          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm close-modal" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary btn-sm submit-edit">Edit</button>
        </div>
      </div>
    </div>
  </div>

  </body>

  </html>