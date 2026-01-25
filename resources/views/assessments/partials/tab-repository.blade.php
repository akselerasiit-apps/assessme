<!-- Filter Section -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Filter Level</label>
                <select class="form-select" id="filterLevel">
                    <option value="">Semua Level</option>
                    <option value="1">Level 1 - Performed</option>
                    <option value="2">Level 2 - Managed</option>
                    <option value="3">Level 3 - Established</option>
                    <option value="4">Level 4 - Predictable</option>
                    <option value="5">Level 5 - Optimizing</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Filter Tipe</label>
                <select class="form-select" id="filterType">
                    <option value="">Semua Tipe</option>
                    <option value="file">📁 File Upload</option>
                    <option value="url">🔗 URL Link</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small mb-1">Cari Evidence</label>
                <input type="text" class="form-control" id="searchEvidence" placeholder="Cari berdasarkan deskripsi atau filename...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" onclick="loadRepositoryEvidence()">
                    <i class="ti ti-refresh me-1"></i>Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Evidence Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-hover">
            <thead>
                <tr>
                    <th>Tipe</th>
                    <th>Nama Evidence</th>
                    <th>Deskripsi</th>
                    <th>Activity</th>
                    <th>GAMO</th>
                    <th>File / URL</th>
                    <th>Upload Date</th>
                    <th class="w-1">Action</th>
                </tr>
            </thead>
            <tbody id="evidenceTableBody">
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="spinner-border text-muted" role="status"></div>
                        <div class="text-muted mt-2">Loading evidence repository...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Statistics Footer -->
<div class="card mt-3">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4">
                <div class="text-muted small">Total Evidence</div>
                <div class="h2 mb-0" id="evidenceCount">0</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">File Uploads</div>
                <div class="h2 mb-0 text-primary" id="fileCount">0</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">URL Links</div>
                <div class="h2 mb-0 text-info" id="urlCount">0</div>
            </div>
        </div>
    </div>
</div>

