<?php

include "../auth.php";
include "../db.php";


/* Services */

$sql = "SELECT
            S.Service_ID,
            S.Service_Name,

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


$services = $conn->query($sql);


/* Parts */

$parts = $conn->query(
    "SELECT
        Part_ID,
        Part_Name,
        Quantity,
        Unit_Price

     FROM PART

     WHERE Quantity > 0

     ORDER BY Part_Name"
);


/* Add */

if (isset($_POST['submit'])) {

    $serviceIdInput = $_POST['service_id'] ?? null;
    $partIdInput = $_POST['part_id'] ?? null;
    $quantityInput = $_POST['quantity'] ?? null;

    $service_id = is_string($serviceIdInput)
        ? filter_var($serviceIdInput, FILTER_VALIDATE_INT)
        : false;
    $part_id = is_string($partIdInput)
        ? filter_var($partIdInput, FILTER_VALIDATE_INT)
        : false;
    $quantity = is_string($quantityInput) && ctype_digit($quantityInput)
        ? (int) $quantityInput
        : 0;

    if (!$service_id || $service_id < 1 || !$part_id || $part_id < 1 || $quantity < 1 || $quantity > 2147483647) {
        echo "<script>alert('Select a service and part, and enter a positive whole quantity.'); window.history.back();</script>";
        exit;
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare(
            "SELECT Quantity, Unit_Price
             FROM PART
             WHERE Part_ID = ?
             FOR UPDATE"
        );
        $stmt->bind_param("i", $part_id);
        $stmt->execute();
        $part = $stmt->get_result()->fetch_assoc();

        if (!$part) {
            $conn->rollback();
            echo "<script>alert('Part not found.'); window.history.back();</script>";
            exit;
        }

        if ($quantity > (int) $part['Quantity']) {
            $conn->rollback();
            echo "<script>alert('Not enough stock available.'); window.history.back();</script>";
            exit;
        }

        $unit_price = $part['Unit_Price'];
        $stmt = $conn->prepare(
            "INSERT INTO SERVICE_PART (Service_ID, Part_ID, Quantity, Unit_Price)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("iiid", $service_id, $part_id, $quantity, $unit_price);
        $stmt->execute();
        $conn->commit();

        header("Location: parts.php");
        exit;
    } catch (Throwable $error) {
        $conn->rollback();
        http_response_code(500);
        exit("Unable to add this part to the service.");
    }

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Part to Service</title>

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
    <a href="parts.php" class="active">Spare Parts</a>
    <a href="../payments/payments.php">Payments</a>
    <a href="../reports/reports.php">Reports</a>
    <a href="../logout.php" class="logout-link">Logout</a>

</div>


<div class="main">

    <div class="topbar">

        <div>

            <h1>Add Part to Service</h1>

            <p>Use a spare part for a vehicle service</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <div class="form-group">

                <label>Select Service</label>

                <select name="service_id" required>

                    <option value="">
                        -- Select Service --
                    </option>


                    <?php while ($service = $services->fetch_assoc()) { ?>

                    <option value="<?php echo $service['Service_ID']; ?>">

                        <?php

                        echo htmlspecialchars(
                            $service['Service_Name']
                            . " - "
                            . $service['Vehicle_No']
                            . " - "
                            . $service['Customer_Name']
                        );

                        ?>

                    </option>

                    <?php } ?>

                </select>

            </div>


            <div class="form-group">

                <label>Select Part</label>

                <select name="part_id" required>

                    <option value="">
                        -- Select Part --
                    </option>


                    <?php while ($part = $parts->fetch_assoc()) { ?>

                    <option value="<?php echo $part['Part_ID']; ?>">

                        <?php

                        echo htmlspecialchars(
                            $part['Part_Name']
                            . " | Stock: "
                            . $part['Quantity']
                            . " | ₹"
                            . $part['Unit_Price']
                        );

                        ?>

                    </option>

                    <?php } ?>

                </select>

            </div>


            <div class="form-group">

                <label>Quantity</label>

                <input
                    type="number"
                    name="quantity"
                    min="1"
                    value="1"
                    required
                >

            </div>


            <button
                type="submit"
                name="submit"
                class="btn"
            >
                Add Part to Service
            </button>


            <a
                href="parts.php"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </form>

    </div>

</div>

<script src="../script.js"></script>

</body>

</html>