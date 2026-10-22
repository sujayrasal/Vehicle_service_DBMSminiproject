<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: mechanics.php");

    exit;
}


$id = (int) $_GET['id'];


/* Current assignment */

$stmt = $conn->prepare(
    "SELECT *
     FROM SERVICE_ASSIGNMENT
     WHERE Assignment_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$assignment =
    $stmt->get_result()->fetch_assoc();


if (!$assignment) {

    die("Assignment not found.");

}


/* Services */

$sql = "SELECT
            S.Service_ID,
            S.Service_Name,
            V.Vehicle_No

        FROM SERVICE S

        JOIN SERVICE_REQUEST SR
        ON S.Request_ID = SR.Request_ID

        JOIN VEHICLE V
        ON SR.Vehicle_ID = V.Vehicle_ID

        ORDER BY S.Service_ID";


$services = $conn->query($sql);


/* Mechanics */

$mechanics = $conn->query(
    "SELECT
        Mechanic_ID,
        Name,
        Specialization

     FROM MECHANIC

     ORDER BY Name"
);


/* Update */

if (isset($_POST['update'])) {

    $service_id = $_POST['service_id'];

    $mechanic_id = $_POST['mechanic_id'];

    $assigned_date = $_POST['assigned_date'];

    $hours_worked = $_POST['hours_worked'];


    $sql = "UPDATE SERVICE_ASSIGNMENT

            SET
                Service_ID = ?,
                Mechanic_ID = ?,
                Assigned_Date = ?,
                Hours_Worked = ?

            WHERE Assignment_ID = ?";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "iisdi",
        $service_id,
        $mechanic_id,
        $assigned_date,
        $hours_worked,
        $id
    );


    if ($stmt->execute()) {

        header("Location: mechanics.php");

        exit;

    }

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Assignment</title>

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

            <h1>Edit Service Assignment</h1>

            <p>Update mechanic assignment details</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <div class="form-group">

                <label>Service</label>

                <select name="service_id" required>


                    <?php while ($service = $services->fetch_assoc()) { ?>

                    <option
                        value="<?php echo $service['Service_ID']; ?>"

                        <?php

                        if (
                            $service['Service_ID']
                            == $assignment['Service_ID']
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

                        <?php

                        echo htmlspecialchars(
                            $service['Service_Name']
                            . " - "
                            . $service['Vehicle_No']
                        );

                        ?>

                    </option>

                    <?php } ?>

                </select>

            </div>


            <div class="form-group">

                <label>Mechanic</label>

                <select name="mechanic_id" required>


                    <?php while ($mechanic = $mechanics->fetch_assoc()) { ?>

                    <option
                        value="<?php echo $mechanic['Mechanic_ID']; ?>"

                        <?php

                        if (
                            $mechanic['Mechanic_ID']
                            == $assignment['Mechanic_ID']
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

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


            <div class="form-group">

                <label>Assigned Date</label>

                <input
                    type="date"
                    name="assigned_date"
                    value="<?php echo $assignment['Assigned_Date']; ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Hours Worked</label>

                <input
                    type="number"
                    name="hours_worked"
                    min="0"
                    step="0.5"
                    value="<?php echo $assignment['Hours_Worked']; ?>"
                    required
                >

            </div>


            <button
                type="submit"
                name="update"
                class="btn"
            >
                Update Assignment
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