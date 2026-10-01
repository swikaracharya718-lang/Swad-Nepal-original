<?php
require_once 'includes/functions.php';
$pageTitle = 'Login';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    flash_set('Welcome back, ' . $user['name'] . '!', 'success');
    redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php');
  } else {
    $errors[] = 'Invalid email or password.';
  }
}

require 'includes/header.php';
?>

<style>
  body.inner-page {
    background: #f8f1e7;
  }

  .signin-shell {
    min-height: calc(100vh - 160px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 42px 16px;
    background:
      linear-gradient(135deg, rgba(255, 213, 79, 0.18), rgba(255, 255, 255, 0) 30%),
      #f5efe7;
  }

  .signin-panel {
    width: min(1160px, 100%);
    display: grid;
    grid-template-columns: 1.18fr 0.92fr;
    background: #fffdf7;
    border: 1px solid rgba(17, 17, 17, 0.12);
    border-radius: 12px;
    box-shadow: 0 24px 60px rgba(43, 35, 24, 0.16), 6px 6px 0 rgba(29, 86, 65, 0.12);
    overflow: hidden;
  }

  .signin-visual {
    position: relative;
    min-height: 660px;
    background:
      linear-gradient(135deg, rgba(17, 17, 17, 0.9), rgba(17, 17, 17, 0.75)),
      url('https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
    padding: 38px 30px 28px;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-right: 1px solid rgba(255, 255, 255, 0.24);
  }

  .signin-visual::before {
    content: "";
    position: absolute;
    inset: 16px auto auto 16px;
    width: 120px;
    height: 120px;
    background: #ffcc4d;
    border: 4px solid #111111;
    box-shadow: 8px 8px 0 #ff5f39;
    transform: rotate(-12deg);
  }

  .signin-brand {
    position: relative;
    z-index: 1;
    font-size: 1.05rem;
    font-weight: 900;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: fit-content;
    padding: 10px 14px;
    border: 3px solid #ffcc4d;
    background: rgba(17, 17, 17, 0.7);
  }

  .signin-brand .brand-nepali {
    color: #ffcc4d;
  }

  .signin-brand .brand-divider {
    color: #fff;
    opacity: 0.75;
  }


  .signin-shell {
    min-height: calc(100vh - 160px);
    padding: 48px 18px;
    background: radial-gradient(circle at 12% 8%, rgba(239, 154, 45, 0.12), transparent 28%), #f5f1e9;
  }

  .signin-panel {
    display: block;
    width: min(460px, 100%);
    border: 1px solid #e8e0d5;
    border-radius: 14px;
    box-shadow: 0 18px 50px rgba(43, 35, 24, 0.12);
    transform: none;
  }

  .signin-visual {
    display: none;
  }

  .signin-form-wrap {
    padding: clamp(28px, 6vw, 44px);
    background: #fff;
  }

  .signin-card .topline .kicker {
    padding: 0;
    margin-bottom: 12px;
    color: #a9472e;
    background: transparent;
    border: 0;
    box-shadow: none;
  }

  .signin-card h2 {
    font-size: 2.2rem;
    line-height: 1.1;
    text-transform: none;
    letter-spacing: 0;
  }

  .signin-card .subtitle {
    margin: 9px 0 26px;
    color: #625d56;
    font-weight: 400;
  }

  .signin-card .form-label {
    margin-bottom: 7px;
    color: #3c3934;
    font-size: 0.88rem;
    font-weight: 600;
    letter-spacing: 0;
    text-transform: none;
  }

  .signin-card .btn-brand {
    margin-top: 6px;
    text-transform: none;
    letter-spacing: 0;
  }

  .signin-card .meta-link {
    margin-top: 22px;
    font-weight: 400;
  }

  .signin-card .meta-link a {
    color: #a9472e;
    text-decoration-thickness: 1px;
  }

  .signin-card .demo-note {
    display: none;
  }

  .signin-brand .brand-english {
    color: #fff;
  }

  .signin-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 26px;
  }

  .signin-badges span {
    background: #ffcc4d;
    color: #111111;
    border: 3px solid #111111;
    box-shadow: 4px 4px 0 #111111;
    padding: 9px 14px;
    font-size: 0.76rem;
    font-weight: 900;
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  .signin-food-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-top: 24px;
  }

  .food-pill {
    background: rgba(255, 255, 255, 0.08);
    border: 3px solid rgba(255, 255, 255, 0.8);
    color: #fff;
    padding: 10px 10px 8px;
    text-align: center;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    font-size: 0.7rem;
    box-shadow: 4px 4px 0 rgba(255, 255, 255, 0.25);
  }

  .signin-map-card {
    margin-top: 28px;
    background: rgba(255, 255, 255, 0.08);
    border: 3px solid #fff;
    box-shadow: 7px 7px 0 rgba(255, 255, 255, 0.18);
    padding: 10px;
    backdrop-filter: blur(2px);
  }

  .signin-map-card .mini-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #ffcc4d;
    margin-bottom: 8px;
  }

  .signin-map-card iframe {
    width: 100%;
    height: 170px;
    border: 0;
    display: block;
    filter: grayscale(0.2) contrast(1.1) brightness(1.1);
  }

  .signin-form-wrap {
    background: #fffdf8;
    padding: 40px clamp(26px, 4vw, 54px);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .signin-card {
    width: 100%;
    max-width: 440px;
  }

  .signin-card .topline {
    text-align: left;
    margin-bottom: 18px;
  }

  .signin-card .topline .kicker {
    display: inline-block;
    background: #ff5f39;
    color: #fff;
    border: 3px solid #111111;
    box-shadow: 4px 4px 0 #111111;
    padding: 8px 12px;
    font-size: 0.68rem;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    font-weight: 900;
    margin-bottom: 18px;
  }

  .signin-card h2 {
    margin: 0;
    font-size: clamp(2.2rem, 4vw, 3.3rem);
    line-height: 0.95;
    color: #111111;
    text-transform: uppercase;
    letter-spacing: -0.05em;
  }

  .signin-card .subtitle {
    text-align: left;
    color: #2e2a27;
    margin: 10px 0 24px;
    font-size: 0.98rem;
    font-weight: 700;
  }

  .signin-card .alert {
    margin-bottom: 18px;
    border: 3px solid #111111;
    border-radius: 0;
    box-shadow: 4px 4px 0 #111111;
    background: #ffd7d1;
    color: #111111;
    font-weight: 700;
  }

  .signin-card .form-group {
    margin-bottom: 18px;
  }

  .signin-card .form-label {
    display: block;
    margin-bottom: 10px;
    color: #111111;
    font-size: 0.72rem;
    font-weight: 900;
    letter-spacing: 0.12em;
    text-transform: uppercase;
  }

  .signin-card .form-control {
    width: 100%;
    border: 1px solid #d9d0c4;
    background: #fff;
    border-radius: 6px;
    min-height: 54px;
    padding: 13px 14px;
    font-size: 1rem;
    box-shadow: 0 2px 5px rgba(43, 35, 24, 0.05);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
    color: #111111;
  }

  .signin-card .form-control:focus {
    outline: 3px solid rgba(255, 95, 57, 0.2);
    outline-offset: 1px;
    box-shadow: none;
    border-color: #e95939;
  }

  .signin-card .btn-brand {
    width: 100%;
    min-height: 56px;
    border: 1px solid #df4e31;
    border-radius: 6px;
    background: #ff5f39;
    color: #fff;
    font-size: 1rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    font-weight: 900;
    box-shadow: 0 5px 12px rgba(185, 56, 39, 0.2);
    margin-top: 8px;
    transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
  }

  .signin-card .btn-brand:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(185, 56, 39, 0.24);
    background: #ff4d2e;
  }

  .signin-card .btn-brand:active {
    transform: translateY(0);
    box-shadow: 0 3px 7px rgba(185, 56, 39, 0.18);
  }

  .signin-card .helper-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin: 24px 0 16px;
    color: #111111;
    font-size: 0.7rem;
    font-weight: 900;
    letter-spacing: 0.14em;
    text-transform: uppercase;
  }

  .signin-card .helper-line::before,
  .signin-card .helper-line::after {
    content: "";
    flex: 1;
    height: 3px;
    background: #111111;
  }

  .google-signin-wrapper {
    display: flex;
    justify-content: center;
    margin: 0 0 18px;
  }

  .signin-card .meta-link {
    text-align: center;
    color: #292522;
    margin-top: 18px;
    font-size: 0.96rem;
    font-weight: 700;
  }

  .signin-card .meta-link a {
    color: #111111;
    text-decoration: underline;
    text-decoration-thickness: 3px;
    text-underline-offset: 3px;
  }

  .signin-card .meta-link a:hover {
    color: #ff5f39;
  }

  .signin-card .demo-note {
    text-align: center;
    margin-top: 18px;
    color: #3a3632;
    font-size: 0.8rem;
    font-weight: 700;
    border-top: 3px solid #111111;
    padding-top: 12px;
  }

  @media (max-width: 920px) {
    .signin-panel {
      grid-template-columns: 1fr;
      box-shadow: 0 18px 42px rgba(43, 35, 24, 0.14);
    }

    .signin-visual {
      min-height: 400px;
      border-right: none;
      border-bottom: 1px solid rgba(255, 255, 255, 0.24);
    }
  }

  @media (max-width: 575px) {
    .signin-shell {
      padding: 18px 10px;
    }

    .signin-form-wrap {
      padding: 22px 18px 20px;
    }

    .signin-visual {
      padding: 26px 18px 22px;
      min-height: 0;
    }

    .signin-feature h1 {
      font-size: 2.2rem;
      line-height: 1;
    }

    .signin-map-card iframe {
      height: 140px;
    }

    .signin-brand {
      letter-spacing: 0.12em;
      font-size: 0.8rem;
    }

    .signin-feature h1 {
      font-size: 2.4rem;
    }
  }
