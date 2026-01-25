@extends('layouts.app')

@section('title', 'Answer Assessment')

@push('styles')
<style>
    .level-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .level-card:hover:not(.locked) {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .level-card.active {
        background-color: var(--tblr-primary);
        color: white;
    }
    .level-card.active .text-black {
        color: white !important;
    }
    .level-card.locked {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .level-connector {
        height: 2px;
        background: var(--tblr-border-color);
        flex: 1;
    }
    .level-connector.active {
        background: var(--tblr-primary);
    }
    .evidence-icon {
        cursor: pointer;
    }
    .rating-badge {
        cursor: pointer;
        transition: all 0.2s;
    }
    .rating-badge:hover {
        transform: scale(1.05);
    }
    .hover-shadow-sm {
        transition: all 0.3s ease;
    }
    .hover-shadow-sm:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-2px);
    }
    .min-w-0 {
        min-width: 0;
    }
</style>
@endpush

@section('content')
@cannot('answer', $assessment)
<div class="container-xl mt-4">
    <div class="alert alert-warning" role="alert">
        <div class="d-flex">
            <div><i class="ti ti-alert-circle icon alert-icon"></i></div>
            <div>
                <h4 class="alert-title">Read-Only Mode</h4>
                <div class="text-muted">You don't have permission to answer this assessment. You can view the data but cannot make changes.</div>
            </div>
        </div>
    </div>
</div>
@endcannot

<div class="page-header d-print-none sticky-top bg-white border-bottom">
    <div class="container-xl p-4">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle"><span class="badge bg-blue-lt">{{ $assessment->code }}</span></div>
                <h2 class="page-title">{{ $assessment->title }}</h2>
                @cannot('answer', $assessment)
                    <span class="badge bg-secondary ms-2">Read-Only</span>
                @endcannot
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>Back to Assessment
                    </a>
                </div>
            </div>
        </div>

        <!-- GAMO Selector -->
        <div class="row mt-3">
            <div class="col-md-5">
                <select class="form-select" id="gamoSelector">
                    @foreach($gamoObjectives as $gamo)
                    <option value="{{ $gamo->id }}" 
                            data-target="{{ $gamo->pivot->target_maturity_level ?? 3 }}"
                            data-code="{{ $gamo->code }}"
                            {{ $loop->first ? 'selected' : '' }}>
                        {{ $gamo->code }} - {{ $gamo->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="card mb-0 border-primary shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ti ti-target text-primary me-2" style="font-size: 1.1rem;"></i>
                            <span class="text-muted small fw-semibold">TARGET</span>
                        </div>
                        <div class="d-flex align-items-baseline justify-content-between">
                            <div class="fw-bold text-primary" style="font-size: 2.5rem; line-height: 1;" id="gamoTargetLevel">
                                @php
                                    $firstGamo = $gamoObjectives->first();
                                    $targetLevel = $firstGamo->pivot->target_maturity_level ?? 3;
                                @endphp
                                {{ $targetLevel }}
                            </div>
                            <div class="text-end">
                                <div class="badge bg-primary-lt">
                                    <span class="text-uppercase small" id="targetLevelName">
                                        @php
                                            $levelNames = ['', 'Performed', 'Managed', 'Established', 'Predictable', 'Optimizing'];
                                            echo $levelNames[$targetLevel] ?? '';
                                        @endphp
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-0 border-success shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ti ti-trending-up text-success me-2" style="font-size: 1.1rem;"></i>
                            <span class="text-muted small fw-semibold">CURRENT</span>
                        </div>
                        <div class="d-flex align-items-baseline justify-content-between">
                            <div class="d-flex align-items-baseline">
                                <div class="fw-bold text-success" style="font-size: 2.5rem; line-height: 1;" id="gamoAchievedLevel">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </div>
                                <span class="text-muted ms-2" style="font-size: 1rem;" id="gamoAchievedPercent"></span>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-success-lt" id="achievedLevelBadge" style="visibility: hidden;">
                                    <span class="text-uppercase small" id="achievedLevelName">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card mb-0 border-info shadow-sm h-100">
                    <div class="card-body p-3 d-flex flex-column justify-content-center">
                        <div class="d-flex align-items-center mb-2 justify-content-center">
                            <i class="ti ti-bulb text-info me-2" style="font-size: 1.1rem;"></i>
                            <span class="text-muted small fw-semibold">OFI</span>
                        </div>
                        <button type="button" class="btn btn-info w-100" onclick="showOFIModal(document.getElementById('gamoSelector').value)">
                            <i class="ti ti-clipboard-list me-1"></i>
                            Lihat OFI
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mt-3" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="#tab-level" class="nav-link active" data-bs-toggle="tab" role="tab">
                    <i class="ti ti-chart-bar me-2"></i>Level
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#tab-repository" class="nav-link" data-bs-toggle="tab" role="tab">
                    <i class="ti ti-database me-2"></i>Repository
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="#tab-summary" class="nav-link" data-bs-toggle="tab" role="tab">
                    <i class="ti ti-clipboard-text me-2"></i>Summary All GAMO Objectives
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Hidden inputs -->
<input type="hidden" name="assessment_id" value="{{ $assessment->id }}">

<div class="page-body">
    <div class="container-xl">
        <div class="tab-content">
            <!-- Tab 1: Level -->
            <div class="tab-pane fade show active" id="tab-level" role="tabpanel">
                @include('assessments.partials.tab-level')
            </div>

            <!-- Tab 2: Repository -->
            <div class="tab-pane fade" id="tab-repository" role="tabpanel">
                @include('assessments.partials.tab-repository')
            </div>

            <!-- Tab 3: Summary -->
            <div class="tab-pane fade" id="tab-summary" role="tabpanel">
                @include('assessments.partials.tab-summary')
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('assessments.modals.penilaian-kapabilitas')
@include('assessments.modals.history-perubahan')
@include('assessments.modals.nilai-rata-rata')
@include('assessments.modals.daftar-catatan')
@include('assessments.modals.daftar-evidence')
@include('assessments.modals.ofi')

@endsection

@push('scripts')
<script>
// Global variables
const assessmentId = {{ $assessment->id }};
const canAnswer = {{ auth()->user()->can('answer', $assessment) ? 'true' : 'false' }};
let currentGamoId = {{ $gamoObjectives->first()->id ?? 'null' }};
let currentLevel = 2;
let allActivitiesByLevel = {};

// Create mapping from GAMO code to ID (exposed to window for other scripts)
window.gamoCodeToId = {};
@foreach($gamoObjectives as $gamo)
    window.gamoCodeToId['{{ $gamo->code }}'] = {{ $gamo->id }};
@endforeach

// Setup AJAX with CSRF token
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'X-Requested-With': 'XMLHttpRequest'
    }
});

