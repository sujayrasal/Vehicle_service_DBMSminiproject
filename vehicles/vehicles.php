<?php

include "../auth.php";
include "../db.php";

$sql = "SELECT V.Vehicle_ID,
               V.Vehicle_No,
               V.Brand,
               V.Model,
               V.Manufacturing_Year,
               V.Vehicle_Type,
               C.Name AS Customer_Name
        FROM VEHICLE V
        JOIN CUSTOMER C
        ON V.Customer_ID = C.Customer_ID
        ORDER BY V.Vehicle_ID DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Vehicles</title>
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

            <h1>Vehicles</h1>

            <p>Manage registered vehicles</p>

        </div>

        <a href="add_vehicle.php" class="btn">
            + Add Vehicle
        </a>

    </div>


    <div class="content-box">

        <div class="content-header">

            <h2>Vehicle List</h2>

        </div>


        <div class="table-tools">

            <input
                type="text"
                id="vehicleSearch"
                placeholder="Search vehicles..."
                onkeyup="searchTable(
                    'vehicleSearch',
                    'vehicleTable'
                )"
            >

        </div>


        <table id="vehicleTable">

            <tr>

                <th>ID</th>
                <th>Vehicle No.</th>
                <th>Customer</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Year</th>
                <th>Type</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Vehicle_ID']; ?>
                </td>

                <td>
                    <span class="vehicle-number">

                        <?php echo htmlspecialchars($row['Vehicle_No']); ?>

                    </span>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Customer_Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Brand']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Model']); ?>
                </td>

                <td>
                    <?php echo $row['Manufacturing_Year']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Vehicle_Type']); ?>
                </td>

                <td>

                    <a
                        href="edit_vehicle.php?id=<?php echo $row['Vehicle_ID']; ?>"
                        class="btn"
                    >
                        Edit
                    </a>

                    <a
                        href="delete_vehicle.php?id=<?php echo $row['Vehicle_ID']; ?>"
                        class="btn btn-danger"
                        onclick="return confirmDelete(
                            'Are you sure you want to delete this vehicle?'
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

<script src="../script.js"></script>

</body>

</html>