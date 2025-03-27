<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Events - Register</title>
    <link href="css/style.css" rel="stylesheet">
  </head>
  <body>
    <div class="login-title">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
      <h1>Registration</h1>
    </div>
    <div class="login-form">
      <section id="register">
        <form id="registerForm">
          <div class="form-group">
            <label for="user_name">Username:</label>
            <input type="text" id="user_name" name="user_name" required>
          </div>
          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
          </div>
          <div class="form-group">
            <label for="firstName">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
          </div>
          <div class="form-group">
            <label for="lastName">Last Name:</label>
            <input type="text" id="last_name" name="last_name" placeholder="Optional">
          </div>
          <div class="form-group">
            <label for="userLevel">User Level:</label>
            <select id="userLevel" name="userLevel" required>
              <option value="student">Student</option>
              <option value="admin">Admin</option>
              <option value="superadmin">Super Admin</option>
            </select>
          </div>
          <span id="registerResult"></span>
          <div class="form-group">
            <button type="button" onclick="doRegister()">Register</button>
          </div>
        </form>
        <p>Already have an account? <a href="index.php">Login here</a></p>
      </section>
    </div>
    <script>
        function doRegister() {
            const user_name = document.getElementById("user_name").value.trim();
            const password = document.getElementById("password").value;
            const firstName = document.getElementById("first_name").value;
            const lastName = document.getElementById("last_name").value;
            const userLevel = document.getElementById("userLevel").value;
            const registerResult = document.getElementById("registerResult");

            registerResult.innerHTML = "";

            console.log("Username:", user_name); // Add these lines
            console.log("Password:", password);
            console.log("First Name:", firstName);
            console.log("Last Name:", lastName);
            console.log("User Level:", userLevel);

            if (user_name === "" || password === "" || firstName === "" || userLevel === "") {
                $(registerResult).append("<p>Please fill out all fields.</p>");
                return;
            }

            fetch('/COP4710/Frontend/WAMPAPI/formhandler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `user_name=${user_name}&password=${password}&first_name=${firstName}&last_name=${lastName}&userLevel=${userLevel}`
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success === false) {
                    $(registerResult).append(`<p>${data.error_message}</p>`);
                } else if (data.success === true) {
                    $(registerResult).append("<p>Registration successful. You may now log in.</p>");
                }
            })
        }
        </script>
  </body>
</html>