<?php
require_once 'includes/functions.php';
$pageTitle = 'Sign Up';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name     = trim($_POST['name'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm  = $_POST['confirm_password'] ?? '';

  if ($name === '' || $email === '' || $password === '') $errors[] = 'All fields are required.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL))        $errors[] = 'Please enter a valid email address.';
  if (strlen($password) < 6)                             $errors[] = 'Password must be at least 6 characters.';
  if ($password !== $confirm)                            $errors[] = 'Passwords do not match.';

  if (empty($errors)) {
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) $errors[] = 'An account with this email already exists.';
  }

  if (empty($errors)) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')")->execute([$name, $email, $hash]);
    flash_set('Account created! You can now log in.', 'success');
    redirect('login.php');
  }
}

require 'includes/header.php';
?>

<style>
  .register-shell {
    min-height: calc(100vh - 160px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 16px;
    background: radial-gradient(circle at 12% 8%, rgba(239,154,45,.12), transparent 28%), #f5f1e9;
  }
  .register-panel {
    width: min(480px, 100%);
    background: #fff;
    border: 1px solid #e8e0d5;
    border-radius: 14px;
    box-shadow: 0 18px 50px rgba(43,35,24,.12);
    overflow: hidden;
  }
  .register-form-wrap { padding: clamp(28px, 6vw, 44px); }
  .register-card .eyebrow {
    display: inline-block;
    margin-bottom: 10px;
    color: #a9472e;
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
  }
  .register-card h2 { margin: 0 0 6px; color: #1c3329; font-size: 2rem; line-height: 1.15; }
  .register-card .subtitle { margin: 0 0 22px; color: #625d56; }
  .register-card .form-group { margin-bottom: 16px; }
  .register-card .form-label { margin-bottom: 6px; color: #3c3934; font-size: .9rem; font-weight: 600; }
  .register-card .form-control { min-height: 48px; border: 1px solid #d9d0c4; border-radius: 6px; }
  .register-card .form-control:focus { border-color: #e95939; box-shadow: 0 0 0 3px rgba(255,95,57,.15); }
  .register-card .btn-brand { width: 100%; min-height: 50px; border-radius: 6px; }
  .register-card .meta-link { margin: 20px 0 0; color: #625d56; text-align: center; }
  .register-card .meta-link a { color: #a9472e; }
  @media (max-width: 575px) { .register-shell { padding: 28px 14px; } }
</style>

<div class="register-shell">
  <div class="register-panel">
    <div class="register-form-wrap">
      <div class="register-card">
        <span class="eyebrow">Join Swad Nepal</span>
        <h2>Create an account</h2>
        <div class="subtitle">Start exploring Nepal's best food spots.</div>

        <?php foreach ($errors as $e): ?>
          <div class="alert alert-danger"><?= clean($e) ?></div>
        <?php endforeach; ?>

        <form method="POST">
          <div class="form-group">
            <label class="form-label" for="register-name">Full name</label>
            <input id="register-name" type="text" name="name" class="form-control" autocomplete="name" value="<?= clean($_POST['name'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="register-email">Email address</label>
            <input id="register-email" type="email" name="email" class="form-control" autocomplete="email" value="<?= clean($_POST['email'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="register-password">Password</label>
            <input id="register-password" type="password" name="password" class="form-control" autocomplete="new-password" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="confirm-password">Confirm password</label>
            <input id="confirm-password" type="password" name="confirm_password" class="form-control" autocomplete="new-password" required>
          </div>
          <button type="submit" class="btn btn-brand w-100">Create account</button>
        </form>

        <p class="meta-link">Already have an account? <a href="login.php">Log in</a></p>
      </div>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>
