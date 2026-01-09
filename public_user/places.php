<?php
/**
 * Restaurants and Cafes Listing Page
 * Shows all visible restaurants and cafes with crowd levels
 */

session_start();

// Redirect logged-in users to their dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: /admin/dashboard.php');
    } else {
        header('Location: /owner/dashboard.php');
    }
    exit();
}

require_once __DIR__ . '/../config/db.php';

$conn = getDBConnection();

// Get all visible places with their crowd status
$query = "SELECT r.id, r.name, r.type, r.location_in_airport, r.description, r.logo,
                 COALESCE(cs.crowd_count, 0) as crowd_count
          FROM restaurants r
          LEFT JOIN crowd_status cs ON r.id = cs.place_id
          WHERE r.is_visible = 1
          ORDER BY r.type, r.name";
$result = $conn->query($query);

// Auto-update stale crowd counts for all places
require_once __DIR__ . '/../includes/functions.php';
$place_ids = [];
while ($row = $result->fetch_assoc()) {
    $place_ids[] = $row['id'];
}
// Reset result pointer
$result->data_seek(0);

// Auto-update each place's crowd count
foreach ($place_ids as $place_id) {
    autoUpdateStaleCrowd($place_id);
}

// Re-fetch with updated counts
$result = $conn->query($query);

$places = [];
while ($row = $result->fetch_assoc()) {
    $places[] = $row;
}

$conn->close();

$page_title = 'Restaurants & Cafes - Traveler Companion';
$include_charts = false;
$extra_js = ['/../assets/js/crowd-polling.js'];
include __DIR__ . '/../includes/header.php';
?>

<div class="hero-section mb-5">
    <div class="text-center py-5">
        <h1 class="display-4">Restaurants & Coffee Shops</h1>
        <p class="lead">Explore dining options and check real-time crowd levels at Hail Airport</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8 mx-auto">
        <input type="text" class="form-control" id="searchInput" placeholder="Search restaurants or cafes...">
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#restaurants" type="button">Restaurants</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#cafes" type="button">Coffee Shops</button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content">
    <!-- Restaurants Tab -->
    <div class="tab-pane fade show active" id="restaurants">
        <h2 class="section-title">Restaurants</h2>
        <div class="row" id="restaurantsList">
            <?php foreach ($places as $place): ?>
                <?php if ($place['type'] === 'restaurant'): ?>
                    <div class="col-md-4 mb-4 place-card" data-name="<?php echo strtolower($place['name']); ?>">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <?php if (!empty($place['logo'])): ?>
                                    <div class="text-center mb-3">
                                        <img src="/uploads/<?php echo htmlspecialchars($place['logo']); ?>" 
                                             alt="<?php echo htmlspecialchars($place['name']); ?> logo" 
                                             style="max-height: 80px; max-width: 150px; object-fit: contain;"
                                             onerror="this.style.display='none'">
                                    </div>
                                <?php endif; ?>
                                <h5 class="card-title"><?php echo htmlspecialchars($place['name']); ?></h5>
                                <p class="card-text">
                                    <small class="text-muted"><?php echo htmlspecialchars($place['location_in_airport'] ?? 'Location TBD'); ?></small>
                                </p>
                                <div class="crowd-status mb-3" data-place-id="<?php echo $place['id']; ?>">
                                    <?php
                                    $count = (int)$place['crowd_count'];
                                    if ($count <= 8) {
                                        $status = 'Light';
                                        $color = 'success';
                                    } elseif ($count <= 20) {
                                        $status = 'Moderate';
                                        $color = 'warning';
                                    } else {
                                        $status = 'Busy';
                                        $color = 'danger';
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>">
                                        <?php echo $status; ?> (<?php echo $count; ?> people)
                                    </span>
                                </div>
                                <a href="/public_user/place.php?id=<?php echo $place['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Cafes Tab -->
    <div class="tab-pane fade" id="cafes">
        <h2 class="section-title">Coffee Shops</h2>
        <div class="row" id="cafesList">
            <?php foreach ($places as $place): ?>
                <?php if ($place['type'] === 'cafe'): ?>
                    <div class="col-md-4 mb-4 place-card" data-name="<?php echo strtolower($place['name']); ?>">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <?php if (!empty($place['logo'])): ?>
                                    <div class="text-center mb-3">
                                        <img src="/uploads/<?php echo htmlspecialchars($place['logo']); ?>" 
                                             alt="<?php echo htmlspecialchars($place['name']); ?> logo" 
                                             style="max-height: 80px; max-width: 150px; object-fit: contain;"
                                             onerror="this.style.display='none'">
                                    </div>
                                <?php endif; ?>
                                <h5 class="card-title"><?php echo htmlspecialchars($place['name']); ?></h5>
                                <p class="card-text">
                                    <small class="text-muted"><?php echo htmlspecialchars($place['location_in_airport'] ?? 'Location TBD'); ?></small>
                                </p>
                                <div class="crowd-status mb-3" data-place-id="<?php echo $place['id']; ?>">
                                    <?php
                                    $count = (int)$place['crowd_count'];
                                    if ($count <= 8) {
                                        $status = 'Light';
                                        $color = 'success';
                                    } elseif ($count <= 20) {
                                        $status = 'Moderate';
                                        $color = 'warning';
                                    } else {
                                        $status = 'Busy';
                                        $color = 'danger';
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $color; ?>">
                                        <?php echo $status; ?> (<?php echo $count; ?> people)
                                    </span>
                                </div>
                                <a href="/public_user/place.php?id=<?php echo $place['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function filterPlaces() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.place-card');
    
    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        if (name.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Auto-filter on input
document.getElementById('searchInput').addEventListener('input', filterPlaces);

// Allow Enter key to trigger search
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        filterPlaces();
    }
});

// Handle hash navigation (e.g., /places.php#restaurants or /places.php#cafes)
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash === '#restaurants' || hash === '#cafes') {
        const tabId = hash.substring(1); // Remove the #
        const tabButton = document.querySelector(`button[data-bs-target="#${tabId}"]`);
        if (tabButton) {
            // Use Bootstrap's tab API to switch tabs
            const tab = new bootstrap.Tab(tabButton);
            tab.show();
        }
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

