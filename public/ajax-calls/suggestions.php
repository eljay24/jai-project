<?php

try {
    /** @var $conn \PDO */
    require_once "../../views/includes/dbconn.php";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// $statement = $conn->prepare("SELECT DISTINCT b.b_id, b.firstname, b.middlename, b.lastname
//                                 FROM jai_db.borrowers as b
//                                 LEFT JOIN jai_db.loans as l
//                                 ON b.b_id = l.b_id 
//                                 WHERE b.isdeleted = 0 AND b.activeloan = 0
//                                 ORDER BY b.b_id ASC");

$statement = $conn->prepare("SELECT b.b_id, b.firstname, b.middlename, b.lastname, l.releasedate
                                FROM jai_db.borrowers as b
                                LEFT JOIN jai_db.loans as l
                                ON b.b_id = l.b_id 
                                WHERE (b.isdeleted = 0 AND l.status = 'Active' AND l.amount IS NOT NULL)
                                ORDER BY b.b_id ASC");
$statement->execute();
$existingNames = $statement->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['suggestion'])) {


    $name = $_POST['suggestion'];
    $output = '';

    foreach ($existingNames as $i => $existingName) {


        if (stripos($existingName['b_id'], $name) !== false or stripos($existingName['firstname'], $name) !== false or stripos($existingName['middlename'], $name) !== false or stripos($existingName['lastname'], $name) !== false) {

            $output .= "<div class='suggestion-container' data-releasedate='". $existingName["releasedate"] ."' data-borrower='" . $existingName["b_id"] . "'>#" . $existingName["b_id"] . " - " . $existingName["firstname"] . " " . $existingName["middlename"] . " " . $existingName["lastname"] . "</div>";
        }
    }
    echo json_encode($output);
}
