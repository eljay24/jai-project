<?php

try {
    /** @var $conn \PDO */
    require_once "../../dbconn.php";

    } 
    catch(PDOException $e) {
        echo "Connection failed: ".$e->getMessage();
    }  

$id = $_POST['id'] ?? null;

if (!$id) {
    header('Location: index.php');
}

$statement = $conn->prepare("DELETE FROM jai_db.borrowers WHERE id = :id");
$statement->bindValue(":id", $id);
$statement->execute();

header('Location: index.php');

// echo "<pre>";
// var_dump($id);
// echo "</pre>";

exit;

?>