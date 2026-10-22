<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: payments.php");

    exit;
}


$id = (int) $_GET['id'];


/* Existing payment */

$stmt = $conn->prepare(
    "SELECT *
     FROM PAYMENT
     WHERE Payment_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$payment =
    $stmt->get_result()->fetch_assoc();


if (!$payment) {

    die("Payment not found.");

}


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

        ORDER BY S.Service_ID";


$services = $conn->query($sql);


/* Update */

if (isset($_POST['update'])) {

    $service_id = $_POST['service_id'];

    $payment_date = $_POST['payment_date'];

    $amount = $_POST['amount'];

    $payment_method = $_POST['payment_method'];

    $payment_status = $_POST['payment_status'];


    $sql = "UPDATE PAYMENT

            SET
                Service_ID = ?,
                Payment_Date = ?,
                Amount = ?,
                Payment_Method = ?,
                Payment_Status = ?

            WHERE Payment_ID = ?";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "isdssi",
        $service_id,
        $payment_date,
        $amount,
        $payment_method,
        $payment_status,
        $id
    );


    if ($stmt->execute()) {

        header("Location: payments.php");

        exit;

    }

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Payment</title>

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
    <a href="../parts/parts.php">Spare Parts</a>
    <a href="payments.php" class="active">Payments</a>
    <a href="../reports/reports.php">Reports</a>
    <a href="../logout.php" class="logout-link">Logout</a>

</div>


<div class="main">

    <div class="topbar">

        <div>

            <h1>Edit Payment</h1>

            <p>Update payment information</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <div class="form-group">

                <label>Service</label>

                <select name="service_id" required>

                    <?php while ($service = $services->fetch_assoc()) { ?>

                    <option
                        value="<?php echo $service['Service_ID']; ?>"

                        <?php

                        if (
                            $service['Service_ID']
                            == $payment['Service_ID']
                        ) {

                            echo "selected";

                        }

                        ?>
                    >

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

                <label>Payment Date</label>

                <input
                    type="date"
                    name="payment_date"
                    value="<?php echo $payment['Payment_Date']; ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Amount (₹)</label>

                <input
                    type="number"
                    name="amount"
                    min="0"
                    step="0.01"
                    value="<?php echo $payment['Amount']; ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Payment Method</label>

                <select name="payment_method" required>

                    <?php

                    $methods = [
                        "Cash",
                        "UPI",
                        "Card",
                        "Bank Transfer"
                    ];


                    foreach ($methods as $method) {

                        $selected =
                            ($method == $payment['Payment_Method'])
                            ? "selected"
                            : "";


                        echo "<option value=\"$method\" $selected>
                                $method
                              </option>";

                    }

                    ?>

                </select>

            </div>


            <div class="form-group">

                <label>Payment Status</label>

                <select name="payment_status" required>

                    <?php

                    $statuses = [
                        "Pending",
                        "Paid"
                    ];


                    foreach ($statuses as $status) {

                        $selected =
                            ($status == $payment['Payment_Status'])
                            ? "selected"
                            : "";


                        echo "<option value=\"$status\" $selected>
                                $status
                              </option>";

                    }

                    ?>

                </select>

            </div>


            <button
                type="submit"
                name="update"
                class="btn"
            >
                Update Payment
            </button>


            <a
                href="payments.php"
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