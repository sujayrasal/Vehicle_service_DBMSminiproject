<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: mechanics.php");

    exit;
}


$id = (int) $_GET['id'];


$stmt = $conn->prepare(
    "DELETE FROM SERVICE_ASSIGNMENT
     WHERE Assignment_ID = ?"
);


$stmt->bind_param("i", $id);


$stmt->execute();


header("Location: mechanics.php");

exit;

?>