<!DOCTYPE html>
<html>
<head>
  <title>Activate Billing</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<img src="ufone.png" alt="Ufone Logo" class="logo-image">
  <?php
  
  require __DIR__ . '/vendor/autoload.php'; // Include the Twilio PHP SDK

use Twilio\Rest\Client;

// Your Twilio account SID, auth token, and phone number
$accountSid = "ACd6bbc176620dbdbe1d1737ee0c737030";
$authToken = "d5febff5a3919263a369fe54bf7bfe63";
$twilioPhoneNumber = "+18145645469";

// Function to send an SMS using Twilio
function sendSms($toPhoneNumber, $message, $accountSid, $authToken, $twilioPhoneNumber) {
    // Create an instance of the Twilio REST client
    $client = new Client($accountSid, $authToken);

    // Send the SMS
    $client->messages->create(
        $toPhoneNumber,
        array(
            'from' => $twilioPhoneNumber,
            'body' => $message
        )
    );
}

// MySQL database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ptcl";

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("<p class='error-message'>Connection failed: " . $conn->connect_error);
}

// Check if the customerId is provided in the URL
if (isset($_GET['customerId'])) {
    $customerId = $_GET['customerId'];

    // Retrieve customer details from the database
    $sql = "SELECT * FROM customers WHERE customerId = '$customerId'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Check if the customer is "In Active"
        if ($row['status'] == 'In Active') {
            // Calculate the billing period based on the customer's balance
            $balance = $row['balance'];
            $billingPeriod = intval($balance / 500);

            // Update the customer's balance and status
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $name = $_POST['name'];
                $phoneNumber = $_POST['phoneNumber'];
                $id = $_POST['id'];
                $balance = floatval($_POST['balance']);
                $billingPeriod = intval($_POST['billingPeriod']);

                // Calculate the required balance based on the billing period
                $requiredBalance = $billingPeriod * 500;

                // Check if the entered name, phone number, and ID match the customer's record
                if ($name === $row['name'] && $phoneNumber === $row['phoneNumber'] && $id === $row['customerId']) {
                    // Check if the customer's balance is sufficient to activate the billing
                    if ($balance >= $requiredBalance) {
                        // Deduct the required balance from the customer's balance
                        $newBalance = $balance - $requiredBalance;

                        // Update the balance, status, and billing period in the database
                        $sql = "UPDATE customers SET balance = $newBalance, status = 'Active', billingPeriod = $billingPeriod WHERE customerId = '$customerId'";
                        if ($conn->query($sql) === TRUE) {
                            
                            // Redirect to the new.php page after successful activation
                            header("Location: Home.php");
                            // Send an SMS to the customer's phone number
                            $message = "Hello " . $row['name'] . ", your billing has been successfully activated. Thank you!";
    
                            $toPhoneNumber = $row['phoneNumber'];
                            sendSms($toPhoneNumber, $message, $accountSid, $authToken, $twilioPhoneNumber);

                            exit;
                        } else {
                            die("<p class='error-message'>Failed to activate billing: " . $conn->error);
                        }
                    } else {
                        die("<p class='error-message'>Cannot activate billing.<br> Insufficient balance.");
                    }
                } else {
                    die("<p class='error-message'>Incorrect name, phone number, or ID.<br> Activation failed.");
                }
            }
            ?>
            <h1>ACTIVATE BILLING</h1>
            <?php if (isset($billingActivated) && $billingActivated === true): ?>
                <p><?php echo $feedbackMessage; ?></p>
            <?php else: ?>
                <form id="activate-billing-form" method="POST" action="">
                    <label for="id">CUSTOMER ID:</label>
                    <input type="text" name="id" value="<?php echo $row['customerId']; ?>" placeholder="1" readonly><br>

                    <label for="name">CUSTOMER NAME:</label>
                    <input type="text" name="name" value="<?php echo $row['name']; ?>" placeholder="Sara Khan" required><br>

                    <label for="phoneNumber">PHONE NUMBER:</label>
                    <input type="text" name="phoneNumber" value="<?php echo $row['phoneNumber']; ?>" placeholder="+923360000000" required><br>

                    <label for="balance">BALANCE:</label>
                    <input type="number" name="balance" step="0.01" value="<?php echo $row['balance']; ?>" placeholder="500" required><br>

                    <label for="billingPeriod">PERIOD(months):</label>
                    <input type="number" name="billingPeriod" min="1" value="<?php echo $billingPeriod; ?>" placeholder="5" required><br>

                    <input type="submit" value="Activate Billing">
                </form>
            <?php endif; ?>

            <script>
                // Submit the form automatically
                document.addEventListener("DOMContentLoaded", function () {
                    var form = document.getElementById("activate-billing-form");
                    var inputs = form.querySelectorAll("input");

                    for (var i = 0; i < inputs.length; i++) {
                        inputs[i].addEventListener("input", checkAllFieldsFilled);
                    }

                    function checkAllFieldsFilled() {
                        var allFilled = true;
                        for (var i = 0; i < inputs.length; i++) {
                            if (inputs[i].value === "") {
                                allFilled = false;
                                break;
                            }
                        }

                        if (allFilled) {
                            form.submit();
                        }
                    }

                    // Trigger form submission if all fields are filled
                    checkAllFieldsFilled();
                });
            </script>
            <?php
        } else {
            echo "<p class='error-message'>Cannot activate billing. <br>Customer is already Active.";
        }
    } else {
        echo "<p class='error-message'>Invalid customer ID.";
    }
} else {
    echo "<p class='error-message'>Customer ID not provided.";
}

// Close the database connection
$conn->close();
?>