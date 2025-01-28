<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coin Details</title>
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/coin.css">
    <style>
        .coin-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .coin-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .coin-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .image-section {
            flex: 1 1 300px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .image-section img {
            width: 100%;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 4px;
            object-fit: contain;
            background-color: #f9f9f9;
        }

        .info-section {
            flex: 1 1 300px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 18px;
            color: #555;
        }

        .info-section p {
            margin: 0;
        }

        @media (max-width: 768px) {
            .coin-details {
                flex-direction: column;
                align-items: center;
            }

            .info-section {
                width: 100%;
            }
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="coin-container">
        <h1>Coin Details</h1>
        <div class="coin-details">
            <div class="image-section">
                <img src="../<?= verify_image($coin['front_path']); ?>" alt="Coin Front"
           />
                <img src="<?= verify_image($coin['back_path']); ?>" alt="Coin Back"
                    onerror="this.onerror=null; this.src='/assets/images/f1.jpg'" />
            </div>
            <div class="info-section">
                <p><strong>Cost:</strong> $<?= number_format($coin['cost'], 2); ?></p>
                <p><strong>Value:</strong> $<?= number_format($coin['value'], 2); ?></p>
                <p><strong>Currency:</strong> <?= htmlspecialchars($coin['currency']); ?></p>
                <p><strong>Country:</strong> <?= htmlspecialchars($coin['country']); ?></p>
                <p><strong>Year:</strong> <?= htmlspecialchars($coin['year']); ?></p>
            </div>
        </div>
        <a href="dashboard.php" class="back-button">← Back to Coins List</a>
    </div>
</body>

</html>