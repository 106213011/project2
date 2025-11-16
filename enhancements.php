<!DOCTYPE html>
<html lang="en">
<?php include 'header.inc'; ?>

<body>
    <?php
    session_start();

    $conn = mysqli_connect("localhost", "root", "", "group_2");
    if (!$conn) die("Connection failed: " . mysqli_connect_error());

    $validSort = [
        "EOInumber", "JobReferenceNumber", "FirstName", "LastName",
        "DOB", "Gender", "StreetAddress", "SuburbTown", "State",
        "Postcode", "Email", "PhoneNumber", "Skills", "OtherSkills", "Status"
    ];

    $sortField = "EOInumber";

    if (isset($_POST["sort"]) && in_array($_POST["sort"], $validSort)) {
        $sortField = $_POST["sort"];
    }

    $eoiResult = mysqli_query($conn, "SELECT * FROM eoi ORDER BY $sortField");

    /* ------------------------------
       FEATURE 2 – MANAGER REGISTRATION
    ------------------------------ */
    $regMessage = "";

    if (isset($_POST["register"])) {
        $username = trim($_POST["new_username"]);
        $password = trim($_POST["new_password"]);

        if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d).{5,}$/", $password)) {
            $regMessage = "Password must be at least 5 characters and contain letters and numbers.";
        } else {
            $check = mysqli_query($conn, "SELECT * FROM managers WHERE username='$username'");
            if (mysqli_num_rows($check) > 0) {
                $regMessage = "Username already exists.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                mysqli_query($conn, "INSERT INTO managers (username, password) VALUES ('$username', '$hashed')");
                $regMessage = "Registration successful!";
            }
        }
    }

    /* ------------------------------
       FEATURE 3 & 4 – LOGIN + LOCKOUT
    ------------------------------ */
    if (!isset($_SESSION["failed_attempts"])) {
        $_SESSION["failed_attempts"] = 0;
    }

    $loginMessage = "";

    if (isset($_SESSION["lockout_time"]) && time() < $_SESSION["lockout_time"]) {
        $remaining = $_SESSION["lockout_time"] - time();
        $loginMessage = "Too many failed attempts. Try again in $remaining seconds.";
    } elseif (isset($_POST["login"])) {
        $user = trim($_POST["username"]);
        $pass = trim($_POST["password"]);

        $search = mysqli_query($conn, "SELECT * FROM managers WHERE username='$user'");

        if (mysqli_num_rows($search) == 1) {
            $row = mysqli_fetch_assoc($search);
            if (password_verify($pass, $row["password"])) {
                $_SESSION["manager"] = $user;
                $_SESSION["failed_attempts"] = 0;
                $loginMessage = "Login successful.";
            } else {
                $_SESSION["failed_attempts"]++;
            }
        } else {
            $_SESSION["failed_attempts"]++;
        }

        if ($_SESSION["failed_attempts"] >= 3) {
            $_SESSION["lockout_time"] = time() + 60;
            $loginMessage = "Account locked for 60 seconds.";
        }
    }
    ?>

    <!-- SORTING FEATURE -->
    <section>
        <h3 id = "enhancements">1. Sort EOI Records</h3>

        <form method="post">
            <p><label for="sort"> Sort by: </label>
                <select name="sort" id="sort">
                    <option value="EOInumber">EOI Number</option>
                    <option value="JobReferenceNumber">Job Reference Number</option>
                    <option value="FirstName">First Name</option>
                    <option value="LastName">Last Name</option>
                    <option value="DOB">Date of Birth</option>
                    <option value="Gender">Gender</option>
                    <option value="StreetAddress">Street Address</option>
                    <option value="SuburbTown">Suburb/Town</option>
                    <option value="State">State</option>
                    <option value="Postcode">Postcode</option>
                    <option value="Email">Email</option>
                    <option value="PhoneNumber">Phone Number</option>
                    <option value="Skills">Skills</option>
                    <option value="OtherSkills">Other Skills</option>
                    <option value="Status">Status</option>
                </select>
                <input type="submit" value="Sort">
                <br>
            </p>
        </form>

        <table id ="sort" border="1" cellpadding="6">
            <tr>
                <th>EOI Number</th>
                <th>Job Reference Number</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Street Address</th>
                <th>Suburb/Town</th>
                <th>State</th>
                <th>Postcode</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Skills</th>
                <th>Other Skills</th>
                <th>Status</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($eoiResult)) : ?>
                <tr>
                    <td><?= $row["EOInumber"]; ?></td>
                    <td><?= $row["JobReferenceNumber"]; ?></td>
                    <td><?= $row["FirstName"]; ?></td>
                    <td><?= $row["LastName"]; ?></td>
                    <td><?= $row["DOB"]; ?></td>
                    <td><?= $row["Gender"]; ?></td>
                    <td><?= $row["StreetAddress"]; ?></td>
                    <td><?= $row["SuburbTown"]; ?></td>
                    <td><?= $row["State"]; ?></td>
                    <td><?= $row["Postcode"]; ?></td>
                    <td><?= $row["Email"]; ?></td>
                    <td><?= $row["PhoneNumber"]; ?></td>
                    <td><?= $row["Skills"]; ?></td>
                    <td><?= $row["OtherSkills"]; ?></td>
                    <td><?= $row["Status"]; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </section>
    <br>

    <hr>

    <!-- REGISTRATION -->
    <section>
        <h3 id = "enhancements"> 2. Manager Registration</h3>

        <form method="post">
            <p><label for="new_username">Username: </label>
                <input type="text" name="new_username" required>
            </p>
            <p><label for="new_password">Password: </label>
                <input type="password" name="new_password" required>
            </p>
            <input type="submit" name="register" value="Register">
        </form>

        <p><strong><?= $regMessage ?></strong></p>
    </section>

    <hr>

    <!-- LOGIN + LOCKOUT -->
    <section>
        <h3 id = "enhancements" > 3. Manager Login </h3>

        <form method="post">
            <p><label for="username">Username: </label>
                <input type="text" name="username" required>
            </p>
            <p><label for="password">Password: </label>
                <input type="password" name="password" required>
            </p>
            <input type="submit" name="login" value="Login">
        </form>

        <p><strong><?= $loginMessage ?></strong></p>

        <?php if (isset($_SESSION["manager"])): ?>
           <p>You are logged in as <strong><?= $_SESSION["manager"] ?></strong></p>
          <p class="mng-container"><a href="manage.php" class ="manage-link">Go to Manage Page</a></p>
        <?php endif; ?>
    </section>

    <?php include 'footer.inc'; ?>
</body>
</html>