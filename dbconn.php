<?php

//CONNECT DATABASE

$conn = new PDO('mysql:host=localhost; port:3306; dbname=jai_db.sql', 'root', '');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>