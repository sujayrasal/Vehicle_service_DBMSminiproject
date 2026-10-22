<?php

include "../auth.php";
include "../db.php";

$deleteSuccess = $_SESSION['service_delete_success'] ?? null;
$deleteError = $_SESSION['service_delete_error'] ?? null;
unset($_SESSION['service_delete_success'], $_SESSION['service_delete_error']);

$sql = "SELECT
            S.Service_ID,
            S.Service_Date,
            S.Service_Name,
            S.Description,
            S.Labour_Cost,
            S.Status,
            S.Completion_Date,

            SR.Service_Type,
            SR.Request_ID,

            V.Vehicle_No,
            V.Brand,
            V.Model,

            C.Name AS Customer_Name

        FROM SERVICE S

        JOIN SERVICE_REQUEST SR
        ON S.Request_ID = SR.Request_ID

        JOIN VEHICLE V
        ON SR.Vehicle_ID = V.Vehicle_ID

        JOIN CUSTOMER C
        ON V.Customer_ID = C.Customer_ID

        ORDER BY S.Service_ID DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Services</title>

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

    <?php if ($deleteSuccess) { ?>
        <div class="alert alert-success" role="status">
            <?php echo htmlspecialchars($deleteSuccess, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php } ?>

    <?php if ($deleteError) { ?>
        <div class="alert alert-error" role="alert">
            <?php echo htmlspecialchars($deleteError, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php } ?>

    <div class="topbar">

        <div>

            <h1>Services</h1>

            <p>Manage vehicle service operations</p>

        </div>

        <a href="add_service.php" class="btn">
            + Add Service
        </a>

    </div>


    <div class="content-box">

        <div class="content-header">

            <h2>Service List</h2>

        </div>


        <div class="table-wrapper">

        <table>

            <tr>

                <th>ID</th>
                <th>Vehicle</th>
                <th>Customer</th>
                <th>Requested Service</th>
                <th>Service</th>
                <th>Date</th>
                <th>Labour Cost</th>
                <th>Status</th>
                <th>Completed</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Service_ID']; ?>
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

                    <?php echo htmlspecialchars($row['Service_Type']); ?>

                </td>


                <td>

                    <?php echo htmlspecialchars($row['Service_Name']); ?>

                </td>


                <td>

                    <?php echo $row['Service_Date']; ?>

                </td>


                <td>

                    ₹<?php echo number_format($row['Labour_Cost'], 2); ?>

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

                    <?php

                    if ($row['Completion_Date']) {

                        echo $row['Completion_Date'];

                    } else {

                        echo "-";

                    }

                    ?>

                </td>


                <td>

                    <a
                        href="edit_service.php?id=<?php echo $row['Service_ID']; ?>"
                        class="btn"
                    >
                        Edit
                    </a>


                    <a
                        href="delete_service.php?id=<?php echo $row['Service_ID']; ?>"
                        class="btn btn-danger"
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