<?php

require "../../views/includes/dbconn.php";

$search = null;

if (isset($_POST['action'])) {


  $search = $_POST['search_value'];
  $pageNum = $_POST['page_number'];

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
                                                 OR CONCAT('l', l.l_id) LIKE :search
                                                 OR CONCAT('b', b.b_id) LIKE :search)
                                          ORDER BY p.date ASC");
    $statementTotalRows->bindValue(':search', "%$search%");
  } else {
    $statementTotalRows = $conn->prepare("SELECT COUNT(*) as count FROM jai_db.payments");
  }

  $statementTotalRows->execute();

  $totalRows = $statementTotalRows->fetchAll(PDO::FETCH_ASSOC);
  $totalPages = ceil($totalRows[0]["count"] / $numOfRowsPerPage);
  $secondLast = $totalPages - 1;

  if ($search) {
    $statement = $conn->prepare("SELECT b.b_id, b.firstname as borrowerfname, b.middlename as borrowermname, b.lastname as borrowerlname, b.picture, b.contactno, l.l_id, l.mode, l.status,
                                        p.p_id, p.amount, p.type, p.date, c.firstname as collectorfname, c.middlename as collectormname, c.lastname as collectorlname
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
                                 ORDER BY p.date DESC, p.p_id DESC
                                 LIMIT :offset, :numOfRowsPerPage");
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    $statement->bindValue(':numOfRowsPerPage', $numOfRowsPerPage, PDO::PARAM_INT); // "PDO::PARAM_INT" removes quotes from SQL
    $statement->bindValue(':search', "%$search%");
  } else {
    $statement = $conn->prepare("SELECT b.b_id, b.firstname as borrowerfname, b.middlename as borrowermname, b.lastname as borrowerlname, b.picture, b.contactno, l.l_id, l.mode, l.status,
                                        p.p_id, p.amount, p.type, p.date, c.firstname as collectorfname, c.middlename as collectormname, c.lastname as collectorlname
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

  $table = '';
  $pagination = '';

  $data = [];

  $table .= '<div class="row">';
  $table .= '<div class="jai-col-ID">Ref#</div>';
  $table .= '<div class="col">Borrower</div>';
  $table .= '<div class="col">Payment Details</div>';
  $table .= '<div class="col">Collector</div>';
  $table .= '<div class="col-1">Action</div>';
  $table .= '</div>';
  $count = 1;
  foreach ($payments as $i => $payment) {

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

    $table .= '<div data-row-id="' . $payment['p_id'] . '" class="row jai-data-row">';
    $table .= '<div class="jai-col-ID">' . $payment['p_id'] . '</div>';
    $table .= '<div class="col">';
    $table .= '<div class="row">';
    $table .= '<!-- <div class="jai-image-col">';
    $table .= '<div class="jai-picture">';
    $table .= '<img src="/JAI/public/' . $payment['picture'] . '" class="thumb-image2">';
    $table .= '</div>';
    $table .= '</div> -->';
    $table .= '<div class="col">';
    $table .= '<p class="jai-table-name primary-font"><span class="jai-table-label"></span> ' . '#' . $payment['b_id'] . ' ' . ucwords(strtolower($payment['borrowerfname'])) . ' ' . ucwords(strtolower($payment['borrowermname'])) . ' ' . ucwords(strtolower($payment['borrowerlname'])) . '</p>';
    $table .= '</div>';
    $table .= '<div class="col-4">';
    $table .= '<p class="primary-font">Loan Ref #' . $payment['l_id'] . '</p>';
    $table .= '</div>';
    $table .= '</div>';
    $table .= '<div class="row">';
    $table .= '<p class="sub-font">Contact: ' . $payment['contactno'] . '</p>';
    $table .= '</div>';
    $table .= '</div>';
    $table .= '<div class="col">';
    $table .= '<div class="row">';
    $table .= '<div class="col">';
    $table .= '<p class="jai-table-comaker primary-font"><span class="jai-table-label">Amount:</span> ' . "₱ " . number_format($payment['amount'], 2) . '</p>';
    $table .= '<p class="jai-table-contact sub-font"> <span class="jai-table-label">Type: </span>' . $payment['type'] . '</p>';
    $table .= '</div>';
    $table .= '<div class="col">';
    $table .= '<p class="primary-font">Date: ' . date_format($date, "M-d-Y") . '</p>';
    $table .= '<p class="sub-font">Mode: ' . ucwords(strtolower($payment['mode'])) . '</p>';
    $table .= '</div>';
    $table .= '<!-- <div class="col">';
    $table .= '<p class="jai-table-amount primary-font"><span class="jai-table-label">Collector: </span>' . ucwords(strtolower($payment['collectorfname'])) . ' ' . ucwords(strtolower($payment['collectormname'])) . ' ' . ucwords(strtolower($payment['collectorlname'])) . '</p>';
    $table .= '</div>';
    $table .= '<div class="col">';
    $table .= '<p class="jai-table-payable primary-font"> <span class="jai-table-label">Remaining Bal.: </span></p>';
    $table .= '</div> -->';
    $table .= '</div>';
    $table .= '<!-- <div class="row">';
    $table .= '<div class="col">';
    $table .= '<p class="jai-table-payment-made sub-font"> <span class="jai-table-label">Payable??: </span></p>';
    $table .= '<p class="jai-table-mode sub-font"> <span class="jai-table-label">Mode & Term??: </span></p>';
    $table .= '<p class="jai-table-amort sub-font"> <span class="jai-table-label">Amortization???: </span></p>';
    $table .= '</div>';
    $table .= '<div class="col">';
    $table .= '<p class="jai-table-release sub-font"> <span class="jai-table-label">Hmm???: </span> 01/01/22</p>';
    $table .= '<p class="jai-table-due sub-font"> <span class="jai-table-label">Due Date???: </span> 01/01/22</p>';
    $table .= '</div>';
    $table .= '</div> -->';
    $table .= '</div>';
    $table .= '<div class="col position-relative">';
    $table .= '<div class="row">';
    $table .= '<div class="col">';
    $table .= '<p class="jai-table-address primary-font"> <span class="jai-table-label">Collector: </span>' . ucwords(strtolower($payment['collectorfname'])) . ' ' . ucwords(strtolower($payment['collectormname'])) . ' ' . ucwords(strtolower($payment['collectorlname'])) . '</p>';
    $table .= '<!-- <textarea class="jai-table-input" type="text"></textarea> -->';
    $table .= '</div>';
    $table .= '<div class="col-3">';
    $table .= '<p class="primary-font">' . $payment['status'] . '</p>';
    $table .= '</div>';
    $table .= '</div>';
    $table .= '</div>';
    $table .= '<div class="col-1 d-flex align-items-center justify-content-around">';
    $table .= '<a title="Edit" href="#" class="btn btn-primary btn-sm edit-btn">Edit</a>';
    $table .= '<button title="Delete" type="button" class="btn btn-danger btn-sm delete-borrower delete-btn" data-toggle="modal" data-target="#deleteBorrower" disabled>Delete</button>';
    $table .= '<form method="get" action="ledger.php" target="_blank">';
    $table .= '<input title="View ledger" type="submit" name="loanID" class="btn btn-primary btn-sm ledger-btn" value="' . $loanID . '" ' . (($totalPayment || $totalPass) == 0 ? 'disabled' : '') . '></input>';
    $table .= '</form>';

    $table .= '</div>';
    $table .= '<div class="d-none hidden-field">';
    $table .= '<form id="hidden-form-' . $count . '" class="hidden-form" action="">';
    $table .= '<input type="hidden" name="data-row" value="row-' . $loanID . '">';
    $table .= '<input type="hidden" name="b_id" value="' . ucwords(strtolower($loanID)) . '">';
    $table .= '<input type="hidden" name="borrower-id" value="' . $loanID . '">';
    $table .= '<input type="hidden" name="borrower-name" value="' . '#' . $payment['b_id'] . ' ' . ucwords(strtolower($payment['borrowerfname'])) . ' ' . ucwords(strtolower($payment['borrowermname'])) . ' ' . ucwords(strtolower($payment['borrowerlname'])) . '">';
    $table .= '<input type="hidden" name="loan-amount" value="' . number_format($payment['amount'], 2) . '">';
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

  if ($pageNum > 1) {
    $pagination .= "<li" . ($pageNum <= 1 ? " class='page-link disabled'" : '') . ">";
    if (!$search) {
      $pagination .= "<a class='page-link' data-pagecount='$previousPage' href='?page=$previousPage'>";
    } else {
      $pagination .= "<a class='page-link' data-pagecount='$previousPage' href='?page=$previousPage&search=$search'>";
    }
    $pagination .= "Previous</a></li>";
  }



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

  if ($totalPages == 0) {
    $pagination .= "<li class='page-item'><a class='page-link active' data-pagecount='1' href='?page=1'>1</a></li>";
  }


  if ($pageNum < $totalPages) {
    $pagination .= "<li " . ($pageNum >= $totalPages ? "class='page-link'" : '') . "><a ";
    if (!$search) {
      $pagination .= "class='page-link' data-pagecount='$nextPage' href='?page=$nextPage'";
    } else {
      $pagination .= "class='page-link' data-pagecount='$nextPage' href='?page=$nextPage&search=$search'";
    }
    $pagination .= ">Next</a></li>";
  }


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
