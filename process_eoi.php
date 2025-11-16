<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>EOI Submitted</title>
</head>
<body>
<?php 
    include 'header.inc';
    echo '<main><section class="about-section" style="max-width:700px;">';
?>

<?php
session_start();

// --- Database connection ---
$conn = mysqli_connect("localhost", "root", "", "group_2");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// --- Helper function to sanitize input ---
function sanitise($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

// --- Check if form was submitted ---
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    // Prevent direct URL access
    header("Location: apply.php");
    exit();
}

// --- Get and sanitize form data ---
$jobreferencenumber = sanitise($conn, $_POST['jobreferencenumber']);
$firstname          = sanitise($conn, $_POST['firstname']);
$lastname           = sanitise($conn, $_POST['lastname']);
$dob                = sanitise($conn, $_POST['dob']);
$gender             = isset($_POST['gender']) ? sanitise($conn, $_POST['gender']) : "";
$streetaddress      = sanitise($conn, $_POST['street']);
$suburbtown         = sanitise($conn, $_POST['suburb']);
$state              = sanitise($conn, $_POST['state']);
$postcode           = sanitise($conn, $_POST['postcode']);
$email              = sanitise($conn, $_POST['email']);
$phonenumber        = sanitise($conn, $_POST['phonenumber']);
$otherskillsText        = sanitise($conn, $_POST['otherskillsText']);

// --- Combine technical skills checkboxes ---
$skills = [];

for ($i = 1; $i <= 6; $i++) {
    if (isset($_POST["req$i"])) {
        $skills[] = $_POST["req$i"];  
    }
}

$skills_text = implode(", ", $skills);

if (!empty($otherskills)) {
    $skills_text .= (!empty($skills_text) ? ", " : "") . "Other: " . $otherskills;
}

// --- Server-side validation ---
$errors = [];

if (!preg_match("/^[A-Za-z ]{1,20}$/", $firstname)) {
    $errors[] = "Invalid first name (max 20 alpha characters).";
}

if (!preg_match("/^[A-Za-z ]{1,20}$/", $lastname)) {
    $errors[] = "Invalid last name (max 20 alpha characters).";
}

if (empty($dob)) {
    $errors[] = "Please enter your date of birth.";
} else {
    // Convert from input type="date" (YYYY-MM-DD)
    $dob_date = DateTime::createFromFormat('Y-m-d', $dob);
    
    if (!$dob_date) {
        $errors[] = "Invalid date format. Please use the calendar input.";
    } else {
        $today = new DateTime();
        $age = $today->diff($dob_date)->y;

        if ($age < 18) {
            $errors[] = "You must be at least 18 years old to apply.";
        }
    }
}

if (empty($gender)) {
    $errors[] = "Please select a gender.";
}

if (strlen($streetaddress) == 0 || strlen($streetaddress) > 40) {
    $errors[] = "Street address must be between 1 and 40 characters.";
}

if (strlen($suburbtown) == 0 || strlen($suburbtown) > 40) {
    $errors[] = "Suburb/town must be between 1 and 40 characters.";
}

// ✅ State validation (must match allowed options)
$valid_states = ["VIC", "NSW", "QLD", "NT", "WA", "SA", "TAS", "ACT"];
if (!in_array($state, $valid_states)) {
    $errors[] = "Invalid state selection.";
}

// ✅ Postcode validation (exactly 4 digits + matches state)
if (!preg_match("/^\d{4}$/", $postcode)) {
    $errors[] = "Postcode must be exactly 4 digits.";
} else {
    $pc = (int)$postcode;
    switch ($state) {
        case "VIC": if ($pc < 3000 || $pc > 3999) $errors[] = "VIC postcodes range from 3000 to 3999."; break;
        case "NSW": if ($pc < 1000 || $pc > 2599) $errors[] = "NSW postcodes range from 1000 to 2599."; break;
        case "QLD": if ($pc < 4000 || $pc > 4999) $errors[] = "QLD postcodes range from 4000 to 4999."; break;
        case "NT":  if ($pc < 800 || $pc > 999)  $errors[] = "NT postcodes range from 0800 to 0999."; break;
        case "WA":  if ($pc < 6000 || $pc > 6999) $errors[] = "WA postcodes range from 6000 to 6999."; break;
        case "SA":  if ($pc < 5000 || $pc > 5799) $errors[] = "SA postcodes range from 5000 to 5799."; break;
        case "TAS": if ($pc < 7000 || $pc > 7999) $errors[] = "TAS postcodes range from 7000 to 7999."; break;
        case "ACT": if ($pc < 2600 || $pc > 2999) $errors[] = "ACT postcodes range from 2600 to 2999."; break;
    }
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (!preg_match("/^[0-9 ]{8,12}$/", $phonenumber)) {
    $errors[] = "Invalid phone number (must be 8–12 digits or spaces).";
}

if (empty($skills)) {
    $errors[] = "Please select at least one technical skill.";
}

if (isset($_POST['otherskillsCheckbox']) && empty($otherskillsText)) {
    $errors[] = "Please describe your other skills.";
}

if (count($errors) > 0) {
    echo "<h2>Form Errors:</h2><ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "<p><a href='apply.php'>Go back to the application form</a></p>";
    exit();
}

//-------------------------styling

include 'header.inc';
include 'nav.inc';

echo '<main><section class="about-section" style="max-width:700px;">';

if (count($errors) > 0) {

    echo "<h2>Form Errors</h2><ul style='color:red; text-align:left;'>";

    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }

    echo "</ul>";
    echo "<a href='apply.php' class='btn'>Go Back to Application</a>";

    echo "</section></main>";
    include 'footer.inc';
    exit();
}

//---------------------------------


// --- CREATE TABLE IF NOT EXISTS (auto creation) ---
$create_table_sql = "
CREATE TABLE IF NOT EXISTS eoi (
    EOInumber INT AUTO_INCREMENT PRIMARY KEY,
    JobReferenceNumber VARCHAR(10),
    FirstName VARCHAR(20),
    LastName VARCHAR(20),
    DOB DATE,
    Gender VARCHAR(10),
    StreetAddress VARCHAR(40),
    SuburbTown VARCHAR(40),
    State VARCHAR(10),
    Postcode VARCHAR(4),
    Email VARCHAR(50),
    PhoneNumber VARCHAR(15),
    OtherSkills TEXT,
    Status VARCHAR(20)
)";
if (!mysqli_query($conn, $create_table_sql)) {
    die("Error creating table: " . mysqli_error($conn));
}

// --- Insert form data into the database ---
$insert_sql = "INSERT INTO eoi 
(JobReferenceNumber, FirstName, LastName, DOB, Gender, StreetAddress, SuburbTown, State, Postcode, Email, PhoneNumber, Skills, OtherSkills, Status) 
VALUES 
('$jobreferencenumber', '$firstname', '$lastname', '$dob', '$gender', '$streetaddress', '$suburbtown', '$state', '$postcode', '$email', '$phonenumber', '$skills_text', '$otherskillsText', 'New')";

if (mysqli_query($conn, $insert_sql)) {
    $eoi_id = mysqli_insert_id($conn); // get auto-generated EOInumber
    echo "<h2>Application Submitted Successfully!</h2>";
    echo "<p>Thank you, $firstname $lastname. Your application for <strong>$jobreferencenumber</strong> has been received.</p>";
    echo "<p>Your EOI Number is: <strong>$eoi_id</strong></p>";
    echo "<p><a href='apply.php'>Submit another application</a></p>";
} else {
    echo "Error inserting record: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

<?php include 'footer.inc'; ?>
</body>
</html>