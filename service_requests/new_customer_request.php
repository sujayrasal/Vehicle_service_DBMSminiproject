<?php

include "../auth.php";
include "../db.php";

$today = date('Y-m-d');
$form = [
    'customer_name' => '',
    'customer_phone' => '',
    'customer_email' => '',
    'customer_address' => '',
    'vehicle_no' => '',
    'brand' => '',
    'model' => '',
    'manufacturing_year' => '',
    'vehicle_type' => '',
    'request_date' => $today,
    'service_type' => '',
    'request_description' => '',
    'request_status' => 'Pending',
    'priority' => 'Normal',
    'service_date' => $today,
    'service_name' => '',
    'service_description' => '',
    'labour_cost' => '0.00',
    'service_status' => 'Pending',
];
$errors = [];
$escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$isValidDate = static function (string $value): bool {
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    return $date !== false && $date->format('Y-m-d') === $value;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $field => $default) {
        $postedValue = $_POST[$field] ?? null;
        if (is_string($postedValue)) {
            $form[$field] = trim($postedValue);
        }
    }

    $form['vehicle_no'] = strtoupper(
        preg_replace('/\s+/', '', $form['vehicle_no'])
    );
    $year = filter_var($form['manufacturing_year'], FILTER_VALIDATE_INT);

    if ($form['customer_name'] === '') {
        $errors[] = 'Enter the customer name.';
    }
    if (!preg_match('/^[0-9]{10,15}$/', $form['customer_phone'])) {
        $errors[] = 'Enter a phone number with 10 to 15 digits.';
    }
    if ($form['customer_email'] !== '' && !filter_var($form['customer_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid customer email address.';
    }
    if ($form['vehicle_no'] === '' || strlen($form['vehicle_no']) > 20) {
        $errors[] = 'Enter a vehicle number up to 20 characters.';
    }
    if ($form['brand'] === '' || $form['model'] === '') {
        $errors[] = 'Enter the vehicle brand and model.';
    }
    if ($year === false || $year < 1900 || $year > (int) date('Y')) {
        $errors[] = 'Enter a valid manufacturing year.';
    }
    if (!in_array($form['vehicle_type'], ['Car', 'Sedan', 'SUV', 'Hatchback', 'Bike', 'Other'], true)) {
        $errors[] = 'Choose a valid vehicle type.';
    }
    if (!$isValidDate($form['request_date']) || !$isValidDate($form['service_date'])) {
        $errors[] = 'Enter valid request and service dates.';
    }
    if (!in_array($form['service_type'], ['General Service', 'Repair', 'Oil Change', 'Brake Service', 'Engine Repair', 'AC Service', 'Other'], true)) {
        $errors[] = 'Choose a valid service type.';
    }
    if (!in_array($form['request_status'], ['Pending', 'In Progress', 'Completed'], true)) {
        $errors[] = 'Choose a valid request status.';
    }
    if (!in_array($form['priority'], ['Low', 'Normal', 'High'], true)) {
        $errors[] = 'Choose a valid priority.';
    }
    if ($form['service_name'] === '') {
        $errors[] = 'Enter the service or repair name.';
    }
    if (!preg_match('/^[0-9]{1,8}(\.[0-9]{1,2})?$/', $form['labour_cost'])) {
        $errors[] = 'Enter a labor cost between 0 and 99,999,999.99.';
    }
    if (!in_array($form['service_status'], ['Pending', 'In Progress', 'Completed'], true)) {
        $errors[] = 'Choose a valid service status.';
    }

    if (!$errors) {
        $customer_name = $form['customer_name'];
        $customer_phone = $form['customer_phone'];
        $customer_email = $form['customer_email'];
        $customer_address = $form['customer_address'];
        $vehicle_no = $form['vehicle_no'];
        $brand = $form['brand'];
        $model = $form['model'];
        $vehicle_type = $form['vehicle_type'];
        $request_date = $form['request_date'];
        $service_type = $form['service_type'];
        $request_description = $form['request_description'];
        $request_status = $form['request_status'];
        $priority = $form['priority'];
        $service_date = $form['service_date'];
        $service_name = $form['service_name'];
        $service_description = $form['service_description'];
        $labour_cost = (float) $form['labour_cost'];
        $service_status = $form['service_status'];

        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare(
                "INSERT INTO CUSTOMER (Name, Phone, Email, Address)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "ssss",
                $customer_name,
                $customer_phone,
                $customer_email,
                $customer_address
            );
            $stmt->execute();
            $customer_id = $conn->insert_id;

            $stmt = $conn->prepare(
                "INSERT INTO VEHICLE
                    (Customer_ID, Vehicle_No, Brand, Model, Manufacturing_Year, Vehicle_Type)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "isssis",
                $customer_id,
                $vehicle_no,
                $brand,
                $model,
                $year,
                $vehicle_type
            );
            $stmt->execute();
            $vehicle_id = $conn->insert_id;

            $stmt = $conn->prepare(
                "INSERT INTO SERVICE_REQUEST
                    (Vehicle_ID, Request_Date, Service_Type, Description, Status, Priority)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "isssss",
                $vehicle_id,
                $request_date,
                $service_type,
                $request_description,
                $request_status,
                $priority
            );
            $stmt->execute();
            $request_id = $conn->insert_id;

            $stmt = $conn->prepare(
                "INSERT INTO SERVICE
                    (Request_ID, Service_Date, Service_Name, Description, Labour_Cost, Status)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "isssds",
                $request_id,
                $service_date,
                $service_name,
                $service_description,
                $labour_cost,
                $service_status
            );
            $stmt->execute();

            $conn->commit();
            header("Location: service_requests.php");
            exit;
        } catch (Throwable $error) {
            $conn->rollback();
            $errors[] = $error instanceof mysqli_sql_exception && $error->getCode() === 1062
                ? 'That vehicle number is already registered.'
                : 'The new customer and service could not be saved. Check the details and try again.';
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Customer &amp; Service - AutoCare</title>
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
    <a href="service_requests.php" class="active">Service Requests</a>
    <a href="../services/services.php">Services</a>
    <a href="../mechanics/mechanics.php">Mechanics</a>
    <a href="../parts/parts.php">Spare Parts</a>
    <a href="../payments/payments.php">Payments</a>
    <a href="../reports/reports.php">Reports</a>
    <a href="../logout.php" class="logout-link">Logout</a>
</div>

<main class="main">
    <div class="topbar">
        <div>
            <h1>New Customer &amp; Service</h1>
            <p>Register a customer, vehicle, request, and service in one step</p>
        </div>
    </div>

    <div class="form-box intake-box">
        <?php if ($errors) { ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $error) { ?>
                    <div><?php echo $escape($error); ?></div>
                <?php } ?>
            </div>
        <?php } ?>

        <form method="POST">
            <section class="intake-section">
                <h2>Customer details</h2>
                <div class="intake-fields">
                    <div class="form-group">
                        <label for="customer_name">Full name</label>
                        <input id="customer_name" name="customer_name" type="text" maxlength="100" value="<?php echo $escape($form['customer_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_phone">Phone</label>
                        <input id="customer_phone" name="customer_phone" type="tel" inputmode="numeric" pattern="[0-9]{10,15}" maxlength="15" value="<?php echo $escape($form['customer_phone']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_email">Email</label>
                        <input id="customer_email" name="customer_email" type="email" maxlength="100" value="<?php echo $escape($form['customer_email']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="customer_address">Address</label>
                        <input id="customer_address" name="customer_address" type="text" maxlength="200" value="<?php echo $escape($form['customer_address']); ?>">
                    </div>
                </div>
            </section>

            <section class="intake-section">
                <h2>Vehicle details</h2>
                <div class="intake-fields">
                    <div class="form-group">
                        <label for="vehicle_no">Vehicle number</label>
                        <input id="vehicle_no" name="vehicle_no" type="text" maxlength="20" oninput="formatVehicleNumber(this)" value="<?php echo $escape($form['vehicle_no']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="brand">Brand</label>
                        <input id="brand" name="brand" type="text" maxlength="50" value="<?php echo $escape($form['brand']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="model">Model</label>
                        <input id="model" name="model" type="text" maxlength="50" value="<?php echo $escape($form['model']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="manufacturing_year">Manufacturing year</label>
                        <input id="manufacturing_year" name="manufacturing_year" type="number" min="1900" max="<?php echo (int) date('Y'); ?>" value="<?php echo $escape($form['manufacturing_year']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="vehicle_type">Vehicle type</label>
                        <select id="vehicle_type" name="vehicle_type" required>
                            <option value="">Select type</option>
                            <?php foreach (['Car', 'Sedan', 'SUV', 'Hatchback', 'Bike', 'Other'] as $type) { ?>
                                <option value="<?php echo $escape($type); ?>" <?php echo $form['vehicle_type'] === $type ? 'selected' : ''; ?>><?php echo $escape($type); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </section>

            <section class="intake-section">
                <h2>Service request</h2>
                <div class="intake-fields">
                    <div class="form-group">
                        <label for="request_date">Request date</label>
                        <input id="request_date" name="request_date" type="date" value="<?php echo $escape($form['request_date']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="service_type">Service type</label>
                        <select id="service_type" name="service_type" required>
                            <option value="">Select service type</option>
                            <?php foreach (['General Service', 'Repair', 'Oil Change', 'Brake Service', 'Engine Repair', 'AC Service', 'Other'] as $type) { ?>
                                <option value="<?php echo $escape($type); ?>" <?php echo $form['service_type'] === $type ? 'selected' : ''; ?>><?php echo $escape($type); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="request_status">Request status</label>
                        <select id="request_status" name="request_status" required>
                            <?php foreach (['Pending', 'In Progress', 'Completed'] as $status) { ?>
                                <option value="<?php echo $escape($status); ?>" <?php echo $form['request_status'] === $status ? 'selected' : ''; ?>><?php echo $escape($status); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="priority">Priority</label>
                        <select id="priority" name="priority" required>
                            <?php foreach (['Low', 'Normal', 'High'] as $priority) { ?>
                                <option value="<?php echo $escape($priority); ?>" <?php echo $form['priority'] === $priority ? 'selected' : ''; ?>><?php echo $escape($priority); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group form-group-wide">
                        <label for="request_description">Customer concern</label>
                        <textarea id="request_description" name="request_description" maxlength="255" rows="3"><?php echo $escape($form['request_description']); ?></textarea>
                    </div>
                </div>
            </section>

            <section class="intake-section">
                <h2>Service details</h2>
                <div class="intake-fields">
                    <div class="form-group">
                        <label for="service_date">Service date</label>
                        <input id="service_date" name="service_date" type="date" value="<?php echo $escape($form['service_date']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="service_name">Work or service name</label>
                        <input id="service_name" name="service_name" type="text" maxlength="100" value="<?php echo $escape($form['service_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="labour_cost">Labor cost (₹)</label>
                        <input id="labour_cost" name="labour_cost" type="number" min="0" max="99999999.99" step="0.01" value="<?php echo $escape($form['labour_cost']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="service_status">Service status</label>
                        <select id="service_status" name="service_status" required>
                            <?php foreach (['Pending', 'In Progress', 'Completed'] as $status) { ?>
                                <option value="<?php echo $escape($status); ?>" <?php echo $form['service_status'] === $status ? 'selected' : ''; ?>><?php echo $escape($status); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group form-group-wide">
                        <label for="service_description">Work description</label>
                        <textarea id="service_description" name="service_description" maxlength="255" rows="3"><?php echo $escape($form['service_description']); ?></textarea>
                    </div>
                </div>
            </section>

            <div class="intake-actions">
                <a href="../index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn">Create Customer &amp; Service</button>
            </div>
        </form>
    </div>
</main>

<script src="../script.js"></script>
</body>
</html>