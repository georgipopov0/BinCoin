<?php
// edit_collection.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page
    header("Location: login.php");
    exit();
}

require 'constants.php';

// Initialize variables
$collection_id = isset($_GET['collection_id']) ? intval($_GET['collection_id']) : 0;

// Validate collection_id
if ($collection_id <= 0) {
    die("Invalid Collection ID.");
}

// Establish database connection
$conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

// Check for connection errors
if ($conn->connect_error) {
    // Log the error and display a generic message
    error_log("Connection failed: " . $conn->connect_error);
    die("An unexpected error occurred. Please try again later.");
}

// Fetch collection details
$collection_sql = "SELECT `id`, `name`, `user_name`, `access`, `created_at` FROM `coin_collection` WHERE `id` = ?";
$stmt_collection = $conn->prepare($collection_sql);
if (!$stmt_collection) {
    error_log("Prepare failed: " . $conn->error);
    die("An unexpected error occurred. Please try again later.");
}

$stmt_collection->bind_param("i", $collection_id);
$stmt_collection->execute();
$result_collection = $stmt_collection->get_result();

if ($result_collection->num_rows === 0) {
    // No collection found with the given ID
    $stmt_collection->close();
    $conn->close();
    die("Collection not found.");
}

$collection = $result_collection->fetch_assoc();
$stmt_collection->close();

// Access Control: Check if the current user is the owner
if ($collection['user_name'] !== $_SESSION['username']) {
    $conn->close();
    die("You do not have permission to edit this collection.");
}

