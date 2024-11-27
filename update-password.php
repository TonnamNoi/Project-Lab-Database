<?php
@session_start();
if (!isset($_SESSION['member_id'])) {
    header('Location: member-signin.php');
    exit();
}

$mysqli = new mysqli('localhost', 'root', 'root', 'project1');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } else {
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = 'UPDATE member SET password = ? WHERE id = ?';
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($sql);
        $stmt->bind_param('si', $new_password_hashed, $_SESSION['member_id']);

        if ($stmt->execute()) {
            $success = 'Password updated successfully!';
        } else {
            $error = 'Failed to update password!';
        }
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
            /* Gradient background to match member-signin.php */
            background: azure;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #update-password-form {
            width: 100%;
            max-width: 500px;
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
    <div id="update-password-form">
        <h3 class="text-center mb-4">Update Password</h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success" role="alert"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Update Password</button>
            <a href="index.php" class="btn btn-secondary btn-block">Cancel</a>
        </form>
    </div>

    <?php require 'footer.php'; ?>
</body>

</html>
