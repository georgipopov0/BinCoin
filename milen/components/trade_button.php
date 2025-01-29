<?php


$coin_id = $_GET['coin_id'] ?? null;
?>

<button id="initiateTrade" data-coin-id="<?php echo $coin_id; ?>">
    Initiate Trade
</button>

<script>
document.getElementById('initiateTrade').addEventListener('click', function() {
    const coinId = this.getAttribute('data-coin-id');
    
    fetch('initiate_trade.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `coin_id=${coinId}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to initiate trade.');
    });
});
</script>