// Initialize variables for form
$new_name = $collection['name'];
$new_access = $collection['access'];
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    // Retrieve and sanitize input
    $new_name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $new_access = isset($_POST['access']) ? trim($_POST['access']) : 'private';

    // Validate inputs
    if (empty($new_name)) {
        $errors[] = "Collection name cannot be empty.";
    }

    if (!in_array($new_access, ['public', 'private', 'protected'])) {
        $errors[] = "Invalid access level selected.";
    }

    // If access level is 'protected', handle allowed users
    $allowed_users = [];
    if ($new_access === 'protected') {
        // Retrieve allowed users from POST data
        $allowed_users = isset($_POST['allowed_users']) ? $_POST['allowed_users'] : [];
        $allowed_users = array_map('trim', $allowed_users); // Trim whitespace
        $allowed_users = array_filter($allowed_users); // Remove empty entries

        // Validate that allowed users exist in the user table
        if (!empty($allowed_users)) {
            // Prepare placeholders for IN clause
            $placeholders = implode(',', array_fill(0, count($allowed_users), '?'));
            $user_validation_sql = "SELECT `name` FROM `user` WHERE `name` IN ($placeholders)";
            $stmt_validate = $conn->prepare($user_validation_sql);
            if ($stmt_validate) {
                // Bind parameters dynamically
                $stmt_validate->bind_param(str_repeat('s', count($allowed_users)), ...$allowed_users);
                $stmt_validate->execute();
                $result_validate = $stmt_validate->get_result();
                $valid_users = [];
                while ($row = $result_validate->fetch_assoc()) {
                    $valid_users[] = $row['name'];
                }
                $stmt_validate->close();

                // Check for invalid users
                $invalid_users = array_diff($allowed_users, $valid_users);
                if (!empty($invalid_users)) {
                    $errors[] = "The following users do not exist: " . implode(', ', $invalid_users);
                }

                // Prevent adding the owner to the allowed users list
                if (in_array($_SESSION['username'], $valid_users)) {
                    $errors[] = "You cannot add yourself to the allowed users list.";
                }
            } else {
                error_log("Prepare failed: " . $conn->error);
                $errors[] = "An unexpected error occurred. Please try again later.";
            }
        }
    }

    if (empty($errors)) {
        // Begin transaction
        $conn->begin_transaction();

        try {
            // Update collection name and access level
            $update_sql = "UPDATE `coin_collection` SET `name` = ?, `access` = ? WHERE `id` = ?";
            $stmt_update = $conn->prepare($update_sql);
            if (!$stmt_update) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt_update->bind_param("ssi", $new_name, $new_access, $collection_id);
            if (!$stmt_update->execute()) {
                throw new Exception("Execute failed: " . $stmt_update->error);
            }
            $stmt_update->close();

            // Handle access_control table based on access level
            if ($new_access === 'protected') {
                // First, delete existing access_control entries for this collection
                $delete_sql = "DELETE FROM `access_control` WHERE `collection_id` = ?";
                $stmt_delete = $conn->prepare($delete_sql);
                if (!$stmt_delete) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $stmt_delete->bind_param("i", $collection_id);
                if (!$stmt_delete->execute()) {
                    throw new Exception("Execute failed: " . $stmt_delete->error);
                }
                $stmt_delete->close();

                // Insert new allowed users
                if (!empty($allowed_users)) {
                    $insert_sql = "INSERT INTO `access_control` (`user_name`, `collection_id`) VALUES (?, ?)";
                    $stmt_insert = $conn->prepare($insert_sql);
                    if (!$stmt_insert) {
                        throw new Exception("Prepare failed: " . $conn->error);
                    }

                    foreach ($allowed_users as $user) {
                        $stmt_insert->bind_param("si", $user, $collection_id);
                        if (!$stmt_insert->execute()) {
                            throw new Exception("Execute failed: " . $stmt_insert->error);
                        }
                    }
                    $stmt_insert->close();
                }
            } else {
                // If access level is not 'protected', remove any existing access_control entries
                $delete_sql = "DELETE FROM `access_control` WHERE `collection_id` = ?";
                $stmt_delete = $conn->prepare($delete_sql);
                if (!$stmt_delete) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $stmt_delete->bind_param("i", $collection_id);
                if (!$stmt_delete->execute()) {
                    throw new Exception("Execute failed: " . $stmt_delete->error);
                }
                $stmt_delete->close();
            }

            // Commit transaction
            $conn->commit();

            // Redirect to collection details page after successful update
            header("Location: collection_details.php?collection_id=" . urlencode($collection_id));
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            error_log("Transaction failed: " . $e->getMessage());
            $errors[] = "Failed to update the collection. Please try again.";
        }
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch current allowed users if access is 'protected'
$allowed_users = [];
if ($collection['access'] === 'protected') {
    $allowed_sql = "SELECT `user_name` FROM `access_control` WHERE `collection_id` = ?";
    $stmt_allowed = $conn->prepare($allowed_sql);
    if ($stmt_allowed) {
        $stmt_allowed->bind_param("i", $collection_id);
        $stmt_allowed->execute();
        $result_allowed = $stmt_allowed->get_result();
        while ($row = $result_allowed->fetch_assoc()) {
            $allowed_users[] = $row['user_name'];
        }
        $stmt_allowed->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Collection - <?= htmlspecialchars($collection['name']); ?></title>
    <!-- Link to external CSS files -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/collections.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <!-- Inline CSS for Additional Styling (Optional) -->
    <style>
        .edit-collection-container {
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .edit-collection-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .edit-collection-form label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .edit-collection-form input[type="text"],
        .edit-collection-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .edit-collection-form button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .edit-collection-form button:hover {
            background-color: #218838;
        }

        .error-messages {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        /* Protected Users Section */
        .protected-section {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }

        .protected-section h3 {
            margin-top: 0;
            color: #555;
        }

        .allowed-users-list {
            list-style: none;
            padding: 0;
        }

        .allowed-users-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .allowed-users-list li:last-child {
            border-bottom: none;
        }

        .remove-user-button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .remove-user-button:hover {
            background-color: #c82333;
        }

        .add-user-form {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .add-user-form input[type="text"] {
            flex: 1;
        }

        .add-user-form button {
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .add-user-form button:hover {
            background-color: #0069d9;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .edit-collection-container {
                padding: 15px;
            }

            .add-user-form {
                flex-direction: column;
            }

            .add-user-form button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="edit-collection-container">
        <h1>Edit Collection</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="edit_collection.php?collection_id=<?= urlencode($collection_id); ?>" method="POST" class="edit-collection-form">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

            <label for="name">Collection Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($new_name); ?>" required>

            <label for="access">Access Level:</label>
            <select id="access" name="access" required onchange="toggleProtectedSection()">
                <option value="public" <?= ($new_access === 'public') ? 'selected' : ''; ?>>Public</option>
                <option value="private" <?= ($new_access === 'private') ? 'selected' : ''; ?>>Private</option>
                <option value="protected" <?= ($new_access === 'protected') ? 'selected' : ''; ?>>Protected</option>
            </select>

            <!-- Protected Users Section -->
            <div id="protected-section" class="protected-section" style="display: <?= ($new_access === 'protected') ? 'block' : 'none'; ?>;">
                <h3>Allowed Users</h3>
                <ul class="allowed-users-list">
                    <?php if ($new_access === 'protected' && !empty($allowed_users)): ?>
                        <?php foreach ($allowed_users as $user): ?>
                            <li>
                                <span><?= htmlspecialchars($user); ?></span>
                                <form action="edit_collection.php?collection_id=<?= urlencode($collection_id); ?>" method="POST" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="remove_user" value="<?= htmlspecialchars($user); ?>">
                                    <button type="submit" class="remove-user-button" onclick="return confirm('Are you sure you want to remove this user?');">Remove</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No users allowed for this collection.</li>
                    <?php endif; ?>
                </ul>

                <!-- Add New User -->
                <form action="edit_collection.php?collection_id=<?= urlencode($collection_id); ?>" method="POST" class="add-user-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="action" value="add_user">
                    <input type="text" name="new_user" placeholder="Enter username to add" required>
                    <button type="submit">Add User</button>
                </form>
            </div>

            <button type="submit">Update Collection</button>
        </form>
    </div>

    <!-- Optional: Add Footer Component -->
    <?php // include 'components/footer.php'; ?>

    <!-- JavaScript for Dynamic UI -->
    <script>
        function toggleProtectedSection() {
            const accessSelect = document.getElementById('access');
            const protectedSection = document.getElementById('protected-section');
            if (accessSelect.value === 'protected') {
                protectedSection.style.display = 'block';
            } else {
                protectedSection.style.display = 'none';
            }
        }
    </script>
</body>

</html>
