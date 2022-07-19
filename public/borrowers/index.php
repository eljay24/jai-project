<?php

try {
  /** @var $conn \PDO */
  require_once "../../dbconn.php";

  $search = $_GET['search'] ?? '';

  if ($search) {
    $statement = $conn->prepare("SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
                                        l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id
                                 FROM jai_db.borrowers as b
                                 LEFT JOIN jai_db.loans as l
                                 ON b.b_id = l.b_id
                                 WHERE firstname LIKE :search OR middlename LIKE :search OR lastname LIKE :search OR comaker LIKE :search OR b.b_id LIKE :search ORDER BY b.b_id ASC");
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
                                        l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id
                                 FROM jai_db.borrowers as b
                                 LEFT JOIN jai_db.loans as l
                                 ON b.b_id = l.b_id
                                 UNION
                                 SELECT b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
                                        l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id
                                 FROM jai_db.borrowers as b
                                 RIGHT JOIN jai_db.loans as l
                                 ON b.b_id = l.b_id");
  }



  $statement->execute();
  $borrowers = $statement->fetchAll(PDO::FETCH_ASSOC);

  // echo "DB connected successfully";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

// echo "<pre>";
// var_dump($borrowers);
// echo '</pre>';
// exit;

?>

<?php include_once "../../views/partials/header.php"; ?>

<div class="content-container">
  <div class="page-name">
    <h1>Borrowers</h1>
  </div>

  <div class="d-flex justify-content-between">
    <a href="create.php" type="button" class="btn btn-outline-success">Create new borrower</a>

    <form>
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Search for borrowers" name="search" value="<?php echo $search; ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </div>
    </form>
  </div>

  <div class="jai-table">
    <div class="row">
      <div class="jai-col-ID">ID</div>
      <div class="col">Borrower</div>
      <div class="col">Loan Details</div>
      <div class="col">Remarks</div>
      <div class="col-1">Action</div>
    </div>
    <?php
    foreach ($borrowers as $i => $borrower) { ?>
      <div class="row jai-data-row">
        <div class="jai-col-ID"><?php echo $borrower['b_id'] ?></div>
        <div class="col">
          <div class="row">
            <div class="jai-image-col">
              <div class="jai-picture">
                <img src="/<?= 'JAI/public/' . $borrower['picture']; ?>" class="thumb-image2">
              </div>
            </div>
            <div class="col">
              <p class="jai-table-name primary-font <?= $borrower['firstname'] == 'Angelo' ? 'red' : ''; ?>
                                                <?= $borrower['firstname'] == 'Lee' ? 'green' : '' ?>"><span class="jai-table-label">Name:</span> <?= ucwords(strtolower($borrower['firstname'])) . ' ' . ucwords(strtolower($borrower['middlename'])) . ' ' . ucwords(strtolower($borrower['lastname'])) ?></p>
              <p class="jai-table-contact sub-font"> <span class="jai-table-label">Contact: </span><?php echo $borrower['contactno'] ?></p>
              <p class="jai-table-address sub-font"> <span class="jai-table-label">Address: </span><?php echo $borrower['address'] ?></p>
            </div>
            <div class="col">
              <p class="jai-table-comaker primary-font <?= $borrower['firstname'] == 'Angelo' ? 'red' : ''; ?>
                                                <?= $borrower['firstname'] == 'Lee' ? 'green' : '' ?>"><span class="jai-table-label">Comaker:</span> <?= ucwords(strtolower($borrower['comaker'])) ?></p>
              <p class="jai-table-contact sub-font"> <span class="jai-table-label">Contact: </span><?php echo $borrower['comakerno'] ?></p>
            </div>
          </div>
        </div>
        <div class="col">
          <?php if (ucwords(strtolower($borrower['amount'])) == "") { ?>
            No active loan
          <?php } else { ?>
            <div class="row">
              <div class="col">
                <p class="jai-table-amount primary-font"><span class="jai-table-label">Amount:</span> <?= "₱" . $amountValue = ucwords(strtolower($borrower['amount'])) == "" ? "N/A" : ucwords(strtolower($borrower['amount'])); ?></p>
              </div>
              <div class="col">
                <p class="jai-table-payable primary-font"> <span class="jai-table-label">Payable: </span> <?= "₱" . ucwords(strtolower($borrower['payable'])) ?></p>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <p class="jai-table-payment-made sub-font"> <span class="jai-table-label">Balance: </span> <?= "₱" . ucwords(strtolower($borrower['balance'])) ?></p>
                <p class="jai-table-mode sub-font"> <span class="jai-table-label">Mode: </span> <?= ucwords(strtolower($borrower['mode'] . ', ' . $borrower['term'])) ?></p>
                <p class="jai-table-amort sub-font"> <span class="jai-table-label">Amortization: </span> <?= "₱" . ucwords(strtolower($borrower['amortization'])) ?></p>
              </div>
              <div class="col">
                <p class="jai-table-release sub-font"> <span class="jai-table-label">Release Date: </span> 01/01/22</p>
                <p class="jai-table-due sub-font"> <span class="jai-table-label">Due Date: </span> 01/01/22</p>
              </div>
            </div>
          <?php
          } ?>
        </div>
        <div class="col position-relative">
          <textarea class="jai-table-input" type="text"></textarea>
        </div>
        <div class="col-1 d-flex align-items-center justify-content-around">
          <!-- <a href="update.php?id=<?php //echo $borrower['b_id'] 
                                      ?>" class="btn btn-primary btn-sm edit-btn">Edit</a> -->
          <a href="#" class="btn btn-primary btn-sm edit-btn">Edit</a>
          <button type="button" class="btn btn-danger btn-sm delete-borrower delete-btn" data-toggle="modal" data-target="#deleteBorrower">Delete</button>
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
            <input type="hidden" name="b_id" value="">
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editBorrower" tabindex="-1" role="dialog" aria-labelledby="editBorrowerLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <form action="" method="post" enctype="multipart/form-data">

            <div class="container">
              <div class="row">
                <div class="col">
                  <div class="mb-3">
                    <input placeholder="First name" type="text" class="form-control" name="firstname" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <input placeholder="Middle name" type="text" class="form-control" name="middlename" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <input placeholder="Last name" type="text" class="form-control" name="lastname" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3">
                    <input placeholder="Address" type="text" class="form-control" name="address" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <input placeholder="Contact number" type="text" class="form-control" name="contactno" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3">
                    <input placeholder="Birthday" type="text" class="form-control" name="birthday" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <input placeholder="Business name" type="text" class="form-control" name="businessname" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="mb-3">
                    <input placeholder="Occupation" type="text" class="form-control" name="occupation" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <input placeholder="Comaker" type="text" class="form-control" name="comaker" value="">
                  </div>
                </div>
              </div>
            </div>


            <div class="mb-3">
              <input placeholder="Comaker Contact Number" type="text" class="form-control" name="comakerno" value="">
            </div>
            <div class="mb-3">
              <textarea placeholder="Remarks" type="text" class="form-control" name="remarks"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>

          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm close-modal" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-secondary btn-sm">Edit</button>
        </div>
      </div>
    </div>
  </div>

  </body>

  </html>