<script>
// Load repository evidence - Load all evidence for the assessment
function loadRepositoryEvidence() {
    const assessmentId = $('input[name="assessment_id"]').val();
    const filterLevel = $('#filterLevel').val();
    const filterType = $('#filterType').val();
    const searchQuery = $('#searchEvidence').val();
    
    $('#evidenceTableBody').html(`
        <tr>
            <td colspan="8" class="text-center py-5">
                <div class="spinner-border text-muted" role="status"></div>
                <div class="text-muted mt-2">Loading evidence repository...</div>
            </td>
        </tr>
    `);
    
    $.ajax({
        url: `/assessments/${assessmentId}/evidence-repository`,
        method: 'GET',
        data: {
            level: filterLevel,
            type: filterType,
            search: searchQuery
        },
        success: function(response) {
            try {
                renderRepositoryGrid(response.evidence || []);
                
                // Update statistics
                const evidenceList = response.evidence || [];
                $('#evidenceCount').text(evidenceList.length);
                
                const fileCount = evidenceList.filter(e => e.file_path && !e.url).length;
                const urlCount = evidenceList.filter(e => e.url).length;
                
                $('#fileCount').text(fileCount);
                $('#urlCount').text(urlCount);
            } catch(e) {
                console.error('Error in success callback:', e);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading evidence:', xhr.status, xhr.responseText, error);
            $('#evidenceGrid').html(`
                <div class="col-12 text-center py-5 text-danger">
                    <i class="ti ti-alert-circle icon mb-2"></i>
                    <div>Error loading evidence</div>
                </div>
            `);
        }
    });
}

// Render evidence grid
function renderRepositoryGrid(evidenceList) {
    if (!evidenceList || evidenceList.length === 0) {
        $('#evidenceTableBody').html(`
            <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                    <i class="ti ti-folder-off icon mb-2" style="font-size: 3rem;"></i>
                    <div>Belum ada evidence</div>
                </td>
            </tr>
        `);
        return;
    }
    
    const canAnswer = typeof window.canAnswer !== 'undefined' ? window.canAnswer : false;
    let html = '';
    
    try {
        evidenceList.forEach(evidence => {
            const isUrl = evidence.url && evidence.url.trim() !== '';
            const icon = isUrl ? 'ti-link' : 'ti-file-text';
            const iconColor = isUrl ? 'text-info' : 'text-primary';
            const typeLabel = isUrl ? 'URL' : 'File';
            
            // Nama Evidence (yang diinput user saat upload)
            const evidenceName = evidence.evidence_name || 'Unnamed Evidence';
            
            // Nama file atau URL untuk kolom terpisah
            let fileName = '-';
            if (isUrl) {
                fileName = evidence.url;
            } else if (evidence.file_path && evidence.file_path.trim() !== '') {
                // Extract filename dari path
                fileName = evidence.file_path.split('/').pop();
            }
            
            // Deskripsi evidence
            const description = evidence.description || '-';
            
            // URL untuk view/download
            const viewUrl = isUrl ? evidence.url : `/assessments/${evidence.assessment_id || 'X'}/evidence/${evidence.id}/download`;
            
            const deleteButton = canAnswer ? `
                <button class="btn btn-sm btn-outline-danger" onclick="deleteEvidence(${evidence.id})" title="Hapus Evidence">
                    <i class="ti ti-trash"></i>
                </button>
            ` : '';
            
            html += `
                <tr>
                    <td>
                        <span class="text-white badge bg-${isUrl ? 'info' : 'primary'}">
                            <i class="ti ${icon} me-1"></i>${typeLabel}
                        </span>
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width: 200px;" title="${evidenceName}">
                            <strong>${evidenceName}</strong>
                        </div>
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width: 200px;" title="${description}">
                            ${description}
                        </div>
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width: 150px;" title="${evidence.activity_code || 'N/A'}">
                            ${evidence.activity_code || 'N/A'}
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-outline">${evidence.gamo_code || 'N/A'}</span>
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width: 250px;" title="${fileName}">
                            <i class="ti ${icon} me-1 ${iconColor}"></i>${fileName}
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <span class="text-muted">${evidence.uploaded_at || 'N/A'}</span>
                    </td>
                    <td class="text-nowrap">
                        <div class="btn-list">
                            <a href="${viewUrl}" class="btn btn-sm btn-outline-primary" ${isUrl ? 'target="_blank"' : ''} title="Lihat Evidence">
                                <i class="ti ti-eye"></i>
                            </a>
                            ${deleteButton}
                        </div>
                    </td>
                </tr>
            `;
        });
    
        $('#evidenceTableBody').html(html);
    
    } catch (error) {
        console.error('Error rendering evidence grid:', error);
        $('#evidenceTableBody').html(`
            <tr>
                <td colspan="8" class="text-center py-5 text-danger">
                    <i class="ti ti-alert-circle icon mb-2"></i>
                    <div>Error rendering evidence</div>
                </td>
            </tr>
        `);
    }
}

// Delete evidence
function deleteEvidence(evidenceId) {
    if (!confirm('Hapus evidence ini?')) return;
    
    const assessmentId = $('input[name="assessment_id"]').val();
    
    $.ajax({
        url: `/assessments/${assessmentId}/evidence/${evidenceId}`,
        type: 'DELETE',
        success: function() {
            toastr.success('Evidence berhasil dihapus');
            loadRepositoryEvidence();
        },
        error: function(xhr) {
            toastr.error(xhr.responseJSON?.message || 'Error deleting evidence');
        }
    });
}

// Filters
$('#filterLevel, #filterType').on('change', loadRepositoryEvidence);
$('#searchEvidence').on('keyup', debounce(loadRepositoryEvidence, 500));

// Debounce helper
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Load repository when tab is shown
$(document).on('shown.bs.tab', 'a[href="#tab-repository"]', function() {
    loadRepositoryEvidence();
});

// Check if repository tab is active on page load
$(document).ready(function() {
    if ($('#tab-repository').hasClass('active') || $('a[href="#tab-repository"]').hasClass('active')) {
        loadRepositoryEvidence();
    }
});
</script>
