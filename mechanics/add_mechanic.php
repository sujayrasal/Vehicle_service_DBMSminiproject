<?php

include "../auth.php";
include "../db.php";


if (isset($_POST['submit'])) {

    $name = $_POST['name'];

    $phone = $_POST['phone'];

    $email = $_POST['email'];

    $specialization = $_POST['specialization'];

    $experience = $_POST['experience'];


    $sql = "INSERT INTO MECHANIC
            (
                Name,
                Phone,
                Email,
                Specialization,
                Experience
            )

            VALUES (?, ?, ?, ?, ?)";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "ssssi",
        $name,
        $phone,
        $email,
        $specialization,
        $experience
    );


    if ($stmt->execute()) {

        header("Location: mechanics.php");

        exit;

    } else {

        echo "Error: " . $stmt->error;

    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Add Mechanic</title>

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

    <a href="../service_requests/service_requests.php">
        Service Requests
    </a>

    <a href="../services/services.php">Services</a>

    <a href="mechanics.php" class="active">
        Mechanics
    </a>

    <a href="../parts/parts.php">Spare Parts</a>

    <a href="../payments/payments.php">Payments</a>
    <a href="../reports/reports.php">Reports</a>
    <a href="../logout.php" class="logout-link">Logout</a>

</div>


<div class="main">

    <div class="topbar">

        <div>

            <h1>Add Mechanic</h1>

            <p>Create a mechanic record</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <div class="form-group">

                <label>Name</label>

                <input
                    type="text"
                    name="name"
                    required
                >

            </div>


            <div class="form-group">

                <label>Phone</label>

                <input
                    type="text"
                    name="phone"
                    maxlength="10"
                    oninput="validatePhone(this)"
                    required
                >

            </div>


            <div class="form-group">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                >

            </div>


            <div class="form-group">

                <label>Specialization</label>

                <select name="specialization" required>

                    <option value="">
                        -- Select Specialization --
                    </option>

                    <option value="Engine Repair">
                        Engine Repair
                    </option>

                    <option value="Brake System">
                        Brake System
                    </option>

                    <option value="General Service">
                        General Service
                    </option>

                    <option value="AC Repair">
                        AC Repair
                    </option>

                    <option value="Electrical System">
                        Electrical System
                    </option>

                    <option value="Transmission">
                        Transmission
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label>Experience (Years)</label>

                <input
                    type="number"
                    name="experience"
                    min="0"
                    value="0"
                    required
                >

            </div>


            <button
                type="submit"
                name="submit"
                class="btn"
            >
                Add Mechanic
            </button>


            <a
                href="mechanics.php"
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