<!-- Modal Penilaian Kapabilitas -->
<div class="modal modal-blur fade" id="penilaianModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Penilaian Kapabilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @cannot('answer', $assessment)
                <div class="alert alert-info mb-3">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Read-Only Mode:</strong> You can view this data but cannot make changes.
                </div>
                @endcannot
                
                <form id="penilaianForm">
                    <input type="hidden" name="activity_id" id="modal_activity_id">
                    
                    <!-- Activity Info -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-muted small">Management Practice</div>
                                    <div class="fw-bold" id="modal_activity_code">-</div>
                                </div>
                                <div class="col-md-9">
                                    <div class="text-muted small">Activity Name</div>
                                    <div class="fw-bold" id="modal_activity_name">-</div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="text-muted small">Translated</div>
                                    <div id="modal_activity_translated">-</div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <div class="text-muted small">Level</div>
                                    <div>
                                        <span class="badge bg-primary text-white" id="modal_activity_level">-</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small">Weight</div>
                                    <div id="modal_activity_weight">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Selection -->
                    <div class="mb-3">
                        <label class="form-label required">Pilih Rating</label>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                            <!-- N/A -->
                            <label class="form-selectgroup-item flex-fill">
                                <input type="radio" name="capability_rating" value="N/A" class="form-selectgroup-input" required>
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div class="form-selectgroup-label-content d-flex align-items-center justify-content-between w-100">
                                        <div>
                                            <span class="form-selectgroup-title strong mb-1">N/A - Not Applicable</span>
                                            <span class="d-block text-muted">Tidak dapat diterapkan / tidak relevan</span>
                                        </div>
                                        <div class="ms-auto">
                                            <span class="badge bg-secondary text-white">0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- N -->
                            <label class="form-selectgroup-item flex-fill">
                                <input type="radio" name="capability_rating" value="N" class="form-selectgroup-input">
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div class="form-selectgroup-label-content d-flex align-items-center justify-content-between w-100">
                                        <div>
                                            <span class="form-selectgroup-title strong mb-1">N - Not Achieved</span>
                                            <span class="d-block text-muted">Tidak ada atau sangat sedikit bukti pencapaian</span>
                                        </div>
                                        <div class="ms-auto">
                                            <span class="badge bg-danger text-white">0.15</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- P -->
                            <label class="form-selectgroup-item flex-fill">
                                <input type="radio" name="capability_rating" value="P" class="form-selectgroup-input">
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div class="form-selectgroup-label-content d-flex align-items-center justify-content-between w-100">
                                        <div>
                                            <span class="form-selectgroup-title strong mb-1">P - Partially Achieved</span>
                                            <span class="d-block text-muted">Ada beberapa bukti pencapaian, tapi tidak lengkap</span>
                                        </div>
                                        <div class="ms-auto">
                                            <span class="badge bg-warning text-white">0.33</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- L -->
                            <label class="form-selectgroup-item flex-fill">
                                <input type="radio" name="capability_rating" value="L" class="form-selectgroup-input">
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div class="form-selectgroup-label-content d-flex align-items-center justify-content-between w-100">
                                        <div>
                                            <span class="form-selectgroup-title strong mb-1">L - Largely Achieved</span>
                                            <span class="d-block text-muted">Sebagian besar tercapai dengan bukti yang cukup</span>
                                        </div>
                                        <div class="ms-auto">
                                            <span class="badge bg-info text-white">0.67</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- F -->
                            <label class="form-selectgroup-item flex-fill">
                                <input type="radio" name="capability_rating" value="F" class="form-selectgroup-input">
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div class="form-selectgroup-label-content d-flex align-items-center justify-content-between w-100">
                                        <div>
                                            <span class="form-selectgroup-title strong mb-1">F - Fully Achieved</span>
                                            <span class="d-block text-muted">Sepenuhnya tercapai dengan bukti lengkap</span>
                                        </div>
                                        <div class="ms-auto">
                                            <span class="badge bg-success text-white">1.00</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Notes/Comments -->
                    <div class="mb-3">
                        <label class="form-label">Catatan / Komentar</label>
                        <textarea class="form-control" name="notes" rows="4" placeholder="Tambahkan catatan atau komentar untuk penilaian ini..."></textarea>
                        <small class="form-hint">Catatan ini akan disimpan dalam history dan dapat dilihat kembali</small>
                    </div>

                    <!-- Current Evidence Count -->
                    <div id="evidenceInfoAlert">
                        <div class="d-flex">
                            <div>
                                <i class="ti ti-info-circle icon alert-icon"></i>
                            </div>
                            <div>
                                <h4 class="alert-title">Evidence Files</h4>
                                <div class="text-muted">
                                    Terdapat <strong><span id="modal_evidence_count">0</span> evidence files</strong> untuk activity ini.
                                    <br>
                                    <a href="#" id="btnLihatEvidence" class="btn btn-sm btn-primary mt-2" style="display: inline-block;">
                                        <i class="ti ti-folder me-1"></i>Lihat Daftar Evidence
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                @can('answer', $assessment)
                <button type="button" class="btn btn-primary" onclick="savePenilaian()">
                    <i class="ti ti-device-floppy me-2"></i>Simpan Penilaian
                </button>
                @else
                <button type="button" class="btn btn-secondary" disabled>
                    <i class="ti ti-lock me-2"></i>Read-Only Mode
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>

