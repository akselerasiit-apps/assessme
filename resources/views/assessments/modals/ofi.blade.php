<!-- Modal OFI (Opportunity for Improvement) -->
<div class="modal modal-blur fade" id="ofiModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary-lt">
                <h5 class="modal-title">
                    <i class="ti ti-bulb me-2"></i>
                    Opportunity for Improvement (OFI)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- GAMO Info -->
                <div class="card mb-3 bg-blue-lt">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-1" id="ofiGamoCode">-</h3>
                                <div class="text-muted" id="ofiGamoName">-</div>
                            </div>
                            <div class="col-auto">
                                <div class="text-end">
                                    <div class="text-muted small">Current Capability</div>
                                    <div class="h2 mb-0">
                                        <span id="ofiCurrentLevel">-</span>
                                        <span class="text-muted mx-2">→</span>
                                        <span class="text-primary" id="ofiTargetLevel">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#ofiAutoTab" role="tab">
                            <i class="ti ti-sparkles me-2"></i>Auto-Generated
                            <span class="badge text-white bg-primary ms-2" id="ofiAutoCount">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#ofiManualTab" role="tab">
                            <i class="ti ti-edit me-2"></i>Manual / Custom
                            <span class="badge text-white bg-cyan ms-2" id="ofiManualCount">0</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Auto-Generated Tab -->
                    <div class="tab-pane active show" id="ofiAutoTab" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="text-muted mb-0">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Rekomendasi aktivitas yang dapat dilakukan untuk mencapai tingkat kematangan target
                                </p>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-primary btn-sm" onclick="generateAutoOFI()">
                                    <i class="ti ti-sparkles me-1"></i>Generate
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="copyAutoOFI()">
                                    <i class="ti ti-copy me-1"></i>Salin
                                </button>
                            </div>
                        </div>
                        
                        <div id="ofiAutoContent">
                            <div class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm mb-2" role="status"></div>
                                <div>Loading recommendations...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Tab -->
                    <div class="tab-pane" id="ofiManualTab" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="text-muted mb-0">
                                    Tambahkan improvement points kustom sesuai kebutuhan organisasi
                                </p>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-primary btn-sm" onclick="showAddOFIForm()">
                                    <i class="ti ti-plus me-1"></i>Tambah OFI
                                </button>
                                <button class="btn btn-outline-primary btn-sm" onclick="copyManualOFI()">
                                    <i class="ti ti-copy me-1"></i>Salin
                                </button>
                            </div>
                        </div>

                        <!-- Add/Edit Form (Hidden by default) -->
                        <div id="ofiFormCard" class="card mb-3" style="display: none;">
                            <div class="card-body">
                                <form id="ofiForm">
                                    <input type="hidden" id="ofiId" name="ofi_id">
                                    <input type="hidden" id="ofiAssessmentId" name="assessment_id">
                                    <input type="hidden" id="ofiGamoId" name="gamo_objective_id">
                                    
                                    <div class="mb-3">
                                        <label class="form-label required">Judul OFI</label>
                                        <input type="text" class="form-control" id="ofiTitle" name="title" required 
                                               placeholder="Contoh: Implementasi dokumentasi proses bisnis">
                                        <!-- Priority hidden, default to medium -->
                                        <input type="hidden" id="ofiPriority" name="priority" value="medium">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Deskripsi / Rencana Aksi</label>
                                        <div id="ofiDescriptionEditor" style="height: 200px;"></div>
                                        <input type="hidden" id="ofiDescription" name="description">
                                    </div>
                                    
                                    <!-- Hidden fields -->
                                    <input type="hidden" id="ofiCategory" name="category" value="">
                                    <input type="hidden" id="ofiTargetDate" name="target_date" value="">
                                    <input type="hidden" id="ofiStatus" name="status" value="open">

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-check me-1"></i>Simpan OFI
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="cancelOFIForm()">
                                            <i class="ti ti-x me-1"></i>Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Manual OFI List -->
                        <div id="ofiManualContent">
                            <div class="text-center py-5 text-muted">
                                <i class="ti ti-notes-off" style="font-size: 3rem;"></i>
                                <div class="mt-2">Belum ada OFI manual</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Quill.js CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
let quillEditor;
// assessmentId and currentGamoId are declared in answer-new.blade.php

