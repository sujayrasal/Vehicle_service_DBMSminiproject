<?php

include "../auth.php";
include "../db.php";


/* Mechanics */

$mechanics = $conn->query(
    "SELECT *
     FROM MECHANIC
     ORDER BY Mechanic_ID DESC"
);


/* Service Assignments */

$sql = "SELECT
            SA.Assignment_ID,
            SA.Assigned_Date,
            SA.Hours_Worked,

            M.Name AS Mechanic_Name,
            M.Specialization,

            S.Service_ID,
            S.Service_Name,

            V.Vehicle_No,

            C.Name AS Customer_Name

        FROM SERVICE_ASSIGNMENT SA

        JOIN MECHANIC M
        ON SA.Mechanic_ID = M.Mechanic_ID

        JOIN SERVICE S
        ON SA.Service_ID = S.Service_ID

        JOIN SERVICE_REQUEST SR
        ON S.Request_ID = SR.Request_ID

        JOIN VEHICLE V
        ON SR.Vehicle_ID = V.Vehicle_ID

        JOIN CUSTOMER C
        ON V.Customer_ID = C.Customer_ID

        ORDER BY SA.Assignment_ID DESC";


$assignments = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Mechanics</title>

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

    <a href="../services/services.php">
        Services
    </a>

    <a href="mechanics.php" class="active">
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


    <!-- Mechanics -->

    <div class="topbar">

        <div>

            <h1>Mechanics</h1>

            <p>Manage mechanics and service assignments</p>

        </div>

        <a href="add_mechanic.php" class="btn">
            + Add Mechanic
        </a>

    </div>


    <div class="content-box">

        <div class="content-header">

            <h2>Mechanic List</h2>

        </div>


        <div class="table-wrapper">

        <table>

            <tr>

                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Specialization</th>
                <th>Experience</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $mechanics->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Mechanic_ID']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Phone']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Email']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Specialization']); ?>
                </td>

                <td>
                    <?php echo $row['Experience']; ?> Years
                </td>

                <td>

                    <a
                        href="edit_mechanic.php?id=<?php echo $row['Mechanic_ID']; ?>"
                        class="btn"
                    >
                        Edit
                    </a>

                    <a
                        href="delete_mechanic.php?id=<?php echo $row['Mechanic_ID']; ?>"
                        class="btn btn-danger"
                        onclick="return confirmDelete(
                            'Are you sure you want to delete this mechanic?'
                        )"
                    >
                        Delete
                    </a>

                </td>

            </tr>

            <?php } ?>

        </table>

        </div>

    </div>


    <br><br>


    <!-- Assignments -->

    <div class="content-box">

        <div class="content-header">

            <h2>Service Assignments</h2>

            <a href="add_assignment.php" class="btn">
                + Assign Mechanic
            </a>

        </div>


        <div class="table-wrapper">

        <table>

            <tr>

                <th>ID</th>
                <th>Service</th>
                <th>Vehicle</th>
                <th>Customer</th>
                <th>Mechanic</th>
                <th>Specialization</th>
                <th>Date</th>
                <th>Hours</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $assignments->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Assignment_ID']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Service_Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Vehicle_No']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Customer_Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Mechanic_Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Specialization']); ?>
                </td>

                <td>
                    <?php echo $row['Assigned_Date']; ?>
                </td>

                <td>
                    <?php echo $row['Hours_Worked']; ?>
                </td>

                <td>

                    <a
                        href="edit_assignment.php?id=<?php echo $row['Assignment_ID']; ?>"
                        class="btn"
                    >
                        Edit
                    </a>

                    <a
                        href="delete_assignment.php?id=<?php echo $row['Assignment_ID']; ?>"
                        class="btn btn-danger"
                        onclick="return confirmDelete(
                            'Are you sure you want to remove this assignment?'
                        )"
                    >
                        Delete
                    </a>

                </td>

            </tr>

            <?php } ?>

        </table>

        </div>

    </div>

</div>

<script src="../script.js"></script>

</body>

</html>