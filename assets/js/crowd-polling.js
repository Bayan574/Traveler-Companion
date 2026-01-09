/**
 * Crowd Polling JavaScript
 * Auto-refreshes crowd counts every 10 seconds
 */

document.addEventListener('DOMContentLoaded', function() {
    const crowdStatusElements = document.querySelectorAll('.crowd-status[data-place-id]');
    
    if (crowdStatusElements.length === 0) {
        return; // No crowd status elements to update
    }
    
    // Function to update crowd counts
    function updateCrowdCounts() {
        const placeIds = Array.from(crowdStatusElements).map(el => el.getAttribute('data-place-id'));
        
        // Create endpoint URL
        fetch('/includes/crowd-endpoint.php?places=' + placeIds.join(','))
            .then(response => response.json())
            .then(data => {
                // Update each crowd status element
                crowdStatusElements.forEach(function(element) {
                    const placeId = element.getAttribute('data-place-id');
                    if (data[placeId]) {
                        const count = parseInt(data[placeId]);
                        let status, color;
                        
                        if (count <= 8) {
                            status = 'Light';
                            color = 'success';
                        } else if (count <= 20) {
                            status = 'Moderate';
                            color = 'warning';
                        } else {
                            status = 'Busy';
                            color = 'danger';
                        }
                        
                        // Update badge
                        const badge = element.querySelector('.badge');
                        if (badge) {
                            badge.className = 'badge bg-' + color;
                            badge.textContent = status + ' (' + count + ' people)';
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error updating crowd counts:', error);
            });
    }
    
    // Update immediately on load, then every 10 seconds
    updateCrowdCounts();
    setInterval(updateCrowdCounts, 10000); // 10 seconds
});