// Initialize Quill editor
function initQuillEditor() {
    if (!quillEditor) {
        quillEditor = new Quill('#ofiDescriptionEditor', {
            theme: 'snow',
            placeholder: 'Tuliskan deskripsi lengkap improvement plan...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });
    }
}

// Show OFI Modal
function showOFIModal(gamoId) {
    // Don't modify global currentGamoId - use parameter only
    $('#ofiModal').modal('show');
    
    // Initialize Quill if not yet initialized
    setTimeout(() => {
        initQuillEditor();
    }, 300);
    
    loadOFIData(gamoId);
}

// Load OFI Data
function loadOFIData(gamoId) {
    // Use global assessmentId from answer-new.blade.php
    const ofiAssessmentId = typeof assessmentId !== 'undefined' ? assessmentId : {{ request()->route('assessment')->id ?? 'null' }};
    
    // Load GAMO info
    $.ajax({
        url: `/assessments/${ofiAssessmentId}/gamo/${gamoId}/ofi`,
        method: 'GET',
        success: function(response) {
            // Update header info
            $('#ofiGamoCode').text(response.gamo.code);
            $('#ofiGamoName').text(response.gamo.name);
            
            // Current level display
            const currentLevelText = response.current_level > 0 ? 'Level ' + response.current_level : 'Not Achieved';
            $('#ofiCurrentLevel').text(currentLevelText);
            $('#ofiTargetLevel').text('Level ' + response.target_level);
            
            // Update counts
            $('#ofiAutoCount').text(response.auto_ofis.length);
            $('#ofiManualCount').text(response.manual_ofis.length);
            
            // Render auto-generated OFIs
            renderAutoOFIs(response.auto_ofis);
            
            // Render manual OFIs
            renderManualOFIs(response.manual_ofis);
            
            // Store for form
            $('#ofiAssessmentId').val(ofiAssessmentId);
            $('#ofiGamoId').val(gamoId);
        },
        error: function(xhr) {
            console.error('Error loading OFI data:', xhr);
        }
    });
}

// Render Auto-Generated OFIs
function renderAutoOFIs(ofis) {
    if (ofis.length === 0) {
        $('#ofiAutoContent').html(`
            <div class="alert alert-info mb-0">
                <div class="d-flex align-items-start">
                    <i class="ti ti-info-circle me-2" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>Belum ada rekomendasi otomatis</strong>
                        <p class="mb-0 mt-1">Klik tombol "Generate" untuk membuat rekomendasi berdasarkan gap analysis antara current level dan target level.</p>
                    </div>
                </div>
            </div>
        `);
        return;
    }
    
    let html = '<div class="list-group list-group-flush">';
    
    ofis.forEach((ofi, index) => {
        html += `
            <div class="list-group-item">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <span class="avatar avatar-sm bg-primary-lt">
                            ${index + 1}
                        </span>
                    </div>
                    <div class="flex-fill">
                        ${ofi.description}
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    $('#ofiAutoContent').html(html);
}

// Render Manual OFIs
function renderManualOFIs(ofis) {
    if (ofis.length === 0) {
        $('#ofiManualContent').html(`
            <div class="text-center py-5 text-muted">
                <i class="ti ti-notes-off" style="font-size: 3rem;"></i>
                <div class="mt-2">Belum ada OFI manual</div>
                <button class="btn btn-primary btn-sm mt-3" onclick="showAddOFIForm()">
                    <i class="ti ti-plus me-1"></i>Tambah OFI Pertama
                </button>
            </div>
        `);
        return;
    }
    
    let html = '<div class="list-group list-group-flush">';
    
    ofis.forEach(ofi => {
        const priorityColors = {
            'low': 'secondary',
            'medium': 'info',
            'high': 'warning',
            'critical': 'danger'
        };
        
        const statusColors = {
            'open': 'secondary',
            'in_progress': 'info',
            'resolved': 'success',
            'closed': 'dark'
        };
        
        html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h4 class="mb-1">${ofi.title}</h4>
                    </div>
                    <div class="btn-list">
                        <button class="btn btn-sm btn-icon btn-ghost-primary" onclick="editOFI(${ofi.id})" title="Edit">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-ghost-danger" onclick="deleteOFI(${ofi.id})" title="Delete">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="text-muted">
                    ${ofi.description}
                </div>
                ${ofi.created_by_name ? `
                    <div class="text-muted small mt-2">
                        <i class="ti ti-user me-1"></i>Created by ${ofi.created_by_name}
                        <span class="mx-2">•</span>
                        <i class="ti ti-clock me-1"></i>${formatDateTime(ofi.created_at)}
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    html += '</div>';
    $('#ofiManualContent').html(html);
}

// Show Add OFI Form
function showAddOFIForm() {
    $('#ofiFormCard').slideDown();
    $('#ofiForm')[0].reset();
    $('#ofiId').val('');
    quillEditor.root.innerHTML = '';
    quillEditor.focus();
}

// Cancel OFI Form
function cancelOFIForm() {
    $('#ofiFormCard').slideUp();
    $('#ofiForm')[0].reset();
    quillEditor.root.innerHTML = '';
}

// Submit OFI Form
$('#ofiForm').on('submit', function(e) {
    e.preventDefault();
    
    // Get Quill content
    const description = quillEditor.root.innerHTML;
    $('#ofiDescription').val(description);
    
    const formData = $(this).serialize();
    const ofiId = $('#ofiId').val();
    const gamoId = $('#ofiGamoId').val();
    
    const url = ofiId ? 
        `/assessments/${assessmentId}/ofi/${ofiId}` : 
        `/assessments/${assessmentId}/gamo/${gamoId}/ofi`;
    const method = ofiId ? 'PUT' : 'POST';
    
    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function(response) {
            toastr.success(ofiId ? 'OFI updated successfully' : 'OFI created successfully');
            cancelOFIForm();
            loadOFIData(currentGamoId);
        },
        error: function(xhr) {
            toastr.error('Error saving OFI');
            console.error(xhr);
        }
    });
});

// Edit OFI
function editOFI(ofiId) {
    $.ajax({
        url: `/assessments/${assessmentId}/ofi/${ofiId}`,
        method: 'GET',
        success: function(ofi) {
            $('#ofiId').val(ofi.id);
            $('#ofiTitle').val(ofi.title);
            $('#ofiPriority').val(ofi.priority);
            $('#ofiCategory').val(ofi.category);
            $('#ofiStatus').val(ofi.status);
            $('#ofiTargetDate').val(ofi.target_date);
            quillEditor.root.innerHTML = ofi.description;
            
            $('#ofiFormCard').slideDown();
            $('html, body').animate({
                scrollTop: $('#ofiFormCard').offset().top - 100
            }, 500);
        },
        error: function(xhr) {
            toastr.error('Error loading OFI');
        }
    });
}

// Delete OFI
function deleteOFI(ofiId) {
    if (!confirm('Apakah Anda yakin ingin menghapus OFI ini?')) return;
    
    $.ajax({
        url: `/assessments/${assessmentId}/ofi/${ofiId}`,
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
            toastr.success('OFI deleted successfully');
            loadOFIData(currentGamoId);
        },
        error: function(xhr) {
            toastr.error('Error deleting OFI');
        }
    });
}

// Generate Auto OFI
function generateAutoOFI() {
    const gamoId = $('#ofiGamoId').val();
    
    if (!gamoId) {
        toastr.error('GAMO ID not found');
        return;
    }
    
    // Show loading
    $('#ofiAutoContent').html(`
        <div class="text-center py-5 text-muted">
            <div class="spinner-border spinner-border-sm mb-2" role="status"></div>
            <div>Generating recommendations...</div>
        </div>
    `);
    
    $.ajax({
        url: `/assessments/${assessmentId}/gamo/${gamoId}/ofi/generate`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            toastr.success(response.message || 'Auto OFI generated successfully');
            // Reload OFI data to show new auto-generated OFIs
            loadOFIData(gamoId);
        },
        error: function(xhr) {
            toastr.error('Error generating auto OFI');
            console.error(xhr);
            // Show empty state
            renderAutoOFIs([]);
        }
    });
}

// Copy Auto OFI
function copyAutoOFI() {
    const content = $('#ofiAutoContent').text().trim();
    navigator.clipboard.writeText(content).then(() => {
        toastr.success('OFI recommendations copied to clipboard');
    }).catch(err => {
        toastr.error('Failed to copy');
    });
}

// Copy Manual OFI
function copyManualOFI() {
    const content = $('#ofiManualContent').text().trim();
    
    if (!content || content === 'Belum ada OFI manual') {
        toastr.warning('Tidak ada OFI manual untuk disalin');
        return;
    }
    
    navigator.clipboard.writeText(content).then(() => {
        toastr.success('Manual OFI copied to clipboard');
    }).catch(err => {
        toastr.error('Failed to copy');
    });
}

// Reset modal on close
$('#ofiModal').on('hidden.bs.modal', function() {
    cancelOFIForm();
    currentGamoId = null;
    $('.nav-tabs a:first').tab('show');
});
</script>
