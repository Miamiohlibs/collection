<?php
// keep file; it is an include file listed for any barcode, inventory, or receipt.php scripts

$username = "user";
$password = "pass";

$dsn = "pgsql:host=0.0.0.0 dbname=iii port=1032 sslmode=require";
//charset seems to not be necessary


try {
        // $connection = new PDO($dsn, $username, $password, array(PDO::ATTR_PERSISTENT => true));
        $connection = new PDO($dsn, $username, $password);
}

catch ( PDOException $e ) {
        $row = null;
        $statement = null;
        $connection = null;

        echo "problem connecting to database...\n";
        error_log('PDO Exception: '.$e->getMessage());
        exit(1);
}


?>
