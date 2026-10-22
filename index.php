<?php

include "auth.php";
include "db.php";


/* Total Customers */

$customers = $conn->query(
    "SELECT COUNT(*) AS total
     FROM CUSTOMER"
)->fetch_assoc()['total'];


/* Total Vehicles */

$vehicles = $conn->query(
    "SELECT COUNT(*) AS total
     FROM VEHICLE"
)->fetch_assoc()['total'];


/* Pending Requests */

$pending_requests = $conn->query(
    "SELECT COUNT(*) AS total
     FROM SERVICE_REQUEST
     WHERE Status = 'Pending'"
)->fetch_assoc()['total'];


/* Completed Services */

$completed_services = $conn->query(
    "SELECT COUNT(*) AS total
     FROM SERVICE
     WHERE Status = 'Completed'"
)->fetch_assoc()['total'];


/* Total Revenue */

$revenue = $conn->query(
    "SELECT COALESCE(SUM(Amount), 0) AS total
     FROM PAYMENT
     WHERE Payment_Status = 'Paid'"
)->fetch_assoc()['total'];


/* Pending Payments */

$pending_payments = $conn->query(
    "SELECT COUNT(*) AS total
     FROM PAYMENT
     WHERE Payment_Status = 'Pending'"
)->fetch_assoc()['total'];


/* Low Stock Parts */

$low_stock = $conn->query(
    "SELECT COUNT(*) AS total
     FROM PART
     WHERE Quantity <= Reorder_Level"
)->fetch_assoc()['total'];


/* Recent Service Requests */

$recent_requests = $conn->query(
    "SELECT
        SR.Request_ID,
        SR.Request_Date,
        SR.Service_Type,
        SR.Status,
        SR.Priority,
        V.Vehicle_No,
        C.Name AS Customer_Name

     FROM SERVICE_REQUEST SR

     JOIN VEHICLE V
     ON SR.Vehicle_ID = V.Vehicle_ID

     JOIN CUSTOMER C
     ON V.Customer_ID = C.Customer_ID

     ORDER BY SR.Request_ID DESC

     LIMIT 5"
);


/* Low Stock List */

$low_stock_parts = $conn->query(
    "SELECT
        Part_Name,
        Quantity,
        Reorder_Level

     FROM PART

     WHERE Quantity <= Reorder_Level

     ORDER BY Quantity ASC

     LIMIT 5"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>AutoCare Dashboard</title>

    <link rel="stylesheet" href="style.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>


<!-- Sidebar -->

<div class="sidebar">

    <div class="logo">

        <h2>AutoCare</h2>

        <p>Vehicle Service Management</p>

    </div>

    <a href="index.php" class="active">
        Dashboard
    </a>

    <a href="customers/customers.php">
        Customers
    </a>

    <a href="vehicles/vehicles.php">
        Vehicles
    </a>

    <a href="service_requests/service_requests.php">
        Service Requests
    </a>

    <a href="services/services.php">
        Services
    </a>

    <a href="mechanics/mechanics.php">
        Mechanics
    </a>

    <a href="parts/parts.php">
        Spare Parts
    </a>

    <a href="payments/payments.php">
        Payments
    </a>

    <a href="reports/reports.php">
        Reports
    </a>

    <a href="logout.php" class="logout-link">
        Logout
    </a>

</div>


<!-- Main -->

<div class="main dashboard-main">

    <div class="topbar">

        <div>

            <h1>Dashboard</h1>

            <p>
                Overview of your vehicle service system
            </p>

        </div>

        <a href="service_requests/new_customer_request.php" class="btn">
            + New Request
        </a>

    </div>


    <!-- Dashboard Cards -->

    <div class="cards dashboard-metrics">

        <div class="card">

            <h3>Total Customers</h3>

            <h2>
                <?php echo $customers; ?>
            </h2>

        </div>


        <div class="card">

            <h3>Total Vehicles</h3>

            <h2>
                <?php echo $vehicles; ?>
            </h2>

        </div>


        <div class="card">

            <h3>Pending Requests</h3>

            <h2>
                <?php echo $pending_requests; ?>
            </h2>

        </div>


        <div class="card">

            <h3>Completed Services</h3>

            <h2>
                <?php echo $completed_services; ?>
            </h2>

        </div>


        <div class="card">

            <h3>Total Revenue</h3>

            <h2>
                ₹<?php echo number_format($revenue, 2); ?>
            </h2>

        </div>


        <div class="card">

            <h3>Pending Payments</h3>

            <h2>
                <?php echo $pending_payments; ?>
            </h2>

        </div>


    </div>


    <div class="dashboard-sections">

    <!-- Recent Requests -->

    <section class="content-box dashboard-section dashboard-requests">

        <div class="content-header">

            <h2>Recent Service Requests</h2>

            <a
                href="service_requests/service_requests.php"
                class="btn"
            >
                View All
            </a>

        </div>


        <div class="table-wrapper">

        <table>

            <tr>

                <th>ID</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>Service</th>
                <th>Date</th>
                <th>Status</th>
                <th>Priority</th>

            </tr>


            <?php if ($recent_requests->num_rows === 0) { ?>

            <tr>
                <td colspan="7" class="dashboard-empty">No service requests found.</td>
            </tr>

            <?php } else { ?>

            <?php while ($row = $recent_requests->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Request_ID']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Customer_Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Vehicle_No']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Service_Type']); ?>
                </td>

                <td>
                    <?php echo $row['Request_Date']; ?>
                </td>

                <td>

                    <span class="
                        status-badge
                        status-<?php
                            echo strtolower(
                                str_replace(
                                    ' ',
                                    '-',
                                    $row['Status']
                                )
                            );
                        ?>
                    ">

                        <?php echo htmlspecialchars($row['Status']); ?>

                    </span>

                </td>


                <td>

                    <span class="
                        priority-badge
                        priority-<?php echo strtolower($row['Priority']); ?>
                    ">

                        <?php echo htmlspecialchars($row['Priority']); ?>

                    </span>

                </td>

            </tr>

            <?php } ?>

            <?php } ?>

        </table>

        </div>

    </section>


    <!-- Low Stock -->

    <aside class="content-box dashboard-section dashboard-stock">

        <div class="content-header">

            <h2>Low Stock Alerts</h2>

            <span class="dashboard-stock-count">
                <?php echo (int) $low_stock; ?>
            </span>

            <a
                href="parts/parts.php"
                class="btn"
            >
                Manage Parts
            </a>

        </div>


        <div class="table-wrapper">

        <table>

            <tr>

                <th>Part</th>
                <th>Available Quantity</th>
                <th>Reorder Level</th>
                <th>Status</th>

            </tr>


            <?php if ($low_stock_parts->num_rows === 0) { ?>

            <tr>
                <td colspan="4" class="dashboard-empty">No parts need reordering.</td>
            </tr>

            <?php } else { ?>

            <?php while ($row = $low_stock_parts->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo htmlspecialchars($row['Part_Name']); ?>
                </td>

                <td>
                    <?php echo $row['Quantity']; ?>
                </td>

                <td>
                    <?php echo $row['Reorder_Level']; ?>
                </td>

                <td>

                    <span class="priority-badge priority-high">
                        Reorder Required
                    </span>

                </td>

            </tr>

            <?php } ?>

            <?php } ?>

        </table>

        </div>

    </aside>

    </div>

</div>

<script src="script.js"></script>

</body>

</html>