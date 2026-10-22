<?php

include "../auth.php";
include "../db.php";


$sql = "SELECT
            M.Mechanic_ID,
            M.Name,
            M.Specialization,

            COUNT(SA.Assignment_ID)
            AS Total_Assignments,

            COALESCE(SUM(SA.Hours_Worked), 0)
            AS Total_Hours

        FROM MECHANIC M

        LEFT JOIN SERVICE_ASSIGNMENT SA
        ON M.Mechanic_ID = SA.Mechanic_ID

        GROUP BY
            M.Mechanic_ID,
            M.Name,
            M.Specialization

        ORDER BY Total_Hours DESC";


$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Mechanic Workload</title>
    <link rel="stylesheet" href="../style.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

<div class="main" style="margin-left: 0;">

    <div class="topbar">

        <div>
            <h1>Mechanic Workload</h1>
            <p>Service assignment and working-hour report</p>
        </div>

        <a href="reports.php" class="btn btn-secondary">
            Back
        </a>

    </div>


    <div class="content-box">

        <table>

            <tr>

                <th>Mechanic</th>
                <th>Specialization</th>
                <th>Total Assignments</th>
                <th>Total Hours</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo htmlspecialchars($row['Name']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Specialization']); ?>
                </td>

                <td>
                    <?php echo $row['Total_Assignments']; ?>
                </td>

                <td>
                    <?php echo $row['Total_Hours']; ?>
                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

<script src="../script.js"></script>

</body>

</html>