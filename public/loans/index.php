<?php

try {
  /** @var $conn \PDO */
  require_once "../../dbconn.php";

  $search = $_GET['search'] ?? '';

  if ($search) {
    $statement = $conn->prepare("SELECT * FROM jai_db.borrowers AS b
                                  INNER JOIN jai_db.loans AS l
                                  ON b.b_id = l.b_id
                                  WHERE b.firstname LIKE :search OR b.middlename LIKE :search OR b.lastname LIKE :search ORDER BY l.l_id ASC");
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
                                        l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id
                                  FROM jai_db.borrowers as b
                                  INNER JOIN jai_db.loans as l
                                  ON b.b_id = l.b_id
                                  ORDER BY l.l_id DESC");
  }

  $statement->execute();
  $loans = $statement->fetchAll(PDO::FETCH_ASSOC);


  //////////////TEST
  // $statement2 = $conn->prepare("SELECT * from jai_db.borrowers ORDER BY b_id ASC");
  // $statement2->execute();
  // $borrowers = $statement2->fetchAll(PDO::FETCH_ASSOC);

  // echo "DB connected successfully";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

// echo "<pre>";
// var_dump($borrowers);
// echo '</pre>';

?>

<?php include_once "../../views/partials/header.php"; ?>

<h1>Loans --TEST--</h1>
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
    <div class="col">Amount</div>
    <div class="col">Payable</div>
    <div class="col">Amortization</div>
    <div class="col-1">Action</div>
  </div>
  <?php
  foreach ($loans as $i => $loan) { ?>
    <div class="row jai-data-row">

      <div class="jai-col-ID"><?php echo $loan['l_id'] ?></div>
      <div class="col">
        <div class="row">
          <div class="jai-picture">
            <img src="/<?= 'JAI/public/' . $loan['picture']; ?>" class="thumb-image2">
          </div>
          <div class="col">
            <p class="jai-table-name primary-font <?= $loan['firstname'] == 'Angelo' ? 'red' : ''; ?>
                                              <?= $loan['firstname'] == 'Lee' ? 'green' : '' ?>"><span class="jai-table-label">Name:</span> <?= '#'.$loan['b_id'].' - '.$loan['firstname'] . ' ' . $loan['middlename'] . ' ' . $loan['lastname'] ?></p>
            <p class="jai-table-contact sub-font"> <span class="jai-table-label">Initial Loan Amount: </span><?php echo $loan['amount'] ?></p>
            <p class="jai-table-address sub-font"> <span class="jai-table-label">Payable: </span><?php echo $loan['payable'] ?></p>
            <p class="jai-table-address sub-font"> <span class="jai-table-label">Balance: </span><?php echo $loan['balance'] ?></p>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="row">
          <div class="col">
            <p class="jai-table-amount primary-font"><span class="jai-table-label">Amount:</span> $100,000.00</p>
          </div>
          <div class="col">
            <p class="jai-table-payable primary-font"> <span class="jai-table-label">Payable: </span> $50,000.00</p>

          </div>
        </div>
        <div class="row">
          <div class="col">
            <p class="jai-table-payment-made sub-font"> <span class="jai-table-label">Payments made: </span> $50,000.00</p>
            <p class="jai-table-mode sub-font"> <span class="jai-table-label">Mode: </span> Weekly, 6 months</p>
            <p class="jai-table-amort sub-font"> <span class="jai-table-label">Amortization: </span> $140.00</p>
          </div>
          <div class="col">
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