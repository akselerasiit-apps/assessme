<!-- Summary Sub-tabs -->
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#summaryPenilaian" role="tab">
            <i class="ti ti-clipboard-check me-2"></i>Penilaian
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#summaryProgress" role="tab">
            <i class="ti ti-trending-up me-2"></i>Progress Kapabilitas
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- Tab Penilaian -->
    <div class="tab-pane active show" id="summaryPenilaian" role="tabpanel">
        <!-- Statistics Cards -->
        <div class="row row-cards mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total Activities</div>
                        </div>
                        <div class="h1 mb-0" id="statTotalActivities">0</div>
                        <div class="text-muted small">Across all levels</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Dinilai</div>
                        <div class="h1 mb-0 text-success" id="statAssessed">0</div>
                        <div class="text-muted small">
                            <span id="statAssessedPercent">0%</span> completion
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Belum Dinilai</div>
                        <div class="h1 mb-0 text-warning" id="statNotAssessed">0</div>
                        <div class="text-muted small">Remaining</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Avg Compliance</div>
                        <div class="h1 mb-0 text-primary" id="statAvgCompliance">0.00</div>
                        <div class="text-muted small">Overall score</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Summary Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Summary per GAMO Objective</h3>
                <div class="card-actions">
                    <button class="btn btn-outline-primary btn-sm" onclick="exportSummary()">
                        <i class="ti ti-download me-2"></i>Export
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>GAMO</th>
                            <th>Name</th>
                            <th class="text-center">Total Activities</th>
                            <th class="text-center">Dinilai</th>
                            <th class="text-center">Progress</th>
                            <th class="text-center">Target</th>
                            <th class="text-center">Current</th>
                            <th class="text-center">Gap</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody id="summaryPenilaianTableBody">
                        <tr>
                            <td colspan="9" class="text-center">
                                <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                <span class="text-muted ms-2">Loading summary...</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="2">Average</td>
                            <td class="text-center" id="totalActivities">0</td>
                            <td class="text-center" id="totalAssessed">0</td>
                            <td class="text-center" id="totalProgress">0%</td>
                            <td class="text-center" id="avgTargetLevel">0.00</td>
                            <td class="text-center" id="avgCurrentLevel">0.00</td>
                            <td class="text-center" id="avgGap">-</td>
                            <td class="text-end"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
                            {{-- <td class="text-center" id="totalNotAssessed">0</td>
                            <td class="text-center" id="totalNA">0</td>
                            <td class="text-center" id="totalN">0</td>
                            <td class="text-center" id="totalP">0</td>
                            <td class="text-center" id="totalL">0</td>
                            <td class="text-center" id="totalF">0</td>
                            <td class="text-end" id="totalCompliance">0.00</td> --}}
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab Progress Kapabilitas -->
    <div class="tab-pane" id="summaryProgress" role="tabpanel">
        <div class="mb-3">
            <h3 class="mb-1">Summary Progress Kapabilitas</h3>
            <p class="text-muted">Rekapitulasi dari Activites Kapabilitas Asesment</p>
        </div>
        
        <!-- Progress Cards -->
        <div class="row row-cards mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-blue-lt me-3">
                                <i class="ti ti-clipboard-list"></i>
                            </span>
                            <div>
                                <div class="h1 mb-0" id="progressTotalActivities">0</div>
                                <div class="text-muted">Total Activities</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-cyan-lt me-3">
                                <i class="ti ti-clipboard-check"></i>
                            </span>
                            <div>
                                <div class="h1 mb-0 text-cyan" id="progressAssessedActivities">0</div>
                                <div class="text-muted">Total Activities Sudah Dinilai</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-blue me-3 text-white">
                                <i class="ti ti-chart-line"></i>
                            </span>
                            <div>
                                <div class="h1 mb-0 text-blue" id="progressPercentage">0%</div>
                                <div class="text-muted">Progress Kapabilitas Asesment</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Progress Table -->
        <div class="mb-3">
            <h3 class="mb-1">Summary Progress</h3>
            <p class="text-muted">Rekapitulasi dari Progress Kapabilitas Asesment</p>
        </div>
        
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center align-middle" style="min-width: 200px;">
                                <div>Governance and Management</div>
                                <div>Objectives</div>
                            </th>
                            <th colspan="5" class="text-center bg-blue-lt">Jumlah Activities</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="min-width: 100px;">Level 2</th>
                            <th class="text-center" style="min-width: 100px;">Level 3</th>
                            <th class="text-center" style="min-width: 100px;">Level 4</th>
                            <th class="text-center" style="min-width: 100px;">Level 5</th>
                            <th class="text-center bg-blue text-white" style="min-width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody id="progressTableBody">
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                <span class="text-muted ms-2">Loading progress data...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Render summary table per GAMO
function renderSummaryPenilaian(data) {
    let html = '';
    let totals = {
        activities: 0,
        assessed: 0,
        score: 0,
        gamoCount: 0
    };
    
    if (data.gamos && data.gamos.length > 0) {
        console.log('GAMO Data:', data.gamos); // Debug log
        console.log('First GAMO keys:', Object.keys(data.gamos[0])); // Show all keys
        data.gamos.forEach(gamo => {
            totals.activities += gamo.total_activities || 0;
            totals.assessed += gamo.assessed_count || 0;
            if (gamo.avg_score) {
                totals.score += parseFloat(gamo.avg_score);
                totals.gamoCount++;
            }
            
            const progress = gamo.total_activities > 0 ? ((gamo.assessed_count / gamo.total_activities) * 100).toFixed(0) : 0;
            const progressClass = progress >= 75 ? 'bg-success' : (progress >= 50 ? 'bg-warning' : 'bg-danger');
            
            const statusBadge = progress >= 100 ? '<span class="badge text-white bg-success">Complete</span>' :
                                progress >= 75 ? '<span class="badge text-white bg-info">Almost Done</span>' :
                                progress >= 50 ? '<span class="badge text-white bg-warning">In Progress</span>' :
                                '<span class="badge text-white bg-secondary">Started</span>';
            
            // Calculate Gap (Target - Current Level)
            const targetLevel = gamo.target_level || 3;
            const currentLevel = gamo.capability_level || 0;
            const gap = targetLevel - currentLevel;
            const gapFormatted = gap > 0 ? `+${gap.toFixed(2)}` : gap.toFixed(2);
            const gapClass = gap > 0 ? 'text-danger' : (gap < 0 ? 'text-success' : 'text-muted');
            
            // Badge color for current level
            const levelColors = {
                0: 'bg-secondary',
                2: 'bg-orange',
                3: 'bg-yellow',
                4: 'bg-cyan',
                5: 'bg-green'
            };
            const currentLevelColor = levelColors[Math.floor(currentLevel)] || 'bg-secondary';
            const currentLevelDisplay = currentLevel > 0 ? `Level ${currentLevel}` : '-';
            
            html += `
                <tr>
                    <td><span class="badge bg-blue-lt">${gamo.code}</span></td>
                    <td>${gamo.name}</td>
                    <td class="text-center">${gamo.total_activities || 0}</td>
                    <td class="text-center">${gamo.assessed_count || 0}</td>
                    <td class="text-center">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar ${progressClass}" style="width: ${progress}%"></div>
                        </div>
                        <small>${progress}%</small>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-outline text-primary">Level ${gamo.target_level || 3}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge text-white ${currentLevelColor}" id="current-level-${gamo.code}">
                            <span class="spinner-border spinner-border-sm" style="width: 1rem; height: 1rem;"></span>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="${gapClass} fw-bold" id="gap-${gamo.code}">-</span>
                    </td>
                    <td class="text-end">${statusBadge}</td>
                </tr>
            `;
        });
    } else {
        html = '<tr><td colspan="9" class="text-center text-muted">No data available</td></tr>';
    }
    
    $('#summaryPenilaianTableBody').html(html);
    
    // Calculate capability level for each GAMO
    if (data.gamos && data.gamos.length > 0) {
        let totalTarget = 0;
        let gamoCount = 0;
        let completedCount = 0;
        const totalGamos = data.gamos.length;
        
        data.gamos.forEach(gamo => {
            // Sum target levels
            totalTarget += (gamo.target_level || 3);
            gamoCount++;
            
            // Lookup GAMO ID from code using global mapping
            const gamoId = window.gamoCodeToId && window.gamoCodeToId[gamo.code];
            if (gamoId && !isNaN(gamoId)) {
                calculateGamoCapabilityLevel(gamoId, gamo.code, gamo.target_level || 3, function() {
                    completedCount++;
                    if (completedCount === totalGamos) {
                        // All GAMOs calculated, now update averages
                        updateAverages();
                    }
                });
            } else {
                console.warn('GAMO ID not found for code:', gamo.code);
                $('#current-level-' + gamo.code).html('-');
                $('#gap-' + gamo.code).html('-');
                completedCount++;
                if (completedCount === totalGamos) {
                    updateAverages();
                }
            }
        });
        
        // Calculate and display average target
        if (gamoCount > 0) {
            const avgTarget = (totalTarget / gamoCount).toFixed(2);
            $('#avgTargetLevel').text(avgTarget);
        }
    }
    
    // Update totals
    const totalProgress = totals.activities > 0 ? ((totals.assessed / totals.activities) * 100).toFixed(0) : 0;
    
    $('#totalActivities').text(totals.activities);
    $('#totalAssessed').text(totals.assessed);
    $('#totalProgress').text(totalProgress + '%');
}

