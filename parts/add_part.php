<?php

include "../auth.php";
include "../db.php";


if (isset($_POST['submit'])) {

    $part_name = $_POST['part_name'];

    $category = $_POST['category'];

    $quantity = $_POST['quantity'];

    $unit_price = $_POST['unit_price'];

    $reorder_level = $_POST['reorder_level'];


    $sql = "INSERT INTO PART
            (
                Part_Name,
                Category,
                Quantity,
                Unit_Price,
                Reorder_Level
            )

            VALUES (?, ?, ?, ?, ?)";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "ssidi",
        $part_name,
        $category,
        $quantity,
        $unit_price,
        $reorder_level
    );


    if ($stmt->execute()) {

        header("Location: parts.php");

        exit;

    } else {

        echo "Error: " . $stmt->error;

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Part</title>

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

            <h1>Add Spare Part</h1>

            <p>Add a new part to inventory</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <div class="form-group">

                <label>Part Name</label>

                <input
                    type="text"
                    name="part_name"
                    required
                >

            </div>


            <div class="form-group">

                <label>Category</label>

                <input
                    type="text"
                    name="category"
                    placeholder="Example: Brake"
                >

            </div>


            <div class="form-group">

                <label>Quantity</label>

                <input
                    type="number"
                    name="quantity"
                    min="0"
                    value="0"
                    required
                >

            </div>


            <div class="form-group">

                <label>Unit Price (₹)</label>

                <input
                    type="number"
                    name="unit_price"
                    min="0"
                    step="0.01"
                    required
                >

            </div>


            <div class="form-group">

                <label>Reorder Level</label>

                <input
                    type="number"
                    name="reorder_level"
                    min="0"
                    value="5"
                    required
                >

            </div>


            <button
                type="submit"
                name="submit"
                class="btn"
            >
                Add Part
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