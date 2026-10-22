<?php

include "../auth.php";
include "../db.php";

/* Get customers for dropdown */

$customers = $conn->query(
    "SELECT Customer_ID, Name
     FROM CUSTOMER
     ORDER BY Name"
);


/* Add vehicle */

if (isset($_POST['submit'])) {

    $customer_id = $_POST['customer_id'];
    $vehicle_no = $_POST['vehicle_no'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $type = $_POST['type'];

    $sql = "INSERT INTO VEHICLE
            (
                Customer_ID,
                Vehicle_No,
                Brand,
                Model,
                Manufacturing_Year,
                Vehicle_Type
            )
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "isssis",
        $customer_id,
        $vehicle_no,
        $brand,
        $model,
        $year,
        $type
    );

    if ($stmt->execute()) {

        header("Location: vehicles.php");
        exit;

    } else {

        echo "Error: " . $stmt->error;

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Vehicle</title>

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
    <a href="vehicles.php" class="active">Vehicles</a>
    <a href="../service_requests/service_requests.php">Service Requests</a>
    <a href="../services/services.php">Services</a>
    <a href="../mechanics/mechanics.php">Mechanics</a>
    <a href="../parts/parts.php">Spare Parts</a>
    <a href="../payments/payments.php">Payments</a>
    <a href="../reports/reports.php">Reports</a>
    <a href="../logout.php" class="logout-link">Logout</a>

</div>


<div class="main">

    <div class="topbar">

        <div>

            <h1>Add Vehicle</h1>

            <p>Register a vehicle for a customer</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">

            <!-- Customer Dropdown -->

            <div class="form-group">

                <label>Select Customer</label>

                <select name="customer_id" required>

                    <option value="">
                        -- Select Customer --
                    </option>

                    <?php while ($customer = $customers->fetch_assoc()) { ?>

                        <option value="<?php echo $customer['Customer_ID']; ?>">

                            <?php echo htmlspecialchars($customer['Name']); ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <!-- Vehicle Number -->

            <div class="form-group">

                <label>Vehicle Number</label>

                <input
                    type="text"
                    name="vehicle_no"
                    placeholder="MH12AB1234"
                    oninput="formatVehicleNumber(this)"
                    required
                >

            </div>


            <!-- Brand -->

            <div class="form-group">

                <label>Brand</label>

                <input
                    type="text"
                    name="brand"
                    placeholder="Example: Toyota"
                    required
                >

            </div>


            <!-- Model -->

            <div class="form-group">

                <label>Model</label>

                <input
                    type="text"
                    name="model"
                    placeholder="Example: Innova"
                    required
                >

            </div>


            <!-- Year -->

            <div class="form-group">

                <label>Manufacturing Year</label>

                <input
                    type="number"
                    name="year"
                    min="1900"
                    max="<?php echo date('Y'); ?>"
                    placeholder="2022"
                    required
                >

            </div>


            <!-- Vehicle Type -->

            <div class="form-group">

                <label>Vehicle Type</label>

                <select name="type" required>

                    <option value="">
                        -- Select Vehicle Type --
                    </option>

                    <option value="Car">Car</option>

                    <option value="Sedan">Sedan</option>

                    <option value="SUV">SUV</option>

                    <option value="Hatchback">Hatchback</option>

                    <option value="Bike">Bike</option>

                    <option value="Other">Other</option>

                </select>

            </div>


            <button
                type="submit"
                name="submit"
                class="btn"
            >
                Add Vehicle
            </button>


            <a
                href="vehicles.php"
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