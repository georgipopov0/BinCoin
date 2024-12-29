
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container">
        <h1>Register</h1>

        <!-- Display Success Message -->
        <?php if ($success): ?>
            <div class="success-message">
                <?= $success; ?>
            </div>
        <?php endif; ?>

        <!-- Display Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= isset($name) ? htmlspecialchars($name) : ''; ?>" required>
            </div>

            <!-- Additional fields like password can be added here -->

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>