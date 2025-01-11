<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Coin</title>
    <!-- Link to external CSS files -->
    <link rel="stylesheet" href="../css/theme.css">
    <!-- <link rel="stylesheet" href="../css/navbar.css"> -->
    <!-- Inline CSS for Autocomplete and Additional Styling -->
    <style>
        .container {
            max-width: 600px;
            width: 100%;
            margin: 40px auto;
            padding: 20px;
            box-sizing: border-box;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus,
        input[type="file"]:focus {
            border-color: #007BFF;
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-messages {
            background-color: #f8d7da;
            color: #842029;
            padding: 15px;
            border: 1px solid #f5c2c7;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .error-messages ul {
            list-style-type: disc;
            padding-left: 20px;
            margin: 0;
        }

        .success-message {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 15px;
            border: 1px solid #badbcc;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        /* Autocomplete Suggestions Styles */
        .suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 1000;
            background-color: #fff;
            border: 1px solid #ccc;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            display: none;
            /* Hidden by default */
        }

        .suggestions div {
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
        }

        .suggestions div:hover,
        .suggestion-active {
            background-color: #f0f0f0;
        }

        /* Responsive Adjustments */
        @media (max-width: 600px) {
            .container {
                padding: 15px;
                margin: 20px auto;
            }

            input[type="submit"] {
                font-size: 16px;
            }

            .suggestions div {
                font-size: 14px;
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <div class="container">
        <h1>Add New Coin</h1>

        <!-- Display Success Message -->
        <?php if ($success): ?>
            <div class="success-message">
                <?= htmlspecialchars($success); ?>
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

        <form action="add_coin.php" method="POST" enctype="multipart/form-data">
            <!-- Cost -->
            <div class="form-group">
                <label for="cost">Cost (e.g., 10.50)</label>
                <input type="text" id="cost" name="cost" value="<?= htmlspecialchars($cost); ?>" required>
            </div>

            <!-- Value -->
            <div class="form-group">
                <label for="value">Value (e.g., 15.75)</label>
                <input type="text" id="value" name="value" value="<?= htmlspecialchars($value); ?>" required>
            </div>

            <!-- Currency with Autocomplete -->
            <div class="form-group autocomplete">
                <label for="currency">Currency</label>
                <input type="text" id="currency" name="currency" placeholder="Currency"
                    value="<?= htmlspecialchars($currency); ?>" autocomplete="off" required aria-autocomplete="list"
                    aria-controls="currency-suggestions" aria-expanded="false">
                <div id="currency-suggestions" class="suggestions" role="listbox">
                    <!-- Suggestions will be populated dynamically via JavaScript -->
                </div>
            </div>

            <!-- Country with Autocomplete -->
            <div class="form-group autocomplete">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" placeholder="Country"
                    value="<?= htmlspecialchars($country); ?>" autocomplete="off" required aria-autocomplete="list"
                    aria-controls="country-suggestions" aria-expanded="false">
                <div id="country-suggestions" class="suggestions" role="listbox">
                    <!-- Suggestions will be populated dynamically via JavaScript -->
                </div>
            </div>

            <!-- Year -->
            <div class="form-group">
                <label for="year">Year</label>
                <input type="number" id="year" name="year" value="<?= htmlspecialchars($year); ?>" required>
            </div>

            <!-- Coin Collection -->
            <div class="form-group">
                <label for="coin_collection_id">Coin Collection</label>
                <select id="coin_collection_id" name="coin_collection_id" required>
                    <option value="">-- Select Collection --</option>
                    <?php foreach ($collections as $collection): ?>
                        <option value="<?= htmlspecialchars($collection['id']); ?>"
                            <?= ($collection['id'] == $coin_collection_id) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($collection['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Front Image -->
            <div class="form-group">
                <label for="front_image">Front Image (JPEG, PNG, GIF | Max: 2MB)</label>
                <input type="file" id="front_image" name="front_image" accept="image/jpeg, image/png, image/gif"
                    required>
            </div>

            <!-- Back Image -->
            <div class="form-group">
                <label for="back_image">Back Image (JPEG, PNG, GIF | Max: 2MB)</label>
                <input type="file" id="back_image" name="back_image" accept="image/jpeg, image/png, image/gif" required>
            </div>

            <!-- Submit Button -->
            <input type="submit" value="Add Coin">
        </form>
    </div>

    <!-- Vanilla JavaScript for Autocomplete -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            /**
             * Initialize autocomplete functionality for a given input field.
             * @param {string} inputId - The ID of the input element.
             * @param {string} suggestionsId - The ID of the suggestions container.
             */
            function initAutocomplete(inputId, suggestionsId) {
                const input = document.getElementById(inputId);
                const suggestionsContainer = document.getElementById(suggestionsId);

                let currentFocus = -1;
                let debounceTimer;

                // Event listener for user input
                input.addEventListener('input', function () {
                    const value = this.value.trim();
                    if (!value) {
                        suggestionsContainer.innerHTML = '';
                        suggestionsContainer.style.display = 'none';
                        input.setAttribute('aria-expanded', 'false');
                        return false;
                    }

                    // Debounce the API call by 300ms
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        fetch(`autocomplete.php?field=${encodeURIComponent(inputId)}&term=${encodeURIComponent(value)}`)
                            .then(response => response.json())
                            .then(data => {
                                suggestionsContainer.innerHTML = '';
                                if (data.length === 0) {
                                    // Optionally display "No results found"
                                    const noResult = document.createElement('div');
                                    noResult.textContent = 'No results found';
                                    noResult.style.color = '#999';
                                    suggestionsContainer.appendChild(noResult);
                                    suggestionsContainer.style.display = 'block';
                                    input.setAttribute('aria-expanded', 'true');
                                    return false;
                                }

                                data.forEach(item => {
                                    const suggestionItem = document.createElement('div');
                                    // Highlight the matching part
                                    const regex = new RegExp(`(${value})`, 'gi');
                                    const highlighted = item.replace(regex, '<strong>$1</strong>');
                                    suggestionItem.innerHTML = highlighted;
                                    suggestionItem.setAttribute('role', 'option');
                                    suggestionItem.addEventListener('click', function () {
                                        input.value = item;
                                        suggestionsContainer.innerHTML = '';
                                        suggestionsContainer.style.display = 'none';
                                        input.setAttribute('aria-expanded', 'false');
                                    });
                                    suggestionsContainer.appendChild(suggestionItem);
                                });

                                suggestionsContainer.style.display = 'block';
                                input.setAttribute('aria-expanded', 'true');
                            })
                            .catch(error => {
                                console.error('Error fetching autocomplete suggestions:', error);
                                suggestionsContainer.style.display = 'none';
                                input.setAttribute('aria-expanded', 'false');
                            });
                    }, 300); // 300ms debounce delay
                });

                // Event listener for keyboard navigation
                input.addEventListener('keydown', function (e) {
                    const items = suggestionsContainer.getElementsByTagName('div');
                    if (e.key === 'ArrowDown') { // Down key
                        currentFocus++;
                        addActive(items);
                    } else if (e.key === 'ArrowUp') { // Up key
                        currentFocus--;
                        addActive(items);
                    } else if (e.key === 'Enter') { // Enter key
                        e.preventDefault();
                        if (currentFocus > -1) {
                            if (items[currentFocus]) {
                                items[currentFocus].click();
                            }
                        }
                    }
                });

                /**
                 * Adds active class to the currently focused suggestion.
                 * @param {HTMLCollection} items - Collection of suggestion items.
                 */
                function addActive(items) {
                    if (!items) return false;
                    removeActive(items);
                    if (currentFocus >= items.length) currentFocus = 0;
                    if (currentFocus < 0) currentFocus = items.length - 1;
                    items[currentFocus].classList.add('suggestion-active');
                    // Scroll into view if necessary
                    items[currentFocus].scrollIntoView({ block: 'nearest' });
                }

                /**
                 * Removes active class from all suggestion items.
                 * @param {HTMLCollection} items - Collection of suggestion items.
                 */
                function removeActive(items) {
                    for (let item of items) {
                        item.classList.remove('suggestion-active');
                    }
                }

                // Close suggestions when clicking outside
                document.addEventListener('click', function (e) {
                    if (e.target !== input) {
                        suggestionsContainer.innerHTML = '';
                        suggestionsContainer.style.display = 'none';
                        input.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Initialize autocomplete for 'country' and 'currency'
            initAutocomplete('country', 'country-suggestions');
            initAutocomplete('currency', 'currency-suggestions');
        });
    </script>
</body>

</html>