<?php
/**
 * Landing Page
 * Welcome page with navigation to restaurants and cafes
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

$page_title = 'Traveler Companion - Airport Dining Guide';
include __DIR__ . '/includes/header.php';
?>

<div class="hero-section mb-5">
    <div class="text-center py-5">
        <h1 class="display-4 mb-4">Welcome to Traveler Companion</h1>
        <p class="lead mb-5">Monitor crowd levels and explore dining options at Hail Airport</p>
        
        <div class="row justify-content-center mt-5">
            <div class="col-md-5 mb-4">
                <div class="card h-100 shadow-lg border-0" style="transition: transform 0.3s ease;">
                    <div class="card-body text-center p-5">
                        
                        <h3 class="card-title mb-3">Restaurants</h3>
                        <p class="card-text text-muted mb-4">Discover delicious dining options and check real-time crowd levels</p>
                        <a href="/public_user/places.php#restaurants" class="btn btn-primary btn-lg">Explore Restaurants</a>
                    </div>
                </div>
            </div>
            <div class="col-md-5 mb-4">
                <div class="card h-100 shadow-lg border-0" style="transition: transform 0.3s ease;">
                    <div class="card-body text-center p-5">
                        
                        <h3 class="card-title mb-3">Coffee Shops</h3>
                        <p class="card-text text-muted mb-4">Find your perfect coffee spot and see current wait times</p>
                        <a href="/public_user/places.php#cafes" class="btn btn-primary btn-lg">Explore Cafes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-8 mx-auto text-center">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-3">Features</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-people text-primary" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.629 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.008.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                            </svg>
                        </div>
                        <h6>Real-Time Crowd Levels</h6>
                        <p class="small text-muted">See current crowd status at each location</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-map text-primary" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.5.5 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103M7 1l-4 1v12l4-1zm7 0v12l-4-1V2z"/>
                            </svg>
                        </div>
                        <h6>Location Information</h6>
                        <p class="small text-muted">Find exact locations within the airport</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-menu-button-wide text-primary" viewBox="0 0 16 16">
                                <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0h13A1.5 1.5 0 0 1 16 1.5v2A1.5 1.5 0 0 1 14.5 5h-13A1.5 1.5 0 0 1 0 3.5zM1.5 1a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.5-.5z"/>
                                <path d="M2 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m0 4A1.5 1.5 0 0 1 3.5 5h9a1.5 1.5 0 0 1 1.5 1.5v1A1.5 1.5 0 0 1 12.5 9h-9A1.5 1.5 0 0 1 2 7.5zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
                            </svg>
                        </div>
                        <h6>Menu Details</h6>
                        <p class="small text-muted">Browse menus and prices before you visit</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-5px);
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>

