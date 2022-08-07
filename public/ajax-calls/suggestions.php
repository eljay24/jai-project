<?php

try {
    /** @var $conn \PDO */
    require_once "../../views/includes/dbconn.php";

    // echo "DB connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$statement = $conn->prepare("SELECT DISTINCT b.b_id, b.firstname, b.middlename, b.lastname
                                FROM jai_db.borrowers as b
                                LEFT JOIN jai_db.loans as l
                                ON b.b_id = l.b_id 
                                WHERE b.isdeleted = 0 AND b.activeloan = 0
                                ORDER BY b.b_id ASC");
$statement->execute();
$existingNames = $statement->fetchAll(PDO::FETCH_ASSOC);

// $existingNames = array("LJ", "willie", "ann", "LA", "ivan", "ian");

if (isset($_POST['suggestion'])) {


    $name = $_POST['suggestion'];

    foreach ($existingNames as $i => $existingName) {



        // echo "<pre>";
        // var_dump($existingName);
        // echo "</pre>";

        if (stripos($existingName['b_id'], $name) !== false or stripos($existingName['firstname'], $name) !== false or stripos($existingName['middlename'], $name) !== false or stripos($existingName['lastname'], $name) !== false) {

            // echo json_encode($existingName);

            echo '<div class="suggestion-container" data-borrower="' . $existingName['b_id'] . '">#' . $existingName['b_id'], ' - ', $existingName['firstname'], ' ', $existingName['middlename'], ' ', $existingName['lastname'] . '</div>';
        }
    }
}
