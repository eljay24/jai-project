<?php

try {
  /** @var $conn \PDO */
  require_once "../../dbconn.php";

  //#region <PAGINATION>

  // PAGE NUMBER
  if (isset($_GET['pageno'])) {
    $pageNum = $_GET['pageno'];
  } else {
    $pageNum = 1;
  }

  $numOfRowsPerPage = 5;

  $offset = ($pageNum - 1) * $numOfRowsPerPage;
  $previousPage = $pageNum - 1;
  $nextPage = $pageNum + 1;
  $adjacents = "2";

  $statementTotalRows = $conn->prepare("SELECT COUNT(*) as count FROM jai_db.borrowers");
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










  $search = $_GET['search'] ?? '';

  if ($search) {
    $statement = $conn->prepare("SELECT b.b_id, b.isdeleted, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated, b.activeloan,
                                        l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id
                                 FROM jai_db.borrowers as b
                                 LEFT JOIN jai_db.loans as l
                                 ON l.l_id = (SELECT MAX(l_id)
                                              FROM jai_db.loans as l2
                                              WHERE l2.b_id = b.b_id LIMIT 1) 
                                 WHERE (isdeleted = 0) AND (firstname LIKE :search OR middlename LIKE :search OR lastname LIKE :search OR comaker LIKE :search OR b.b_id LIKE :search) ORDER BY b.b_id ASC
                                 LIMIT :offset, :numOfRowsPerPage");
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT b.b_id, b.isdeleted, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                        b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated, b.activeloan,
                                        l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
                                        l.releasedate, l.duedate, l.status, l.c_id
                                 FROM jai_db.borrowers as b
                                 LEFT JOIN jai_db.loans as l
                                 ON l.l_id = (SELECT MAX(l_id)
                                              FROM jai_db.loans as l2
                                              WHERE l2.b_id = b.b_id LIMIT 1) 
                                 WHERE b.isdeleted = 0
                                 LIMIT :offset, :numOfRowsPerPage");

    // $statement = $conn->prepare("SELECT b.isdeleted, b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
    //                                     b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
    //                                     l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
    //                                     l.releasedate, l.duedate, l.status, l.c_id
    //                              FROM jai_db.borrowers as b
    //                              LEFT JOIN jai_db.loans as l
    //                              ON b.b_id = l.b_id
    //                              WHERE b.isdeleted = 0
    //                              UNION
    //                              SELECT b.isdeleted, b.b_id, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
    //                                     b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated,
    //                                     l.l_id, l.amount, l.payable, l.balance, l.mode, l.term, l.interestrate, l.amortization,
    //                                     l.releasedate, l.duedate, l.status, l.c_id
    //                              FROM jai_db.borrowers as b
    //                              RIGHT JOIN jai_db.loans as l
    //                              ON b.b_id = l.b_id
    //                              WHERE b.isdeleted = 0");
  }

  $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
  $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL

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
      <div class="col">Borrower Details</div>
      <div class="col">Loan Details</div>
      <div class="col">Remarks</div>
      <div class="col-1">Action</div>
    </div>
    <?php
    $count = 1;
    foreach ($borrowers as $i => $borrower) { ?>
      <div data-row-id="<?php echo $borrower['b_id'] ?>" class="row jai-data-row">
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
          <?php if ($borrower['activeloan'] == 0) { ?>
            <h4>
              No active loan
            </h4>
          <?php } else { ?>
            <div class="row">
              <div class="col">
                <p class="jai-table-amount primary-font"><span class="jai-table-label">Amount:</span> <?= "₱ " . number_format($borrower['amount'], 2); ?></p>
              </div>
              <div class="col">
                <p class="jai-table-payable primary-font"> <span class="jai-table-label">Balance: </span> <?= "₱ " . number_format($borrower['balance'], 2) ?></p>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <p class="jai-table-payment-made sub-font"> <span class="jai-table-label">Payable: </span> <?= "₱ " . number_format($borrower['payable'], 2) ?></p>
                <p class="jai-table-mode sub-font"> <span class="jai-table-label">Mode & Term: </span> <?= ucwords(strtolower($borrower['mode'] . ', ' . $borrower['term'])) ?></p>
                <p class="jai-table-amort sub-font"> <span class="jai-table-label">Amortization: </span> <?= "₱ " . number_format($borrower['amortization'], 2) ?></p>
              </div>
              <div class="col">
                <p class="jai-table-release sub-font"> <span class="jai-table-label">Release Date: </span> 01/01/22</p>
                <p class="jai-table-due sub-font"> <span class="jai-table-label">Due Date: </span> 01/01/22</p>
                <p class="sub-font"> <span class="jai-table-label"><strong>(TEST) LOAN ID: </span> <?= $borrower['l_id'] ?> </strong></p>
              </div>
            </div>
          <?php
          } ?>
        </div>
        <div class="col position-relative">
          <textarea class="jai-table-input" type="text"></textarea>
        </div>
        <div class="col-1 d-flex align-items-center justify-content-around">
          <a href="#" class="btn btn-primary btn-sm edit-btn">Edit</a>
          <button type="button" class="btn btn-danger btn-sm delete-borrower delete-btn" data-toggle="modal" data-target="#deleteBorrower">Delete</button>
        </div>
        <div class="d-none hidden-field">
          <form id="hidden-form-<?= $count; ?>" class="hidden-form" action="">
            <input type="hidden" name="b_id" value="<?= ucwords(strtolower($borrower['b_id'])) ?>">
            <input type="hidden" name="firstname" value="<?= ucwords(strtolower($borrower['firstname'])) ?>">
            <input type="hidden" name="middlename" value="<?= ucwords(strtolower($borrower['middlename'])) ?>">
            <input type="hidden" name="lastname" value="<?= ucwords(strtolower($borrower['lastname'])) ?>">
            <input type="hidden" name="address" value="<?= ucwords(strtolower($borrower['address'])) ?>">
            <input type="hidden" name="contactno" value="<?= ucwords(strtolower($borrower['contactno'])) ?>">
            <input type="hidden" name="birthday" value="<?= ucwords(strtolower($borrower['birthday'])) ?>">
            <input type="hidden" name="businessname" value="<?= ucwords(strtolower($borrower['businessname'])) ?>">
            <input type="hidden" name="occupation" value="<?= ucwords(strtolower($borrower['occupation'])) ?>">
            <input type="hidden" name="comaker" value="<?= ucwords(strtolower($borrower['comaker'])) ?>">
            <input type="hidden" name="comakerno" value="<?= ucwords(strtolower($borrower['comakerno'])) ?>">
          </form>
        </div>
      </div>
    <?php
      $count++;
    } ?>
  </div>

  <!-- PAGE NAVIGATION -->
  <div style='padding: 10px 20px 0px; border-top: dotted 1px #CCC;'>
    <strong>Page <?php echo $pageNum . " of " . $totalPages; ?></strong>
  </div>

  <ul class="pagination">
    <?php if ($pageNum > 1) {
      echo "<li class='page-item'><a class='page-link' href='?pageno=1'>First Page</a></li>";
    } ?>

    <li <?php if ($pageNum <= 1) {
          echo "class='page-link disabled'";
        } ?>>
      <a <?php if ($pageNum > 1) {
            echo "class='page-link' href='?pageno=$previousPage'";
          } ?>>Previous</a>
    </li>

    <?php
    if ($totalPages <= 10) {
      for ($counter = 1; $counter <= $totalPages; $counter++) {
        if ($counter == $pageNum) {
          echo "<li class='page-item active'><a class='page-link'>$counter</a></li>";
        } else {
          echo "<li class='page-item'><a class='page-link' href='?pageno=$counter'>$counter</a></li>";
        }
      }
    } elseif ($totalPages > 10) {
      if ($pageNum <= 4) {
        for ($counter = 1; $counter < 8; $counter++) {
          if ($counter == $pageNum) {
            echo "<li class='page-item active'><a class='page-link'>$counter</a></li>";
          } else {
            echo "<li class='page-item'><a class='page-link' href='?pageno=$counter'>$counter</a></li>";
          }
        }
        echo "<li class='page-item'><a class='page-link'>...</a></li>";
        echo "<li class='page-item'><a class='page-link' href='?pageno=$secondLast'>$secondLast</a></li>";
        echo "<li class='page-item'><a class='page-link' href='?pageno=$totalPages'>$totalPages</a></li>";
      } elseif ($pageNum > 4 && $pageNum < $totalPages - 4) {
        echo "<li class='page-item'><a class='page-link' href='?pageno=1'>1</a></li>";
        echo "<li class='page-item'><a class='page-link' href='?pageno=2'>2</a></li>";
        echo "<li class='page-item'><a class='page-link'>...</a></li>";
        for (
          $counter = $pageNum - $adjacents;
          $counter <= $pageNum + $adjacents;
          $counter++
        ) {
          if ($counter == $pageNum) {
            echo "<li class='page-item active'><a class='page-link'>$counter</a></li>";
          } else {
            echo "<li class='page-item'><a class='page-link' href='?pageno=$counter'>$counter</a></li>";
          }
        }
        echo "<li class='page-item'><a class='page-link'>...</a></li>";
        echo "<li class='page-item'><a class='page-link' href='?pageno=$secondLast'>$secondLast</a></li>";
        echo "<li class='page-item'><a class='page-link' href='?pageno=$totalPages'>$totalPages</a></li>";
      } else {
        echo "<li class='page-item'><a class='page-link' href='?pageno=1'>1</a></li>";
        echo "<li class='page-item'><a class='page-link' href='?pageno=2'>2</a></li>";
        echo "<li class='page-item'><a class='page-link'>...</a></li>";
        for (
          $counter = $totalPages - 6;
          $counter <= $totalPages;
          $counter++
        ) {
          if ($counter == $pageNum) {
            echo "<li class='page-item active'><a class='page-link'>$counter</a></li>";
          } else {
            echo "<li class='page-item'><a class='page-link' href='?pageno=$counter'>$counter</a></li>";
          }
        }
      }
    }
    ?>

    <li <?php if ($pageNum >= $totalPages) {
          echo "class='page-link disabled'";
        } ?>>
      <a <?php if ($pageNum < $totalPages) {
            echo "class='page-link' href='?pageno=$nextPage'";
          } ?>>Next</a>
    </li>

    <?php if ($pageNum < $totalPages) {
      echo "<li class='page-item'><a class='page-link' href='?pageno=$totalPages'>Last &rsaquo;&rsaquo;</a></li>";
    } ?>
  </ul>

  <!-- END - PAGE NAVIGATION -->

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
          <form class="edit-form" autocomplete="off" action="edit-borrower" method="post" enctype="multipart/form-data">
            <input id="b_id" type="hidden" class="d-none" name="b_id" value="">

            <div class="container">
              <div class="row">
                <div class="col">
                  <h5 class="modal-body-label">Borrower</h5>
                </div>
              </div>
              <div class="row">

                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="First name" type="text" class="form-control letters-only" name="firstname" value="" required>
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Middle name" type="text" class="form-control letters-only" name="middlename" value="" required>
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Last name" type="text" class="form-control letters-only" name="lastname" value="" required>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-2">
                    <input readonly placeholder="Birthday" type="text" class="form-control datepicker" name="birthday" value="" required>
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Contact number" type="text" class="form-control phone-number" name="contactno" value="" required>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Address" type="text" class="form-control alphanumeric" name="address" value="" required>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Occupation" type="text" class="form-control letters-only" name="occupation" value="" required>
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Business name" type="text" class="form-control alphanumeric" name="businessname" value="" required>
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
                    <input placeholder="Comaker" type="text" class="form-control letters-only" name="comaker" value="" required>
                  </div>
                </div>
                <div class="col">
                  <div class="jai-mb-2">
                    <input placeholder="Comaker Contact Number" type="text" class="form-control phone-number" name="comakerno" value="" required>
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
          <button type="button" class="btn btn-primary btn-sm submit-edit">Submit</button>
        </div>
      </div>
      <div class="success-message" style="display: none;">
        <div class="close-container">
          <div class="close-button"></div>
        </div>
        <h3>
          Borrower has been updated.
        </h3>
      </div>
    </div>
  </div>

  </body>

  </html>