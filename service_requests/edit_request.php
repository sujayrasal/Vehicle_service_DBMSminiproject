<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: service_requests.php");

    exit;
}


$id = (int) $_GET['id'];


/* Get request */

$stmt = $conn->prepare(
    "SELECT *
     FROM SERVICE_REQUEST
     WHERE Request_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$request = $result->fetch_assoc();


if (!$request) {

    die("Service request not found.");

}


/* Get vehicles */

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


/* Update */

if (isset($_POST['update'])) {

    $vehicle_id = $_POST['vehicle_id'];

    $request_date = $_POST['request_date'];

    $service_type = $_POST['service_type'];

    $description = $_POST['description'];

    $status = $_POST['status'];

    $priority = $_POST['priority'];


    $sql = "UPDATE SERVICE_REQUEST

            SET
                Vehicle_ID = ?,
                Request_Date = ?,
                Service_Type = ?,
                Description = ?,
                Status = ?,
                Priority = ?

            WHERE Request_ID = ?";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "isssssi",
        $vehicle_id,
        $request_date,
        $service_type,
        $description,
        $status,
        $priority,
        $id
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

    <title>Edit Service Request</title>

    <link rel="stylesheet" href="../style.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>


<div class="sidebar">

    <div class="logo">

        <h2>AutoCare</h2>

        <p>Vehicle Service Management</p>

    </div>

    <a href="../index.php">
        Dashboard
    </a>

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

            <h1>Edit Service Request</h1>

            <p>Update service request details</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <!-- Vehicle -->

            <div class="form-group">

                <label>Vehicle</label>

                <select name="vehicle_id" required>


                    <?php while ($vehicle = $vehicles->fetch_assoc()) { ?>


                    <option
                        value="<?php echo $vehicle['Vehicle_ID']; ?>"

                        <?php

                        if (
                            $vehicle['Vehicle_ID']
                            == $request['Vehicle_ID']
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

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


            <!-- Request Date -->

            <div class="form-group">

                <label>Request Date</label>

                <input
                    type="date"
                    name="request_date"
                    value="<?php echo $request['Request_Date']; ?>"
                    required
                >

            </div>


            <!-- Service Type -->

            <div class="form-group">

                <label>Service Type</label>

                <select name="service_type" required>

                    <?php

                    $service_types = [
                        "General Service",
                        "Repair",
                        "Oil Change",
                        "Brake Service",
                        "Engine Repair",
                        "AC Service",
                        "Other"
                    ];


                    foreach ($service_types as $type) {

                        $selected =
                            ($type == $request['Service_Type'])
                            ? "selected"
                            : "";


                        echo "<option value=\"" .
                             htmlspecialchars($type) .
                             "\" $selected>" .
                             htmlspecialchars($type) .
                             "</option>";

                    }

                    ?>

                </select>

            </div>


            <!-- Description -->

            <div class="form-group">

                <label>Description</label>

                <textarea
                    name="description"
                    rows="4"
                ><?php echo htmlspecialchars($request['Description']); ?></textarea>

            </div>


            <!-- Status -->

            <div class="form-group">

                <label>Status</label>

                <select name="status" required>

                    <?php

                    $statuses = [
                        "Pending",
                        "In Progress",
                        "Completed"
                    ];


                    foreach ($statuses as $status) {

                        $selected =
                            ($status == $request['Status'])
                            ? "selected"
                            : "";


                        echo "<option value=\"$status\" $selected>
                                $status
                              </option>";

                    }

                    ?>

                </select>

            </div>


            <!-- Priority -->

            <div class="form-group">

                <label>Priority</label>

                <select name="priority" required>

                    <?php

                    $priorities = [
                        "Low",
                        "Normal",
                        "High"
                    ];


                    foreach ($priorities as $priority) {

                        $selected =
                            ($priority == $request['Priority'])
                            ? "selected"
                            : "";


                        echo "<option value=\"$priority\" $selected>
                                $priority
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
                Update Request
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