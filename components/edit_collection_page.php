<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Collection - <?= htmlspecialchars($collection['name']); ?></title>
    <!-- <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/collections.css"> -->
    <link rel="stylesheet" href="../css/theme.css">

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

        <form action="edit_collection.php?collection_id=<?= urlencode($collection_id); ?>" method="POST"
            class="edit-collection-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

            <label for="name">Collection Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($new_name); ?>" required>

            <label for="access">Access Level:</label>
            <select id="access" name="access" required onchange="toggleProtectedSection()">
                <option value="public" <?= ($new_access === 'public') ? 'selected' : ''; ?>>Public</option>
                <option value="private" <?= ($new_access === 'private') ? 'selected' : ''; ?>>Private</option>
                <option value="protected" <?= ($new_access === 'protected') ? 'selected' : ''; ?>>Protected</option>
            </select>

            <div id="protected-section" class="protected-section"
                style="display: <?= ($new_access === 'protected') ? 'block' : 'none'; ?>;">
                <h3>Allowed Users</h3>
                <ul class="allowed-users-list">
                    <?php if ($new_access === 'protected' && !empty($allowed_users)): ?>
                        <?php foreach ($allowed_users as $user): ?>
                            <li>
                                <span><?= htmlspecialchars($user); ?></span>
                                <form action="edit_collection.php?collection_id=<?= urlencode($collection_id); ?>" method="POST"
                                    style="display: inline;">
                                    <input type="hidden" name="csrf_token"
                                        value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="remove_user" value="<?= htmlspecialchars($user); ?>">
                                    <button type="submit" class="remove-user-button"
                                        onclick="return confirm('Are you sure you want to remove this user?');">Remove</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No users allowed for this collection.</li>
                    <?php endif; ?>
                </ul>

                <form action="edit_collection.php?collection_id=<?= urlencode($collection_id); ?>" method="POST"
                    class="add-user-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="action" value="add_user">
                    <input type="text" name="new_user" placeholder="Enter username to add" required>
                    <button type="submit">Add User</button>
                </form>
            </div>

            <button type="submit">Update Collection</button>
        </form>
    </div>

    <?php // include 'components/footer.php'; ?>

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