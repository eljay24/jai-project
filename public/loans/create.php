<?php

try {
  /** @var $conn \PDO */
  require_once "../../dbconn.php";

  // echo "DB connected successfully";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

$statement = $conn->prepare("SELECT b.b_id, b.firstname, b.middlename, b.lastname
                                FROM jai_db.borrowers as b
                                LEFT JOIN jai_db.loans as l
                                ON b.b_id = l.b_id 
                                WHERE l.amount IS NULL
                                ORDER BY b.b_id ASC");
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

<div class="content-container">
  <p>
    <a href="index.php" class="btn btn-secondary">Go back</a>
  </p>
  <h1>Add new loan</h1>
  <br>

  <script>

    $(document).ready(function() {
      $("#namesearch").keyup(function() {
        var name = $("#namesearch").val();
        $.ajax({
          url: "suggestions.php",
          method: "POST",
          data: {
            suggestion: name
          },
          dataType: "html",
          beforeSend: function() {},
          success: function(data) {
            
            //response (data);
            $("#test").html(data);
            console.log(data)
          },
          error: function(response) {
            console.log(response);
          },
        });
      });
    });

    // $( "#namesearch" ).autocomplete({
    //   source: function( request, response ) {
    //     $.ajax( {
    //       url: "suggestions.php",
    //       dataType: "jsonp",
    //       data: {
    //         term: request.term
    //       },
    //       success: function( data ) {
    //         response( data );
    //       }
    //     } );
    //   },
    //   minLength: 2,
    //   select: function( event, ui ) {
    //     log( "Selected: " + ui.item.value + " aka " + ui.item.id );
    //   }
    // } );

  </script>



  <input data-borrower-name="" type="text" name="name" id="namesearch" placeholder="Search for borrowers...">
  <br>
  <span></span>
  <select id="test">
  <?php
  foreach ($loans as $i => $loan) {
    echo '<option>#'.$loan['b_id'].' - '.$loan['firstname'].' '.$loan['middlename'].' '.$loan['lastname'].'</option>';
  }
?>
  </select>

  <!-- <script>
    var existingNames = ["lee", "jordan", "angelo", "ivan", "willie", "ann"];

    $("#namesearch").autocomplete({
      source: existingNames
    }, {
      
    });
  </script> -->


  <?php include_once "../../views/loans/form.php" ?>

  </body>

  </html>