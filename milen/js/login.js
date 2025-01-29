document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const username = document.getElementById('username').value.trim();

    if (username === "") {
        document.getElementById('error').classList.remove('hidden');
    } else {
        document.getElementById('error').classList.add('hidden');
        alert(`Welcome, ${username}!`);
        // Redirect or perform other actions
        // Example: window.location.href = '/dashboard.html';
    }
});
