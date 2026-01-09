<?php
/**
 * Admin Dashboard
 * Shows statistics and charts for crowd levels
 */

require_once __DIR__ . '/../includes/functions.php';
checkAuth('admin');

require_once __DIR__ . '/../config/db.php';

$conn = getDBConnection();

// Get all place IDs first to auto-update stale crowd counts
$place_ids_query = "SELECT id FROM restaurants";
$place_ids_result = $conn->query($place_ids_query);
$place_ids = [];
while ($row = $place_ids_result->fetch_assoc()) {
    $place_ids[] = $row['id'];
}

// Auto-update stale crowd counts for all places
foreach ($place_ids as $place_id) {
    autoUpdateStaleCrowd($place_id);
}

// Get crowd statistics (after auto-update)
$query = "SELECT r.name, COALESCE(cs.crowd_count, 0) as crowd_count, r.type
          FROM restaurants r
          LEFT JOIN crowd_status cs ON r.id = cs.place_id
          ORDER BY r.name";
$result = $conn->query($query);

$places_data = [];
$total_places = 0;
$total_people = 0;
$light_count = 0;
$moderate_count = 0;
$busy_count = 0;


while ($row = $result->fetch_assoc()) {
    $places_data[] = $row;
    $total_places++;
    $total_people += (int)$row['crowd_count'];
    
    $count = (int)$row['crowd_count'];
    if ($count <= 8) $light_count++;
    elseif ($count <= 20) $moderate_count++;
    else $busy_count++;
}

$conn->close();

$page_title = 'Admin Dashboard';
$include_charts = true;
include __DIR__ . '/../includes/header.php';
?>

<h1>Admin Dashboard</h1>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Total Places</h5>
                <h2><?php echo $total_places; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Total People</h5>
                <h2><?php echo $total_people; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Light Traffic</h5>
                <h2 class="text-success"><?php echo $light_count; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Busy Places</h5>
                <h2 class="text-danger"><?php echo $busy_count; ?></h2>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3">
    <div class="card text-center">
        <div class="card-body">
            <h5 class="card-title">Medium Places</h5>
            <h2 class="text-warning"><?php echo $moderate_count; ?></h2>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Crowd Distribution</h5>
            </div>
            <div class="card-body" style="height: 400px; position: relative;">
                <canvas id="crowdChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Places by Crowd Level</h5>
            </div>
            <div class="card-body" style="height: 400px; position: relative;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>


<script>
// Wait for Chart.js to be loaded
function waitForChart(callback, maxAttempts = 50) {
    if (typeof Chart !== 'undefined') {
        callback();
    } else if (maxAttempts > 0) {
        setTimeout(function() {
            waitForChart(callback, maxAttempts - 1);
        }, 100);
    } else {
        console.error('Chart.js failed to load after multiple attempts');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    waitForChart(initCharts);
});

function initCharts() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }

    // Prepare data
    const placesLabels = [<?php 
        if (!empty($places_data)) {
            echo implode(',', array_map(function($p) { return "'" . addslashes($p['name']) . "'"; }, $places_data));
        }
    ?>];
    
    const placesData = [<?php 
        if (!empty($places_data)) {
            echo implode(',', array_map(function($p) { return $p['crowd_count']; }, $places_data));
        }
    ?>];
    
    const placesColors = [
        <?php 
        if (!empty($places_data)) {
            foreach ($places_data as $p) {
                $count = (int)$p['crowd_count'];
                if ($count <= 8) echo "'rgba(40, 167, 69, 0.8)',";
                elseif ($count <= 20) echo "'rgba(255, 193, 7, 0.8)',";
                else echo "'rgba(220, 53, 69, 0.8)',";
            }
        }
        ?>
    ];

    // Crowd Distribution Chart
    const crowdCtx = document.getElementById('crowdChart');
    if (crowdCtx) {
        new Chart(crowdCtx, {
            type: 'bar',
            data: {
                labels: placesLabels.length > 0 ? placesLabels : ['No Data'],
                datasets: [{
                    label: 'Crowd Count',
                    data: placesData.length > 0 ? placesData : [0],
                    backgroundColor: placesColors.length > 0 ? placesColors : ['rgba(128, 128, 128, 0.8)']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Light (0-8)', 'Moderate (9-20)', 'Busy (21+)'],
                datasets: [{
                    data: [<?php echo $light_count; ?>, <?php echo $moderate_count; ?>, <?php echo $busy_count; ?>],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

