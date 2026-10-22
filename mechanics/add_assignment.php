<?php

include "../auth.php";
include "../db.php";


/* Get services */

$sql = "SELECT
            S.Service_ID,
            S.Service_Name,
            S.Status,

            V.Vehicle_No,

            C.Name AS Customer_Name

        FROM SERVICE S

        JOIN SERVICE_REQUEST SR
        ON S.Request_ID = SR.Request_ID

        JOIN VEHICLE V
        ON SR.Vehicle_ID = V.Vehicle_ID

        JOIN CUSTOMER C
        ON V.Customer_ID = C.Customer_ID

        ORDER BY S.Service_ID DESC";


$services = $conn->query($sql);


/* Get mechanics */

$mechanics = $conn->query(
    "SELECT
        Mechanic_ID,
        Name,
        Specialization

     FROM MECHANIC

     ORDER BY Name"
);


/* Insert assignment */

if (isset($_POST['submit'])) {

    $service_id = $_POST['service_id'];

    $mechanic_id = $_POST['mechanic_id'];

    $assigned_date = $_POST['assigned_date'];

    $hours_worked = $_POST['hours_worked'];


    $sql = "INSERT INTO SERVICE_ASSIGNMENT
            (
                Service_ID,
                Mechanic_ID,
                Assigned_Date,
                Hours_Worked
            )

            VALUES (?, ?, ?, ?)";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "iisd",
        $service_id,
        $mechanic_id,
        $assigned_date,
        $hours_worked
    );


    if ($stmt->execute()) {

        header("Location: mechanics.php");

        exit;

    } else {

        echo "Error: " . $stmt->error;

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Assign Mechanic</title>

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

    <a href="../service_requests/service_requests.php">
        Service Requests
    </a>

    <a href="../services/services.php">Services</a>

    <a href="mechanics.php" class="active">
        Mechanics
    </a>

    <a href="../parts/parts.php">Spare Parts</a>

    <a href="../payments/payments.php">Payments</a>
    <a href="../reports/reports.php">Reports</a>
    <a href="../logout.php" class="logout-link">Logout</a>

</div>


<div class="main">


    <div class="topbar">

        <div>

            <h1>Assign Mechanic</h1>

            <p>Assign a mechanic to a vehicle service</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <!-- Service -->

            <div class="form-group">

                <label>Select Service</label>

                <select name="service_id" required>

                    <option value="">
                        -- Select Service --
                    </option>


                    <?php while ($service = $services->fetch_assoc()) { ?>

                    <option value="<?php echo $service['Service_ID']; ?>">

                        <?php

                        echo htmlspecialchars(
                            $service['Service_Name']
                            . " - "
                            . $service['Vehicle_No']
                            . " - "
                            . $service['Customer_Name']
                            . " - "
                            . $service['Status']
                        );

                        ?>

                    </option>

                    <?php } ?>

                </select>

            </div>


            <!-- Mechanic -->

            <div class="form-group">

                <label>Select Mechanic</label>

                <select name="mechanic_id" required>

                    <option value="">
                        -- Select Mechanic --
                    </option>


                    <?php while ($mechanic = $mechanics->fetch_assoc()) { ?>

                    <option value="<?php echo $mechanic['Mechanic_ID']; ?>">

                        <?php

                        echo htmlspecialchars(
                            $mechanic['Name']
                            . " - "
                            . $mechanic['Specialization']
                        );

                        ?>

                    </option>

                    <?php } ?>

                </select>

            </div>


            <!-- Date -->

            <div class="form-group">

                <label>Assigned Date</label>

                <input
                    type="date"
                    name="assigned_date"
                    value="<?php echo date('Y-m-d'); ?>"
                    required
                >

            </div>


            <!-- Hours -->

            <div class="form-group">

                <label>Hours Worked</label>

                <input
                    type="number"
                    name="hours_worked"
                    min="0"
                    step="0.5"
                    value="0"
                    required
                >

            </div>


            <button
                type="submit"
                name="submit"
                class="btn"
            >
                Assign Mechanic
            </button>


            <a
                href="mechanics.php"
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