<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bincoin";
$sqlFilePath = "fill_database.sql"; // Path to the SQL file

// Establish a connection
$conn = mysqli_connect($servername, $username, $password);

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Select the database
mysqli_select_db($conn, $dbname);

// Execute the SQL file
if (file_exists($sqlFilePath)) {
    $sqlContent = file_get_contents($sqlFilePath);
    if (mysqli_multi_query($conn, $sqlContent)) {
        echo "SQL file executed successfully.\n";
        // Wait for all queries to finish
        while (mysqli_next_result($conn)) {;}
    } else {
        die("Error executing SQL file: " . mysqli_error($conn));
    }
} else {
    die("SQL file '$sqlFilePath' not found. Exiting.\n");
}

echo "Setup complete.\n";

// Close the connection
mysqli_close($conn);
?>
