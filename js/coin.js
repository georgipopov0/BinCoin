// Simulated data for the selected coin
const coinData = {
    cost: 10.0,
    value: 5.0,
    currency: "USD",
    country: "USA",
    year: 2000,
    front_path: "images/front1.png",
    back_path: "images/back1.png",
};

// Function to display coin details
function displayCoinDetails() {
    document.getElementById("cost").innerText = coinData.cost.toFixed(2);
    document.getElementById("value").innerText = coinData.value.toFixed(2);
    document.getElementById("currency").innerText = coinData.currency;
    document.getElementById("country").innerText = coinData.country;
    document.getElementById("year").innerText = coinData.year;

    // Set image paths
    document.getElementById("frontImage").src = coinData.front_path;
    document.getElementById("backImage").src = coinData.back_path;
}

// Load details when the page is ready
window.onload = displayCoinDetails;
