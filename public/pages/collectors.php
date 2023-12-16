<?php

try {
  /** @var $conn \PDO */
  require_once "../../views/includes/dbconn.php";

  $search = $_GET['search'] ?? '';

  //#region <PAGINATION>
  // SET CURRENT PAGE NUMBER
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
    FROM
      jai_db.collectors as c
    WHERE
      (is_deleted = 0)
      AND (
        c.firstname LIKE :search
        OR c.middlename LIKE :search
        OR c.lastname LIKE :search
        OR CONCAT(c.firstname, ' ', c.middlename, ' ', c.lastname) LIKE :search
        OR CONCAT(c.firstname, ' ', c.lastname) LIKE :search
        OR CONCAT(c.lastname, ' ', c.firstname) LIKE :search
      )
    ORDER BY
      c.c_id ASC");
    $statementTotalRows->bindValue(':search', "%$search%");
  } else {
    $statementTotalRows = $conn->prepare("SELECT COUNT(*) as count FROM jai_db.collectors
                                          WHERE (is_deleted = 0)");
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
    $statement = $conn->prepare("SELECT  c.c_id,
    c.firstname,
    c.lastname,
    c.contactno,
    c.birthday,
    c.address,
    c.datecreated,
    COUNT(l.c_id) as active_loans
    FROM
      jai_db.collectors as c
    LEFT JOIN jai_db.loans as l 
    ON c.c_id = l.c_id
    WHERE
      (is_deleted = 0) AND l.activeloan <> 0
      AND (
        c.firstname LIKE :search
        OR c.middlename LIKE :search
        OR c.lastname LIKE :search
        OR CONCAT(c.firstname, ' ', c.middlename, ' ', c.lastname) LIKE :search
        OR CONCAT(c.firstname, ' ', c.lastname) LIKE :search
        OR CONCAT(c.lastname, ' ', c.firstname) LIKE :search
      )
    GROUP BY
      c.c_id
    ORDER BY
      c.c_id ASC
    LIMIT
      :offset, :numOfRowsPerPage");

    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT
                                  c.c_id,
                                  c.firstname,
                                  c.lastname,
                                  c.contactno,
                                  c.birthday,
                                  c.address,
                                  c.datecreated,
                                  COUNT(CASE WHEN l.activeloan <> 0 THEN c.c_id ELSE NULL END ) as active_loans
                                FROM
                                  jai_db.collectors as c
                                LEFT JOIN jai_db.loans as l 
                                ON c.c_id = l.c_id
                                WHERE
                                  c.is_deleted = 0 
                                --   AND l.activeloan <> 0
                                GROUP BY
                                  c.c_id
                                ORDER BY
                                  c.c_id ASC
                                LIMIT
                                  :offset, :numOfRowsPerPage");
  }

  $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
  $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL
  $statement->execute();

  $collectors = $statement->fetchAll(PDO::FETCH_ASSOC);
  // var_dump($collectors);

  $statementLastCollectorID = $conn->prepare("SELECT MAX(c.c_id) as id
                                           FROM jai_db.collectors AS c");
  $statementLastCollectorID->execute();
  $lastCollectorID = $statementLastCollectorID->fetch(PDO::FETCH_ASSOC);


  // echo "DB connected successfully";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>

<?php include_once "../../views/partials/header.php"; ?>

<div class="content-container">
  <div class="page-name">
    <div class="title-container d-flex">
      <img src="../assets/icons/users_dark.svg">
      <h1>Collectors</h1>
    </div>
    <div class="actions-container">
      <a href="#" type="button" class="btn create-collector create-btn"><img src="../assets/icons/plus.svg"> Add Collector</a>

      <form class="table-search">
        <div class="input-group search-group">
          <input type="text" class="form-control search-input" placeholder="Search..." name="search" value="<?= $search; ?>" autofocus onfocus="this.select()">
          <button class="btn search-btn" type="submit">Search</button>
        </div>
      </form>
    </div>
  </div>


  <div class="neumorph-container">
    <div class="table-wrapper">
      <div class="jai-table table-container collector-table">
        <div class="row table-header">
          <div class="jai-col-ID">ID</div>
          <div class="col">Personal Info</div>
          <div class="col">Details</div>
          <!-- <div class="col">Remarks</div> -->
          <div class="col-1 text-center">Action</div>
        </div>
        <?php
        $count = 1;
        foreach ($collectors as $i => $collector) {

          $c_id = $collector['c_id'];
          $picturePath = 'assets/icons/collector-picture-placeholder.jpg';
        ?>
          <div class="row jai-data-row" data-row="row-<?= $collector['c_id'] ?>">
            <div class="jai-col-ID"><?= $collector['c_id'] ?></div>
            <div class="col">
              <div class="row">
                <div class="jai-image-col">
                  <div class="jai-picture zoom">
                    <img src="../<?= $picturePath; ?>" class="thumb-image2">
                  </div>
                </div>
                <div class="col">
                  <div class="row">
                    <p class="jai-table-name primary-font"><span class="jai-table-label">Name:</span> <span class="value"><?= ucwords(strtolower($collector['firstname'])) . ' ' . ucwords(strtolower($collector['lastname'])) ?></span></p>
                    <p class="jai-table-contact primary-font"> <span class="jai-table-label">Contact: </span> <span class="value"><?= $collector['contactno'] ?></span></p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="row">
                <p class="jai-table-contact primary-font"> <span class="jai-table-label">Date Joined: </span> <span class="value"><?= $collector['datecreated'] ?></span></p>
                <p class="jai-table-contact primary-font"> <span class="jai-table-label">Active Loans: </span> <span class="value"><?= $collector['active_loans'] ?></span></p>
              </div>
            </div>
            <div class="col-1">
              <div class="row">
              </div>
            </div>
            <!-- <div class="col position-relative">
          <textarea class="jai-table-input" type="text"></textarea>
        </div> -->
          </div>
        <?php
          $count++;
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

  <div class="modal fade" data-collector="1" id="deletecollector" tabindex="-1" role="dialog" aria-labelledby="deletecollectorLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm close-modal" data-dismiss="modal">Cancel</button>
          <form class="delete-form" style="display: inline-block" method="post" action="/JAI/views/functions/delete-collector.php">
            <input type="hidden" name="c_id" value="">
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade form-modal" data-collector="1" id="createCollector" tabindex="-1" role="dialog" aria-labelledby="createCXollectorLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Collector</h5>
        </div>
        <div class="modal-body">
          <form class="action-form" autocomplete="off" action="create-collector" method="post" enctype="multipart/form-data">
            <input type="hidden" class="d-none" name="c_id" value="">
            <input name="data-row" type="hidden" class="d-none" value=''>

            <div class="container">
              <div class="row">
                <div class="col-3">
                  <div class="form-image-container">
                    <img id="formImg" class="form-image" src="../assets/icons/collector-picture-placeholder.jpg" alt="your image" />
                    <input accept="image/*" type='file' id="imgInp" name="picture" class="img-input d-none" />
                  </div>
                </div>
                <div class="col">
                  <div class="row">
                    <div class="col">
                      <h5 class="modal-body-label">New Collector (#<?= $lastCollectorID['id'] + 1 ?>)</h5>
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
                        <input placeholder="Middle name" type="text" class="form-control letters-only" name="middlename" value="">
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
                        <input placeholder="Address" type="text" class="form-control" name="address" value="" required>
                      </div>
                    </div>
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