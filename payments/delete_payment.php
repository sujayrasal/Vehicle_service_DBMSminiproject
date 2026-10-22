<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: payments.php");

    exit;
}


$id = (int) $_GET['id'];


$stmt = $conn->prepare(
    "DELETE FROM PAYMENT
     WHERE Payment_ID = ?"
);


$stmt->bind_param("i", $id);


$stmt->execute();


header("Location: payments.php");

exit;

?>