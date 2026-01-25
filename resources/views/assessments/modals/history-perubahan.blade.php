<!-- Modal History Perubahan -->
<div class="modal modal-blur fade" id="historyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History Perubahan Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Filter Section -->
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <select class="form-select" id="historyFilterAction">
                            <option value="">Semua Aksi</option>
                            <option value="update_rating">Update Rating</option>
                            <option value="upload_evidence">Upload Evidence</option>
                            <option value="delete_evidence">Delete Evidence</option>
                            <option value="update_answer">Update Answer</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="historyFilterDate" placeholder="Filter by date">
                    </div>
                </div>

                <!-- Timeline -->
                <div id="historyTimeline">
                    <div class="text-center py-4">
                        <div class="spinner-border text-muted" role="status"></div>
                        <div class="text-muted mt-2">Loading history...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Tutup</button>
                {{-- <button type="button" class="btn btn-outline-primary" onclick="exportHistory()"> --}}
                    <i class="ti ti-download me-2"></i>Export History
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Show history modal
function showHistoryModal() {
    $('#historyModal').modal('show');
    loadHistory();
}

// Load history
function loadHistory() {
    const assessmentId = $('input[name="assessment_id"]').val();
    const gamoId = $('#gamoSelector').val();
    const filterAction = $('#historyFilterAction').val();
    const filterDate = $('#historyFilterDate').val();
    
    $('#historyTimeline').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-muted" role="status"></div>
            <div class="text-muted mt-2">Loading history...</div>
        </div>
    `);
    
    $.ajax({
        url: `/assessments/${assessmentId}/gamo/${gamoId}/history`,
        data: {
            action: filterAction,
            date: filterDate
        },
        success: function(response) {
            renderHistoryTimeline(response.history || []);
        },
        error: function() {
            $('#historyTimeline').html(`
                <div class="text-center py-4 text-danger">
                    <i class="ti ti-alert-circle icon mb-2"></i>
                    <div>Error loading history</div>
                </div>
            `);
        }
    });
}

// Render history timeline
function renderHistoryTimeline(historyList) {
    if (historyList.length === 0) {
        $('#historyTimeline').html(`
            <div class="text-center py-4 text-muted">
                <i class="ti ti-history-off icon mb-2" style="font-size: 2rem;"></i>
                <div>Belum ada history perubahan</div>
            </div>
        `);
        return;
    }
    
    let html = '<div class="list-group list-group-flush">';
    
    historyList.forEach(item => {
        const actionIcon = getActionIcon(item.action);
        const actionClass = getActionClass(item.action);
        const changes = item.changes ? JSON.parse(item.changes) : {};
        
        html += `
            <div class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar text-white ${actionClass}">
                            <i class="ti ${actionIcon}"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="fw-bold">${item.action_description || item.action}</div>
                        <div class="text-muted small">
                            <i class="ti ti-user me-1"></i>${item.user_name || 'System'}
                            <span class="mx-2">•</span>
                            <i class="ti ti-clock me-1"></i>${formatDateTime(item.created_at)}
                        </div>
                        ${renderChanges(changes)}
                    </div>
                    <div class="col-auto">
                        <span class="badge text-white ${actionClass}">${item.action}</span>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    $('#historyTimeline').html(html);
}

// Get action icon
function getActionIcon(action) {
    const icons = {
        'update_rating': 'ti-star',
        'update_answer': 'ti-edit',
        'upload_evidence': 'ti-upload',
        'delete_evidence': 'ti-trash',
        'created': 'ti-plus',
        'updated': 'ti-edit',
        'deleted': 'ti-trash'
    };
    return icons[action] || 'ti-dots';
}

// Get action class
function getActionClass(action) {
    const classes = {
        'update_rating': 'bg-primary',
        'update_answer': 'bg-info',
        'upload_evidence': 'bg-success',
        'delete_evidence': 'bg-danger',
        'created': 'bg-success',
        'updated': 'bg-info',
        'deleted': 'bg-danger'
    };
    return classes[action] || 'bg-secondary';
}

// Render changes
function renderChanges(changes) {
    if (!changes || Object.keys(changes).length === 0) {
        return '';
    }
    
    let html = '<div class="mt-2"><small class="text-muted">';
    
    if (changes.old_value !== undefined && changes.new_value !== undefined) {
        html += `
            <div class="d-flex gap-2">
                <div>
                    <span class="badge bg-red-lt">Old:</span> ${changes.old_value || '-'}
                </div>
                <div>
                    <i class="ti ti-arrow-right"></i>
                </div>
                <div>
                    <span class="badge bg-green-lt">New:</span> ${changes.new_value || '-'}
                </div>
            </div>
        `;
    } else {
        html += JSON.stringify(changes);
    }
    
    html += '</small></div>';
    return html;
}

// Format datetime
function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Export history
// function exportHistory() {
//     const assessmentId = $('input[name="assessment_id"]').val();
//     const gamoId = $('#gamoSelector').val();
//     window.location.href = `/assessments/${assessmentId}/gamo/${gamoId}/export-history`;
// }

// Filter handlers
$('#historyFilterAction, #historyFilterDate').on('change', function() {
    loadHistory();
});

// Reset filters when modal is closed
$('#historyModal').on('hidden.bs.modal', function() {
    $('#historyFilterAction').val('');
    $('#historyFilterDate').val('');
});
</script>
