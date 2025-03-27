<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Events - Login</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="css/style.css" rel="stylesheet"> </head>
<body id="body">
    <div class="login-title">
        <h2 id="title">College Events</h2> <br><br>
    </div>
    <div class="login-form">
        <h3>User Login</h3>
        <form id="loginForm">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <span id="loginResult"></span>
            <div class="form-group">
                <button type="button" onclick="doLogin()">Login</button>
            </div>
            <div class="form-group">
                <button type="button" onclick="goToRegister()">Create Account</button>
            </div>
        </form>
    </div>

    <script>
   function doLogin() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const loginResult = document.getElementById("loginResult");

    loginResult.innerHTML = "";

    if (username === "" || password === "") {
        $(loginResult).append("<p>Please enter your username and password.</p>");
        return;
    }

    fetch('/COP4710/Frontend/WAMPAPI/login_process.php', { // Changed URL
        method: 'POST', // Changed method to POST
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded', // Added header
        },
        body: `username=${username}&password=${password}` // Sent data as form URL-encoded
    })
    .then(response => response.json()) // Assuming the server returns JSON
    .then(data => {
        console.log(data);
        if (data.success === false) {
            $(loginResult).append(`<p>${data.error_message}</p>`); // Display error message
        } else if (data.success === true) {
            sessionStorage.setItem("userID", data.userID); //example of how to access userID
            sessionStorage.setItem("userRole", data.userRole);
            window.location.href = "/eventsPage.php";
        }
    })
    .catch(error => {
        console.error("Fetch error:", error);
        $(loginResult).append("<p>An error occurred during login. Please try again.</p>");
    });
}
      
      function goToRegister() {
          window.location.href = "register.html";
      }
      </script>
</body>
</html>