
<!DOCTYPE html>
<html>
<head>
  <title>Billing System</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      
  <script>
    $(document).ready(function() {
      // Function to check and activate billing automatically
      function checkAndActivateBilling() {
        // Loop through each row in the table
        $("tbody tr").each(function() {
          var billingPeriod = parseInt($(this).find("td:nth-child(7)").text());
          var balance = parseFloat($(this).find("td:nth-child(6)").text());

          // Check if billing period is 0 and balance is greater than 500
          if (billingPeriod === 0 && balance > 499) {
            var activateBtn = $(this).find(".activate-billing-btn");
            var customerId = activateBtn.data("customer-id");
            window.location.href = "activate_billing.php?customerId=" + customerId;
          }
     // Click event handler for the "Show History" button
     $(".billing-history-btn").click(function() {
      var customerId = $(this).data("customer-id");
      window.location.href = "billing-history.php?customerId=" + customerId;
    });
        });
      }

      // Call the checkAndActivateBilling function initially
      checkAndActivateBilling();
      // Click event handler for the "Activate Billing" button
      $(".activate-billing-btn").click(function() {
        // Disable the button
        $(this).attr("disabled", true);
        
        var customerId = $(this).data("customer-id");
        window.location.href = "activate_billing.php?customerId=" + customerId;
      });
    });
  </script>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <img src="ufone.png" alt="Ufone Logo" class="logo-image">

  
  <h1>BILLING SYSTEM</h1>
  <table>
    <thead>
    
      <tr>
        <th>CUSTOMER ID</th>
        <th>NAME</th>
        <th>EMAIL</th>
        <th>PHONE NUMBER</th>
        <th>PURPOSE</th>
        <th>BALANCE</th>
        <th>PERIOD(Months)</th>
        <th>STATUS</th>
        <th>ACTION</th>
        <th>HISTORY</th> <!-- New column header -->
      </tr>
    </thead>
    <tbody>
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

      // SQL query to retrieve customer data
      $sql = "SELECT * FROM customers";
      $result = $conn->query($sql);

      // Fetch customer data and display in the table
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['customerId'] . "</td>";
              echo "<td>" . $row['name'] . "</td>";
              echo "<td>" . $row['email'] . "</td>";
              echo "<td>" . $row['phoneNumber'] . "</td>";
              echo "<td>" . $row['purpose'] . "</td>";
              echo "<td>" . $row['balance'] . "</td>";
              echo "<td>" . $row['billingPeriod'] . "</td>";
              echo "<td>" . $row['status'] . "</td>";
                // Existing code for "Activate Billing" button
                if ($row['status'] == 'In Active') {
                  echo "<td><button class='activate-billing-btn' data-customer-id='" . $row['customerId'] . "' disabled>Activate Billing</button></td>";
                } else {
                  echo "<td></td>";
                }
  
                // New "billing-history" button
                echo "<td><button class='billing-history-btn' data-customer-id='" . $row['customerId'] . "'>Show History</button></td>";
  
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No customers found.</td></tr>";
        }

      // Close the database connection
      $conn->close();
      ?>
    </tbody>
  </table>
</body>
</html>