// Update statistics cards
function updateStatistics(data) {
    if (!data.gamos) {
        return;
    }
    
    const totals = data.totals || {};
    const total = totals.activities || 0;
    const assessed = totals.assessed || 0;
    const notAssessed = total - assessed;
    const percent = total > 0 ? Math.round((assessed / total) * 100) : 0;
    
    // Calculate average score
    let totalScore = 0;
    let gamoCount = 0;
    data.gamos.forEach(gamo => {
        if (gamo.avg_score && gamo.avg_score > 0) {
            totalScore += parseFloat(gamo.avg_score);
            gamoCount++;
        }
    });
    const avgScore = gamoCount > 0 ? (totalScore / gamoCount).toFixed(2) : '0.00';
    
    $('#statTotalActivities').text(total);
    $('#statAssessed').text(assessed);
    $('#statNotAssessed').text(notAssessed);
    $('#statAssessedPercent').text(percent + '%');
    $('#statAvgCompliance').text(avgScore);
}

// Load progress data
function loadProgressCapabilitas() {
    const assessmentId = $('input[name="assessment_id"]').val();
    
    $.ajax({
        url: `/assessments/${assessmentId}/summary-all-gamos`,
        method: 'GET',
        success: function(response) {
            updateProgressCards(response);
            renderCapabilityChart(response);
        },
        error: function() {
            console.error('Error loading progress data');
        }
    });
}

