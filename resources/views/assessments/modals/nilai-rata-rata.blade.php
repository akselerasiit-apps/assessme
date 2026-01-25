<!-- Modal Nilai Rata-rata -->
<div class="modal modal-blur fade" id="averageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nilai Rata-rata per Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Overall Score Card -->
                <div class="card bg-primary text-white mb-3">
                    <div class="card-body text-center">
                        <div class="text-white-50 mb-2">Overall Compliance Score</div>
                        <div class="display-4 fw-bold" id="overallScore">0.00</div>
                        <div class="text-white-50 small">Average across all levels</div>
                    </div>
                </div>

                <!-- Level Scores Table -->
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Level</th>
                                    <th>Description</th>
                                    <th class="text-center">Activities</th>
                                    <th class="text-center">Assessed</th>
                                    <th class="text-end">Compliance Score</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="averageTableBody">
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                        <span class="text-muted ms-2">Loading average scores...</span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="2">Total / Average</td>
                                    <td class="text-center" id="totalActivitiesAvg">0</td>
                                    <td class="text-center" id="totalAssessedAvg">0</td>
                                    <td class="text-end" id="totalComplianceAvg">0.00</td>
                                    <td class="text-center">-</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Score Distribution Chart -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Score Distribution</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="averageScoreChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Rating Distribution -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Rating Distribution</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="text-center">
                                    <div class="h3 mb-0 text-success" id="ratingF">0</div>
                                    <div class="text-muted small">F - Fully</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-center">
                                    <div class="h3 mb-0 text-info" id="ratingL">0</div>
                                    <div class="text-muted small">L - Largely</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-center">
                                    <div class="h3 mb-0 text-warning" id="ratingP">0</div>
                                    <div class="text-muted small">P - Partially</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-center">
                                    <div class="h3 mb-0 text-danger" id="ratingN">0</div>
                                    <div class="text-muted small">N - Not Achieved</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-center">
                                    <div class="h3 mb-0 text-secondary" id="ratingNA">0</div>
                                    <div class="text-muted small">N/A</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-outline-primary" onclick="printAverageScore()">
                    <i class="ti ti-printer me-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let averageScoreChartInstance = null;

// Show average modal
function showAverageModal() {
    $('#averageModal').modal('show');
    loadAverageScore();
}

// Load average score
function loadAverageScore() {
    const assessmentId = $('input[name="assessment_id"]').val();
    const gamoId = $('#gamoSelector').val();
    
    $('#averageTableBody').html(`
        <tr>
            <td colspan="6" class="text-center">
                <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                <span class="text-muted ms-2">Loading average scores...</span>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: `/assessments/${assessmentId}/gamo/${gamoId}/average-score`,
        success: function(response) {
            renderAverageTable(response);
            renderAverageChart(response);
            updateRatingDistribution(response);
        },
        error: function() {
            $('#averageTableBody').html(`
                <tr>
                    <td colspan="6" class="text-center text-danger">Error loading average scores</td>
                </tr>
            `);
        }
    });
}

// Render average table
function renderAverageTable(data) {
    const levelDescriptions = {
        2: 'Managed',
        3: 'Established',
        4: 'Predictable',
        5: 'Optimizing'
    };
    
    let html = '';
    let totalActivities = 0;
    let totalAssessed = 0;
    let sumCompliance = 0;
    let levelsWithData = 0;
    
    for (let level = 2; level <= 5; level++) {
        const levelData = data.levels[level] || {};
        const activities = levelData.total_activities || 0;
        const assessed = levelData.assessed || 0;
        const compliance = levelData.compliance || 0;
        
        if (activities > 0) levelsWithData++;
        
        totalActivities += activities;
        totalAssessed += assessed;
        sumCompliance += compliance;
        
        const complianceClass = compliance >= 0.67 ? 'text-success' : (compliance >= 0.33 ? 'text-warning' : 'text-danger');
        const statusIcon = compliance >= 0.67 ? 'ti-check' : (compliance >= 0.33 ? 'ti-dots' : 'ti-x');
        const statusClass = compliance >= 0.67 ? 'bg-success' : (compliance >= 0.33 ? 'bg-warning' : 'bg-danger');
        
        html += `
            <tr>
                <td><strong>Level ${level}</strong></td>
                <td>${levelDescriptions[level]}</td>
                <td class="text-center">${activities}</td>
                <td class="text-center">${assessed}</td>
                <td class="text-end ${complianceClass} fw-bold">${compliance.toFixed(2)}</td>
                <td class="text-center">
                    <span class="avatar avatar-xs ${statusClass}">
                        <i class="ti ${statusIcon}"></i>
                    </span>
                </td>
            </tr>
        `;
    }
    
    $('#averageTableBody').html(html);
    
    // Update totals
    const avgCompliance = levelsWithData > 0 ? (sumCompliance / levelsWithData) : 0;
    $('#totalActivitiesAvg').text(totalActivities);
    $('#totalAssessedAvg').text(totalAssessed);
    $('#totalComplianceAvg').text(avgCompliance.toFixed(2));
    $('#overallScore').text(avgCompliance.toFixed(2));
}

// Render average chart
function renderAverageChart(data) {
    const ctx = document.getElementById('averageScoreChart');
    if (!ctx) return;
    
    // Destroy previous chart if exists
    if (averageScoreChartInstance) {
        averageScoreChartInstance.destroy();
    }
    
    const labels = [];
    const scores = [];
    
    for (let level = 2; level <= 5; level++) {
        labels.push('Level ' + level);
        scores.push(data.levels[level]?.compliance || 0);
    }
    
    averageScoreChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Compliance Score',
                data: scores,
                borderColor: 'rgba(32, 107, 196, 1)',
                backgroundColor: 'rgba(32, 107, 196, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1,
                    ticks: {
                        callback: function(value) {
                            return (value * 100) + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Score: ' + (context.parsed.y * 100).toFixed(0) + '%';
                        }
                    }
                }
            }
        }
    });
}

// Update rating distribution
function updateRatingDistribution(data) {
    const ratings = data.rating_distribution || {};
    $('#ratingF').text(ratings.F || 0);
    $('#ratingL').text(ratings.L || 0);
    $('#ratingP').text(ratings.P || 0);
    $('#ratingN').text(ratings.N || 0);
    $('#ratingNA').text(ratings['N/A'] || 0);
}

// Print average score
function printAverageScore() {
    window.print();
}

// Clean up chart when modal is closed
$('#averageModal').on('hidden.bs.modal', function() {
    if (averageScoreChartInstance) {
        averageScoreChartInstance.destroy();
        averageScoreChartInstance = null;
    }
});
</script>
