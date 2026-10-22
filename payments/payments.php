<?php

include "../auth.php";
include "../db.php";


$sql = "SELECT
            P.Payment_ID,
            P.Payment_Date,
            P.Amount,
            P.Payment_Method,
            P.Payment_Status,

            S.Service_ID,
            S.Service_Name,
            S.Labour_Cost,

            V.Vehicle_No,

            C.Name AS Customer_Name

        FROM PAYMENT P

        JOIN SERVICE S
        ON P.Service_ID = S.Service_ID

        JOIN SERVICE_REQUEST SR
        ON S.Request_ID = SR.Request_ID

        JOIN VEHICLE V
        ON SR.Vehicle_ID = V.Vehicle_ID

        JOIN CUSTOMER C
        ON V.Customer_ID = C.Customer_ID

        ORDER BY P.Payment_ID DESC";


$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Payments</title>

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

    <a href="../parts/parts.php">
        Spare Parts
    </a>

    <a href="payments.php" class="active">
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

            <h1>Payments</h1>

            <p>Manage service payments</p>

        </div>

        <a href="add_payment.php" class="btn">
            + Add Payment
        </a>

    </div>


    <div class="content-box">

        <div class="content-header">

            <h2>Payment List</h2>

        </div>


        <div class="table-wrapper">

        <table>

            <tr>

                <th>ID</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>Service</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Payment_ID']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Customer_Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Vehicle_No']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Service_Name']); ?>
                </td>

                <td>
                    <?php echo $row['Payment_Date']; ?>
                </td>

                <td>
                    ₹<?php echo number_format($row['Amount'], 2); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Payment_Method']); ?>
                </td>

                <td>

                    <?php if ($row['Payment_Status'] == "Paid") { ?>

                        <span class="status-badge status-completed">
                            Paid
                        </span>

                    <?php } else { ?>

                        <span class="status-badge status-pending">
                            Pending
                        </span>

                    <?php } ?>

                </td>

                <td>

                    <a
                        href="edit_payment.php?id=<?php echo $row['Payment_ID']; ?>"
                        class="btn"
                    >
                        Edit
                    </a>

                    <a
                        href="delete_payment.php?id=<?php echo $row['Payment_ID']; ?>"
                        class="btn btn-danger"
                        onclick="return confirmDelete(
                            'Are you sure you want to delete this payment?'
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