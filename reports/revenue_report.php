<?php

include "../auth.php";
include "../db.php";


$summary = $conn->query(
    "SELECT
        COALESCE(SUM(
            CASE
                WHEN Payment_Status = 'Paid'
                THEN Amount
                ELSE 0
            END
        ), 0) AS Paid_Revenue,

        COALESCE(SUM(
            CASE
                WHEN Payment_Status = 'Pending'
                THEN Amount
                ELSE 0
            END
        ), 0) AS Pending_Amount

     FROM PAYMENT"
)->fetch_assoc();


$payments = $conn->query(
    "SELECT
        P.Payment_Date,
        P.Amount,
        P.Payment_Method,
        P.Payment_Status,

        S.Service_Name,
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

     ORDER BY P.Payment_Date DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Revenue Report</title>

    <link rel="stylesheet" href="../style.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

<div class="main" style="margin-left: 0;">


    <div class="topbar">

        <div>
            <h1>Revenue Report</h1>
            <p>Paid and pending payment analysis</p>
        </div>

        <a href="reports.php" class="btn btn-secondary">
            Back
        </a>

    </div>


    <div class="cards">

        <div class="card">

            <h3>Paid Revenue</h3>

            <h2>
                ₹<?php echo number_format($summary['Paid_Revenue'], 2); ?>
            </h2>

        </div>


        <div class="card">

            <h3>Pending Amount</h3>

            <h2>
                ₹<?php echo number_format($summary['Pending_Amount'], 2); ?>
            </h2>

        </div>

    </div>


    <div class="content-box">

        <table>

            <tr>

                <th>Date</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>Service</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>

            </tr>


            <?php while ($row = $payments->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Payment_Date']; ?>
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
                    ₹<?php echo number_format($row['Amount'], 2); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Payment_Method']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Payment_Status']); ?>
                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

<script src="../script.js"></script>

</body>

</html>