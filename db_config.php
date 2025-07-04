<?php

$dbhost = "localhost";
$dbname = "<insert_database_name>";
$dbuser = "<insert_database_username>";
$dbpass = "<insert_database_password>";

try {
        $conn = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die("Unable to connect to $dbname at $dbhost.");
        @mysqli_query($conn, "SET CHARACTER SET 'utf8'");
        @mysqli_query($conn, "SET SESSION collation_connection ='utf8_swedish_ci'");
    } catch (mysqli_sql_exception $e) {
        exit("Unable to connect to database. Check your configuration file.");
    }
