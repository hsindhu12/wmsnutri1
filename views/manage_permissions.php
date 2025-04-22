<?php
session_start();
require '../includes/db.php';

// Check if user is logged in and is an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: forbidden.php");
    exit();
}

// Fetch all roles
$roles_stmt = $conn->query("SELECT * FROM roles");
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all permissions
$permissions_stmt = $conn->query("SELECT * FROM permissions");
$permissions = $permissions_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for setting/removing permissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role_id = $_POST['role_id'];
    $selected_permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    // Clear existing permissions for this role
    $conn->prepare("DELETE FROM role_permissions WHERE role_id = ?")->execute([$role_id]);

    // Assign selected permissions
    foreach ($selected_permissions as $permission_id) {
        $conn->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)")->execute([$role_id, $permission_id]);
    }

    echo "<p>Permissions updated successfully!</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Permissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Permissions</h2>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="role_id" class="form-label">Select Role:</label>
                <select id="role_id" name="role_id" class="form-select" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= $role['role_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h5>Permissions:</h5>
            <?php foreach ($permissions as $permission): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $permission['id'] ?>" id="perm_<?= $permission['id'] ?>">
                    <label class="form-check-label" for="perm_<?= $permission['id'] ?>">
                        <?= $permission['permission_name'] ?> (<?= $permission['page_url'] ?>)
                    </label>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary mt-3">Update Permissions</button>
        </form>
    </div>
</body>
</html>
