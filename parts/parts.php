<?php

include "../auth.php";
include "../db.php";


/* Spare parts */

$parts = $conn->query(
    "SELECT *
     FROM PART
     ORDER BY Part_ID DESC"
);


/* Service parts */

$sql = "SELECT
            SP.ServicePart_ID,
            SP.Quantity,
            SP.Unit_Price,

            P.Part_Name,
            P.Category,

            S.Service_Name,

            V.Vehicle_No,

            C.Name AS Customer_Name

        FROM SERVICE_PART SP

        JOIN PART P
        ON SP.Part_ID = P.Part_ID

        JOIN SERVICE S
        ON SP.Service_ID = S.Service_ID

        JOIN SERVICE_REQUEST SR
        ON S.Request_ID = SR.Request_ID

        JOIN VEHICLE V
        ON SR.Vehicle_ID = V.Vehicle_ID

        JOIN CUSTOMER C
        ON V.Customer_ID = C.Customer_ID

        ORDER BY SP.ServicePart_ID DESC";


$service_parts = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Spare Parts</title>

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

    <a href="../mechanics/mechanics.php">
        Mechanics
    </a>

    <a href="parts.php" class="active">
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

            <h1>Spare Parts</h1>

            <p>Manage inventory and service parts</p>

        </div>

        <a href="add_part.php" class="btn">
            + Add Part
        </a>

    </div>


    <div class="content-box">

        <div class="content-header">

            <h2>Parts Inventory</h2>

        </div>


        <div class="table-wrapper">

        <table>

            <tr>

                <th>ID</th>
                <th>Part Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Reorder Level</th>
                <th>Status</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $parts->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Part_ID']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Part_Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Category']); ?>
                </td>

                <td>
                    <?php echo $row['Quantity']; ?>
                </td>

                <td>
                    ₹<?php echo number_format($row['Unit_Price'], 2); ?>
                </td>

                <td>
                    <?php echo $row['Reorder_Level']; ?>
                </td>

                <td>

                    <?php if ($row['Quantity'] <= $row['Reorder_Level']) { ?>

                        <span class="priority-badge priority-high">
                            Low Stock
                        </span>

                    <?php } else { ?>

                        <span class="status-badge status-completed">
                            Available
                        </span>

                    <?php } ?>

                </td>

                <td>

                    <a
                        href="edit_part.php?id=<?php echo $row['Part_ID']; ?>"
                        class="btn"
                    >
                        Edit
                    </a>

                    <a
                        href="delete_part.php?id=<?php echo $row['Part_ID']; ?>"
                        class="btn btn-danger"
                        onclick="return confirmDelete(
                            'Are you sure you want to delete this part?'
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


    <div class="content-box">

        <div class="content-header">

            <h2>Parts Used in Services</h2>

            <a href="add_service_part.php" class="btn">
                + Add Part to Service
            </a>

        </div>


        <div class="table-wrapper">

        <table>

            <tr>

                <th>ID</th>
                <th>Service</th>
                <th>Vehicle</th>
                <th>Customer</th>
                <th>Part</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $service_parts->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['ServicePart_ID']; ?>
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
                    <?php echo htmlspecialchars($row['Part_Name']); ?>
                </td>

                <td>
                    <?php echo $row['Quantity']; ?>
                </td>

                <td>
                    ₹<?php echo number_format($row['Unit_Price'], 2); ?>
                </td>

                <td>
                    ₹<?php
                    echo number_format(
                        $row['Quantity'] * $row['Unit_Price'],
                        2
                    );
                    ?>
                </td>

                <td>

                    <a
                        href="edit_service_part.php?id=<?php echo $row['ServicePart_ID']; ?>"
                        class="btn"
                    >
                        Edit
                    </a>

                    <a
                        href="delete_service_part.php?id=<?php echo $row['ServicePart_ID']; ?>"
                        class="btn btn-danger"
                        onclick="return confirmDelete(
                            'Are you sure you want to remove this part from service?'
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