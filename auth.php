<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    $scriptDirectory = basename(
        str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']))
    );

    $projectDirectory = basename(__DIR__);

    $loginPath = ($scriptDirectory === '' || $scriptDirectory === $projectDirectory)
        ? 'login.php'
        : '../login.php';

    header("Location: $loginPath");
    exit;
}

?>
