<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: parts.php");

    exit;
}


$id = (int) $_GET['id'];


/* Existing row */

$stmt = $conn->prepare(
    "SELECT *
     FROM SERVICE_PART
     WHERE ServicePart_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$servicePart =
    $stmt->get_result()->fetch_assoc();


if (!$servicePart) {

    die("Service part record not found.");

}


/* Update */

if (isset($_POST['update'])) {

    $quantityInput = $_POST['quantity'] ?? null;
    $new_quantity = is_string($quantityInput) && ctype_digit($quantityInput)
        ? (int) $quantityInput
        : 0;

    if ($new_quantity < 1 || $new_quantity > 2147483647) {
        echo "<script>alert('Enter a positive whole quantity.'); window.history.back();</script>";
        exit;
    }

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
        $servicePart = $stmt->get_result()->fetch_assoc();

        if (!$servicePart) {
            $conn->rollback();
            header("Location: parts.php");
            exit;
        }

        $old_quantity = (int) $servicePart['Quantity'];
        $part_id = (int) $servicePart['Part_ID'];
        $difference = $new_quantity - $old_quantity;

        $stmt = $conn->prepare(
            "SELECT Quantity
             FROM PART
             WHERE Part_ID = ?
             FOR UPDATE"
        );
        $stmt->bind_param("i", $part_id);
        $stmt->execute();
        $part = $stmt->get_result()->fetch_assoc();

        if (!$part) {
            throw new RuntimeException("Part not found.");
        }

        if ($difference > (int) $part['Quantity']) {
            $conn->rollback();
            echo "<script>alert('Not enough stock available.'); window.history.back();</script>";
            exit;
        }

        $stmt = $conn->prepare(
            "UPDATE PART
             SET Quantity = Quantity - ?
             WHERE Part_ID = ?"
        );
        $stmt->bind_param("ii", $difference, $part_id);
        $stmt->execute();

        $stmt = $conn->prepare(
            "UPDATE SERVICE_PART
             SET Quantity = ?
             WHERE ServicePart_ID = ?"
        );
        $stmt->bind_param("ii", $new_quantity, $id);
        $stmt->execute();

        $conn->commit();
        header("Location: parts.php");
        exit;
    } catch (Throwable $error) {
        $conn->rollback();
        http_response_code(500);
        exit("Unable to update this service part.");
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Service Part</title>

    <link rel="stylesheet" href="../style.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>


<div class="sidebar">

    <div class="logo">
        <h2>AutoCare</h2>
        <p>Vehicle Service Management</p>
    </div>

    <a href="../index.php">Dashboard</a>
    <a href="../customers/customers.php">Customers</a>
    <a href="../vehicles/vehicles.php">Vehicles</a>
    <a href="../service_requests/service_requests.php">Service Requests</a>
    <a href="../services/services.php">Services</a>
    <a href="../mechanics/mechanics.php">Mechanics</a>
    <a href="parts.php" class="active">Spare Parts</a>
    <a href="../payments/payments.php">Payments</a>
    <a href="../reports/reports.php">Reports</a>
    <a href="../logout.php" class="logout-link">Logout</a>

</div>


<div class="main">

    <div class="topbar">

        <div>

            <h1>Edit Service Part</h1>

            <p>Update quantity used</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <div class="form-group">

                <label>Quantity</label>

                <input
                    type="number"
                    name="quantity"
                    min="1"
                    value="<?php echo $servicePart['Quantity']; ?>"
                    required
                >

            </div>


            <button
                type="submit"
                name="update"
                class="btn"
            >
                Update
            </button>


            <a
                href="parts.php"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </form>

    </div>

</div>

<script src="../script.js"></script>

</body>

</html>