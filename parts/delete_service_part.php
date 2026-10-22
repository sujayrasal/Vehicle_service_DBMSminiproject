<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: parts.php");

    exit;
}


$id = (int) $_GET['id'];


$conn->begin_transaction();

try {
    $stmt = $conn->prepare(
        "SELECT Part_ID, Quantity
         FROM SERVICE_PART
         WHERE ServicePart_ID = ?
         FOR UPDATE"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) {
        $conn->rollback();
        header("Location: parts.php");
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE PART
         SET Quantity = Quantity + ?
         WHERE Part_ID = ?"
    );
    $stmt->bind_param("ii", $data['Quantity'], $data['Part_ID']);
    $stmt->execute();

    $stmt = $conn->prepare(
        "DELETE FROM SERVICE_PART
         WHERE ServicePart_ID = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $conn->commit();
} catch (Throwable $error) {
    $conn->rollback();
    http_response_code(500);
    exit("Unable to remove this service part.");
}

header("Location: parts.php");
exit;

?>