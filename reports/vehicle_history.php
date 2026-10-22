<?php

include "../auth.php";
include "../db.php";


$vehicles = $conn->query(
    "SELECT
        V.Vehicle_ID,
        V.Vehicle_No,
        V.Brand,
        V.Model,
        C.Name AS Customer_Name

     FROM VEHICLE V

     JOIN CUSTOMER C
     ON V.Customer_ID = C.Customer_ID

     ORDER BY V.Vehicle_No"
);


$history = null;


if (isset($_GET['vehicle_id'])) {

    $vehicle_id = (int) $_GET['vehicle_id'];


    $stmt = $conn->prepare(
        "SELECT
            S.Service_ID,
            S.Service_Date,
            S.Service_Name,
            S.Description,
            S.Labour_Cost,
            S.Status,
            S.Completion_Date,
            SR.Service_Type

         FROM SERVICE S

         JOIN SERVICE_REQUEST SR
         ON S.Request_ID = SR.Request_ID

         WHERE SR.Vehicle_ID = ?

         ORDER BY S.Service_Date DESC"
    );


    $stmt->bind_param("i", $vehicle_id);

    $stmt->execute();

    $history = $stmt->get_result();

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Vehicle Service History</title>
    <link rel="stylesheet" href="../style.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>

<body>

<div class="main" style="margin-left: 0;">

    <div class="topbar">

        <div>

            <h1>Vehicle Service History</h1>

            <p>Search complete service history by vehicle</p>

        </div>

        <a href="reports.php" class="btn btn-secondary">
            Back
        </a>

    </div>


    <div class="form-box">

        <form method="GET">

            <div class="form-group">

                <label>Select Vehicle</label>

                <select name="vehicle_id" required>

                    <option value="">
                        -- Select Vehicle --
                    </option>


                    <?php while ($vehicle = $vehicles->fetch_assoc()) { ?>

                    <option
                        value="<?php echo $vehicle['Vehicle_ID']; ?>"
                    >

                        <?php

                        echo htmlspecialchars(
                            $vehicle['Vehicle_No']
                            . " - "
                            . $vehicle['Brand']
                            . " "
                            . $vehicle['Model']
                            . " - "
                            . $vehicle['Customer_Name']
                        );

                        ?>

                    </option>

                    <?php } ?>

                </select>

            </div>


            <button class="btn">
                View History
            </button>

        </form>

    </div>


    <br><br>


    <?php if ($history !== null) { ?>

    <div class="content-box">

        <h2>Service History</h2>

        <br>

        <table>

            <tr>

                <th>ID</th>
                <th>Date</th>
                <th>Requested Service</th>
                <th>Service Performed</th>
                <th>Labour Cost</th>
                <th>Status</th>
                <th>Completion Date</th>

            </tr>


            <?php while ($row = $history->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Service_ID']; ?>
                </td>

                <td>
                    <?php echo $row['Service_Date']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Service_Type']); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Service_Name']); ?>
                </td>

                <td>
                    ₹<?php echo number_format($row['Labour_Cost'], 2); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Status']); ?>
                </td>

                <td>
                    <?php echo $row['Completion_Date'] ?: "-"; ?>
                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

    <?php } ?>

</div>

<script src="../script.js"></script>

</body>

</html>