$(document).ready(function() {
    // Initialize currentGamoId from selector
    currentGamoId = $('#gamoSelector').val() || {{ $gamoObjectives->first()->id ?? 'null' }};
    
    // Initialize toastr
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000
        };
    }
    
    // Disable forms if user cannot answer
    if (!canAnswer) {
        $('input[name="capability_rating"]').attr('disabled', true);
        $('textarea[name="notes"]').attr('disabled', true);
        $('#evidenceUploadForm input, #evidenceUploadForm select, #evidenceUploadForm button').attr('disabled', true);
        $('#evidenceUploadFormModal input, #evidenceUploadFormModal select, #evidenceUploadFormModal button').attr('disabled', true);
    }

    // GAMO Selector change
    $('#gamoSelector').on('change', function() {
        currentGamoId = $(this).val();
        const targetLevel = $(this).find('option:selected').data('target');
        
        // Update target level display
        const levelNames = ['', 'Performed', 'Managed', 'Established', 'Predictable', 'Optimizing'];
        $('#gamoTargetLevel').text(targetLevel);
        $('#targetLevelName').text(levelNames[targetLevel] || '');
        
        // Update achieved level
        updateAchievedLevel(currentGamoId);
        
        loadActivitiesByLevel(currentLevel);
    });

    // Level card click in main tab
    $(document).on('click', '#tab-level .level-card', function() {
        const locked = $(this).attr('data-locked');
        if (locked === 'true' || $(this).hasClass('locked')) {
            Swal.fire({
                icon: 'warning',
                title: 'Level Terkunci',
                text: 'Complete previous level dengan rating minimal "Largely Achieved" untuk unlock level ini.',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        const level = $(this).data('level');
        $('#tab-level .level-card').removeClass('active');
        $(this).addClass('active');
        currentLevel = level;
        loadActivitiesByLevel(level);
    });

    // Initialize - load first level with activities
    if (typeof allActivitiesByLevel !== 'undefined' && allActivitiesByLevel) {
        let firstLevel = 2;
        for (let level = 2; level <= 5; level++) {
            if (allActivitiesByLevel[level] && allActivitiesByLevel[level].length > 0) {
                firstLevel = level;
                break;
            }
        }
        loadActivitiesByLevel(firstLevel);
    } else {
        loadActivitiesByLevel(2);
    }
    
    // Initialize achieved level
    updateAchievedLevel(currentGamoId);
});

// Update achieved level display based on compliance
function updateAchievedLevel(gamoId) {
    $('#gamoAchievedLevel').html('<span class="spinner-border spinner-border-sm"></span>');
    
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
            
            // Display: Level integer with percentage info
            const levelNames = ['', 'Performed', 'Managed', 'Established', 'Predictable', 'Optimizing'];
            
            if (achievedLevel > 0) {
                // Update level number
                $('#gamoAchievedLevel').text(achievedLevel);
                
                // Update percentage next to level
                $('#gamoAchievedPercent').text('(' + achievedCompliance.toFixed(0) + '%)');
                
                // Update badge
                $('#achievedLevelName').text(levelNames[achievedLevel] || '');
                $('#achievedLevelBadge').css('visibility', 'visible');
                
            } else {
                $('#gamoAchievedLevel').html('<span class="text-muted">0</span>');
                $('#gamoAchievedPercent').text('');
                $('#achievedLevelBadge').css('visibility', 'hidden');
            }
        },
        error: function() {
            $('#gamoAchievedLevel').html('<span class="text-danger">Err</span>');
        }
    });
}

