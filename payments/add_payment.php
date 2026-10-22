<?php

include "../auth.php";
include "../db.php";


/* Get services */

$sql = "SELECT
            S.Service_ID,
            S.Service_Name,

            V.Vehicle_No,

            C.Name AS Customer_Name,

            Calculate_Service_Cost(S.Service_ID) AS Total_Cost

        FROM SERVICE S

        JOIN SERVICE_REQUEST SR
        ON S.Request_ID = SR.Request_ID

        JOIN VEHICLE V
        ON SR.Vehicle_ID = V.Vehicle_ID

        JOIN CUSTOMER C
        ON V.Customer_ID = C.Customer_ID

        ORDER BY S.Service_ID DESC";


$services = $conn->query($sql);


/* Add payment */

if (isset($_POST['submit'])) {

    $service_id = $_POST['service_id'];

    $payment_date = $_POST['payment_date'];

    $amount = $_POST['amount'];

    $payment_method = $_POST['payment_method'];

    $payment_status = $_POST['payment_status'];


    $sql = "INSERT INTO PAYMENT
            (
                Service_ID,
                Payment_Date,
                Amount,
                Payment_Method,
                Payment_Status
            )

            VALUES (?, ?, ?, ?, ?)";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "isdss",
        $service_id,
        $payment_date,
        $amount,
        $payment_method,
        $payment_status
    );


    if ($stmt->execute()) {

        header("Location: payments.php");

        exit;

    } else {

        echo "Error: " . $stmt->error;

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Payment</title>

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

            <h1>Add Payment</h1>

            <p>Create a payment record for a service</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <div class="form-group">

                <label>Select Service</label>

                <select name="service_id" id="service_id" required>

                    <option value="">
                        -- Select Service --
                    </option>


                    <?php while ($service = $services->fetch_assoc()) { ?>

                    <option
                        value="<?php echo $service['Service_ID']; ?>"
                        data-cost="<?php echo $service['Total_Cost']; ?>"
                    >

                        <?php

                        echo htmlspecialchars(
                            $service['Service_Name']
                            . " - "
                            . $service['Vehicle_No']
                            . " - "
                            . $service['Customer_Name']
                            . " - ₹"
                            . number_format($service['Total_Cost'], 2)
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
                    value="<?php echo date('Y-m-d'); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Amount (₹)</label>

                <input
                    type="number"
                    name="amount"
                    id="amount"
                    min="0"
                    step="0.01"
                    required
                >

            </div>


            <div class="form-group">

                <label>Payment Method</label>

                <select name="payment_method" required>

                    <option value="">
                        -- Select Method --
                    </option>

                    <option value="Cash">
                        Cash
                    </option>

                    <option value="UPI">
                        UPI
                    </option>

                    <option value="Card">
                        Card
                    </option>

                    <option value="Bank Transfer">
                        Bank Transfer
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label>Payment Status</label>

                <select name="payment_status" required>

                    <option value="Pending">
                        Pending
                    </option>

                    <option value="Paid">
                        Paid
                    </option>

                </select>

            </div>


            <button
                type="submit"
                name="submit"
                class="btn"
            >
                Add Payment
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


<script>

const serviceSelect =
    document.getElementById("service_id");

const amountInput =
    document.getElementById("amount");


serviceSelect.addEventListener("change", function() {

    const selected =
        this.options[this.selectedIndex];

    const cost =
        selected.getAttribute("data-cost");


    if (cost) {

        amountInput.value = cost;

    }

});

</script>


<script src="../script.js"></script>

</body>

</html>