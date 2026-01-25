<!-- Modal Daftar Evidence -->
<div class="modal modal-blur fade" id="daftarEvidenceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- GAMO Info (ketika dari menu dropdown) -->
                <div class="card mb-3" id="evidenceGamoInfo">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-muted small">GAMO Code</div>
                                <div class="fw-bold" id="evidenceGamoCode">-</div>
                            </div>
                            <div class="col-md-9">
                                <div class="text-muted small">GAMO Objective</div>
                                <div class="fw-bold" id="evidenceGamoTitle">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Info (ketika dari activity spesifik) -->
                <div class="card mb-3" id="evidenceActivityInfo" style="display: none;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-muted small">Management Practice</div>
                                <div class="fw-bold" id="evidenceActivityCode">-</div>
                            </div>
                            <div class="col-md-9">
                                <div class="text-muted small">Activity Name</div>
                                <div class="fw-bold" id="evidenceActivityName">-</div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="text-muted small">Translated</div>
                                <div id="evidenceActivityTranslated">-</div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <div class="text-muted small">Level</div>
                                <div>
                                    <span class="badge bg-primary text-white" id="evidenceActivityLevel">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @can('answer', $assessment)
                <!-- Upload Evidence Form -->
                <div class="card mb-3" id="evidenceUploadSection">
                    <div class="card-header">
                        <h3 class="card-title">Upload Evidence Baru</h3>
                    </div>
                    <div class="card-body">
                        <form id="evidenceUploadFormModal" enctype="multipart/form-data">
                            <input type="hidden" id="modal_activity_id" name="activity_id">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Nama Evidence</label>
                                    <input type="text" class="form-control" name="evidence_name_modal" placeholder="Nama evidence" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">Tipe Evidence</label>
                                    <select class="form-select" name="evidence_type_modal" id="evidenceTypeModal" required>
                                        <option value="file">File Upload</option>
                                        <option value="url">URL Link</option>
                                    </select>
                                </div>
                                <div class="col-md-12" id="evidenceFileField">
                                    <label class="form-label required">Upload File</label>
                                    <input type="file" class="form-control" name="evidence_file_modal" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                                </div>
                                <div class="col-md-12" id="evidenceUrlField" style="display: none;">
                                    <label class="form-label required">URL Link</label>
                                    <input type="url" class="form-control" name="evidence_url_modal" placeholder="https://...">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Keterangan</label>
                                    <textarea class="form-control" name="evidence_description_modal" rows="2" placeholder="Keterangan tambahan..."></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-upload me-2"></i>Upload Evidence
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endcan

                <!-- Evidence List -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Evidence</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Evidence</th>
                                    <th>Tipe</th>
                                    <th>Keterangan</th>
                                    <th>Upload By</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="modalEvidenceTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                                        <span class="text-muted ms-2">Loading evidence...</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {

    /* =========================
     * RENDER EVIDENCE TABLE
     * ========================= */
    window.renderEvidence = function (evidenceList) {
        console.log('[renderEvidence] START');
        console.log('[renderEvidence] Input:', evidenceList);
        
        // Get fresh reference to tbody - TIDAK pakai cache
        const tbody = document.getElementById('modalEvidenceTableBody');
        if (!tbody) {
            console.error('[renderEvidence] tbody NOT FOUND!');
            return;
        }
        console.log('[renderEvidence] tbody found via getElementById');
        
        // FORCE CLEAR dengan vanilla JS
        tbody.innerHTML = '';
        
        if (!Array.isArray(evidenceList) || evidenceList.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        Belum ada evidence
                    </td>
                </tr>
            `;
            console.log('[renderEvidence] Empty state set');
            return;
        }

        const assessmentId = document.querySelector('input[name="assessment_id"]').value;
        console.log('[renderEvidence] Building', evidenceList.length, 'rows');

        // Build dengan vanilla JS innerHTML (paling reliable)
        const rowsHtml = evidenceList.map((ev, idx) => {
            const downloadBtn = ev.url
                ? `<a href="${ev.url}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-external-link"></i></a>`
                : `<a href="/assessments/${assessmentId}/evidence/${ev.id}/download" class="btn btn-sm btn-outline-primary"><i class="ti ti-download"></i></a>`;
            
            return `
            <tr>
                <td>${idx + 1}</td>
                <td>
                    <div class="fw-bold">${ev.evidence_name || '-'}</div>
                    <div class="text-muted small">${ev.activity_code || '-'}</div>
                </td>
                <td>${ev.url ? '<span class="badge text-white bg-info">URL</span>' : '<span class="badge text-white bg-primary">File</span>'}</td>
                <td class="text-muted small">${ev.evidence_description || ev.description || '-'}</td>
                <td>${ev.uploaded_by || (ev.user && ev.user.name) || '-'}</td>
                <td class="small">${ev.uploaded_at || ev.created_at || '-'}</td>
                <td class="text-center">${downloadBtn}</td>
            </tr>`;
        }).join('');
        
        tbody.innerHTML = rowsHtml;
        console.log('[renderEvidence] COMPLETE - innerHTML set, rows:', tbody.querySelectorAll('tr').length);
    };


    /* =========================
     * LOAD EVIDENCE (AMAN)
     * ========================= */
    function loadEvidence({ assessmentId, activityId = null, gamoId = null }) {
        console.log('[loadEvidence] START', { assessmentId, activityId, gamoId });

        // Show loading dengan vanilla JS
        const tbody = document.getElementById('modalEvidenceTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center">
                    <div class="spinner-border spinner-border-sm text-muted"></div>
                    <span class="text-muted ms-2">Loading evidence...</span>
                </td>
            </tr>
        `;

        const url = activityId
            ? `/assessments/${assessmentId}/activity/${activityId}/evidence`
            : `/assessments/${assessmentId}/evidence-repository?gamo_id=${gamoId || ''}`;
        
        console.log('[loadEvidence] AJAX URL:', url);

        $.ajax({
            url: url,
            success: function (response) {
                console.log('[loadEvidence] SUCCESS');
                const list = response.evidence || response.data || [];
                console.log('[loadEvidence] Count:', list.length);
                
                // Render immediately - no delay needed since modal already shown
                window.renderEvidence(list);
            },
            error: function (xhr, status, error) {
                console.error('[loadEvidence] ERROR:', status, error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-danger">
                            Gagal memuat evidence
                        </td>
                    </tr>
                `;
            }
        });
    }


    /* =========================
     * SHOW EVIDENCE MODAL
     * ========================= */
    window.showEvidence = function (activityId = null) {
        const assessmentId = $('input[name="assessment_id"]').val();
        if (!assessmentId) return;

        if (activityId) {
            // === DARI ACTIVITY ===
            $.get(`/assessments/${assessmentId}/activity/${activityId}`, function (activity) {

                $('#evidenceGamoInfo').hide();
                $('#evidenceActivityInfo').show();
                $('#evidenceUploadSection').show();

                $('#evidenceActivityCode').text(activity.code || '-');
                $('#evidenceActivityName').text(activity.name || '-');
                $('#evidenceActivityTranslated').text(activity.translated_text || '-');
                $('#evidenceActivityLevel').text('Level ' + (activity.level || '-'));

                $('#modal_activity_id').val(activityId);

                const $modal = $('#daftarEvidenceModal');
                $modal.data('assessmentId', assessmentId)
                      .data('activityId', activityId)
                      .data('gamoId', null);
                
                // Load evidence AFTER modal shown
                $modal.one('shown.bs.modal', function() {
                    loadEvidence({ assessmentId, activityId });
                });
                
                $modal.modal('show');
            });

        } else {
            // === DARI GAMO ===
            const gamoId = $('#gamoSelector').val();
            const gamoText = $('#gamoSelector option:selected').text().split(' - ');

            $('#evidenceGamoInfo').show();
            $('#evidenceActivityInfo').hide();
            $('#evidenceUploadSection').hide();

            $('#evidenceGamoCode').text(gamoText[0] || '-');
            $('#evidenceGamoTitle').text(gamoText.slice(1).join(' - ') || '-');

            const $modal = $('#daftarEvidenceModal');
            $modal.data('assessmentId', assessmentId)
                  .data('gamoId', gamoId)
                  .data('activityId', null);
            
            // Load evidence AFTER modal shown
            $modal.one('shown.bs.modal', function() {
                loadEvidence({ assessmentId, gamoId });
            });
            
            $modal.modal('show');
        }
    };


    /* =========================
     * TOGGLE FILE/URL FIELD
     * ========================= */
    $('#evidenceTypeModal').on('change', function() {
        const type = $(this).val();
        if (type === 'file') {
            $('#evidenceFileField').show();
            $('#evidenceUrlField').hide();
            $('input[name="evidence_file_modal"]').prop('required', true);
            $('input[name="evidence_url_modal"]').prop('required', false);
        } else {
            $('#evidenceFileField').hide();
            $('#evidenceUrlField').show();
            $('input[name="evidence_file_modal"]').prop('required', false);
            $('input[name="evidence_url_modal"]').prop('required', true);
        }
    });


    /* =========================
     * UPLOAD EVIDENCE (AJAX)
     * ========================= */
    $('#evidenceUploadFormModal').on('submit', function(e) {
        e.preventDefault();
        
        const assessmentId = $('input[name="assessment_id"]').val();
        const activityId = $('#modal_activity_id').val();
        
        if (!assessmentId || !activityId) {
            toastr.error('Assessment or Activity ID not found');
            return;
        }
        
        const formData = new FormData();
        formData.append('evidence_name', $('input[name="evidence_name_modal"]').val());
        formData.append('evidence_description', $('textarea[name="evidence_description_modal"]').val());
        
        const evidenceType = $('#evidenceTypeModal').val();
        if (evidenceType === 'file') {
            const fileInput = $('input[name="evidence_file_modal"]')[0];
            if (fileInput.files.length > 0) {
                formData.append('file', fileInput.files[0]);
            }
        } else {
            formData.append('url', $('input[name="evidence_url_modal"]').val());
        }
        
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Uploading...');
        
        $.ajax({
            url: `/assessments/${assessmentId}/activity/${activityId}/evidence`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Evidence berhasil diupload! 🎉');
                    
                    // Reset form
                    $('#evidenceUploadFormModal')[0].reset();
                    $('#evidenceFileField').show();
                    $('#evidenceUrlField').hide();
                    
                    // Reload evidence list
                    loadEvidence({ assessmentId, activityId });
                    
                    // Update evidence count badge di halaman utama
                    const $badge = $(`.evidence-badge[data-activity-id="${activityId}"]`);
                    if ($badge.length) {
                        const currentCount = parseInt($badge.text()) || 0;
                        $badge.text(currentCount + 1);
                    }
                } else {
                    toastr.error(response.message || 'Gagal upload evidence');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Gagal upload evidence';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMsg = errors.join(', ');
                }
                toastr.error(errorMsg);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });


    /* =========================
     * RESET MODAL (BUG FIX)
     * ========================= */
    $('#daftarEvidenceModal').on('hidden.bs.modal', function () {
        $('#evidenceUploadFormModal')[0]?.reset();
        $('#evidenceFileField').show();
        $('#evidenceUrlField').hide();

        $(this)
            .removeData('activityId')
            .removeData('gamoId')
            .removeData('assessmentId');
    });

});
</script>
