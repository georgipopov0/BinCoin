function openCollectionPopup(tradeId, coinId) {
    document.getElementById('popupTradeId').value = tradeId;
    document.getElementById('popupCoinId').value = coinId;

    // Fetch collections dynamically via AJAX
    fetch("../php/fetch_collections.php")
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('collectionSelect');
            select.innerHTML = ''; // Clear previous options
            data.forEach(collection => {
                const option = document.createElement('option');
                option.value = collection.id;
                option.textContent = collection.name;
                select.appendChild(option);
            });
        });

    // Show the popup
    document.getElementById('collectionPopup').style.display = 'block';
    document.getElementById('popupBackdrop').style.display = 'block';
}

function closeCollectionPopup() {
    document.getElementById('collectionPopup').style.display = 'none';
    document.getElementById('popupBackdrop').style.display = 'none';
}
