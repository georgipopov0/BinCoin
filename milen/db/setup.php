<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bincoin";
$sqlFilePath = "setup.sql"; // Path to the SQL file

$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$dbCheck = mysqli_query($conn, "SHOW DATABASES LIKE '$dbname'");
if (mysqli_num_rows($dbCheck) === 0) {
    if (mysqli_query($conn, "CREATE DATABASE $dbname")) {
        echo "Database '$dbname' created successfully.\n";
    } else {
        die("Error creating database: " . mysqli_error($conn));
    }
}else{
    die("Database already initialized");
}

mysqli_select_db($conn, $dbname);

if (file_exists($sqlFilePath)) {
    $sqlContent = file_get_contents($sqlFilePath);
    if (mysqli_multi_query($conn, $sqlContent)) {
        echo "SQL file executed successfully.\n";
        while (mysqli_next_result($conn)) {;}
    } else {
        die("Error executing SQL file: " . mysqli_error($conn));
    }
} else {
    die("SQL file '$sqlFilePath' not found. Exiting.\n");
}

echo "Setup complete.\n";

mysqli_close($conn);
?>
