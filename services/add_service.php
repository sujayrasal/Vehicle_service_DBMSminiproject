<?php

include "../auth.php";
include "../db.php";


/* Get service requests */

$sql = "SELECT
            SR.Request_ID,
            SR.Service_Type,
            SR.Status,

            V.Vehicle_No,
            V.Brand,
            V.Model,

            C.Name AS Customer_Name

        FROM SERVICE_REQUEST SR

        JOIN VEHICLE V
        ON SR.Vehicle_ID = V.Vehicle_ID

        JOIN CUSTOMER C
        ON V.Customer_ID = C.Customer_ID

        ORDER BY SR.Request_ID DESC";


$requests = $conn->query($sql);


/* Add service */

if (isset($_POST['submit'])) {

    $request_id = $_POST['request_id'];

    $service_date = $_POST['service_date'];

    $service_name = $_POST['service_name'];

    $description = $_POST['description'];

    $labour_cost = $_POST['labour_cost'];

    $status = $_POST['status'];


    $sql = "INSERT INTO SERVICE
            (
                Request_ID,
                Service_Date,
                Service_Name,
                Description,
                Labour_Cost,
                Status
            )

            VALUES (?, ?, ?, ?, ?, ?)";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "isssds",
        $request_id,
        $service_date,
        $service_name,
        $description,
        $labour_cost,
        $status
    );


    if ($stmt->execute()) {

        header("Location: services.php");

        exit;

    } else {

        echo "Error: " . $stmt->error;

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Service</title>

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

    <a href="../service_requests/service_requests.php">
        Service Requests
    </a>

    <a href="services.php" class="active">
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

            <h1>Add Service</h1>

            <p>Create a service for an existing request</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <!-- Request -->

            <div class="form-group">

                <label>Select Service Request</label>

                <select name="request_id" required>

                    <option value="">
                        -- Select Request --
                    </option>


                    <?php while ($request = $requests->fetch_assoc()) { ?>

                    <option value="<?php echo $request['Request_ID']; ?>">

                        <?php

                        echo htmlspecialchars(
                            "Request "
                            . $request['Request_ID']
                            . " - "
                            . $request['Vehicle_No']
                            . " - "
                            . $request['Brand']
                            . " "
                            . $request['Model']
                            . " - "
                            . $request['Customer_Name']
                            . " - "
                            . $request['Service_Type']
                        );

                        ?>

                    </option>

                    <?php } ?>

                </select>

            </div>


            <!-- Date -->

            <div class="form-group">

                <label>Service Date</label>

                <input
                    type="date"
                    name="service_date"
                    value="<?php echo date('Y-m-d'); ?>"
                    required
                >

            </div>


            <!-- Service Name -->

            <div class="form-group">

                <label>Service Name</label>

                <input
                    type="text"
                    name="service_name"
                    placeholder="Example: Brake Pad Replacement"
                    required
                >

            </div>


            <!-- Description -->

            <div class="form-group">

                <label>Description</label>

                <textarea
                    name="description"
                    rows="4"
                    placeholder="Enter work performed..."
                ></textarea>

            </div>


            <!-- Labour Cost -->

            <div class="form-group">

                <label>Labour Cost (₹)</label>

                <input
                    type="number"
                    name="labour_cost"
                    min="0"
                    step="0.01"
                    value="0"
                    required
                >

            </div>


            <!-- Status -->

            <div class="form-group">

                <label>Status</label>

                <select name="status" required>

                    <option value="Pending">
                        Pending
                    </option>

                    <option value="In Progress" selected>
                        In Progress
                    </option>

                </select>

            </div>


            <button
                type="submit"
                name="submit"
                class="btn"
            >
                Add Service
            </button>


            <a
                href="services.php"
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