// Update progress cards
function updateProgressCards(data) {
    if (!data.gamos || data.gamos.length === 0) {
        return;
    }
    
    let totalGamos = 0;
    let totalScore = 0;
    let totalTarget = 0;
    
    data.gamos.forEach(gamo => {
        const currentScore = gamo.avg_score ? parseFloat(gamo.avg_score) : 0;
        const targetLevel = gamo.target_level || 3;
        
        totalGamos++;
        totalScore += currentScore;
        totalTarget += targetLevel;
    });
    
    const avgScore = totalGamos > 0 ? (totalScore / totalGamos) : 0;
    const avgTarget = totalGamos > 0 ? (totalTarget / totalGamos) : 3;
    const overallProgress = avgTarget > 0 ? ((avgScore / avgTarget) * 100) : 0;
    
    $('#currentCapabilityLevel').text(avgScore.toFixed(2));
    $('#targetLevel').text(avgTarget.toFixed(1));
    $('#progressPercent').text(overallProgress.toFixed(0) + '%');
    $('#progressBar').css('width', overallProgress + '%');
    
    // Update progress bar color
    if (overallProgress >= 100) {
        $('#progressBar').removeClass('bg-info bg-warning bg-danger').addClass('bg-success');
    } else if (overallProgress >= 75) {
        $('#progressBar').removeClass('bg-info bg-warning bg-danger bg-success').addClass('bg-info');
    } else if (overallProgress >= 50) {
        $('#progressBar').removeClass('bg-info bg-warning bg-danger bg-success').addClass('bg-warning');
    } else {
        $('#progressBar').removeClass('bg-info bg-warning bg-success').addClass('bg-danger');
    }
}

