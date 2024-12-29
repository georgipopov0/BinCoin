
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>

        <!-- Display Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= isset($name) ? htmlspecialchars($name) : ''; ?>" required>
            </div>

            <!-- Additional fields like password can be added here -->

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>