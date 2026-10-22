<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: services.php");

    exit;
}


$id = (int) $_GET['id'];


/* Get service */

$stmt = $conn->prepare(
    "SELECT *
     FROM SERVICE
     WHERE Service_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$service = $result->fetch_assoc();


if (!$service) {

    die("Service not found.");

}


/* Get requests */

$sql = "SELECT
            SR.Request_ID,
            SR.Service_Type,

            V.Vehicle_No,

            C.Name AS Customer_Name

        FROM SERVICE_REQUEST SR

        JOIN VEHICLE V
        ON SR.Vehicle_ID = V.Vehicle_ID

        JOIN CUSTOMER C
        ON V.Customer_ID = C.Customer_ID

        ORDER BY SR.Request_ID";


$requests = $conn->query($sql);


/* Update */

if (isset($_POST['update'])) {

    $request_id = $_POST['request_id'];

    $service_date = $_POST['service_date'];

    $service_name = $_POST['service_name'];

    $description = $_POST['description'];

    $labour_cost = $_POST['labour_cost'];

    $status = $_POST['status'];


    $sql = "UPDATE SERVICE

            SET
                Request_ID = ?,
                Service_Date = ?,
                Service_Name = ?,
                Description = ?,
                Labour_Cost = ?,
                Status = ?

            WHERE Service_ID = ?";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "isssdsi",
        $request_id,
        $service_date,
        $service_name,
        $description,
        $labour_cost,
        $status,
        $id
    );


    if ($stmt->execute()) {

        /*
        If Status changes to Completed,
        the MySQL trigger will automatically
        set Completion_Date.
        */

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

    <title>Edit Service</title>

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

            <h1>Edit Service</h1>

            <p>Update service information</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <!-- Request -->

            <div class="form-group">

                <label>Service Request</label>

                <select name="request_id" required>


                    <?php while ($request = $requests->fetch_assoc()) { ?>


                    <option
                        value="<?php echo $request['Request_ID']; ?>"

                        <?php

                        if (
                            $request['Request_ID']
                            == $service['Request_ID']
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        <?php

                        echo htmlspecialchars(
                            "Request "
                            . $request['Request_ID']
                            . " - "
                            . $request['Vehicle_No']
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
                    value="<?php echo $service['Service_Date']; ?>"
                    required
                >

            </div>


            <!-- Service Name -->

            <div class="form-group">

                <label>Service Name</label>

                <input
                    type="text"
                    name="service_name"
                    value="<?php echo htmlspecialchars($service['Service_Name']); ?>"
                    required
                >

            </div>


            <!-- Description -->

            <div class="form-group">

                <label>Description</label>

                <textarea
                    name="description"
                    rows="4"
                ><?php echo htmlspecialchars($service['Description']); ?></textarea>

            </div>


            <!-- Labour -->

            <div class="form-group">

                <label>Labour Cost (₹)</label>

                <input
                    type="number"
                    name="labour_cost"
                    min="0"
                    step="0.01"
                    value="<?php echo $service['Labour_Cost']; ?>"
                    required
                >

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
                            ($status == $service['Status'])
                            ? "selected"
                            : "";


                        echo "<option value=\"$status\" $selected>
                                $status
                              </option>";

                    }

                    ?>

                </select>

            </div>


            <?php if ($service['Completion_Date']) { ?>

                <div class="form-group">

                    <label>Completion Date</label>

                    <input
                        type="text"
                        value="<?php echo $service['Completion_Date']; ?>"
                        disabled
                    >

                </div>

            <?php } ?>


            <button
                type="submit"
                name="update"
                class="btn"
            >
                Update Service
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