<script>
// Open assessment modal
function openAssessmentModal(activityId) {
    const assessmentId = $('input[name="assessment_id"]').val();
    
    if (!assessmentId) {
        console.error('Assessment ID not found');
        alert('Error: Assessment ID not found');
        return;
    }
    
    // Load activity details
    $.ajax({
        url: `/assessments/${assessmentId}/activity/${activityId}`,
        success: function(activity) {
            $('#modal_activity_id').val(activity.id);
            $('#modal_activity_code').text(activity.code || '-');
            $('#modal_activity_name').text(activity.name || '-');
            $('#modal_activity_translated').text(activity.translated_text || '-');
            $('#modal_activity_level').text('Level ' + (activity.level || '-'));
            $('#modal_activity_weight').text(activity.weight || '1');
            $('#modal_evidence_count').text(activity.evidence_count || 0);
            
            // Update button onclick with activity ID
            $('#btnLihatEvidence').off('click').on('click', function(e) {
                e.preventDefault();
                const actId = $('#modal_activity_id').val();
                if (actId && typeof showEvidence === 'function') {
                    showEvidence(actId);
                }
                return false;
            });
            
            // Set current rating if exists
            if (activity.answer && activity.answer.capability_rating) {
                $(`input[name="capability_rating"][value="${activity.answer.capability_rating}"]`).prop('checked', true);
                $('textarea[name="notes"]').val(activity.answer.notes || '');
            } else {
                $('input[name="capability_rating"]').prop('checked', false);
                $('textarea[name="notes"]').val('');
            }
            
            // Show modal
            $('#penilaianModal').modal('show');
        },
        error: function() {
            toastr.error('Error loading activity details');
        }
    });
}

// Save assessment
function savePenilaian() {
    const form = $('#penilaianForm');
    
    if (!form[0].checkValidity()) {
        form[0].reportValidity();
        return;
    }
    
    const activityId = $('#modal_activity_id').val();
    const assessmentId = $('input[name="assessment_id"]').val();
    const rating = $('input[name="capability_rating"]:checked').val();
    const notes = $('textarea[name="notes"]').val();
    
    $.ajax({
        url: `/assessments/${assessmentId}/activity/${activityId}/answer`,
        type: 'POST',
        data: {
            capability_rating: rating,
            notes: notes,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            $('.modal-footer .btn-primary').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
        },
        success: function(response) {
            if (typeof toastr !== 'undefined') {
                toastr.success('Penilaian berhasil disimpan');
            } else {
                alert('Penilaian berhasil disimpan');
            }
            $('#penilaianModal').modal('hide');
            
            // Reload current level activities
            const currentLevel = $('.level-card.active').data('level') || 1;
            if (typeof loadActivitiesByLevel === 'function') {
                loadActivitiesByLevel(currentLevel);
                // Note: checkAndUpdateLevelAccess() is called automatically after loadActivitiesByLevel completes
            } else {
                location.reload();
            }
            
            // Update achieved level
            const currentGamoId = $('#gamoSelector').val();
            if (typeof updateAchievedLevel === 'function') {
                updateAchievedLevel(currentGamoId);
            }
        },
        error: function(xhr) {
            console.error('Error saving assessment:', xhr);
            const errorMsg = xhr.responseJSON?.message || 'Error saving assessment';
            if (typeof toastr !== 'undefined') {
                toastr.error(errorMsg);
            } else {
                alert('Error: ' + errorMsg);
            }
        },
        complete: function() {
            $('.modal-footer .btn-primary').prop('disabled', false).html('<i class="ti ti-device-floppy me-2"></i>Simpan Penilaian');
        }
    });
}

// Reset modal when closed
$('#penilaianModal').on('hidden.bs.modal', function() {
    $('#penilaianForm')[0].reset();
});
</script>
