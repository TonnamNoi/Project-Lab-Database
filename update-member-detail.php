<?php
@session_start();
if (!isset($_SESSION['member_id'])) {
    header('Location: member-signin.php');
    exit();
}

$mysqli = new mysqli('localhost', 'root', 'root', 'project1');
$member_id = $_SESSION['member_id'];

// Fetch current member details
$sql = 'SELECT * FROM member WHERE id = ?';
$stmt = $mysqli->stmt_init();
$stmt->prepare($sql);
$stmt->bind_param('i', $member_id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_object();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    // Update the user's details in the database
    $sql = 'UPDATE member SET firstname = ?, lastname = ?, address = ?, phone = ? WHERE id = ?';
    $stmt = $mysqli->stmt_init();
    $stmt->prepare($sql);
    $stmt->bind_param('ssssi', $firstname, $lastname, $address, $phone, $member_id);

    if ($stmt->execute()) {
        $success = 'Profile updated successfully!';
        $_SESSION['member_name'] = $firstname;
    } else {
        $error = 'Failed to update profile!';
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">



<head>
    <?php require 'head.php'; ?>
    <style>
        html, body {
            /* Apply the same gradient background used in member-signin.php */
            background: azure;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #update-form {
            width: 100%;
            max-width: 600px;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border: 1px solid #dee2e6;
        }

        .alert {
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 5px;
        }

        button[type="submit"], .btn-secondary {
            background-color: #17a2b8; /* Matches navbar color */
            border-color: #17a2b8;
            color: white;
        }

        button[type="submit"]:hover, .btn-secondary:hover {
            background-color: #138496; /* Darker shade for hover effect */
            border-color: #138496;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        h3 {
            color: #17a2b8; /* Matches navbar color */
            font-size: 1.5rem;
        }
    </style>
</head>

<body>
    <?php require 'navbar.php'; ?>
    <div id="update-form">
        <h3 class="text-center mb-4">Update Personal Details</h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success" role="alert"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" name="firstname" id="firstname" class="form-control" value="<?= htmlspecialchars($member->firstname) ?>" required>
            </div>

            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" name="lastname" id="lastname" class="form-control" value="<?= htmlspecialchars($member->lastname) ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" name="address" id="address" class="form-control" value="<?= htmlspecialchars($member->address) ?>">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($member->phone) ?>">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Update</button>
            <a href="index.php" class="btn btn-secondary btn-block">Cancel</a>
        </form>
    </div>

    <?php require 'footer.php'; ?>
</body>

</html>