// Render capability chart
function renderCapabilityChart(data) {
    const ctx = document.getElementById('capabilityChart');
    if (!ctx) {
        console.error('Canvas element not found');
        return;
    }
    
    if (!data.gamos || data.gamos.length === 0) {
        console.error('No GAMO data available');
        return;
    }
    
    // Prepare data per GAMO
    const labels = [];
    const realizationData = [];
    const targetData = [];
    
    data.gamos.forEach(gamo => {
        labels.push(gamo.code);
        realizationData.push(gamo.avg_score ? parseFloat(gamo.avg_score) : 0);
        targetData.push(gamo.target_level || 3);
    });
    
    // Destroy existing chart if it exists
    if (window.capabilityChartInstance) {
        window.capabilityChartInstance.destroy();
    }
    
    // Create new chart
    try {
        window.capabilityChartInstance = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Realisasi',
                        data: realizationData,
                        backgroundColor: 'rgba(32, 107, 196, 0.2)',
                        borderColor: 'rgba(32, 107, 196, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(32, 107, 196, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(32, 107, 196, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Target',
                        data: targetData,
                        backgroundColor: 'rgba(76, 175, 80, 0.2)',
                        borderColor: 'rgba(76, 175, 80, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(76, 175, 80, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(76, 175, 80, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5,
                scales: {
                    r: {
                        beginAtZero: true,
                        min: 0,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return 'L' + value;
                            },
                            font: {
                                size: 10
                            }
                        },
                        pointLabels: {
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 10,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                const index = context[0].dataIndex;
                                return data.gamos[index].name;
                            },
                            label: function(context) {
                                const value = context.parsed.r.toFixed(2);
                                return `${context.dataset.label}: ${value}`;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error creating chart:', error);
    }
}

// Export summary
function exportSummary() {
    const assessmentId = $('input[name="assessment_id"]').val();
    const gamoId = $('#gamoSelector').val();
    window.location.href = `/assessments/${assessmentId}/gamo/${gamoId}/export-summary`;
}

// Load summary when tab is shown
$(document).on('shown.bs.tab', 'a[href="#tab-summary"]', function() {
    loadSummaryPenilaian();
});

// Load summary when sub-tab Penilaian is shown
$(document).on('shown.bs.tab', 'a[href="#summaryPenilaian"]', function() {
    loadSummaryPenilaian();
});

// Load progress when sub-tab is shown
$(document).on('shown.bs.tab', 'a[href="#summaryProgress"]', function() {
    loadProgressCapabilitas();
});

// Check if summary tab is active on page load
$(document).ready(function() {
    // Define function in global scope
    window.loadSummaryPenilaian = function() {
        const assessmentId = $('input[name="assessment_id"]').val();
        
        $.ajax({
            url: `/assessments/${assessmentId}/summary-all-gamos`,
            method: 'GET',
            success: function(response) {
                renderSummaryPenilaian(response);
                updateStatistics(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading summary:', xhr.status, xhr.responseText, error);
                $('#summaryPenilaianTableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center text-danger">Error loading summary</td>
                    </tr>
                `);
            }
        });
    };
    
    if ($('#tab-summary').hasClass('active') || $('a[href="#tab-summary"]').hasClass('active')) {
        loadSummaryPenilaian();
    }
    // Also check if summaryPenilaian sub-tab is active
    if ($('#summaryPenilaian').hasClass('active') && $('#tab-summary').hasClass('active')) {
        loadSummaryPenilaian();
    }
    
    // Handle Progress Kapabilitas tab click
    $('a[href="#summaryProgress"]').on('shown.bs.tab', function() {
        loadProgressKapabilitasData();
    });
    
    // Load Progress Kapabilitas data
    window.loadProgressKapabilitasData = function() {
        const assessmentId = $('input[name="assessment_id"]').val();
        
        $.ajax({
            url: `/assessments/${assessmentId}/summary-all-gamos`,
            method: 'GET',
            success: function(response) {
                renderProgressKapabilitas(response);
            },
            error: function(xhr) {
                console.error('Error loading progress data:', xhr);
                $('#progressTableBody').html(`
                    <tr>
                        <td colspan="6" class="text-center text-danger">Error loading progress data</td>
                    </tr>
                `);
            }
        });
    };
});

// Calculate capability level for each GAMO using COBIT 2019 rules
function calculateGamoCapabilityLevel(gamoId, gamoCode, targetLevel, callback) {
    const assessmentId = $('input[name="assessment_id"]').val();
    
    // Validate gamoId and assessmentId
    if (!gamoId || !assessmentId || !gamoCode) {
        console.error('Invalid parameters:', {gamoId, assessmentId, gamoCode});
        $('#current-level-' + gamoCode).html('-');
        $('#gap-' + gamoCode).html('-');
        if (callback) callback();
        return;
    }
    
    $.ajax({
        url: `/assessments/${assessmentId}/gamo/${gamoId}/activities`,
        method: 'GET',
        success: function(response) {
            const activities = response.activities || {};
            let achievedLevel = 0;
            let achievedCompliance = 0;
            
            // Calculate achieved level based on COBIT 2019 rules
            // Threshold: 85%, Sequential, Skip empty levels
            // COBIT 2019: Levels start from 2 (Managed) to 5 (Optimizing)
            for (let level = 2; level <= 5; level++) {
                const levelActivities = activities[level] || [];
                
                // Skip level jika tidak ada activities
                if (levelActivities.length === 0) continue;
                
                let totalWeight = 0;
                let weightedScore = 0;
                
                levelActivities.forEach(activity => {
                    const weight = activity.weight || 1;
                    totalWeight += weight;
                    
                    if (activity.answer && activity.answer.capability_score) {
                        weightedScore += weight * activity.answer.capability_score;
                    }
                });
                
                // Calculate compliance for this level
                const compliance = totalWeight > 0 ? ((weightedScore / totalWeight) * 100) : 0;
                
                // Level achieved if compliance >= 85% (COBIT 2019)
                if (compliance >= 85) {
                    achievedLevel = level;
                    achievedCompliance = compliance;
                } else {
                    // Stop if level not achieved
                    break;
                }
            }
            
            // Update display
            const levelColors = {
                0: 'bg-secondary',
                2: 'bg-orange',
                3: 'bg-yellow',
                4: 'bg-cyan',
                5: 'bg-green'
            };
            
            const $badge = $('#current-level-' + gamoCode);
            const $gap = $('#gap-' + gamoCode);
            
            if (achievedLevel > 0) {
                // Update current level badge
                $badge.removeClass('bg-secondary bg-red bg-orange bg-yellow bg-cyan bg-green')
                      .addClass('text-white ' + levelColors[achievedLevel]);
                $badge.html('Level ' + achievedLevel);
                
                // Update gap
                const gap = targetLevel - achievedLevel;
                const gapDisplay = gap > 0 ? '+' + gap.toFixed(2) : gap.toFixed(2);
                const gapClass = gap > 0 ? 'text-danger' : (gap < 0 ? 'text-success' : 'text-muted');
                $gap.removeClass('text-danger text-success text-muted').addClass(gapClass + ' fw-bold');
                $gap.text(gapDisplay);
                
                // Call callback when done
                if (callback) callback();
            } else {
                $badge.removeClass('bg-red bg-orange bg-yellow bg-cyan bg-green')
                      .addClass('bg-secondary');
                $badge.html('-');
                
                const gap = targetLevel - 0;
                $gap.removeClass('text-danger text-success text-muted').addClass('text-danger fw-bold');
                $gap.text('+' + gap.toFixed(2));
                
                // Call callback when done
                if (callback) callback();
            }
        },
        error: function() {
            $('#current-level-' + gamoCode).html('-');
            $('#gap-' + gamoCode).html('-');
            if (callback) callback();
        }
    });
}

// Update average current level and gap (called after all GAMOs calculated)
function updateAverages() {
    let totalLevel = 0;
    let count = 0;
    
    // Count ALL GAMOs including those with level 0
    $('[id^="current-level-"]').each(function() {
        const text = $(this).text().trim();
        let level = 0;
        
        if (text.startsWith('Level ')) {
            level = parseInt(text.replace('Level ', ''));
            if (isNaN(level)) level = 0;
        } else if (text === '-' || text === '0') {
            level = 0;
        }
        
        totalLevel += level;
        count++;
    });
    
    // Update average current level (include all GAMOs, even those with 0)
    const avgCurrent = count > 0 ? (totalLevel / count).toFixed(2) : '0.00';
    $('#avgCurrentLevel').text(avgCurrent);
    
    // Calculate average gap
    const avgTarget = parseFloat($('#avgTargetLevel').text()) || 0;
    const avgCurrentNum = parseFloat(avgCurrent) || 0;
    const avgGap = avgTarget - avgCurrentNum;
    const gapDisplay = avgGap > 0 ? '+' + avgGap.toFixed(2) : avgGap.toFixed(2);
    const gapClass = avgGap > 0 ? 'text-danger' : (avgGap < 0 ? 'text-success' : 'text-muted');
    
    $('#avgGap').removeClass('text-danger text-success text-muted')
                .addClass(gapClass + ' fw-bold')
                .text(gapDisplay);
}

// Render Progress Kapabilitas table
function renderProgressKapabilitas(data) {
    if (!data.gamos || data.gamos.length === 0) {
        $('#progressTableBody').html('<tr><td colspan="6" class="text-center text-muted">No data available</td></tr>');
        return;
    }
    
    // Clear loading spinner first
    $('#progressTableBody').empty();
    
    let totalActivitiesOverall = 0;
    let totalAssessedOverall = 0;
    let completedRequests = 0;
    const totalGamos = data.gamos.length;
    
    // Group by category
    const categories = {};
    data.gamos.forEach(gamo => {
        const category = gamo.category || 'Other';
        if (!categories[category]) {
            categories[category] = [];
        }
        categories[category].push(gamo);
    });
    
    // Render each category
    Object.keys(categories).sort().forEach(category => {
        const gamos = categories[category];
        
        gamos.forEach(gamo => {
            const gamoId = window.gamoCodeToId && window.gamoCodeToId[gamo.code];
            
            // Fetch activities breakdown per level
            if (gamoId) {
                $.ajax({
                    url: `/assessments/${assessmentId}/gamo/${gamoId}/activities`,
                    method: 'GET',
                    success: function(response) {
                        const activities = response.activities || {};
                        
                        // Count activities per level (include ALL levels 1-5)
                        const levelData = {};
                        let totalActivities = 0;
                        let totalAssessed = 0;
                        
                        // Count activities per level (COBIT 2019: Level 2-5)
                        for (let level = 2; level <= 5; level++) {
                            const levelActivities = activities[level] || [];
                            const count = levelActivities.length;
                            const assessed = levelActivities.filter(a => a.answer && a.answer.capability_score !== null).length;
                            
                            levelData[level] = { count, assessed };
                            totalActivities += count;
                            totalAssessed += assessed;
                        }
                        
                        totalActivitiesOverall += totalActivities;
                        totalAssessedOverall += totalAssessed;
                        
                        // Build table row
                        let rowHtml = `
                            <tr>
                                <td>
                                    <div class="fw-bold">${gamo.code}</div>
                                    <div class="text-muted small">${gamo.name}</div>
                                </td>
                        `;
                        
                        // Level 2-5 columns
                        for (let level = 2; level <= 5; level++) {
                            const data = levelData[level];
                            const isComplete = data.count > 0 && data.assessed === data.count;
                            const badgeClass = isComplete ? 'bg-cyan-lt text-cyan' : '';
                            
                            rowHtml += `
                                <td class="text-center ${badgeClass}">
                                    <div class="h4 mb-1">${data.count}</div>
                                    <div class="text-muted small">${data.assessed} Sudah Dinilai</div>
                                </td>
                            `;
                        }
                        
                        // Total column
                        const totalComplete = totalActivities > 0 && totalAssessed === totalActivities;
                        const totalBadgeClass = totalComplete ? 'bg-cyan-lt text-cyan' : '';
                        
                        rowHtml += `
                                <td class="text-center ${totalBadgeClass}">
                                    <div class="h4 mb-1 fw-bold">${totalActivities}</div>
                                    <div class="text-muted small">${totalAssessed} Sudah Dinilai</div>
                                </td>
                            </tr>
                        `;
                        
                        // Append row (use data attribute to maintain order)
                        const $row = $(rowHtml);
                        $row.attr('data-gamo-code', gamo.code);
                        
                        // Insert in correct position
                        const $tbody = $('#progressTableBody');
                        const $existingRow = $tbody.find(`tr[data-gamo-code="${gamo.code}"]`);
                        
                        if ($existingRow.length) {
                            $existingRow.replaceWith($row);
                        } else {
                            // Remove loading if still exists
                            $tbody.find('tr:not([data-gamo-code])').remove();
                            $tbody.append($row);
                        }
                        
                        // Track completion
                        completedRequests++;
                        
                        // Update summary cards
                        updateProgressCards();
                    },
                    error: function() {
                        console.error('Failed to load activities for GAMO:', gamo.code);
                        completedRequests++;
                    }
                });
            } else {
                completedRequests++;
            }
        });
    });
}

// Update progress summary cards
function updateProgressCards() {
    let totalActivities = 0;
    let totalAssessed = 0;
    
    // Calculate from table data
    $('#progressTableBody tr[data-gamo-code]').each(function() {
        const $lastCell = $(this).find('td').last();
        const activitiesText = $lastCell.find('.h4').text().trim();
        const assessedText = $lastCell.find('.small').text().trim();
        
        const activities = parseInt(activitiesText) || 0;
        const assessed = parseInt(assessedText.split(' ')[0]) || 0;
        
        totalActivities += activities;
        totalAssessed += assessed;
    });
    
    const progress = totalActivities > 0 ? ((totalAssessed / totalActivities) * 100).toFixed(2) : 0;
    
    $('#progressTotalActivities').text(totalActivities);
    $('#progressAssessedActivities').text(totalAssessed);
    $('#progressPercentage').text(progress + '%');
}
</script>

