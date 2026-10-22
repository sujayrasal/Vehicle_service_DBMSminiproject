<?php

include "../auth.php";
include "../db.php";

if (!isset($_GET['id'])) {

    header("Location: vehicles.php");
    exit;
}

$id = (int) $_GET['id'];


/* Check whether vehicle has service requests */

$check = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM SERVICE_REQUEST
     WHERE Vehicle_ID = ?"
);

$check->bind_param("i", $id);

$check->execute();

$result = $check->get_result();

$data = $result->fetch_assoc();


if ($data['total'] > 0) {

    echo "

    <script>

        alert(
            'Cannot delete this vehicle because it has service requests.'
        );

        window.location = 'vehicles.php';

    </script>

    ";

    exit;
}


/* Delete vehicle */

$stmt = $conn->prepare(
    "DELETE FROM VEHICLE
     WHERE Vehicle_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();


header("Location: vehicles.php");

exit;

?>