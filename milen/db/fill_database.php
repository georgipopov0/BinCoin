<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bincoin";
$sqlFilePath = "fill_database.sql"; // Path to the SQL file

$conn = mysqli_connect($servername, $username, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
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
