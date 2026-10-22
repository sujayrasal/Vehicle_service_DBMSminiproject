<?php

include "../auth.php";
include "../db.php";


$sql = "SELECT
            S.Service_ID,
            S.Service_Name,
            S.Labour_Cost,

            Calculate_Service_Cost(S.Service_ID)
            AS Total_Cost,

            V.Vehicle_No,

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

    <title>Service Cost Report</title>
    <link rel="stylesheet" href="../style.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

<div class="main" style="margin-left: 0;">

    <div class="topbar">

        <div>
            <h1>Service Cost Report</h1>
            <p>Calculated using database function</p>
        </div>

        <a href="reports.php" class="btn btn-secondary">
            Back
        </a>

    </div>


    <div class="content-box">

        <table>

            <tr>

                <th>Service</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>Labour Cost</th>
                <th>Total Service Cost</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo htmlspecialchars($row['Service_Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Customer_Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Vehicle_No']); ?>
                </td>

                <td>
                    ₹<?php echo number_format($row['Labour_Cost'], 2); ?>
                </td>

                <td>
                    <strong>
                        ₹<?php echo number_format($row['Total_Cost'], 2); ?>
                    </strong>
                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

<script src="../script.js"></script>

</body>

</html>