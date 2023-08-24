<?php
// MySQL database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ptcl";

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve customer data from the database
$sql = "SELECT * FROM customers";
$result = $conn->query($sql);

// Fetch customer data and update the billing period
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customerId = $row['customerId'];
        $billingPeriod = $row['billingPeriod'];
        $status = $row['status'];

        // Decrement the billing period by 1 if it's greater than 0
        if ($billingPeriod > 0) {
            $newBillingPeriod = $billingPeriod - 1;

            // Update the billing period in the database
            $updateSql = "UPDATE customers SET billingPeriod = $newBillingPeriod WHERE customerId = '$customerId'";
            $conn->query($updateSql);

            // Check if the billing period has reached 0
            if ($newBillingPeriod == 0 && $status == 'Active') {
                // Update the status to 'Inactive'
                $updateStatusSql = "UPDATE customers SET status = 'In Active' WHERE customerId = '$customerId'";
                $conn->query($updateStatusSql);
            }
        }
    }
}

// Close the database connection
$conn->close();
?>
