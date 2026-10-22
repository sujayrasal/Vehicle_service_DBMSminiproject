<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: parts.php");

    exit;
}


$id = (int) $_GET['id'];


/* Check usage */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM SERVICE_PART
     WHERE Part_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();


if ($data['total'] > 0) {

    echo "
    <script>

        alert(
            'Cannot delete this part because it has been used in a service.'
        );

        window.location = 'parts.php';

    </script>
    ";

    exit;
}


/* Delete */

$stmt = $conn->prepare(
    "DELETE FROM PART
     WHERE Part_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();


header("Location: parts.php");

exit;

?>