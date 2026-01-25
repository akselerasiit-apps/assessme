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
                                    <div class="text-muted small">Target Level (P/T2: <span id="ofiTargetYear">-</span>)</div>
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
                            <span class="badge bg-primary ms-2" id="ofiAutoCount">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#ofiManualTab" role="tab">
                            <i class="ti ti-edit me-2"></i>Manual / Custom
                            <span class="badge bg-cyan ms-2" id="ofiManualCount">0</span>
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
                            <button class="btn btn-outline-primary btn-sm" onclick="copyAutoOFI()">
                                <i class="ti ti-copy me-1"></i>Salin Semua
                            </button>
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
                            <button class="btn btn-primary btn-sm" onclick="showAddOFIForm()">
                                <i class="ti ti-plus me-1"></i>Tambah OFI
                            </button>
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

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Kategori</label>
                                            <select class="form-select" id="ofiCategory" name="category">
                                                <option value="">Pilih kategori...</option>
                                                <option value="Process">Process</option>
                                                <option value="People">People</option>
                                                <option value="Technology">Technology</option>
                                                <option value="Governance">Governance</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Target Date</label>
                                            <input type="date" class="form-control" id="ofiTargetDate" name="target_date">
                                        </div>
                                        <!-- Status hidden, default to open -->
                                        <input type="hidden" id="ofiStatus" name="status" value="open">
                                    </div>

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
// currentGamoId and currentAssessmentId are declared in answer-new.blade.php

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
    currentGamoId = gamoId;
    currentAssessmentId = $('input[name="assessment_id"]').val();
    
    $('#ofiModal').modal('show');
    
    // Initialize Quill if not yet initialized
    setTimeout(() => {
        initQuillEditor();
    }, 300);
    
    loadOFIData(gamoId);
}

// Load OFI Data
function loadOFIData(gamoId) {
    const assessmentId = $('input[name="assessment_id"]').val();
    
    // Load GAMO info
    $.ajax({
        url: `/assessments/${assessmentId}/gamo/${gamoId}/ofi`,
        method: 'GET',
        success: function(response) {
            // Update header info
            $('#ofiGamoCode').text(response.gamo.code);
            $('#ofiGamoName').text(response.gamo.name);
            $('#ofiCurrentLevel').text('Level ' + (response.current_level || '-'));
            $('#ofiTargetLevel').text('Level ' + (response.target_level || '-'));
            $('#ofiTargetYear').text(response.target_year || '4');
            
            // Update counts
            $('#ofiAutoCount').text(response.auto_ofis.length);
            $('#ofiManualCount').text(response.manual_ofis.length);
            
            // Render auto-generated OFIs
            renderAutoOFIs(response.auto_ofis);
            
            // Render manual OFIs
            renderManualOFIs(response.manual_ofis);
            
            // Store for form
            $('#ofiAssessmentId').val(assessmentId);
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
            <div class="alert alert-info">
                <i class="ti ti-info-circle me-2"></i>
                Tidak ada gap yang perlu diperbaiki. Current level sudah mencapai atau melebihi target.
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
                    <div>
                        <h4 class="mb-1">${ofi.title}</h4>
                        <div class="d-flex gap-2 flex-wrap">
                            ${ofi.category ? `<span class="badge bg-blue-lt">${ofi.category}</span>` : ''}
                            ${ofi.target_date ? `<span class="badge bg-orange-lt"><i class="ti ti-calendar me-1"></i>${ofi.target_date}</span>` : ''}
                        </div>
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
    const url = ofiId ? 
        `/assessments/${currentAssessmentId}/ofi/${ofiId}` : 
        `/assessments/${currentAssessmentId}/ofi`;
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
        url: `/assessments/${currentAssessmentId}/ofi/${ofiId}`,
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
        url: `/assessments/${currentAssessmentId}/ofi/${ofiId}`,
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

// Copy Auto OFI
function copyAutoOFI() {
    const content = $('#ofiAutoContent').text().trim();
    navigator.clipboard.writeText(content).then(() => {
        toastr.success('OFI recommendations copied to clipboard');
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
