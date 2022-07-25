<?php

try {
  /** @var $conn \PDO */
  require_once "../../dbconn.php";

  $search = $_GET['search'] ?? '';

  if ($search) {
    $statement = $conn->prepare("SELECT * FROM jai_db.borrowers AS b
                                  INNER JOIN jai_db.loans AS l
                                  ON b.b_id = l.b_id
                                  WHERE (b.isdeleted = 0) AND (b.firstname LIKE :search OR b.middlename LIKE :search OR b.lastname LIKE :search) ORDER BY l.l_id ASC");
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
                                        l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id, l.paymentsmade, l.passes
                                  FROM jai_db.borrowers as b
                                  INNER JOIN jai_db.loans as l
                                  ON b.b_id = l.b_id
                                  WHERE (b.isdeleted = 0)
                                  ORDER BY b.b_id ASC");
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
    <a href="create.php" type="button" class="btn btn-outline-success">New loan</a>

    <form>
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Search for loans" name="search" value="<?php echo $search; ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </div>
    </form>
  </div>

  <div class="jai-table">
    <div class="row">
      <div class="jai-col-ID">Loan ID</div>
      <div class="col">Loan Details</div>
      <div class="col">Payment Details</div>
      <div class="col">Amortization</div>
      <div class="col-1">Action</div>
    </div>
    <?php
    foreach ($loans as $i => $loan) {

      $loanID = $loan['l_id'];

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

        <div class="jai-col-ID"><?php echo $loan['l_id'] ?></div>
        <div class="col">
          <div class="row">
            <div class="jai-image-col">
              <div class="jai-picture">
                <img src="/<?= 'JAI/public/' . $loan['picture']; ?>" class="thumb-image2">
              </div>
            </div>
            <div class="col">
              <p class="jai-table-name primary-font <?= $loan['firstname'] == 'Angelo' ? 'red' : ''; ?>
                                              <?= $loan['firstname'] == 'Lee' ? 'green' : '' ?>"><span class="jai-table-label">Name:</span> <?= '#' . $loan['b_id'] . ' - ' . $loan['firstname'] . ' ' . $loan['middlename'] . ' ' . $loan['lastname'] ?></p>
              <p class="jai-table-contact sub-font"> <span class="jai-table-label">Initial Loan Amount: </span><?= "₱ " . number_format($loan['amount'],2) ?></p>
              <p class="jai-table-address sub-font"> <span class="jai-table-label">Payable: </span><?= "₱ " . number_format($loan['payable'],2) ?></p>
              <p class="jai-table-address sub-font"> <span class="jai-table-label">Balance: </span><?= "₱ " . number_format($loan['balance'],2) ?></p>
              <p class="jai-table-address sub-font"> <span class="jai-table-label">Amortization: </span><?= "₱ " . number_format($loan['amortization'],2) ?></p>
            </div>
          </div>
        </div>
        <div class="col">
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
              <p class="jai-table-payment-made sub-font"> <span class="jai-table-label">Total Amount Paid: </span> <b> <?= "₱ " . number_format($amount, 2) ?> </b> </p>
              <p class="jai-table-mode sub-font"> <span class="jai-table-label">to follow: </span> TEST</p>
              <p class="jai-table-amort sub-font"> <span class="jai-table-label">to follow: </span> TEST</p>
            </div>
            <div class="col">
              <p class="jai-table-release sub-font"> <span class="jai-table-label">Estimated Loss: </span> <b> <?= "₱ " . number_format($passAmount, 2) ?> </b> </p>
              <p class="jai-table-release sub-font"> <span class="jai-table-label">Release Date: </span> 01/01/22</p>
              <p class="jai-table-due sub-font"> <span class="jai-table-label">Due Date: </span> 01/01/22</p>
            </div>
          </div>
        </div>
        <div class="col position-relative">
          <textarea class="jai-table-input" type="text"></textarea>
        </div>
        <div class="col-1 d-flex align-items-center justify-content-around">
          <a href="update.php?id=<?php echo $loan['l_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
          <button type="button" class="btn btn-danger btn-sm delete-borrower" data-toggle="modal" data-target="#deleteBorrower">Delete</button>
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

</body>

</html>