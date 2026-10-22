<?php

include "../auth.php";
include "../db.php";


if (!isset($_GET['id'])) {

    header("Location: mechanics.php");

    exit;
}


$id = (int) $_GET['id'];


/* Get mechanic */

$stmt = $conn->prepare(
    "SELECT *
     FROM MECHANIC
     WHERE Mechanic_ID = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$mechanic =
    $stmt->get_result()->fetch_assoc();


if (!$mechanic) {

    die("Mechanic not found.");

}


/* Update */

if (isset($_POST['update'])) {

    $name = $_POST['name'];

    $phone = $_POST['phone'];

    $email = $_POST['email'];

    $specialization = $_POST['specialization'];

    $experience = $_POST['experience'];


    $sql = "UPDATE MECHANIC

            SET
                Name = ?,
                Phone = ?,
                Email = ?,
                Specialization = ?,
                Experience = ?

            WHERE Mechanic_ID = ?";


    $stmt = $conn->prepare($sql);


    $stmt->bind_param(
        "ssssii",
        $name,
        $phone,
        $email,
        $specialization,
        $experience,
        $id
    );


    if ($stmt->execute()) {

        header("Location: mechanics.php");

        exit;

    }

}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Mechanic</title>

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

            <h1>Edit Mechanic</h1>

            <p>Update mechanic information</p>

        </div>

    </div>


    <div class="form-box">

        <form method="POST">


            <div class="form-group">

                <label>Name</label>

                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($mechanic['Name']); ?>"
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
                    value="<?php echo htmlspecialchars($mechanic['Phone']); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    value="<?php echo htmlspecialchars($mechanic['Email']); ?>"
                >

            </div>


            <div class="form-group">

                <label>Specialization</label>

                <input
                    type="text"
                    name="specialization"
                    value="<?php echo htmlspecialchars($mechanic['Specialization']); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label>Experience</label>

                <input
                    type="number"
                    name="experience"
                    min="0"
                    value="<?php echo $mechanic['Experience']; ?>"
                    required
                >

            </div>


            <button
                type="submit"
                name="update"
                class="btn"
            >
                Update Mechanic
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