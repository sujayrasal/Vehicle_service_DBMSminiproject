<?php

include "../auth.php";
include "../db.php";


/* Get vehicles for dropdown */

$sql = "SELECT
            V.Vehicle_ID,
            V.Vehicle_No,
            V.Brand,
            V.Model,
            C.Name AS Customer_Name

        FROM VEHICLE V

        JOIN CUSTOMER C
        ON V.Customer_ID = C.Customer_ID

        ORDER BY V.Vehicle_No";

$vehicles = $conn->query($sql);


/* Insert request */

if (isset($_POST['submit'])) {

    $vehicle_id = $_POST['vehicle_id'];
    $request_date = $_POST['request_date'];
    $service_type = $_POST['service_type'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];


    $sql = "INSERT INTO SERVICE_REQUEST
            (
                Vehicle_ID,
                Request_Date,
                Service_Type,
                Description,
                Status,
                Priority
            )

            VALUES (?, ?, ?, ?, ?, ?)";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "isssss",
        $vehicle_id,
        $request_date,
        $service_type,
        $description,
        $status,
        $priority
    );


    if ($stmt->execute()) {

        header("Location: service_requests.php");

        exit;

    } else {

        echo "Error: " . $stmt->error;

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Service Request</title>

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

    <a href="../customers/customers.php">
        Customers
    </a>

    <a href="../vehicles/vehicles.php">
        Vehicles
    </a>

    <a href="service_requests.php" class="active">
        Service Requests
    </a>

    <a href="../services/services.php">
        Services
    </a>

    <a href="../mechanics/mechanics.php">
        Mechanics
    </a>

    <a href="../parts/parts.php">
        Spare Parts
    </a>

    <a href="../payments/payments.php">
        Payments
    </a>

    <a href="../reports/reports.php">
        Reports
    </a>

    <a href="../logout.php" class="logout-link">
        Logout
    </a>

</div>


<div class="main">

    <div class="topbar">

        <div>

            <h1>Add Service Request</h1>

            <p>Create a new vehicle service request</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <!-- Vehicle -->

            <div class="form-group">

                <label>Select Vehicle</label>

                <select name="vehicle_id" required>

                    <option value="">
                        -- Select Vehicle --
                    </option>


                    <?php while ($vehicle = $vehicles->fetch_assoc()) { ?>

                    <option value="<?php echo $vehicle['Vehicle_ID']; ?>">

                        <?php

                        echo htmlspecialchars(
                            $vehicle['Vehicle_No']
                            . " - "
                            . $vehicle['Brand']
                            . " "
                            . $vehicle['Model']
                            . " - "
                            . $vehicle['Customer_Name']
                        );

                        ?>

                    </option>

                    <?php } ?>

                </select>

            </div>


            <!-- Date -->

            <div class="form-group">

                <label>Request Date</label>

                <input
                    type="date"
                    name="request_date"
                    value="<?php echo date('Y-m-d'); ?>"
                    required
                >

            </div>


            <!-- Service Type -->

            <div class="form-group">

                <label>Service Type</label>

                <select name="service_type" required>

                    <option value="">
                        -- Select Service Type --
                    </option>

                    <option value="General Service">
                        General Service
                    </option>

                    <option value="Repair">
                        Repair
                    </option>

                    <option value="Oil Change">
                        Oil Change
                    </option>

                    <option value="Brake Service">
                        Brake Service
                    </option>

                    <option value="Engine Repair">
                        Engine Repair
                    </option>

                    <option value="AC Service">
                        AC Service
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>


            <!-- Description -->

            <div class="form-group">

                <label>Description</label>

                <textarea
                    name="description"
                    rows="4"
                    placeholder="Describe the vehicle problem..."
                ></textarea>

            </div>


            <!-- Status -->

            <div class="form-group">

                <label>Status</label>

                <select name="status" required>

                    <option value="Pending" selected>
                        Pending
                    </option>

                    <option value="In Progress">
                        In Progress
                    </option>

                    <option value="Completed">
                        Completed
                    </option>

                </select>

            </div>


            <!-- Priority -->

            <div class="form-group">

                <label>Priority</label>

                <select name="priority" required>

                    <option value="Low">
                        Low
                    </option>

                    <option value="Normal" selected>
                        Normal
                    </option>

                    <option value="High">
                        High
                    </option>

                </select>

            </div>


            <button
                type="submit"
                name="submit"
                class="btn"
            >
                Add Request
            </button>


            <a
                href="service_requests.php"
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