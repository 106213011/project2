<?php
include 'settings.php';


$conn = mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['delete_jobref'])) {
        $job_ref = mysqli_real_escape_string($conn, $_POST['job_ref']);
        $sql = "DELETE FROM eoi WHERE JobReferenceNumber='$job_ref'";
        mysqli_query($conn, $sql);
        echo "<p>All EOIs with Job Reference $job_ref deleted.</p>";
    }


    if (isset($_POST['update_status'])) {
        $eoi_id = mysqli_real_escape_string($conn, $_POST['eoi_id']);
        $new_status = mysqli_real_escape_string($conn, $_POST['status']);
        $sql = "UPDATE eoi SET status='$new_status' WHERE id='$eoi_id'";
        mysqli_query($conn, $sql);
        echo "<p>Status of EOI ID $eoi_id updated to $new_status.</p>";
    }
}


$where_clause = "1"; 
if (isset($_GET['JobReferenceNumber']) && !empty($_GET['JobReferenceNumber'])) {
    $job_ref = mysqli_real_escape_string($conn, $_GET['JobReferenceNumber']);
    $where_clause = "JobReferenceNumber='$job_ref'";
} elseif ((isset($_GET['first_name']) && !empty($_GET['first_name'])) || 
          (isset($_GET['last_name']) && !empty($_GET['last_name']))) {
    $conditions = [];
    if (!empty($_GET['first_name'])) {
        $first_name = mysqli_real_escape_string($conn, $_GET['first_name']);
        $conditions[] = "first_name='$first_name'";
    }
    if (!empty($_GET['last_name'])) {
        $last_name = mysqli_real_escape_string($conn, $_GET['last_name']);
        $conditions[] = "last_name='$last_name'";
    }
    $where_clause = implode(" AND ", $conditions);
}


$sql = "SELECT * FROM eoi WHERE $where_clause";
$result = mysqli_query($conn, $sql);
?>

<h1>EOI Manager</h1>


<h2>Search EOIs</h2>
<form method="GET">
    Job Reference: <input type="text" name="JobReferenceNumber">
    <button type="submit">Search</button>
</form>

<form method="GET">
    First Name: <input type="text" name="first_name">
    Last Name: <input type="text" name="last_name">
    <button type="submit">Search</button>
</form>


<h2>Delete EOIs</h2>
<form method="POST">
    Job Reference to delete: <input type="text" name="job_ref">
    <button type="submit" name="delete_jobref">Delete</button>
</form>


<h2>Update EOI Status</h2>
<form method="POST">
    EOI ID: <input type="text" name="eoi_id">
    New Status: <input type="text" name="status">
    <button type="submit" name="update_status">Update Status</button>
</form>


<h2>EOI List</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Job Reference</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Status</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
    <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>
        <td><?= htmlspecialchars($row['JobReferenceNumber']) ?></td>
        <td><?= htmlspecialchars($row['first_name']) ?></td>
        <td><?= htmlspecialchars($row['last_name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['status']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<?php
mysqli_close($conn);
?>