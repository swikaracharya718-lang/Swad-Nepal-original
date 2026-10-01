<?php
require_once 'includes/functions.php';
$pageTitle = 'About';

$totalRestaurants = $pdo->query("SELECT COUNT(*) FROM restaurants WHERE is_demo = 0")->fetchColumn();
$totalReviews     = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
$totalUsers       = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();

require 'includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <p class="section-kicker text-warning mb-2">Our story</p>
    <h1 class="mb-2">Made for the joy of eating local.</h1>
    <p class="mb-0">SWAD NEPAL connects curious eaters with the everyday places that make Nepal taste unforgettable.</p>
  </div>
</section>

<main class="container py-5">

  <!-- Mission -->
  <div class="row align-items-center g-5 mb-5">
    <div class="col-lg-6">
      <div class="about-image">
        <div>
          <p class="section-kicker text-warning">From street stalls to family kitchens</p>
          <h2 class="mb-0">Every good meal has a story.</h2>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <p class="section-kicker mb-2">Why Swad Nepal?</p>
      <h2 class="section-heading mb-3">A more local way to discover food.</h2>
      <p class="text-muted">The best meals in Nepal are rarely on Google Maps. They're the momo stall your college friend swears by, the Thakali kitchen that's been feeding the neighbourhood for 20 years, the chatpat cart that only opens at 4 PM.</p>
      <p class="text-muted mb-0">SWAD NEPAL was built to capture those recommendations — honest, community-driven, and focused on the food that actually matters to people who live here.</p>
    </div>
  </div>

  <!-- Live stats -->
  <div class="about-stats row g-3 mb-5 text-center">
    <div class="col-4">
      <div class="card p-3 p-md-4" style="border-top:3px solid var(--saffron);">
        <div class="stat-number"><?= $totalRestaurants ?>+</div>
        <div class="text-muted small">Verified food spots</div>
      </div>
    </div>
    <div class="col-4">
      <div class="card p-3 p-md-4" style="border-top:3px solid var(--red);">
        <div class="stat-number"><?= $totalReviews ?>+</div>
        <div class="text-muted small">Community reviews</div>
      </div>
    </div>
    <div class="col-4">
      <div class="card p-3 p-md-4" style="border-top:3px solid var(--green);">
        <div class="stat-number"><?= $totalUsers ?>+</div>
        <div class="text-muted small">Registered users</div>
      </div>
    </div>
  </div>

  <!-- How it works -->
  <div class="mb-5">
    <p class="section-kicker mb-1">How it works</p>
    <h2 class="section-heading mb-4">Simple. Honest. Local.</h2>
    <div class="row g-4">
      <div class="col-md-3 col-6">
        <div class="card feature-card text-center">
          <div class="feature-icon mx-auto mb-3">01</div>
          <h5>Search</h5>
          <p class="text-muted small mb-0">Find food by dish name, city, category, or budget.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="card feature-card text-center">
          <div class="feature-icon mx-auto mb-3">02</div>
          <h5>Discover</h5>
          <p class="text-muted small mb-0">Read real reviews with photos from people who actually ate there.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="card feature-card text-center">
          <div class="feature-icon mx-auto mb-3">03</div>
          <h5>Review</h5>
          <p class="text-muted small mb-0">Share your experience — star rating, text, and a photo.</p>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="card feature-card text-center">
          <div class="feature-icon mx-auto mb-3">04</div>
          <h5>Suggest</h5>
          <p class="text-muted small mb-0">Know a hidden gem? Suggest it and help the community grow.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Values -->
  <div class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="card feature-card">
        <h4>Local first</h4>
        <p class="text-muted mb-0">We celebrate small eateries and regional flavours — the places that define a neighbourhood's identity.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card feature-card">
        <h4>Genuine reviews</h4>
        <p class="text-muted mb-0">No paid placements. Every listing is here because a real person recommends it. Reviews come from verified accounts only.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card feature-card">
        <h4>Community powered</h4>
        <p class="text-muted mb-0">Anyone can suggest a place. Every thoughtful review helps someone find their next memorable meal.</p>
      </div>
    </div>
  </div>

  <!-- CTA -->
  <div class="text-center py-4" style="border-top:1px solid var(--line);">
    <p class="section-kicker mb-2">Ready to explore?</p>
    <h3 class="mb-3">Find your next favourite bite.</h3>
    <div class="d-flex flex-wrap gap-2 justify-content-center">
      <a href="<?= base_url('listings.php') ?>" class="btn btn-brand px-4">Explore food spots</a>
      <a href="<?= base_url('suggest_place.php') ?>" class="btn btn-outline-brand px-4">Suggest a place</a>
    </div>
  </div>

</main>

<style>
  .stat-number { font-family: 'Libre Baskerville', serif; font-size: clamp(1.8rem, 4vw, 2.6rem); color: var(--green); font-weight: 700; line-height: 1; margin-bottom: .25rem; }
  .feature-card { text-align: left; }
  @media (max-width: 575px) { .about-stats .card { padding: .85rem !important; } }
</style>

<?php require 'includes/footer.php'; ?>
