<?php

try {
    /** @var $conn \PDO */
    require_once "../../dbconn.php";

    } 
    catch(PDOException $e) {
        echo "Connection failed: ".$e->getMessage();
    }

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

$statement = $conn->prepare("SELECT * FROM jai_db.borrowers WHERE id = :id");
$statement->bindValue(':id', $id);
$statement->execute();
$borrower = $statement->fetch(PDO::FETCH_ASSOC);

date_default_timezone_set("Asia/Manila");

$errors = [];

$picture = $borrower['picture'];
$firstname = $borrower['firstname'];
$middlename = $borrower['middlename'];
$lastname = $borrower['lastname'];
$address = $borrower['address'];
$contactno = $borrower['contactno'];
$birthday = $borrower['birthday'];
$businessname = $borrower['businessname'];
$occupation = $borrower['occupation'];
$comaker = $borrower['comaker'];
$remarks = $borrower['remarks'];
$datecreated = $borrower['datecreated'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  

    require_once "../../validate_borrower.php";

    if (empty($errors)) {

        $statement = $conn->prepare("UPDATE jai_db.borrowers SET picture=:picture, firstname=:firstname, middlename=:middlename, lastname=:lastname, address=:address,
                                                                 contactno=:contactno, birthday=:birthday, businessname=:businessname, occupation=:occupation,
                                                                 comaker=:comaker, remarks=:remarks WHERE id=:id");
        
        $statement->bindValue(':id', $id);
        $statement->bindValue(':picture', $picturePath);    
        $statement->bindValue(':firstname', $firstname);
        $statement->bindValue(':middlename', $middlename);
        $statement->bindValue(':lastname', $lastname);
        $statement->bindValue(':address', $address);
        $statement->bindValue(':contactno', $contactno);
        $statement->bindValue(':birthday', $birthday);
        $statement->bindValue(':businessname', $businessname);
        $statement->bindValue(':occupation', $occupation);
        $statement->bindValue(':comaker', $comaker);
        $statement->bindValue(':remarks', $remarks);

        $statement->execute();

        header('Location: index.php');

    }

}

?>

<?php include_once "../../views/partials/header.php"; ?>

    <p>
        <a href="index.php" class="btn btn-secondary">Go back</a>
    </p>
    <h1>Update borrower</h1>
    <h3><?php echo '#'.$borrower['id'].' - '.$borrower['firstname'].' '.$borrower['lastname'];?></h3>

    <?php include_once "../../views/borrowers/form.php" ?>

  </body>
</html>