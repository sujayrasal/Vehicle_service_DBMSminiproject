<?php

include "../auth.php";
include "../db.php";


/* Check vehicle ID */

if (!isset($_GET['id'])) {

    header("Location: vehicles.php");
    exit;
}

$id = (int) $_GET['id'];


/* Get vehicle */

$stmt = $conn->prepare(
    "SELECT *
     FROM VEHICLE
     WHERE Vehicle_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$vehicle = $result->fetch_assoc();


if (!$vehicle) {

    die("Vehicle not found.");

}


/* Get customers */

$customers = $conn->query(
    "SELECT Customer_ID, Name
     FROM CUSTOMER
     ORDER BY Name"
);


/* Update vehicle */

if (isset($_POST['update'])) {

    $customer_id = $_POST['customer_id'];
    $vehicle_no = $_POST['vehicle_no'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $type = $_POST['type'];

    $sql = "UPDATE VEHICLE

            SET Customer_ID = ?,
                Vehicle_No = ?,
                Brand = ?,
                Model = ?,
                Manufacturing_Year = ?,
                Vehicle_Type = ?

            WHERE Vehicle_ID = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "isssisi",
        $customer_id,
        $vehicle_no,
        $brand,
        $model,
        $year,
        $type,
        $id
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

    <title>Edit Vehicle</title>

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

            <h1>Edit Vehicle</h1>

            <p>Update vehicle information</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <!-- Customer -->

            <div class="form-group">

                <label>Customer</label>

                <select name="customer_id" required>

                    <?php while ($customer = $customers->fetch_assoc()) { ?>

                        <option
                            value="<?php echo $customer['Customer_ID']; ?>"

                            <?php

                            if (
                                $customer['Customer_ID']
                                == $vehicle['Customer_ID']
                            ) {
                                echo "selected";
                            }

                            ?>
                        >

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
                    value="<?php echo htmlspecialchars($vehicle['Vehicle_No']); ?>"
                    required
                >

            </div>


            <!-- Brand -->

            <div class="form-group">

                <label>Brand</label>

                <input
                    type="text"
                    name="brand"
                    value="<?php echo htmlspecialchars($vehicle['Brand']); ?>"
                    required
                >

            </div>


            <!-- Model -->

            <div class="form-group">

                <label>Model</label>

                <input
                    type="text"
                    name="model"
                    value="<?php echo htmlspecialchars($vehicle['Model']); ?>"
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
                    value="<?php echo $vehicle['Manufacturing_Year']; ?>"
                    required
                >

            </div>


            <!-- Type -->

            <div class="form-group">

                <label>Vehicle Type</label>

                <select name="type" required>

                    <?php

                    $types = [
                        "Car",
                        "Sedan",
                        "SUV",
                        "Hatchback",
                        "Bike",
                        "Other"
                    ];

                    foreach ($types as $type) {

                        $selected =
                            ($type == $vehicle['Vehicle_Type'])
                            ? "selected"
                            : "";

                        echo "<option value='$type' $selected>
                                $type
                              </option>";
                    }

                    ?>

                </select>

            </div>


            <button
                type="submit"
                name="update"
                class="btn"
            >
                Update Vehicle
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