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
        if (mysqli_query($conn, $sql)) {
            $message = "Status of EOI ID <strong>$eoi_id</strong> updated to <strong>$new_status</strong>.";
        } else {
            $message = "Error updating status: " . mysqli_error($conn);
        }
    }
}

// -------------------- SEARCH LOGIC --------------------
$where_clause = "1"; // default: show all

if (isset($_GET['clear_search'])) {
    $where_clause = "1"; 
} elseif (!empty($_GET['search_type']) && !empty($_GET['search_value'])) {
    $type = mysqli_real_escape_string($conn, $_GET['search_type']);
    $value = mysqli_real_escape_string($conn, $_GET['search_value']);
    $where_clause = "$type LIKE '%$value%'";
}

$sql = "SELECT * FROM eoi WHERE $where_clause";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<body>

<?php include 'nav.inc'; ?>
<head>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<div class="container">

    <?php if ($message != ""): ?>
        <p style="color: green; font-weight: bold;"><?= $message ?></p>
    <?php endif; ?>

    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        <!-- SEARCH FORM -->
        <form method="GET" style="flex: 1; min-width: 250px; border: 1px solid #ccc; padding: 10px;">
            <h3>Search EOIs</h3>
            <label for="search_type">Search by:</label>
            <select id="search_type" name="search_type">
                <option value="JobReferenceNumber">Job Reference</option>
                <option value="FirstName">First Name</option>
                <option value="LastName">Last Name</option>
            </select>

            <input type="text" name="search_value" placeholder="Enter search term">
            <button type="submit">Search</button>
            <button type="submit" name="clear_search" value="1">Clear Search</button>
        </form>

        <!-- DELETE FORM -->
        <form method="POST" style="flex: 1; min-width: 250px; border: 1px solid #ccc; padding: 10px;">
            <h3>Delete EOI</h3>
            <label>Job Reference to delete:
                <input type="text" name="job_ref" required>
            </label>
            <br><br>
            <button type="submit" name="delete_jobref">Delete</button>
        </form>

        <!-- UPDATE STATUS FORM -->
        <form method="POST" style="flex: 1; min-width: 250px; border: 1px solid #ccc; padding: 10px;">
            <h3>Update EOI Status</h3>
            <label>EOI ID:
                <input type="text" name="eoi_id" required>
            </label>
            <br><br>
            <label>New Status:
                <select name="status" required>
                    <option value="New">New</option>
                    <option value="Current">Current</option>
                    <option value="Final">Final</option>
                </select>
            </label>
            <br><br>
            <button type="submit" name="update_status">Update Status</button>
        </form>
    </div>

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