</style>

<div class="signin-shell">
  <div class="signin-panel">
    <div class="signin-visual">
      <div class="signin-brand"><span class="brand-nepali">स्वाद</span><span class="brand-divider">/</span><span class="brand-english">NEPAL</span></div>

      <div class="signin-feature">
        <span class="eyebrow">Fresh Finds</span>
        <h1>Discover local flavors near you.</h1>
        <p>Log in to save your favorite dining spots, write reviews, and keep exploring the best food experiences across Nepal.</p>

        <div class="signin-badges">
          <span>Curated eateries</span>
          <span>Local favorites</span>
          <span>Food stories</span>
        </div>

        <div class="signin-food-grid">
          <div class="food-pill">Momo</div>
          <div class="food-pill">Thakali</div>
          <div class="food-pill">Sel Roti</div>
          <div class="food-pill">Daal Bhat</div>
          <div class="food-pill">Chatamari</div>
          <div class="food-pill">Aalu Tama</div>
        </div>

        <div class="signin-map-card">
          <div class="mini-header">
            <span>Live spots</span>
            <span>4.8 ★</span>
          </div>
          <iframe
            src="https://www.google.com/maps?q=Kathmandu%20Nepal&z=12&output=embed"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Kathmandu map">
          </iframe>
        </div>
      </div>
    </div>

    <div class="signin-form-wrap">
      <div class="signin-card">
        <div class="topline">
          <span class="kicker">Welcome back</span>
          <h2>Sign in</h2>
        </div>

        <div class="subtitle">Save places and share your reviews.</div>

        <?php foreach ($errors as $e): ?>
          <div class="alert alert-danger"><?= clean($e) ?></div>
        <?php endforeach; ?>

        <form method="POST">
          <div class="form-group">
            <label class="form-label" for="login-email">Email address</label>
            <input id="login-email" type="email" name="email" class="form-control" autocomplete="email" value="<?= clean($_POST['email'] ?? '') ?>" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="login-password">Password</label>
            <input id="login-password" type="password" name="password" class="form-control" autocomplete="current-password" required>
          </div>

          <button type="submit" class="btn btn-brand">Login</button>
        </form>

        <?php if (google_enabled()): ?>
          <div class="helper-line"><span>or continue with</span></div>
          <div id="g_id_onload"
            data-client_id="<?= clean(google_client_id()) ?>"
            data-callback="handleCredentialResponse">
          </div>
          <div class="g_id_signin"
            data-type="standard"
            data-shape="rectangular"
            data-theme="outline"
            data-text="continue_with"
            data-size="large">
          </div>
        <?php endif; ?>

        <div class="meta-link">No account? <a href="register.php">Create one</a></div>
      </div>
    </div>
  </div>
</div>

<?php if (google_enabled()): ?>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <script>
    function handleCredentialResponse(response) {
      const token = response.credential;
      console.log('Encoded JWT ID token: ' + token);

      const alertTarget = document.querySelector('.signin-card');

      fetch('<?= base_url('auth/google.php') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            credential: token
          })
        })
        .then(function(result) {
          return result.json();
        })
        .then(function(data) {
          if (data.success) {
            window.location.href = data.redirect || 'index.php';
            return;
          }

          const message = data.message || 'Google sign-in failed.';
          const alert = document.createElement('div');
          alert.className = 'alert alert-danger mt-3';
          alert.textContent = message;
          alertTarget.insertBefore(alert, alertTarget.querySelector('.g_id_signin'));
        })
        .catch(function() {
          const alert = document.createElement('div');
          alert.className = 'alert alert-danger mt-3';
          alert.textContent = 'Something went wrong while signing in with Google.';
          alertTarget.insertBefore(alert, alertTarget.querySelector('.g_id_signin'));
        });
    }
  </script>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>