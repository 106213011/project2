<?php
session_start();

// BLOCK ACCESS IF NOT LOGGED IN
if (!isset($_SESSION['manager'])) {
    header("Location:enhancements.php");
    exit();
}

include 'header.inc';
include 'settings.php';

$conn = mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

// -------------------- POST ACTIONS --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // DELETE EOIs by Job Reference
    if (isset($_POST['delete_jobref'])) {
        $job_ref = mysqli_real_escape_string($conn, $_POST['job_ref']);
        $sql = "DELETE FROM eoi WHERE JobReferenceNumber='$job_ref'";
        mysqli_query($conn, $sql);
        $message = "All EOIs with Job Reference <strong>$job_ref</strong> deleted.";
    }

    // UPDATE STATUS
    if (isset($_POST['update_status'])) {
        $eoi_id = mysqli_real_escape_string($conn, $_POST['eoi_id']);
        $new_status = mysqli_real_escape_string($conn, $_POST['status']);
        $sql = "UPDATE eoi SET Status='$new_status' WHERE EOInumber='$eoi_id'";
        mysqli_query($conn, $sql);
        $message = "Status of EOI ID <strong>$eoi_id</strong> updated to <strong>$new_status</strong>.";
    }
}

// -------------------- SEARCH LOGIC --------------------
$where_clause = "1";

if (!empty($_GET['JobReferenceNumber'])) {
    $job_ref = mysqli_real_escape_string($conn, $_GET['JobReferenceNumber']);
    $where_clause = "JobReferenceNumber='$job_ref'";
}
elseif (!empty($_GET['first_name']) || !empty($_GET['last_name'])) {
    $conditions = [];

    if (!empty($_GET['first_name'])) {
        $first = mysqli_real_escape_string($conn, $_GET['first_name']);
        $conditions[] = "FirstName='$first'";
    }
    if (!empty($_GET['last_name'])) {
        $last = mysqli_real_escape_string($conn, $_GET['last_name']);
        $conditions[] = "LastName='$last'";
    }

    $where_clause = implode(" AND ", $conditions);
}

$sql = "SELECT * FROM eoi WHERE $where_clause";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<body>

<header class="hero">
    <img src="images/ALIMSON-LOGO.png" alt="Website Logo" class="logo">
    <h1 id="logo">EOI Manager</h1>
    <p>Manage, search, update and delete job applications.</p>
</header>

<?php include 'nav.inc'; ?>

<div class="container">

    <?php if ($message != ""): ?>
        <p style="color: green; font-weight: bold;"><?= $message ?></p>
    <?php endif; ?>

    <h2>Search EOIs</h2>
    <form method="GET">
        <p><label>Job Reference:
            <input type="text" name="JobReferenceNumber">
        </label></p>
        <button type="submit">Search</button>
    </form>

    <form method="GET">
        <p><label>First Name:
            <input type="text" name="first_name">
        </label></p>

        <p><label>Last Name:
            <input type="text" name="last_name">
        </label></p>
        <button type="submit">Search</button>
    </form>

    <h2>Delete EOIs</h2>
    <form method="POST">
        <p><label>Job Reference to delete:
            <input type="text" name="job_ref" required>
        </label></p>
        <button type="submit" name="delete_jobref">Delete</button>
    </form>

    <h2>Update EOI Status</h2>
    <form method="POST">
        <p><label>EOI ID:
            <input type="text" name="eoi_id" required>
        </label></p>

        <p><label>New Status:
            <input type="text" name="status" required>
        </label></p>

        <button type="submit" name="update_status">Update Status</button>
    </form>

    <h2>EOI List</h2>

    <table border="1" cellpadding="6" cellspacing="0" style="width: 100%; background: #fff;">
        <tr style="background: #333; color: white;">
            <th>ID</th>
            <th>Job Reference</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($row['EOInumber']) ?></td>
            <td><?= htmlspecialchars($row['JobReferenceNumber']) ?></td>
            <td><?= htmlspecialchars($row['FirstName']) ?></td>
            <td><?= htmlspecialchars($row['LastName']) ?></td>
            <td><?= htmlspecialchars($row['Email']) ?></td>
            <td><?= htmlspecialchars($row['Status']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

<?php include 'footer.inc'; ?>

</body>
</html>

<?php mysqli_close($conn); ?>