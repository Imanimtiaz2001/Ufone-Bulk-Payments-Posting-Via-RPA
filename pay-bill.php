<!DOCTYPE html>
<html>
<head>
  <title>Pay Bill</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <img src="ufone.png" alt="Ufone Logo" class="logo-image">

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
      die("<p class='feedback'>Connection failed: " . $conn->connect_error);
  }
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phoneNumber = $_POST['phoneNumber'];
    $balance = floatval($_POST['balance']);

    // Retrieve customer details from the database based on the customer number
    $sql = "SELECT * FROM customers WHERE phoneNumber = '$phoneNumber'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $customerId = $row['customerId'];
        $currentBalance = $row['balance'];

        // Update the customer's balance
        $newBalance = $currentBalance + $balance;

        // Update the customer's balance in the database
        $updateSql = "UPDATE customers SET balance = '$newBalance' WHERE phoneNumber = '$phoneNumber'";
        if ($conn->query($updateSql) === TRUE) {
            // Retrieve the last used billing ID for the specific customer
            $lastBillingIdSql = "SELECT MAX(billingId) AS lastBillingId FROM billing_history WHERE customerId = '$customerId'";
            $lastBillingIdResult = $conn->query($lastBillingIdSql);
            $lastBillingIdRow = $lastBillingIdResult->fetch_assoc();
            $lastBillingId = $lastBillingIdRow['lastBillingId'];
            
            // Increment the billing ID
            $newBillingId = $lastBillingId + 1;

            // Format current date and time in the specified pattern
            $currentDateTime = date("Y-m-d H:i:s");

            // Insert a new record into the billing_history table
            $insertBillingSql = "INSERT INTO billing_history (customerId, billingId, billingDate, billingAmount, billingType) VALUES ('$customerId', '$newBillingId', '$currentDateTime', $balance, 'Online')";
            
            if ($conn->query($insertBillingSql) === TRUE) {
                echo "<span class='feedback'>Balance added successfully! Billing history updated.</span>";
            } else {
                echo "<span class='feedback'>Failed to add balance and update billing history: " . $conn->error . "</span>";
            }
        } else {
            echo "<span class='feedback'>Failed to add balance: " . $conn->error . "</span>";
        }
    } else {
        echo "<span class='feedback'>Invalid customer number!</span>";
    }
}

  // Close the database connection
  $conn->close();
  ?>
 <h1>PAY BILL</h1>
  <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
   
    <label for="phoneNumber">CUSTOMER NUMBER:</label>
    <input type="text" name="phoneNumber" placeholder="+923360000000" required><br>

    <label for="balance">BILL AMOUNT:</label>
    <input type="number" name="balance" placeholder="500" step="0.01" required><br>

    <input type="submit" value="Pay Bill">
  </form>
</body>
</html>
