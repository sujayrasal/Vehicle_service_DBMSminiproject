<?php

include "../auth.php";
include "../db.php";

$deleteError = $_SESSION['customer_delete_error'] ?? null;
unset($_SESSION['customer_delete_error']);

$result = $conn->query("SELECT * FROM CUSTOMER ORDER BY Customer_ID DESC");

?>

<!DOCTYPE html>
<html>

<head>

    <title>Customers</title>

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

    <a href="customers.php" class="active">
        Customers
    </a>

    <a href="../vehicles/vehicles.php">Vehicles</a>

    <a href="../service_requests/service_requests.php">
        Service Requests
    </a>

    <a href="../services/services.php">Services</a>

    <a href="../mechanics/mechanics.php">Mechanics</a>

    <a href="../parts/parts.php">Spare Parts</a>

    <a href="../payments/payments.php">Payments</a>
    <a href="../reports/reports.php">Reports</a>
    <a href="../logout.php" class="logout-link">Logout</a>

</div>


<div class="main">

    <?php if ($deleteError) { ?>
        <div class="alert alert-error" role="alert">
            <?php echo htmlspecialchars($deleteError, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php } ?>

    <div class="topbar">

        <div>

            <h1>Customers</h1>

            <p>Manage customer information</p>

        </div>

        <a href="add_customer.php" class="btn">
            + Add Customer
        </a>

    </div>


    <div class="content-box">

        <div class="content-header">

            <h2>Customer List</h2>

        </div>


        <div class="table-tools">

            <input
                type="text"
                id="customerSearch"
                placeholder="Search customers..."
                onkeyup="searchTable(
                    'customerSearch',
                    'customerTable'
                )"
            >

        </div>


        <table id="customerTable">

            <tr>

                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Action</th>

            </tr>


            <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['Customer_ID']; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Name'], ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Phone'], ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Email'], ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row['Address'], ENT_QUOTES, 'UTF-8'); ?>
                </td>

                <td>

                    <a
                        href="edit_customer.php?id=<?php echo $row['Customer_ID']; ?>"
                        class="btn"
                    >
                        Edit
                    </a>

                    <a
                        href="delete_customer.php?id=<?php echo $row['Customer_ID']; ?>"
                        class="btn btn-danger"
                        onclick="return confirmDelete(
                            'Are you sure you want to delete this customer?'
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