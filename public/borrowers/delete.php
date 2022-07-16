<?php

try {
    /** @var $conn \PDO */
    require_once "../../dbconn.php";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$id = $_POST['b_id'] ?? null;



if (!$id) {
    // echo "<pre>";
    // var_dump($id);
    // echo "</pre>";
    // exit;
    header('Location: index.php');
}

$statement = $conn->prepare("DELETE FROM jai_db.borrowers WHERE b_id = :id");
$statement->bindValue(":id", $id);
$statement->execute();

header('Location: index.php');
