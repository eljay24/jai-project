<?php

try {
  /** @var $conn \PDO */
  require_once "../../views/includes/dbconn.php";
  require_once "../../views/includes/loanform.php";

  $search = $_GET['search'] ?? '';

  if ($search) {
    $statement = $conn->prepare("SELECT * FROM jai_db.borrowers AS b
                                  INNER JOIN jai_db.loans AS l
                                  ON b.b_id = l.b_id
                                  WHERE (b.isdeleted = 0) AND (b.b_id LIKE :search OR b.firstname LIKE :search OR b.middlename LIKE :search OR b.lastname LIKE :search) ORDER BY l.l_id ASC");
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
                                        l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id, l.paymentsmade, l.passes
                                  FROM jai_db.borrowers as b
                                  INNER JOIN jai_db.loans as l
                                  ON b.b_id = l.b_id
                                  WHERE b.isdeleted = 0
                                  ORDER BY l.activeloan DESC, l.l_id DESC");
  }

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
    <h1>Loans</h1>
  </div>
  <div class="d-flex justify-content-between">
    <a href="create.php" type="button" class="btn btn-outline-success btn-new-loan">New loan</a>

    <form>
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Search for loans" name="search" value="<?php echo $search; ?>" autofocus>
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </div>
    </form>
  </div>

  <div class="jai-table">
    <div class="row">
      <div class="jai-col-ID">ID</div>
      <div class="col">Borrower</div>
      <div class="col">Loan Details</div>
      <div class="col">Payment Details</div>
      <div class="col-1">Action</div>
    </div>
    <?php
    foreach ($loans as $i => $loan) {

      // SELECT LAST PAYMENT OF LOAN      
      $statementLastPayment = $conn->prepare("SELECT l_id, amount, type, date
                                              FROM jai_db.payments
                                              WHERE l_id = :loanid AND date = (SELECT MAX(date)
                                                                               FROM jai_db.payments
                                                                               WHERE l_id = :loanid)");
      $statementLastPayment->bindValue(":loanid", $loan['l_id']);
      $statementLastPayment->execute();

      $lastPayment = $statementLastPayment->fetch(PDO::FETCH_ASSOC);
      // END - SELECT LAST PAYMENT OF LOAN

      // GET (MONTHLY)/INTEREST RATE
      $loanID = $loan['l_id'];

      $loanPayable = $loan['payable'];
      $loanAmount = $loan['amount'];
      $loanDuration = (int)substr($loan['term'], 0, 1);

      $interestRate = ($loanPayable / $loanAmount) - 1;
      $monthlyInterestRate = $interestRate / $loanDuration;
      // END - GET (MONTHLY)/INTEREST RATE

      // GET TOTAL AMOUNT PAID     
      $statementPayment = $conn->prepare("SELECT sum(amount) as amount
                                          FROM jai_db.payments
                                          WHERE l_id = :l_id");
      $statementPayment->bindValue(':l_id', $loanID);
      $statementPayment->execute();
      $amountPaid = $statementPayment->fetch(PDO::FETCH_ASSOC);
      $amount = $amountPaid['amount'];
      // END - GET TOTAL AMOUNT PAID



      // GET EST. PASS AMOUNT
      $passAmount = $loan['amortization'] * $loan['passes'];
      // END - GET EST. PASS AMOUNT



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
              <p class="jai-table-name primary-font <?= $loan['firstname'] == 'Angelo' ? 'red' : ''; ?>
                                              <?= $loan['firstname'] == 'Lee' ? 'green' : '' ?>"><span class="jai-table-label"></span> <?= ucwords(strtolower($loan['firstname'])) . ' ' . ucwords(strtolower($loan['middlename'])) . ' ' . ucwords(strtolower($loan['lastname'])) ?></p>
              <p class="jai-table-name primary-font"><?= $loan['status'] ?></p>

            </div>
          </div>

        </div>
        <div class="col">
          <div class="row">
            <div class="col">
              <p class="jai-table-name primary-font"><span class="jai-table-label">Loan Reference # <?= $loan['l_id'] ?></span></p>
              <p class="jai-table-contact sub-font"> <span class="jai-table-label">Amount: </span><?= "₱ " . number_format($loan['amount'], 2) ?></p>
              <p class="jai-table-address sub-font"> <span class="jai-table-label">Payable: </span><?= "₱ " . number_format($loan['payable'], 2) ?></p>
              <p class="jai-table-address sub-font"> <span class="jai-table-label">Balance: </span><?= "₱ " . number_format($loan['balance'], 2) ?></p>
              <p class="jai-table-address sub-font"> <span class="jai-table-label">Amortization: </span><?= "₱ " . number_format($loan['amortization'], 2) ?></p>

            </div>
            <div class="col">
              <br>
              <p class="jai-table-address sub-font"> <?= ucwords(strtolower($loan['mode'])) . ', ' . $loan['term'] ?></p>
              <p class="jai-table-address sub-font">Interest: <?= number_format($interestRate * 100, 2) . '%' ?></p>
              <p class="jai-table-address sub-font">Monthly interest: <?= number_format($monthlyInterestRate * 100, 2) . '%' ?></p>


            </div>
          </div>
        </div>
        <div class="col position-relative">
          <div class="row">
            <div class="col">
              <p class="jai-table-amount primary-font"><span class="jai-table-label">Payments made:</span> <?php echo $loan['paymentsmade'] ?></p>
            </div>
            <div class="col">
              <p class="jai-table-payable primary-font"> <span class="jai-table-label">Passes: </span> <?php echo $loan['passes'] ?></p>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <p class="jai-table-payment-made sub-font"> <span class="jai-table-label">Total Paid: </span> <?= "₱ " . number_format($amount, 2) ?> </p>
              <p class="jai-table-mode sub-font"> <span class="jai-table-label">to follow: </span> TEST</p>
              <p class="jai-table-amort sub-font"> <span class="jai-table-label">to follow: </span> TEST</p>
            </div>
            <div class="col">
              <p class="jai-table-release sub-font"> <span class="jai-table-label">Est. Loss: </span> <?= "₱ " . number_format($passAmount, 2) ?> </p>
              <p class="jai-table-release sub-font"> <span class="jai-table-label">Release Date: </span> 01/01/22</p>
              <p class="jai-table-due sub-font"> <span class="jai-table-label">Due Date: </span> 01/01/22</p>
            </div>
          </div>
          <?php if ($lastPayment != 0) { ?>
            <div class="row">
              <p class="primary-font">Latest Payment</p>
              <p class="sub-font"> <span class="jai-table-label">Date: </span> <?= $lastPayment['date'] ?></p>
              <p class="sub-font"> <span class="jai-table-label">Type: </span> <?= $lastPayment['type'] ?></p>
              <p class="sub-font"> <span class="jai-table-label">Amount: </span> <?= number_format($lastPayment['amount'], 2) ?></p>
            </div>
          <?php } ?>
          <!-- <textarea class="jai-table-input" type="text"></textarea> -->
        </div>
        <div class="col-1 d-flex align-items-center justify-content-around">
          <a href="update.php?id=<?php echo $loan['l_id'] ?>" class="btn btn-primary btn-sm edit-btn">Edit</a>
          <button type="button" class="btn btn-danger btn-sm delete-btn delete-borrower" data-toggle="modal" data-target="#deleteBorrower">Delete</button>
        </div>
      </div>
    <?php } ?>
  </div>

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

<div class="modal fade" data-loan="1" id="createloan" tabindex="-1" role="dialog" aria-labelledby="createloanLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Loan</h5>
      </div>
      <div class="modal-body">
        <form class="create-form" autocomplete="off" action="create-loan" method="post" enctype="multipart/form-data">
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
                <div class="jai-mb-2">
                  <select name="borrower" id="borrower" class="form-control" required>
                    <option value="" disabled selected>Select borrower</option>
                    <?php
                    foreach ($borrowers as $i => $borrower) {
                      echo '<option value="' . $borrower['b_id'] . '">#' . $borrower['b_id'] . ' ' . ucwords(strtolower($borrower['firstname'])) . ' ' . ucwords(strtolower($borrower['middlename'])) . ' ' . ucwords(strtolower($borrower['lastname'])) . '</option>';
                    }
                    ?>
                  </select>
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
                      echo "<option>" . ucwords(strtolower($term['term']))  . "</option>";
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
                  <input placeholder="Release Date" type="text" class="form-control datepicker no-limit" name="contactno" value="" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="jai-mb-2">
                  <input placeholder="Due Date" type="text" class="form-control" name="address" value="" readonly>
                </div>
              </div>
            </div>

          </div>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm close-modal" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary btn-sm submit-create-loan">Submit</button>
      </div>
    </div>
    <div class="success-message" style="display: none;">
      <div class="close-container">
        <div class="close-button"></div>
      </div>
      <h3>
        Loan Created.
      </h3>
    </div>
  </div>
</div>

</body>

</html>