// Load activities by level
function loadActivitiesByLevel(level) {
    currentLevel = level;
    
    $('#activitiesTableBody').html(`
        <tr>
            <td colspan="8" class="text-center">
                <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                <span class="text-muted ms-2">Loading activities...</span>
            </td>
        </tr>
    `);
    
    $('#currentLevelTitle').text(`Level ${level}`);
    
    // Update active level card
    $('.level-card').removeClass('active');
    $(`.level-card[data-level="${level}"]`).addClass('active');
    
    $.ajax({
        url: `/assessments/${assessmentId}/gamo/${currentGamoId}/activities`,
        method: 'GET',
        data: { level: level },
        success: function(response) {
            allActivitiesByLevel = response.activities || {};
            const activities = response.activities[level] || [];
            renderActivities(activities);
            updateSummary(activities);
            
            // Update level counts
            if (typeof updateLevelCounts === 'function') {
                updateLevelCounts(allActivitiesByLevel);
            }
            
            // Check and update level unlock status
            checkAndUpdateLevelAccess();
        },
        error: function(xhr) {
            console.error('Error loading activities:', xhr);
            $('#activitiesTableBody').html(`
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        <i class="ti ti-alert-circle me-2"></i>Error loading activities
                    </td>
                </tr>
            `);
        }
    });
}

