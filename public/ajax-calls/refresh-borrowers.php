<?php

// /** @var $conn \PDO */
require "../../views/includes/dbconn.php";

$search = null;

//#region <PAGINATION>
// PAGE NUMBER

if (isset($_POST['action'])) {

  // echo json_encode($_POST['action'] . $_POST['search_value'] . $_POST['page_number']);

  $search = $_POST['search_value'];
  $pageNum = $_POST['page_number'];

  $numOfRowsPerPage = 10;

  $offset = ($pageNum - 1) * $numOfRowsPerPage;
  $previousPage = $pageNum - 1;
  $nextPage = $pageNum + 1;
  $adjacents = "2";

  if ($search) {
    $statementTotalRows = $conn->prepare("SELECT COUNT(*) as count FROM jai_db.borrowers as b
                                            WHERE (isdeleted = 0) AND (firstname LIKE :search OR middlename LIKE :search OR lastname LIKE :search OR comaker LIKE :search OR b.b_id LIKE :search) ORDER BY b.b_id ASC
                                            ");
    $statementTotalRows->bindValue(':search', "%$search%");
  } else {
    $statementTotalRows = $conn->prepare("SELECT COUNT(*) as count FROM jai_db.borrowers
                                            WHERE (isdeleted = 0)");
  }
  $statementTotalRows->execute();

  $totalRows = $statementTotalRows->fetchAll(PDO::FETCH_ASSOC);
  $totalPages = ceil($totalRows[0]["count"] / $numOfRowsPerPage);
  $secondLast = $totalPages - 1;

  if ($search) {
    $statement = $conn->prepare("SELECT b.b_id, b.isdeleted, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                          b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated, b.activeloan,
                                          l.l_id, l.amount, l.payable, l.mode, l.term, l.interestrate, l.amortization,
                                          l.releasedate, l.duedate, l.status, l.c_id
                                   FROM jai_db.borrowers as b
                                   LEFT JOIN jai_db.loans as l
                                   ON l.l_id = (SELECT MAX(l_id)
                                                FROM jai_db.loans as l2
                                                WHERE l2.b_id = b.b_id LIMIT 1) 
                                   WHERE (isdeleted = 0) AND (firstname LIKE :search OR middlename LIKE :search OR lastname LIKE :search OR comaker LIKE :search OR b.b_id LIKE :search
                                          OR CONCAT(b.firstname, ' ', b.middlename, ' ', b.lastname) LIKE :search
                                          OR CONCAT(b.firstname, ' ', b.lastname) LIKE :search
                                          OR CONCAT(b.lastname, ' ', b.firstname) LIKE :search)
                                   ORDER BY b.b_id ASC
                                   LIMIT :offset, :numOfRowsPerPage");
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT b.b_id, b.isdeleted, b.picture, b.firstname, b.middlename, b.lastname, b.address, b.contactno,
                                          b.birthday, b.businessname, b.occupation, b.comaker, b.comakerno, b.remarks, b.datecreated, b.activeloan,
                                          l.l_id, l.amount, l.payable, l.mode, l.term, l.interestrate, l.amortization,
                                          l.releasedate, l.duedate, l.status, l.c_id
                                   FROM jai_db.borrowers as b
                                   LEFT JOIN jai_db.loans as l
                                   ON l.l_id = (SELECT MAX(l_id)
                                                FROM jai_db.loans as l2
                                                WHERE l2.b_id = b.b_id LIMIT 1) 
                                   WHERE b.isdeleted = 0
                                   ORDER BY b.activeloan DESC, b.b_id ASC
                                   LIMIT :offset, :numOfRowsPerPage");
  }

  $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
  $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL

  $statement->execute();
  $borrowers = $statement->fetchAll(PDO::FETCH_ASSOC);

  $table = '';
  $pagination = '';

  $data = [];

  $table .= '<div class="row">';
  $table .= '<div class="jai-col-ID">ID</div>';
  $table .= '<div class="col">Borrower Details</div>';
  $table .= '<div class="col">Loan Summary</div>';
  $table .= '<div class="col-1">Action</div>';
  $table .= '</div>';
  $count = 1;
  foreach ($borrowers as $i => $borrower) {

    $l_id = $borrower['l_id'];
    $statementPayment = $conn->prepare("SELECT SUM(p.amount) as totalpayment
                                         FROM jai_db.payments as p
                                         WHERE l_id = :l_id");
    $statementPayment->bindValue(':l_id', $l_id);
    $statementPayment->execute();
    $totalPayment = $statementPayment->fetch(PDO::FETCH_ASSOC);

    $picturePath = $borrower['picture'] ? $borrower['picture'] : 'assets/icons/borrower-picture-placeholder.jpg';

    $table .= '<div class="row jai-data-row" data-row="row-' . $borrower['b_id'] . '">';
    $table .= '<div class="jai-col-ID">' . $borrower['b_id'] . '</div>';
    $table .= '<div class="col">';
    $table .= '<div class="row">';
    $table .= '<div class="jai-image-col">';
    $table .= '<div class="jai-picture zoom">';
    $table .= '<img src="../' . $picturePath . '" class="thumb-image2">';
    $table .= '</div>';
    $table .= '</div>';
    $table .= '<div class="col">';
    $table .= '<div class="row">';
    $table .= '<p class="jai-table-name primary-font"><span class="jai-table-label">Name:</span> <span class="value">' . ucwords(strtolower($borrower['firstname'])) . ' ' . ucwords(strtolower($borrower['middlename'])) . ' ' . ucwords(strtolower($borrower['lastname'])) . '</span></p>';
    $table .= '<p class="jai-table-contact primary-font"> <span class="jai-table-label">Contact: </span> <span class="value">' . $borrower['contactno'] . '</span></p>';
    $table .= '</div>';
    $table .= '</div>';
    $table .= '</div>';
    $table .= '<div class="row">';
    $table .= '<p class="jai-table-address sub-font"> <span class="jai-table-label">Address: </span> <span class="value">' . $borrower['address'] . '</span></p>';
    $table .= '<p class="jai-table-comaker sub-font"><span class="jai-table-label">Comaker:</span> <span class="value">' . ucwords(strtolower($borrower['comaker'])) . '</span></p>';
    $table .= '<p class="jai-table-comakerno sub-font"> <span class="jai-table-label">Contact: </span> <span class="value">' . $borrower['comakerno'] . '</span></p>';
    $table .= '</div>';
    $table .= '</div>';
    $table .= '<div class="col">';
    if ($borrower['activeloan'] == 0) {
      $table .= '<h4>';
      $table .= 'No active loan';
      $table .= '</h4>';
    } else {
      $table .= '<div class="row">';
      $table .= '<div class="col">';
      $table .= '<p class="jai-table-amount primary-font"><span class="jai-table-label">Amount:</span> <span class="value">' .  "₱ " . number_format($borrower['amount'], 2) . '</span></p>';
      $table .= '</div>';
      $table .= '<div class="col">';
      $table .= '<p class="jai-table-payable primary-font"> <span class="jai-table-label">Balance: </span> <span class="value">' . "₱ " . number_format($borrower['payable'] - $totalPayment['totalpayment'], 2) . '</span></p> <!-- CALCULATED BALANCE -->';
      $table .= '</div>';
      $table .= '</div>';
      $table .= '<div class="row">';
      $table .= '<div class="col">';
      $table .= '<p class="jai-table-payment-made sub-font"> <span class="jai-table-label">Payable: </span> <span class="value">' . "₱ " . number_format($borrower['payable'], 2) . '</span></p>';
      $table .= '<p class="jai-table-mode sub-font"> <span class="jai-table-label">Mode & Term: </span> <span class="value">' . ucwords(strtolower($borrower['mode'] . ', ' . $borrower['term'])) . '</span></p>';
      $table .= '<p class="jai-table-amort sub-font"> <span class="jai-table-label">Amortization: </span> <span class="value">' . "₱ " . number_format($borrower['amortization'], 2) . '</span></p>';
      $table .= '</div>';
      $table .= '<div class="col">';
      $table .= '<p class="jai-table-release sub-font"> <span class="jai-table-label">Release Date: </span> ' . date_format(date_create($borrower['releasedate']), 'M-d-Y') . '</p>';
      $table .= '<p class="jai-table-due sub-font"> <span class="jai-table-label">Due Date: </span> ' . date_format(date_create($borrower['duedate']), 'M-d-Y') . '</p>';
      $table .= '<p class="sub-font"> <span class="jai-table-label"><strong>(TEST) LOAN ID: </span> ' .  $borrower['l_id'] . ' </strong></p>';
      $table .= '</div>';
      $table .= '</div>';
    }
    $table .= '</div>';
    $table .= '<div class="col-1 d-flex align-items-center justify-content-around">';
    $table .= '<a title="Edit" href="#" class="btn btn-primary btn-sm edit-btn">Edit</a>';
    $table .= '<button title="Delete" type="button" class="btn btn-danger btn-sm delete-borrower delete-btn" data-toggle="modal" data-target="#deleteBorrower"' . ($borrower['activeloan'] == 1 ? 'disabled' : '') . '>Delete</button>';
    $table .= '</div>';
    $table .= '<div class="d-none hidden-field">';
    $table .= '<form id="hidden-form-' . $count . '" class="hidden-form" action="">';
    $table .= '<input type="hidden" name="data-row" value="row-' . $borrower['b_id'] . '">';
    $table .= '<input type="hidden" name="b_id" value="' . ucwords(strtolower($borrower['b_id'])) . '">';
    $table .= '<input type="hidden" name="firstname" value="' . ucwords(strtolower($borrower['firstname'])) . '">';
    $table .= '<input type="hidden" name="middlename" value="' . ucwords(strtolower($borrower['middlename'])) . '">';
    $table .= '<input type="hidden" name="lastname" value="' . ucwords(strtolower($borrower['lastname'])) . '">';
    $table .= '<input type="hidden" name="address" value="' . ucwords(strtolower($borrower['address'])) . '">';
    $table .= '<input type="hidden" name="contactno" value="' . ucwords(strtolower($borrower['contactno'])) . '">';
    $table .= '<input type="hidden" name="birthday" value="' . ucwords(strtolower($borrower['birthday'])) . '">';
    $table .= '<input type="hidden" name="businessname" value="' . ucwords(strtolower($borrower['businessname'])) . '">';
    $table .= '<input type="hidden" name="occupation" value="' . ucwords(strtolower($borrower['occupation'])) . '">';
    $table .= '<input type="hidden" name="comaker" value="' . ucwords(strtolower($borrower['comaker'])) . '">';
    $table .= '<input type="hidden" name="comakerno" value="' . ucwords(strtolower($borrower['comakerno'])) . '">';
    $table .= '</form>';
    $table .= '</div>';
    $table .= '</div>';
    $count++;
  }


  if ($pageNum > 1) {
    if (!$search) {
      $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='1' href='?page=1'>First Page</a></li>";
    } else {
      $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='1' href='?page=1&search=$search'>First Page</a></li>";
    }
  }

  $pagination .= "<li" . ($pageNum <= 1 ? " class='page-link disabled'" : '') . ">";
  if ($pageNum > 1) {
    if (!$search) {
      $pagination .= "<a class='page-link' data-pagecount='$previousPage' href='?page=$previousPage'>";
    } else {
      $pagination .= "<a class='page-link' data-pagecount='$previousPage' href='?page=$previousPage&search=$search'>";
    }
  }
  $pagination .= "Previous</a></li>";



  if ($totalPages <= 10) {
    for ($counter = 1; $counter <= $totalPages; $counter++) {
      if ($counter == $pageNum) {
        $pagination .= "<li class='page-item active'><a data-pagecount='$counter' class='page-link active'>$counter</a></li>";
      } else {
        if (!$search) {
          $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$counter' href='?page=$counter'>$counter</a></li>";
        } else {
          $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$counter' href='?page=$counter&search=$search'>$counter</a></li>";
        }
      }
    }
  } elseif ($totalPages > 10) {
    if ($pageNum <= 4) {
      for ($counter = 1; $counter < 8; $counter++) {
        if ($counter == $pageNum) {
          $pagination .= "<li class='page-item active'><a data-pagecount='$counter' class='page-link'>$counter</a></li>";
        } else {
          if (!$search) {
            $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$counter' href='?page=$counter'>$counter</a></li>";
          } else {
            $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$counter' href='?page=$counter&search=$search'>$counter</a></li>";
          }
        }
      }
      $pagination .= "<li class='page-item'><a class='page-link'>...</a></li>";
      if (!$search) {
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$secondLast' href='?page=$secondLast'>$secondLast</a></li>";
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$totalPages' href='?page=$totalPages'>$totalPages</a></li>";
      } else {
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$secondLast' href='?page=$secondLast&search=$search'>$secondLast</a></li>";
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$totalPages' href='?page=$totalPages&search=$search'>$totalPages</a></li>";
      }
    } elseif ($pageNum > 4 && $pageNum < $totalPages - 4) {
      if (!$search) {
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='1' href='?page=1'>1</a></li>";
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='2' href='?page=2'>2</a></li>";
      } else {
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='1' href='?page=1&search=$search'>1</a></li>";
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='2' href='?page=2&search=$search'>2</a></li>";
      }


      $pagination .= "<li class='page-item'><a class='page-link'>...</a></li>";
      for (
        $counter = $pageNum - $adjacents;
        $counter <= $pageNum + $adjacents;
        $counter++
      ) {
        if ($counter == $pageNum) {
          $pagination .= "<li class='page-item active'><a class='page-link' data-pagecount='" . $counter . "'>$counter</a></li>";
        } else {
          if (!$search) {
            $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='" . $counter . "' href='?page=$counter'>$counter</a></li>";
          } else {
            $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='" . $counter . "' href='?page=$counter&search=$search'>$counter</a></li>";
          }
        }
      }
      $pagination .= "<li class='page-item'><a class='page-link'>...</a></li>";
      if (!$search) {
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='" . $secondLast . "' href='?page=$secondLast'>$secondLast</a></li>";
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='" . $totalPages . "' href='?page=$totalPages'>$totalPages</a></li>";
      } else {
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='" . $secondLast . "' href='?page=$secondLast&search=$search'>$secondLast</a></li>";
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='" . $totalPages . "' href='?page=$totalPages&search=$search'>$totalPages</a></li>";
      }
    } else {
      if (!$search) {
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='1' href='?page=1'>1</a></li>";
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='2' href='?page=2'>2</a></li>";
      } else {
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='1' href='?page=1&search=$search'>1</a></li>";
        $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='2' href='?page=2&search=$search'>2</a></li>";
      }
      $pagination .= "<li class='page-item'><a class='page-link'>...</a></li>";
      for (
        $counter = $totalPages - 6;
        $counter <= $totalPages;
        $counter++
      ) {
        if ($counter == $pageNum) {
          $pagination .= "<li class='page-item active'><a class='page-link'>$counter</a></li>";
        } else {
          if (!$search) {
            $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$counter' href='?page=$counter'>$counter</a></li>";
          } else {
            $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$counter' href='?page=$counter&search=$search'>$counter</a></li>";
          }
        }
      }
    }
  }


  $pagination .= "<li " . ($pageNum >= $totalPages ? "class='page-link disabled'" : '') . "><a ";
  if ($pageNum < $totalPages) {
    if (!$search) {
      $pagination .= "class='page-link' data-pagecount='$nextPage' href='?page=$nextPage'";
    } else {
      $pagination .= "class='page-link' data-pagecount='$nextPage href='?page=$nextPage&search=$search'";
    }
  }
  $pagination .= ">Next</a></li>";


  if ($pageNum < $totalPages) {
    if (!$search) {
      $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$totalPages' href='?page=$totalPages'>Last &rsaquo;&rsaquo;</a></li>";
    } else {
      $pagination .= "<li class='page-item'><a class='page-link' data-pagecount='$totalPages' href='?page=$totalPages&search=$search'>Last &rsaquo;&rsaquo;</a></li>";
    }
  }

  $data['table'] = $table;
  $data['pagination'] = $pagination;

  echo json_encode($data);
}
