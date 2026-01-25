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
                            <th class="text-center">Avg Score</th>
                            <th class="text-center">Target</th>
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
                            <td colspan="2">Total</td>
                            <td class="text-center" id="totalActivities">0</td>
                            <td class="text-center" id="totalAssessed">0</td>
                            <td class="text-center" id="totalProgress">0%</td>
                            <td class="text-center" id="totalAvgScore">0.00</td>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
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
        <!-- Progress Cards -->
        <div class="row row-cards mb-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Current Avg Score</div>
                        <div class="h1 mb-0 text-primary" id="currentCapabilityLevel">-</div>
                        <div class="text-muted small">Across all GAMOs</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Avg Target Level</div>
                        <div class="h1 mb-0 text-success" id="targetLevel">-</div>
                        <div class="text-muted small">Goal to achieve</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="subheader">Progress to Target</div>
                        <div class="h1 mb-0 text-info" id="progressPercent">0%</div>
                        <div class="progress progress-sm mt-2">
                            <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level Progress Chart -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Capability Maturity Progress (Realisasi vs Target)</h3>
            </div>
            <div class="card-body" style="height: 400px;">
                <canvas id="capabilityChart"></canvas>
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
        data.gamos.forEach(gamo => {
            totals.activities += gamo.total_activities || 0;
            totals.assessed += gamo.assessed_count || 0;
            if (gamo.avg_score) {
                totals.score += parseFloat(gamo.avg_score);
                totals.gamoCount++;
            }
            
            const progress = gamo.total_activities > 0 ? ((gamo.assessed_count / gamo.total_activities) * 100).toFixed(0) : 0;
            const progressClass = progress >= 75 ? 'bg-success' : (progress >= 50 ? 'bg-warning' : 'bg-danger');
            
            const statusBadge = progress >= 100 ? '<span class="badge bg-success">Complete</span>' :
                                progress >= 75 ? '<span class="badge bg-info">Almost Done</span>' :
                                progress >= 50 ? '<span class="badge bg-warning">In Progress</span>' :
                                '<span class="badge bg-secondary">Started</span>';
            
            // Calculate Gap (Target - Current Avg Score)
            const targetLevel = gamo.target_level || 3;
            const currentScore = gamo.avg_score ? parseFloat(gamo.avg_score) : 0;
            const gap = targetLevel - currentScore;
            const gapFormatted = gap.toFixed(2);
            const gapClass = gap > 0 ? 'text-danger' : (gap < 0 ? 'text-success' : 'text-muted');
            const gapDisplay = gap > 0 ? `+${gapFormatted}` : gapFormatted;
            
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
                    <td class="text-center">${gamo.avg_score ? parseFloat(gamo.avg_score).toFixed(2) : '0.00'}</td>
                    <td class="text-center">
                        <span class="badge badge-outline text-primary">Level ${gamo.target_level || 3}</span>
                    </td>
                    <td class="text-center">
                        <span class="${gapClass} fw-bold">${gapDisplay}</span>
                    </td>
                    <td class="text-end">${statusBadge}</td>
                </tr>
            `;
        });
    } else {
        html = '<tr><td colspan="9" class="text-center text-muted">No data available</td></tr>';
    }
    
    $('#summaryPenilaianTableBody').html(html);
    
    // Update totals
    const totalProgress = totals.activities > 0 ? ((totals.assessed / totals.activities) * 100).toFixed(0) : 0;
    const avgScore = totals.gamoCount > 0 ? (totals.score / totals.gamoCount).toFixed(2) : '0.00';
    
    $('#totalActivities').text(totals.activities);
    $('#totalAssessed').text(totals.assessed);
    $('#totalProgress').text(totalProgress + '%');
    $('#totalAvgScore').text(avgScore);
    
    $('#summaryPenilaianTableBody').html(html);
    
    // Update totals
    const avgCompliance = totals.activities > 0 ? (totals.compliance / 4).toFixed(2) : '0.00';
    $('#totalActivities').text(totals.activities);
    $('#totalAssessed').text(totals.assessed);
    $('#totalNotAssessed').text(totals.notAssessed);
    $('#totalNA').text(totals.na);
    $('#totalN').text(totals.n);
    $('#totalP').text(totals.p);
    $('#totalL').text(totals.l);
    $('#totalF').text(totals.f);
    $('#totalCompliance').text(data.overall_compliance || avgCompliance);
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
});
</script>
