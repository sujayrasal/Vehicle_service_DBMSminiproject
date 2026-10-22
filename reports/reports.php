<?php

include "../auth.php";
include "../db.php";

?>

<!DOCTYPE html>
<html>

<head>

    <title>Reports</title>
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
    <a href="../service_requests/service_requests.php">Service Requests</a>
    <a href="../services/services.php">Services</a>
    <a href="../mechanics/mechanics.php">Mechanics</a>
    <a href="../parts/parts.php">Spare Parts</a>
    <a href="../payments/payments.php">Payments</a>

    <a href="reports.php" class="active">
        Reports
    </a>

    <a href="../logout.php" class="logout-link">
        Logout
    </a>

</div>


<div class="main">

    <div class="topbar">

        <div>
            <h1>Reports</h1>
            <p>View system reports and analysis</p>
        </div>

    </div>


    <div class="cards">

        <div class="card">

            <h3>Vehicle Service History</h3>

            <p>
                View complete service records for a vehicle.
            </p>

            <br>

            <a href="vehicle_history.php" class="btn">
                View Report
            </a>

        </div>


        <div class="card">

            <h3>Service Cost</h3>

            <p>
                Calculate labour and spare-parts cost.
            </p>

            <br>

            <a href="service_cost.php" class="btn">
                View Report
            </a>

        </div>


        <div class="card">

            <h3>Mechanic Workload</h3>

            <p>
                View total services and working hours.
            </p>

            <br>

            <a href="mechanic_workload.php" class="btn">
                View Report
            </a>

        </div>


        <div class="card">

            <h3>Revenue Report</h3>

            <p>
                View payment and revenue information.
            </p>

            <br>

            <a href="revenue_report.php" class="btn">
                View Report
            </a>

        </div>

    </div>

</div>

<script src="../script.js"></script>

</body>

</html>