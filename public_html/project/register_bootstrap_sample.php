<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<div class="container-fluid">
    <form onsubmit="return validate(this)" method="POST">
        
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" required />
        </div>

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input class="form-control" type="text" name="username" required maxlength="30" />
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required minlength="8" />
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm</label>
            <input class="form-control" type="password" name="confirm" required minlength="8" />
        </div>

        <input class="btn btn-primary" type="submit" value="Register" />
    </form>
</div>

<script>
function validate(form) {
    let password = form.password.value;
    let confirm = form.confirm.value;

    if (password.length < 8) {
        alert("Password must be at least 8 characters long");
        return false;
    }

    if (password !== confirm) {
        alert("Passwords must match");
        return false;
    }

    return true;
}
</script>

<?php
if (isset($_POST["email"], $_POST["password"], $_POST["confirm"], $_POST["username"])) {

    $email = trim(se($_POST, "email", "", false));
    $password = trim(se($_POST, "password", "", false));
    $confirm = trim(se($_POST, "confirm", "", false));
    $username = trim(se($_POST, "username", "", false));

    $hasError = false;

    if (empty($email)) {
        flash("Email must not be empty", "danger");
        $hasError = true;
    }

    $email = sanitize_email($email);

    if (!is_valid_email($email)) {
        flash("Invalid email address", "danger");
        $hasError = true;
    }

    if (!is_valid_username($username)) {
        flash("Username must be 3-16 characters (a-z, 0-9, _, -)", "danger");
        $hasError = true;
    }

    if (!is_valid_password($password)) {
        flash("Password must be at least 8 characters", "danger");
        $hasError = true;
    }

    if ($password !== $confirm) {
        flash("Passwords must match", "danger");
        $hasError = true;
    }

    if (!$hasError) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();

        $stmt = $db->prepare("INSERT INTO Users (email, password, username) 
                             VALUES(:email, :password, :username)");

        try {
            $stmt->execute([
                ":email" => $email,
                ":password" => $hash,
                ":username" => $username
            ]);

            flash("Successfully registered!", "success");

            // Optional redirect after register
            die(header("Location: " . get_url("login.php")));

        } catch (PDOException $e) {
            users_check_duplicate($e->errorInfo);
        }
    }
}
?>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>