<?php
session_start();
include('config.php'); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($action == 'Login') {
        // Handle login
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password); // Use variables here
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $_SESSION['user'] = $email;
            header('Location: index.html');
            exit(); // Add this line to stop further execution
        } else {
            echo "User does not exist.";
        }
        $stmt->close();
    } elseif ($action == 'SignUp') {
        // Handle signup
        $confirm_password = $_POST['confirm_password'];
        if ($password != $confirm_password) {
            echo "Passwords do not match.";
            exit();
        }

        // Check if user already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "User already registered.";
            exit();
        }

        // Register the user
        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $password); // Use variables here
        
        if ($stmt->execute()) {
            echo "User registered successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>Sign Up | Log In</title>
  <link rel="stylesheet" href="css/login.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <div class="wrapper">
    <div class="title-text">
      <div class="title login">
        Account
      </div>
      <div class="title signup">
        Account
      </div>
    </div>
    <div class="form-container">
      <div class="slide-controls">
        <input type="radio" name="slide" id="login" checked>
        <input type="radio" name="slide" id="signup">
        <label for="login" class="slide login">Login</label>
        <label for="signup" class="slide signup">SignUp</label>
        <div class="slider-tab"></div>
      </div>
      <div class="form-inner">
        <form action="login.php" method="post" class="login">
          <div class="field">
            <input type="text" name="email" placeholder="Email Address" required>
          </div>
          <div class="field">
            <input type="password" name="password" placeholder="Password" required>
          </div>
          <div class="pass-link">
            <a href="#">Reset password?</a>
          </div>
          <div class="field btn">
            <div class="btn-layer"></div>
            <input type="submit" name="action" value="Login">
          </div>
          <div class="signup-link">
            Don't Have Account? <a href="">Create A New</a>
          </div>
        </form>
        <form action="login.php" method="post" class="signup">
          <div class="field">
            <input type="text" name="email" placeholder="Email Address" required>
          </div>
          <div class="field">
            <input type="password" name="password" placeholder="Password" required>
          </div>
          <div class="field">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
          </div>
          <div class="field btn">
            <div class="btn-layer"></div>
            <input type="submit" name="action" value="SignUp">
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    const loginText = document.querySelector(".title-text .login");
    const loginForm = document.querySelector("form.login");
    const loginBtn = document.querySelector("label.login");
    const signupBtn = document.querySelector("label.signup");
    const signupLink = document.querySelector("form .signup-link a");
    signupBtn.onclick = (() => {
      loginForm.style.marginLeft = "-50%";
      loginText.style.marginLeft = "-50%";
    });
    loginBtn.onclick = (() => {
      loginForm.style.marginLeft = "0%";
      loginText.style.marginLeft = "0%";
    });
    signupLink.onclick = (() => {
      signupBtn.click();
      return false;
    });
  </script>
</body>

</html>

