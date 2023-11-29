<?php

try {
  /** @var $conn \PDO */
  require_once "../../views/includes/dbconn.php";

  $statement = $conn->prepare("WITH ModeCTE AS (
    SELECT
      p.l_id,
      p.amount,
      l.b_id,
      l.mode,
      l.amortization,
    ROW_NUMBER() OVER (PARTITION BY l_id ORDER BY COUNT(CASE WHEN p.amount <> 0 THEN 1 END) DESC) AS rnk
    FROM jai_db.payments as p
    JOIN jai_db.loans as l ON p.l_id = l.l_id
    WHERE l.activeloan = 1
    GROUP BY l_id, amount
  ), Yesterday AS (
    SELECT p.amount, p.b_id
    FROM jai_db.payments as p
    WHERE p.date = CURDATE() - INTERVAL 1 DAY
  )
  SELECT b.b_id, 
        b.firstname as firstname, 
        b.lastname as lastname, 
        r.l_id, r.amount as mode, 
        r.mode as ptype, 
        y.amount as payment_yesterday, 
        r.amortization as amort, 
        p.amount as payment_today
  FROM ModeCTE as r
  JOIN jai_db.borrowers as b ON r.b_id = b.b_id
  JOIN jai_db.payments as p ON r.b_id = p.b_id
  JOIN Yesterday as y ON r.b_id = y.b_id
  WHERE (rnk = 1 AND r.amount != 0) AND (p.date = CURDATE()) AND (p.amount != 0)
  ORDER BY p.amount < r.amount DESC, r.l_id");

  $statement->execute();
  $mode_datas = $statement->fetchAll(PDO::FETCH_ASSOC);

  // echo json_encode($data);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>

<div class="table-wrapper no-border">
  <div class="jai-table table-container">
    <div class="row table-header">
      <div class="jai-col-ID">L. ID</div>
      <div class="jai-col-ID">B. ID</div>
      <div class="col">Borrower</div>
      <div class="col">Amortization</div>
      <div class="col">Mode</div>
      <div class="col">Yesterday</div>
      <div class="col">Today</div>
      <!-- <div class="col-1 text-center">Action</div> -->
    </div>
    <?php
    $count = 1;
    foreach ($mode_datas as $i => $mode_data) {

    ?>

      <div data-row-id="<?php echo $mode_data['l_id'] ?>" class="row jai-data-row">
        <div class="jai-col-ID"><?php echo $mode_data['l_id'] ?></div>
        <div class="jai-col-ID"><?php echo $mode_data['b_id'] ?></div>
        <div class="col">
          <div class="row">
            <p class="jai-table-name primary-font"><span class="jai-table-label"></span> <?= ucwords(strtolower($mode_data['firstname'])) . ' ' . ucwords(strtolower($mode_data['lastname'])) ?></p>
          </div>
        </div>
        <div class="col">
          <div class="row">
            <p class="primary-font">₱<?= $mode_data['amort'] ?></p>
          </div>
        </div>
        <div class="col">
          <div class="row">
            <p class="primary-font">₱<?= $mode_data['mode'] ?></p>
          </div>
        </div>
        <div class="col">
          <div class="row">
            <p class="primary-font <?= ((float)$mode_data['payment_yesterday'] < (float)$mode_data['mode'] ? 'red-font' : 'green-font')  ?>">₱<?= $mode_data['payment_yesterday'] ?></p>
          </div>
        </div>
        <div class="col">
          <div class="row">
            <p class="primary-font <?= ((float)$mode_data['payment_today'] < (float)$mode_data['mode'] ? 'red-font' : 'green-font')  ?>">₱<?= $mode_data['payment_today'] ?></p>
          </div>
        </div>
      </div>
    <?php $count++;
    } ?>
  </div>
</div>
<div class="table-padding">
</div>