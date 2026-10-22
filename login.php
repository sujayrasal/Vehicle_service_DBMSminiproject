<?php

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

include "db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare(
        "SELECT User_ID, Name, Email, Password, Role
         FROM USERS
         WHERE Email = ?
         LIMIT 1"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['Password'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['User_ID'];
        $_SESSION['user_name'] = $user['Name'];
        $_SESSION['user_email'] = $user['Email'];
        $_SESSION['user_role'] = $user['Role'];

        header("Location: index.php");
        exit;
    }

    $error = "Invalid email or password.";
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - AutoCare</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="login-page">

<div class="login-box">

    <div class="logo">
        <h2>AutoCare</h2>
        <p>Vehicle Service Management</p>
    </div>

    <h1>Staff Login</h1>

    <?php if ($error) { ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Sign In</button>

    </form>

</div>

<script src="script.js"></script>

</body>

</html>