// Render activities table
function renderActivities(activities) {
    const tbody = $('#activitiesTableBody');
    tbody.empty();
    
    if (activities.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="ti ti-inbox-off icon mb-2" style="font-size: 2rem;"></i>
                    <div>No activities for this level</div>
                </td>
            </tr>
        `);
        return;
    }

    activities.forEach((activity, index) => {
        const evidenceCount = activity.evidence_count || 0;
        const evidenceIcon = evidenceCount > 0
            ? `<span class="badge bg-success text-white cursor-pointer" onclick="showEvidence(${activity.id})">${evidenceCount}</span>`
            : `<span class="text-muted cursor-pointer" onclick="showEvidence(${activity.id})">-</span>`;
        
        const rating = activity.answer?.capability_rating || null;
        let ratingBadge = '-';
        
        if (rating) {
            const ratingColors = {
                'F': 'bg-success',
                'L': 'bg-info',
                'P': 'bg-warning',
                'N': 'bg-danger',
                'N/A': 'bg-secondary'
            };
            const ratingClass = ratingColors[rating] || 'bg-secondary';
            const clickAction = canAnswer ? `onclick="openAssessmentModal(${activity.id})"` : '';
            ratingBadge = `<span class="badge text-white ${ratingClass} ${canAnswer ? 'cursor-pointer' : ''}" ${clickAction}>${rating}</span>`;
        } else {
            if (canAnswer) {
                ratingBadge = `<button class="btn btn-sm btn-outline-primary" onclick="openAssessmentModal(${activity.id})">Rate</button>`;
            } else {
                ratingBadge = `<span class="text-muted">-</span>`;
            }
        }

        tbody.append(`
            <tr>
                <td class="text-center">${index + 1}</td>
                <td><code>${activity.code || '-'}</code></td>
                <td>${activity.name || '-'}</td>
                <td class="text-muted small">${activity.translated_text || '-'}</td>
                <td class="text-center">${evidenceIcon}</td>
                <td class="text-center">${ratingBadge}</td>
                <td class="text-center">${activity.weight || 1}</td>
            </tr>
        `);
    });
}

// Update summary when activities change
function updateSummary(activities) {
    const totalActivities = activities.length;
    const completedActivities = activities.filter(a => a.answer && a.answer.capability_rating).length;
    
    let totalWeight = 0;
    let weightedScore = 0;
    
    activities.forEach(activity => {
        const weight = activity.weight || 1;
        totalWeight += weight;
        
        if (activity.answer && activity.answer.capability_score) {
            weightedScore += weight * activity.answer.capability_score;
        }
    });
    
    const compliance = totalWeight > 0 ? ((weightedScore / totalWeight) * 100).toFixed(2) : '0.00';
    
    $('#totalValues').text(weightedScore.toFixed(2));
    $('#totalWeight').text(totalWeight);
    $('#totalCompliances').text(compliance + '%');
    $('#completedCount').text(`${completedActivities}/${totalActivities}`);
    
    // Update level count
    $(`#level-${currentLevel}-count`).text(`${totalActivities} Activities`);
}

// Update level counts for all levels
function updateLevelCounts(allActivitiesByLevel) {
    for (let level = 2; level <= 5; level++) {
        const activities = allActivitiesByLevel[level] || [];
        const count = activities.length;
        $(`#level-${level}-count`).text(`${count} ${count === 1 ? 'Activity' : 'Activities'}`);
    }
}

// Check and update level unlock status based on COBIT 2019 rules
function checkAndUpdateLevelAccess() {
    if (!allActivitiesByLevel) {
        return;
    }
    
    // Find first level with activities (starting point)
    let firstActiveLevel = null;
    for (let level = 2; level <= 5; level++) {
        const activities = allActivitiesByLevel[level] || [];
        if (activities.length > 0) {
            firstActiveLevel = level;
            break;
        }
    }
    
    // If no activities at all, unlock all (shouldn't happen)
    if (!firstActiveLevel) {
        for (let level = 2; level <= 5; level++) {
            updateLevelLockState(level, false);
        }
        return;
    }
    
    // First level with activities is always unlocked
    updateLevelLockState(firstActiveLevel, false);
    
    // Check subsequent levels
    for (let level = 2; level <= 5; level++) {
        if (level === firstActiveLevel) continue; // Already handled
        
        const activities = allActivitiesByLevel[level] || [];
        
        // If current level has no activities, lock it (skip/hide)
        if (activities.length === 0) {
            updateLevelLockState(level, true);
            continue;
        }
        
        // Find the last non-empty level before current level
        let lastValidLevel = null;
        for (let prev = level - 1; prev >= 1; prev--) {
            const prevActivities = allActivitiesByLevel[prev] || [];
            if (prevActivities.length > 0) {
                lastValidLevel = prev;
                break;
            }
        }
        
        // If no previous valid level found, lock it
        if (!lastValidLevel) {
            updateLevelLockState(level, true);
            continue;
        }
        
        // Check if previous valid level is achieved
        const prevActivities = allActivitiesByLevel[lastValidLevel] || [];
        const isUnlocked = isPreviousLevelAchieved(prevActivities);
        updateLevelLockState(level, !isUnlocked);
    }
}

// Check if a level has achieved minimum "Largely" rating (COBIT 2019 rule)
function isPreviousLevelAchieved(activities) {
    // Empty level should not happen here (filtered before calling)
    if (activities.length === 0) {
        return false;
    }
    
    let ratedActivities = 0;
    let largeLyOrFullyCount = 0;
    
    // Rating values: F=5.0, L=3.75, P=2.5, N=1.25, N/A=0
    activities.forEach(activity => {
        if (activity.answer && activity.answer.capability_rating) {
            ratedActivities++;
            const rating = activity.answer.capability_rating;
            
            // Count "Largely" (L) or "Fully" (F) achieved ratings
            if (rating === 'F' || rating === 'L') {
                largeLyOrFullyCount++;
            }
        }
    });
    
    // Unlock if at least 50% of activities rated L or F
    // This follows COBIT 2019 principle: "Largely Achieved" level unlocks next level
    if (ratedActivities === 0) {
        return false; // Has activities but none rated yet = not achieved
    }
    
    const achievementPercentage = (largeLyOrFullyCount / ratedActivities) * 100;
    return achievementPercentage >= 50; // At least 50% must be L or F
}

// Update visual lock state for a level
function updateLevelLockState(level, isLocked) {
    const $levelCard = $(`.level-card[data-level="${level}"]`);
    const $lockOverlay = $levelCard.find('.lock-overlay');
    
    $levelCard.attr('data-locked', isLocked ? 'true' : 'false');
    
    if (isLocked) {
        $lockOverlay.show();
        $levelCard.addClass('locked');
    } else {
        $lockOverlay.hide();
        $levelCard.removeClass('locked');
    }
}

// Global modal functions - must be accessible from onclick attributes
window.canAnswer = canAnswer;
</script>
@endpush
