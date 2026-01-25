<!-- Modal Daftar Catatan -->
<div class="modal modal-blur fade" id="notesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Catatan / Note Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Filter Section -->
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <select class="form-select" id="notesFilterLevel">
                            <option value="">Semua Level</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3</option>
                            <option value="4">Level 4</option>
                            <option value="5">Level 5</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="notesSearch" placeholder="Cari catatan...">
                    </div>
                </div>

                <!-- Notes List -->
                <div id="notesList">
                    <div class="text-center py-4">
                        <div class="spinner-border text-muted" role="status"></div>
                        <div class="text-muted mt-2">Loading notes...</div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card mt-3 bg-light">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col">
                                <div class="h4 mb-0" id="notesTotal">0</div>
                                <div class="text-muted small">Total Notes</div>
                            </div>
                            <div class="col">
                                <div class="h4 mb-0" id="notesWithRating">0</div>
                                <div class="text-muted small">With Rating</div>
                            </div>
                            <div class="col">
                                <div class="h4 mb-0" id="notesWithoutRating">0</div>
                                <div class="text-muted small">Without Rating</div>
                            </div>
                        </div>
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
// Show notes modal
function showNotesModal() {
    $('#notesModal').modal('show');
    loadNotesList();
}

// Load notes list
function loadNotesList() {
    const assessmentId = $('input[name="assessment_id"]').val();
    const gamoId = $('#gamoSelector').val();
    const filterLevel = $('#notesFilterLevel').val();
    const searchQuery = $('#notesSearch').val();
    
    $('#notesList').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-muted" role="status"></div>
            <div class="text-muted mt-2">Loading notes...</div>
        </div>
    `);
    
    $.ajax({
        url: `/assessments/${assessmentId}/gamo/${gamoId}/notes`,
        data: {
            level: filterLevel,
            search: searchQuery
        },
        success: function(response) {
            renderNotesList(response.notes || []);
            updateNotesStatistics(response.statistics || {});
        },
        error: function() {
            $('#notesList').html(`
                <div class="text-center py-4 text-danger">
                    <i class="ti ti-alert-circle icon mb-2"></i>
                    <div>Error loading notes</div>
                </div>
            `);
        }
    });
}

// Render notes list
function renderNotesList(notesList) {
    if (notesList.length === 0) {
        $('#notesList').html(`
            <div class="text-center py-4 text-muted">
                <i class="ti ti-notes-off icon mb-2" style="font-size: 2rem;"></i>
                <div>Belum ada catatan</div>
            </div>
        `);
        return;
    }
    
    let html = '<div class="list-group list-group-flush">';
    
    notesList.forEach(note => {
        const hasRating = note.rating && note.rating !== 'N/A';
        const ratingBadge = getRatingBadge(note.rating);
        const levelBadge = `<span class="badge text-white bg-primary">Level ${note.level || '-'}</span>`;
        
        html += `
            <div class="list-group-item">
                <div class="row align-items-start">
                    <div class="col-auto">
                        <span class="avatar text-white ${hasRating ? 'bg-success' : 'bg-secondary'}">
                            <i class="ti ${hasRating ? 'ti-notes' : 'ti-note'}"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-bold">${note.activity_code || 'Unknown Activity'}</div>
                                <div class="text-muted small">${note.activity_name || ''}</div>
                            </div>
                            <div class="d-flex gap-1">
                                ${levelBadge}
                                ${ratingBadge}
                            </div>
                        </div>
                        <div class="card bg-light mb-2">
                            <div class="card-body p-2">
                                <small>${note.notes || 'No notes'}</small>
                            </div>
                        </div>
                        <div class="text-muted small">
                            <i class="ti ti-user me-1"></i>${note.user_name || 'Unknown'}
                            <span class="mx-2">•</span>
                            <i class="ti ti-clock me-1"></i>${formatDateTime(note.updated_at || note.created_at)}
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="viewNoteDetail(${note.activity_id})">
                                <i class="ti ti-eye me-1"></i>View Activity
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="editNote(${note.activity_id})">
                                <i class="ti ti-edit me-1"></i>Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    $('#notesList').html(html);
}

// Get rating badge
function getRatingBadge(rating) {
    if (!rating) return '';
    
    const badges = {
        'F': '<span class="badge text-white bg-success">F - Fully</span>',
        'L': '<span class="badge text-white bg-info">L - Largely</span>',
        'P': '<span class="badge text-white bg-warning">P - Partially</span>',
        'N': '<span class="badge text-white bg-danger">N - Not Achieved</span>',
        'N/A': '<span class="badge text-white bg-secondary">N/A</span>'
    };
    
    return badges[rating] || '';
}

// Update statistics
function updateNotesStatistics(stats) {
    $('#notesTotal').text(stats.total || 0);
    $('#notesWithRating').text(stats.with_rating || 0);
    $('#notesWithoutRating').text(stats.without_rating || 0);
}

// View note detail
function viewNoteDetail(activityId) {
    $('#notesModal').modal('hide');
    setTimeout(() => {
        openAssessmentModal(activityId);
    }, 300);
}

// Edit note
function editNote(activityId) {
    viewNoteDetail(activityId);
}


// Filter handlers
$('#notesFilterLevel').on('change', loadNotesList);
$('#notesSearch').on('keyup', debounce(loadNotesList, 500));

// Reset filters when modal is closed
$('#notesModal').on('hidden.bs.modal', function() {
    $('#notesFilterLevel').val('');
    $('#notesSearch').val('');
});
</script>
