<!DOCTYPE html>
<html>
<head>
  <title>Billing History</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    
<img src="ufone.png" alt="Ufone Logo" class="logo-image">
  <h1>BILLING HISTORY</h1>
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
      die("<p class='error-message'>Connection failed: " . $conn->connect_error);
  }

  // Get the customerId from the URL parameter
  $customerId = $_GET['customerId'];

  // SQL query to retrieve billing history for the specific customer
  $sql = "SELECT * FROM billing_history WHERE customerId = $customerId";
  $result = $conn->query($sql);

  // Display the billing history table
  if ($result->num_rows > 0) {
      echo "<table>";
      echo "<tr><th>Billing ID</th><th>Customer ID</th><th>Billing Date</th><th>Billing Amount</th><th>Billing Type</th></tr>";
      while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row['billingId'] . "</td>";
          echo "<td>" . $row['customerId'] . "</td>";
          echo "<td>" . $row['billingDate'] . "</td>";
          echo "<td>" . $row['billingAmount'] . "</td>";
          echo "<td>" . $row['billingType'] . "</td>";
          echo "</tr>";
      }
      echo "</table>";
  } else {
      echo "<p class='error-message'>No billing history found for this customer.";
  }

  // Close the database connection
  $conn->close();
  ?>
</body>
</html>
