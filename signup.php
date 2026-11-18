<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
require __DIR__ . '/validation.php';

$errors = [];
$old = ['name' => '', 'username' => '', 'student_id' => '', 'age' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } else {
        foreach ($old as $key => $_) {
            $old[$key] = trim((string) ($_POST[$key] ?? ''));
        }
        $errors = validate_signup($_POST);

        if (!$errors) {
            try {
                $stmt = db()->prepare(
                    'INSERT INTO users (full_name, username, student_id, age, phone, password_hash)
                     VALUES (:name, :username, :sid, :age, :phone, :hash)'
                );
                $stmt->execute([
                    ':name'     => $old['name'],
                    ':username' => $old['username'],
                    ':sid'      => $old['student_id'],
                    ':age'      => (int) $old['age'],
                    ':phone'    => $old['phone'],
                    ':hash'     => password_hash((string) $_POST['password'], PASSWORD_DEFAULT),
                ]);
                header('Location: login.php?registered=1');
                exit;
            } catch (PDOException $ex) {
                if ($ex->getCode() === '23000') {
                    $msg = $ex->getMessage();
                    if (stripos($msg, 'username') !== false) {
                        $errors['username'] = 'That username is already taken.';
                    } else {
                        $errors['student_id'] = 'An account with this Student ID already exists.';
                    }
                } else {
                    error_log($ex->getMessage());
                    $errors['form'] = 'Something went wrong. Please try again later.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" <?= theme_attrs() ?>>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="card">
  <?= brand() ?>

  <?php if (!empty($errors['form'])): ?>
    <div class="message fail"><?= e($errors['form']) ?></div>
  <?php endif; ?>

  <form method="post" action="signup.php" data-validate novalidate>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div class="field">
      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" data-rule="name"
             value="<?= e($old['name']) ?>" placeholder="Enter Full Name" autocomplete="name">
      <div class="error"><?= e($errors['name'] ?? '') ?></div>
    </div>

    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" data-rule="username"
             value="<?= e($old['username']) ?>" placeholder="Enter username" autocomplete="username">
      <div class="hint">3-15 characters: letters, numbers or underscores.</div>
      <div class="error"><?= e($errors['username'] ?? '') ?></div>
    </div>

    <div class="field">
      <label for="student_id">Student ID</label>
      <input type="text" inputmode="numeric" maxlength="8" id="student_id" name="student_id" data-rule="student_id"
             value="<?= e($old['student_id']) ?>" placeholder="Enter Student ID">
      <div class="hint">Exactly 8 digits.</div>
      <div class="error"><?= e($errors['student_id'] ?? '') ?></div>
    </div>

    <div class="field">
      <label for="age">Age</label>
      <input type="text" inputmode="numeric" maxlength="3" id="age" name="age" data-rule="age"
             value="<?= e($old['age']) ?>" placeholder="Enter age">
      <div class="error"><?= e($errors['age'] ?? '') ?></div>
    </div>

    <div class="field">
      <label for="phone">Phone Number</label>
      <input type="tel" inputmode="numeric" maxlength="11" id="phone" name="phone" data-rule="phone"
             value="<?= e($old['phone']) ?>" placeholder="Enter phone number" autocomplete="tel">
      <div class="hint">Exactly 11 digits.</div>
      <div class="error"><?= e($errors['phone'] ?? '') ?></div>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" data-rule="password"
             placeholder="Enter your password" autocomplete="new-password">
      <div class="hint">At least 8 characters with upper, lower, number and symbol.</div>
      <div class="error"><?= e($errors['password'] ?? '') ?></div>
    </div>


    <div class="field">
      <label for="confirm">Confirm Password</label>
      <input type="password" id="confirm" name="confirm" data-rule="confirm"
             placeholder="Re-enter your password" autocomplete="new-password">
      <div class="error"><?= e($errors['confirm'] ?? '') ?></div>
    </div>

    <button class="submit" type="submit">SIGN IN</button>
  </form>

  <div class="divider">OR</div>
  <div class="alt">
    <a class="btn-outline" href="reset.php">Forgot Password</a>
    <a class="btn-outline" href="login.php">Log In</a>
  </div>
</div>
<script src="assets/validate.js"></script>
</body>
</html>