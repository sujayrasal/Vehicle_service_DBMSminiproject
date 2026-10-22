<?php

include "../auth.php";
include "../db.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: customers.php");
    exit;
}

$check = $conn->prepare(
    "SELECT 1
     FROM VEHICLE
     WHERE Customer_ID = ?
     LIMIT 1"
);
$check->bind_param("i", $id);
$check->execute();

if ($check->get_result()->fetch_row()) {
    $_SESSION['customer_delete_error'] =
        "This customer cannot be deleted while vehicles are linked to them. Delete or reassign those vehicles first.";
    header("Location: customers.php");
    exit;
}

try {
    $stmt = $conn->prepare(
        "DELETE FROM CUSTOMER
         WHERE Customer_ID = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
} catch (mysqli_sql_exception $error) {
    if ($error->getCode() !== 1451) {
        throw $error;
    }

    $_SESSION['customer_delete_error'] =
        "This customer cannot be deleted while related records exist.";
}

header("Location: customers.php");

exit;

?>