<?php

include "../auth.php";
include "../db.php";

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$idInput = $method === 'POST'
    ? ($_POST['id'] ?? null)
    : ($_GET['id'] ?? null);
$id = is_string($idInput)
    ? filter_var($idInput, FILTER_VALIDATE_INT)
    : false;

if (!in_array($method, ['GET', 'POST'], true)) {
    http_response_code(405);
    exit("Method not allowed.");
}

if (!$id || $id < 1) {
    $_SESSION['service_delete_error'] = "Invalid service record.";
    header("Location: services.php");
    exit;
}

$escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

if ($method === 'POST') {
    $submittedToken = $_POST['token'] ?? '';
    $expectedToken = $_SESSION['service_delete_tokens'][$id] ?? '';
    unset($_SESSION['service_delete_tokens'][$id]);

    if (
        !is_string($submittedToken) ||
        !is_string($expectedToken) ||
        $expectedToken === '' ||
        !hash_equals($expectedToken, $submittedToken)
    ) {
        $_SESSION['service_delete_error'] = "The delete confirmation expired. Please try again.";
        header("Location: services.php");
        exit;
    }

    try {
        $conn->begin_transaction();

        $stmt = $conn->prepare(
            "SELECT Service_ID
             FROM SERVICE
             WHERE Service_ID = ?
             FOR UPDATE"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if (!$stmt->get_result()->fetch_assoc()) {
            $conn->rollback();
            $_SESSION['service_delete_error'] = "That service no longer exists.";
            header("Location: services.php");
            exit;
        }

        $stmt = $conn->prepare(
            "SELECT Part_ID, Quantity
             FROM SERVICE_PART
             WHERE Service_ID = ?
             FOR UPDATE"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $serviceParts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $partId = 0;
        $quantity = 0;
        $restock = $conn->prepare(
            "UPDATE PART
             SET Quantity = Quantity + ?
             WHERE Part_ID = ?"
        );
        $restock->bind_param("ii", $quantity, $partId);
        $returnedUnits = 0;

        foreach ($serviceParts as $servicePart) {
            $partId = (int) $servicePart['Part_ID'];
            $quantity = (int) $servicePart['Quantity'];
            $restock->execute();
            $returnedUnits += $quantity;
        }

        $stmt = $conn->prepare(
            "DELETE FROM SERVICE_ASSIGNMENT
             WHERE Service_ID = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $deletedAssignments = $stmt->affected_rows;

        $stmt = $conn->prepare(
            "DELETE FROM SERVICE_PART
             WHERE Service_ID = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $deletedParts = $stmt->affected_rows;

        $stmt = $conn->prepare(
            "DELETE FROM PAYMENT
             WHERE Service_ID = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $deletedPayments = $stmt->affected_rows;

        $stmt = $conn->prepare(
            "DELETE FROM SERVICE
             WHERE Service_ID = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows !== 1) {
            throw new RuntimeException("Service was not deleted.");
        }

        $conn->commit();
        $_SESSION['service_delete_success'] = sprintf(
            "Service deleted. Removed %d mechanic assignment(s), %d part usage row(s), returned %d part unit(s) to stock, and deleted %d payment record(s).",
            $deletedAssignments,
            $deletedParts,
            $returnedUnits,
            $deletedPayments
        );
    } catch (Throwable $error) {
        $conn->rollback();
        $_SESSION['service_delete_error'] =
            "The service could not be deleted. No related records were removed.";
    }

    header("Location: services.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT Service_ID, Service_Name, Service_Date
     FROM SERVICE
     WHERE Service_ID = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$service = $stmt->get_result()->fetch_assoc();

if (!$service) {
    $_SESSION['service_delete_error'] = "That service no longer exists.";
    header("Location: services.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT
        (SELECT COUNT(*) FROM SERVICE_ASSIGNMENT WHERE Service_ID = ?) AS assignments,
        (SELECT COUNT(*) FROM SERVICE_PART WHERE Service_ID = ?) AS parts,
        (SELECT COUNT(*) FROM PAYMENT WHERE Service_ID = ?) AS payments"
);
$stmt->bind_param("iii", $id, $id, $id);
$stmt->execute();
$dependencies = $stmt->get_result()->fetch_assoc();

$token = bin2hex(random_bytes(32));
$_SESSION['service_delete_tokens'][$id] = $token;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Service Deletion - AutoCare</title>
    <link rel="stylesheet" href="../style.css">
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
    <a href="services.php" class="active">Services</a>
    <a href="../mechanics/mechanics.php">Mechanics</a>
    <a href="../parts/parts.php">Spare Parts</a>
    <a href="../payments/payments.php">Payments</a>
    <a href="../reports/reports.php">Reports</a>
    <a href="../logout.php" class="logout-link">Logout</a>
</div>

<main class="main">
    <div class="topbar">
        <div>
            <h1>Confirm Service Deletion</h1>
            <p>Review the related records before continuing</p>
        </div>
    </div>

    <section class="content-box delete-confirmation">
        <h2><?php echo $escape($service['Service_Name']); ?></h2>
        <p>Service date: <?php echo $escape($service['Service_Date']); ?></p>
        <p>Deleting this service will also remove:</p>
        <ul class="delete-dependencies">
            <li><?php echo (int) $dependencies['assignments']; ?> mechanic assignment(s)</li>
            <li><?php echo (int) $dependencies['parts']; ?> part usage row(s); used quantities will be returned to inventory</li>
            <li><?php echo (int) $dependencies['payments']; ?> payment record(s)</li>
        </ul>

        <?php if ((int) $dependencies['payments'] > 0) { ?>
            <div class="alert alert-error" role="alert">
                Payment history, including any paid records, will be permanently deleted.
            </div>
        <?php } ?>

        <p>The service request and its customer and vehicle records will remain.</p>

        <form method="POST" class="delete-confirmation-actions">
            <input type="hidden" name="id" value="<?php echo (int) $id; ?>">
            <input type="hidden" name="token" value="<?php echo $escape($token); ?>">
            <a href="services.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-danger">Delete service and related records</button>
        </form>
    </section>
</main>
</body>
</html>