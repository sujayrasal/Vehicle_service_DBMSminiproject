<?php

include "../auth.php";
include "../db.php";

$sql = "SELECT
            SR.Request_ID,
            SR.Request_Date,
            SR.Service_Type,
            SR.Description,
            SR.Status,
            SR.Priority,
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

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Service Requests</title>

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

            <h1>Service Requests</h1>

            <p>Manage vehicle service requests</p>

        </div>

        <a href="add_request.php" class="btn">
            + Add Request
        </a>

    </div>


    <div class="content-box">

        <div class="content-header">

            <h2>Service Request List</h2>

        </div>


        <div class="table-tools">

            <input
                type="text"
                id="requestSearch"
                placeholder="Search requests..."
                onkeyup="searchTable(
                    'requestSearch',
                    'requestTable'
                )"
            >

            <select
                id="statusFilter"
                onchange="filterByStatus(
                    'statusFilter',
                    'requestTable',
                    5
                )"
            >

                <option value="">
                    All Status
                </option>

                <option value="Pending">
                    Pending
                </option>

                <option value="In Progress">
                    In Progress
                </option>

                <option value="Completed">
                    Completed
                </option>

            </select>

            <select
                id="priorityFilter"
                onchange="filterByPriority(
                    'priorityFilter',
                    'requestTable',
                    6
                )"
            >

                <option value="">
                    All Priority
                </option>

                <option value="Low">
                    Low
                </option>

                <option value="Normal">
                    Normal
                </option>

                <option value="High">
                    High
                </option>

            </select>

        </div>


        <table id="requestTable">

            <tr>

                <th>ID</th>
                <th>Vehicle</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Service Type</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Request_ID']; ?>
                </td>

                <td>

                    <strong>
                        <?php echo htmlspecialchars($row['Vehicle_No']); ?>
                    </strong>

                    <br>

                    <small>
                        <?php
                        echo htmlspecialchars(
                            $row['Brand'] . " " . $row['Model']
                        );
                        ?>
                    </small>

                </td>

                <td>
                    <?php echo htmlspecialchars($row['Customer_Name']); ?>
                </td>

                <td>
                    <?php echo $row['Request_Date']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Service_Type']); ?>
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


                <td>

                    <a
                        href="edit_request.php?id=<?php echo $row['Request_ID']; ?>"
                        class="btn"
                    >
                        Edit
                    </a>

                    <a
                        href="delete_request.php?id=<?php echo $row['Request_ID']; ?>"
                        class="btn btn-danger"
                        onclick="return confirmDelete(
                            'Are you sure you want to delete this service request?'
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