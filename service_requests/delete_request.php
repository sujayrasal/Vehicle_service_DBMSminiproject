<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: service_requests.php");

    exit;
}


$id = (int) $_GET['id'];


/* Check whether service exists */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM SERVICE
     WHERE Request_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$data = $result->fetch_assoc();


if ($data['total'] > 0) {

    echo "

    <script>

        alert(
            'Cannot delete this request because a service record already exists.'
        );

        window.location = 'service_requests.php';

    </script>

    ";

    exit;
}


/* Delete request */

$stmt = $conn->prepare(
    "DELETE FROM SERVICE_REQUEST
     WHERE Request_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();


header("Location: service_requests.php");

exit;

?>