<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: mechanics.php");

    exit;
}


$id = (int) $_GET['id'];


/* Check assignments */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM SERVICE_ASSIGNMENT
     WHERE Mechanic_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();


if ($data['total'] > 0) {

    echo "

    <script>

        alert(
            'Cannot delete this mechanic because service assignments exist.'
        );

        window.location = 'mechanics.php';

    </script>

    ";

    exit;
}


/* Delete */

$stmt = $conn->prepare(
    "DELETE FROM MECHANIC
     WHERE Mechanic_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();


header("Location: mechanics.php");

exit;

?>