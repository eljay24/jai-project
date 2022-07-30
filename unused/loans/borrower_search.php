<?php

try {
    /** @var $conn \PDO */
    require_once "../../views/includes/dbconn.php";

    // echo "DB connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$search = $_GET['search'] ?? '';

$statement = $conn->prepare("SELECT *
                                FROM jai_db.borrowers
                                WHERE b_id LIKE :search OR firstname LIKE :search OR middlename LIKE :search OR lastname LIKE :search
                                ORDER BY b_id ASC");

$statement->bindValue(':search', "%$search%");
$statement->execute();
$borrower = $statement->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['search'])) {
    $products = products($conn, $_GET['term']);
    $getProducts = array();
    foreach($products as $product){
        $getProducts[] = $product['name'];
    }
    echo json_encode($getProducts);
}
