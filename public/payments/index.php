<?php

try {
  /** @var $conn \PDO */
  require_once "../../dbconn.php";

  $search = $_GET['search'] ?? '';

  if ($search) {
    $statement = $conn->prepare("SELECT b.firstname as borrowerfname, b.middlename as borrowermname, b.lastname as borrowerlname, b.picture, l.l_id,
                                        p.p_id, p.amount, p.type, p.date, c.firstname as collectorfname, c.middlename as collectormname, c.lastname as collectorlname
                                 FROM jai_db.payments as p
                                 INNER JOIN jai_db.collectors as c 
                                 ON p.c_id = c.c_id
                                 INNER JOIN jai_db.loans as l
                                 ON p.l_id = l.l_id
                                 INNER JOIN jai_db.borrowers as b 
                                 ON b.b_id = l.b_id
                                 WHERE b.firstname LIKE :search OR b.middlename LIKE :search OR b.lastname LIKE :search OR b.b_id LIKE :search OR c.firstname LIKE :search
                                 OR c.middlename LIKE :search OR c.lastname LIKE :search OR p.type LIKE :search");
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT b.firstname as borrowerfname, b.middlename as borrowermname, b.lastname as borrowerlname, b.picture, l.l_id,
                                        p.p_id, p.amount, p.type, p.date, c.firstname as collectorfname, c.middlename as collectormname, c.lastname as collectorlname
                                 FROM jai_db.payments as p
                                 INNER JOIN jai_db.collectors as c 
                                 ON p.c_id = c.c_id
                                 INNER JOIN jai_db.loans as l
                                 ON p.l_id = l.l_id
                                 INNER JOIN jai_db.borrowers as b 
                                 ON b.b_id = l.b_id
                                 ORDER BY p.p_id ASC");
  }



  $statement->execute();
  $payments = $statement->fetchAll(PDO::FETCH_ASSOC);



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


    <?php //TOTAL AMOUNT TEST

    // foreach ($borrowers as $i => $borrower) {
    //   $totalAmount += $borrower['amount'];
    //   $totalPayable += $borrower['payable'];
    //   $totalBalance += $borrower['balance'];
    // }

    // echo "Total Amount: ₱" . number_format($totalAmount, 2) . "<br>";
    // echo "Total Payable: ₱" . number_format($totalPayable, 2) . "<br>";
    // echo "Total Balance: ₱" . number_format($totalBalance, 2);

    ?>

    <h1>Payments</h1>
  </div>

  <div class="d-flex justify-content-between">
    <a href="addpayment.php" type="button" class="btn btn-outline-success">Add new payment</a>

    <form>
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Search..." name="search" value="<?php echo $search; ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
      </div>
    </form>
  </div>

  <div class="jai-table">
    <div class="row">
      <div class="jai-col-ID">Pay. ID</div>
      <div class="col">Payment Details</div>
      <div class="col">Loan Details</div>
      <div class="col">Remarks</div>
      <div class="col-1">Action</div>
    </div>
    <?php
    foreach ($payments as $i => $payment) {

      $date = date_create($payment['date']); ?>

      <div data-row-id="<?php echo $payment['p_id'] ?>" class="row jai-data-row">
        <div class="jai-col-ID"><?php echo $payment['p_id'] ?></div>
        <div class="col">
          <div class="row">
            <div class="jai-image-col">
              <div class="jai-picture">
                <img src="/<?= 'JAI/public/' . $payment['picture']; ?>" class="thumb-image2">
              </div>
            </div>
            <div class="col">
              <p class="jai-table-name primary-font <?= $payment['borrowerfname'] == 'Angelo' ? 'red' : ''; ?>
                                                <?= $payment['borrowerfname'] == 'Lee' ? 'green' : '' ?>"><span class="jai-table-label">Borrower:</span> <?= ucwords(strtolower($payment['borrowerfname'])) . ' ' . ucwords(strtolower($payment['borrowermname'])) . ' ' . ucwords(strtolower($payment['borrowerlname'])) ?></p>

              <p class="jai-table-address sub-font"> <span class="jai-table-label">Collector: </span><?php echo ucwords(strtolower($payment['collectorfname'])) . ' ' . ucwords(strtolower($payment['collectormname'])) . ' ' . ucwords(strtolower($payment['collectorlname'])) ?></p>
            </div>
            <div class="col">
              <p class="jai-table-comaker primary-font <?= $payment['borrowerfname'] == 'Angelo' ? 'red' : ''; ?>
                                                <?= $payment['borrowerfname'] == 'Lee' ? 'green' : '' ?>"><span class="jai-table-label">Paid Amount:</span> <?= ucwords(strtolower($payment['amount'])) ?></p>
              <p class="jai-table-contact sub-font"> <span class="jai-table-label">Type: </span><?php echo $payment['type'] ?></p>
              <p class="sub-font">Date: <?= date_format($date, "M-d-Y") ?></p>
            </div>

          </div>
        </div>
        <div class="col">
          <div class="row">
            <div class="col">
              <p class="jai-table-amount primary-font"><span class="jai-table-label">Collector???: </span><?php ?></p>
            </div>
            <div class="col">
              <p class="jai-table-payable primary-font"> <span class="jai-table-label">Remaining Bal.: </span> <?php  ?></p>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <p class="jai-table-payment-made sub-font"> <span class="jai-table-label">Payable??: </span> <?php  ?></p>
              <p class="jai-table-mode sub-font"> <span class="jai-table-label">Mode & Term??: </span> <?php ?></p>
              <p class="jai-table-amort sub-font"> <span class="jai-table-label">Amortization???: </span> <?php ?></p>
            </div>
            <div class="col">
              <p class="jai-table-release sub-font"> <span class="jai-table-label">Hmm???: </span> 01/01/22</p>
              <p class="jai-table-due sub-font"> <span class="jai-table-label">Due Date???: </span> 01/01/22</p>
            </div>
          </div>
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
        <div class="d-none hidden-field">
          <input type="hidden" data-jai-firstname="<?= ucwords(strtolower($borrower['firstname'])) ?>" data-jai-middlename="<?= ucwords(strtolower($borrower['middlename'])) ?>" data-jai-lastname="<?= ucwords(strtolower($borrower['lastname'])) ?>" data-jai-address="<?= ucwords(strtolower($borrower['address'])) ?>" data-jai-contactno="<?= ucwords(strtolower($borrower['contactno'])) ?>" data-jai-birthday="<?= ucwords(strtolower($borrower['birthday'])) ?>" data-jai-businessname="<?= ucwords(strtolower($borrower['businessname'])) ?>" data-jai-occupation="<?= ucwords(strtolower($borrower['occupation'])) ?>" data-jai-comaker="<?= ucwords(strtolower($borrower['comaker'])) ?>" data-jai-comakerno="<?= ucwords(strtolower($borrower['comakerno'])) ?>">
        </div>
      </div>
    <?php } ?>
  </div>

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
                  <div class="jai-mb-1">
                    <input placeholder="First name" type="text" class="form-control" name="firstname" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-1">
                    <input placeholder="Middle name" type="text" class="form-control" name="middlename" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-1">
                    <input placeholder="Last name" type="text" class="form-control" name="lastname" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-1">
                    <input placeholder="Birthday" type="text" class="form-control" name="birthday" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-1">
                    <input placeholder="Contact number" type="text" class="form-control" name="contactno" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-1">
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
                  <div class="jai-mb-1">
                    <input placeholder="Comaker" type="text" class="form-control" name="comaker" value="">
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-1">
                    <input placeholder="Comaker Contact Number" type="text" class="form-control" name="comakerno" value="">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-1">
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