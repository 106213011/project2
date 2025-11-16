<!DOCTYPE html>
<html lang="en">
<?php include 'header.inc'; ?>

<body>
    <?php
    session_start();

    $conn = mysqli_connect("localhost", "root", "", "group_2");
    if (!$conn) die("Connection failed: " . mysqli_connect_error());

    $validSort = ["firstname", "lastname", "jobref", "date_applied"];
    $sortField = "firstname";

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
        <h3>1. Sort EOI Records</h3>

        <form method="post">
            <p><label for="sort"> Sort by: </label>
                <select name="sort" id="sort">
                    <option value="firstname">First Name</option>
                    <option value="lastname">Last Name</option>
                    <option value="jobref">Job Reference</option>
                    <option value="date_applied">Date Applied</option>
                </select>
                <input type="submit" value="Sort">
            </p>
        </form>

        <table border="1" cellpadding="6">
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Job Ref</th>
                <th>Date Applied</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($eoiResult)) : ?>
                <tr>
                    <td><?= $row["firstname"]; ?></td>
                    <td><?= $row["lastname"]; ?></td>
                    <td><?= $row["jobref"]; ?></td>
                    <td><?= $row["date_applied"]; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </section>

    <!-- REGISTRATION -->
    <section>
        <h3> Manager Registration</h3>

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

    <!-- LOGIN + LOCKOUT -->
    <section>
        <h3>Manager Login </h3>

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
        <?php endif; ?>
    </section>

    <?php include 'footer.inc'; ?>
</body>
</html>