<?php

try {
  /** @var $conn \PDO */
  require_once "../../dbconn.php";

  // echo "DB connected successfully";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

$statement = $conn->prepare("SELECT *
                              FROM jai_db.borrowers
                              ORDER BY b_id ASC");
$statement->execute();
$loans = $statement->fetchAll(PDO::FETCH_ASSOC);

date_default_timezone_set("Asia/Manila");

$errors = [];

$b_id = '';
$amount = '';
$payable = '';
$balance = '';
$mode = '';
$term = '';
$interestrate = '';
$amortization = '';
$releasedate = '';
$duedate = '';
$status = '';

$loan = [
  'b_id' => '',
  'amount' => '',
  'payable' => '',
  'balance' => '',
  'mode' => '',
  'term' => '',
  'interestrate' => '',
  'amortization' => '',
  'releasedate' => '',
  'duedate' => '',
  'status' => '',
];

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {

//   require_once "../../validate_loan.php";

//   if (empty($errors)) {

//     $statement = $conn->prepare("INSERT INTO jai_db.borrowers (picture, firstname, middlename, lastname, address, contactno, birthday, businessname,
//                                                                     occupation, comaker, remarks, datecreated)
//                                                            VALUES (:picture, :firstname, :middlename, :lastname, :address, :contactno, :birthday, :businessname,
//                                                                     :occupation, :comaker, :remarks, :datecreated)");

//     $statement->bindValue(':picture', $picturePath);
//     $statement->bindValue(':firstname', $firstname);
//     $statement->bindValue(':middlename', $middlename);
//     $statement->bindValue(':lastname', $lastname);
//     $statement->bindValue(':address', $address);
//     $statement->bindValue(':contactno', $contactno);
//     $statement->bindValue(':birthday', $birthday);
//     $statement->bindValue(':businessname', $businessname);
//     $statement->bindValue(':occupation', $occupation);
//     $statement->bindValue(':comaker', $comaker);
//     $statement->bindValue(':remarks', $remarks);
//     $statement->bindValue(':datecreated', date('Y-m-d H:i:s'));

//     $statement->execute();

//     header('Location: index.php');
//   }
// }


?>

<?php include_once "../../views/partials/header.php"; ?>

<p>
  <a href="index.php" class="btn btn-secondary">Go back</a>
</p>
<h1>Add new loan</h1>
<br>

<!-- TEST AUTOCOMPLETE SEARCH -->
<div class="form-group">
  <label><strong>Borrower:</strong></label>
  <input type="text" autocomplete="off" name="search" id="search" placeholder="Type to search borrower..." class="form-control">
</div>

<script type="text/javascript">
  $(function() {
    $("#search").autocomplete({
      source: 'borrower_search.php',
    });
  });
</script>
<!-- TEST AUTOCOMPLETE SEARCH -->



<!--
<script type="text/javascript">
  function changeFunc() {
    var selectBox = document.getElementById("loaner");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    alert(selectedValue);
  }
</script>

<form action="">
  <select id="loaner" name="loaner" onchange="changeFunc();">
    <option value="">Select borrower</option>
    <?php foreach ($loans as $i => $loaner) { ?>
      <option value="<?= $loaner['b_id'] ?>"><?= '#' . $loaner['b_id'] . ' - ' . $loaner['firstname'] . ' ' . $loaner['middlename'] . ' ' . $loaner['lastname'] ?></option>
    <?php } ?>
  </select>
  <a href="create.php?id=" class="btn btn-primary btn-sm">OK</a>
</form>
-->

<?php include_once "../../views/loans/form.php" ?>

</body>

</html>