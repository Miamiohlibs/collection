<?php

function getdb() {

   $host        = "host=host";
   $port        = "port=1032";
   $dbname      = "dbname=iii";
   $credentials = "user=user password=pass";
   $ssl         = "sslmode=require";

   $db = pg_connect("$host $port $dbname $credentials $ssl") or die('connection failed');
   return $db;
}

?>
