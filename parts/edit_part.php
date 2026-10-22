<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: parts.php");

    exit;
}


$id = (int) $_GET['id'];


$stmt = $conn->prepare(
    "SELECT *
     FROM PART
     WHERE Part_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$part = $stmt->get_result()->fetch_assoc();


if (!$part) {

    die("Part not found.");

}


if (isset($_POST['update'])) {

    $part_name = $_POST['part_name'];

    $category = $_POST['category'];

    $quantity = $_POST['quantity'];

    $unit_price = $_POST['unit_price'];

    $reorder_level = $_POST['reorder_level'];


    $sql = "UPDATE PART

            SET
                Part_Name = ?,
                Category = ?,
                Quantity = ?,
                Unit_Price = ?,
                Reorder_Level = ?

            WHERE Part_ID = ?";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "ssidii",
        $part_name,
        $category,
        $quantity,
        $unit_price,
        $reorder_level,
        $id
    );


    if ($stmt->execute()) {

        header("Location: parts.php");

        exit;

    }

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Part</title>

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

            <h1>Edit Spare Part</h1>

            <p>Update inventory details</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <div class="form-group">

                <label>Part Name</label>

                <input
                    type="text"
                    name="part_name"
                    value="<?php echo htmlspecialchars($part['Part_Name']); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Category</label>

                <input
                    type="text"
                    name="category"
                    value="<?php echo htmlspecialchars($part['Category']); ?>"
                >

            </div>


            <div class="form-group">

                <label>Quantity</label>

                <input
                    type="number"
                    name="quantity"
                    min="0"
                    value="<?php echo $part['Quantity']; ?>"
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
                    value="<?php echo $part['Unit_Price']; ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Reorder Level</label>

                <input
                    type="number"
                    name="reorder_level"
                    min="0"
                    value="<?php echo $part['Reorder_Level']; ?>"
                    required
                >

            </div>


            <button
                type="submit"
                name="update"
                class="btn"
            >
                Update Part
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