<!-- Level Cards -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between" style="gap: 1rem;">
            @for($level = 1; $level <= 5; $level++)
            <div class="level-card text-center p-3 rounded locked" 
                 data-level="{{ $level }}" 
                 data-locked="true"
                 style="flex: 1;">
                <div style="position: relative; display: inline-block; margin-bottom: 0.5rem; line-height: 0;">
                    <div class="lock-overlay" style="position: absolute; top: 0; left: 0; width: 48px; height: 48px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; z-index: 10;">
                        <i class="ti ti-lock text-muted" style="font-size: 1.5rem; line-height: 1;"></i>
                    </div>
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                         style="width: 48px; height: 48px; font-size: 1.5rem; font-weight: bold;">
                        {{ $level }}
                    </div>
                </div>
                <div class="fw-bold">Level {{ $level }}</div>
                <div class="text-muted small" id="pbc-level-{{ $level }}-count">0 Documents</div>
            </div>
            @if($level < 5)
            <div class="level-connector"></div>
            @endif
            @endfor
        </div>
    </div>
</div>

<!-- Level Title -->
<div class="mb-3">
    <h3 id="pbcCurrentLevelTitle">Level 1 - Kebutuhan Dokumen</h3>
</div>

<!-- PBC Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Urutan</th>
                    <th style="width: 150px;">Management Practice Code</th>
                    <th>Kebutuhan Dokumen</th>
                    <th class="text-center" style="width: 100px;">Evidence</th>
                    <th class="text-center" style="width: 120px;">Status</th>
                    <th>Keterangan</th>
                    <th class="text-center" style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="pbcTableBody">
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                        <span class="text-muted ms-2">Loading document requirements...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
// Load PBC for selected level
function loadPBCByLevel(level) {
    $('#pbcTableBody').html(`
        <tr>
            <td colspan="7" class="text-center">
                <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                <span class="text-muted ms-2">Loading document requirements...</span>
            </td>
        </tr>
    `);
    
    $('#pbcCurrentLevelTitle').text(`Level ${level} - Kebutuhan Dokumen`);
    
    const assessmentId = $('input[name="assessment_id"]').val();
    const gamoId = $('#gamoSelector').val();
    
    $.ajax({
        url: `/assessments/${assessmentId}/gamo/${gamoId}/pbc`,
        data: { level: level },
        success: function(response) {
            if (response.activities && response.activities.length > 0) {
                renderPBCTable(response.activities, level);
            } else {
                $('#pbcTableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="ti ti-file-off icon mb-2" style="font-size: 2rem;"></i>
                            <div>Tidak ada kebutuhan dokumen untuk level ini</div>
                        </td>
                    </tr>
                `);
            }
        },
        error: function() {
            $('#pbcTableBody').html(`
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        <i class="ti ti-alert-circle me-2"></i>Error loading document requirements
                    </td>
                </tr>
            `);
        }
    });
}

// Render PBC table
function renderPBCTable(activities, level) {
    let html = '';
    
    activities.forEach((activity, index) => {
        // Determine status badge from backend
        let statusBadge = '';
        let statusText = '';
        
        switch(activity.status) {
            case 'complete':
                statusBadge = '<span class="badge bg-success">Selesai</span>';
                statusText = 'Evidence tersedia & sudah dinilai';
                break;
            case 'partial':
                statusBadge = '<span class="badge bg-info">Sebagian</span>';
                statusText = 'Evidence tersedia, belum dinilai';
                break;
            case 'rated':
                statusBadge = '<span class="badge bg-warning">Dinilai</span>';
                statusText = 'Sudah dinilai, belum ada evidence';
                break;
            default:
                statusBadge = '<span class="badge bg-secondary">Belum</span>';
                statusText = 'Belum ada evidence dan belum dinilai';
        }
        
        const evidenceDisplay = activity.evidence_count > 0 ? 
            `<span class="badge bg-success cursor-pointer" onclick="showEvidence(${activity.id})">${activity.evidence_count}</span>` : 
            `<span class="text-muted">-</span>`;
        
        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td><code>${activity.code || '-'}</code></td>
                <td>
                    <div>${activity.name || '-'}</div>
                    <div class="text-muted small">${activity.translated_text || ''}</div>
                </td>
                <td class="text-center">${evidenceDisplay}</td>
                <td class="text-center" title="${statusText}">${statusBadge}</td>
                <td class="text-muted small">
                    ${activity.notes ? activity.notes.substring(0, 50) + (activity.notes.length > 50 ? '...' : '') : '-'}
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-icon btn-outline-primary" onclick="showEvidence(${activity.id})" title="Evidence">
                            <i class="ti ti-folder"></i>
                        </button>
                        <button class="btn btn-icon btn-outline-success" onclick="openAssessmentModal(${activity.id})" title="Penilaian">
                            <i class="ti ti-checks"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    if (html === '') {
        html = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="ti ti-file-off icon mb-2" style="font-size: 2rem;"></i>
                    <div>Tidak ada kebutuhan dokumen untuk level ini</div>
                </td>
            </tr>
        `;
    }
    
    $('#pbcTableBody').html(html);
}

// Handle PBC level card clicks
$(document).on('click', '#tab-pbc .level-card', function() {
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
    
    $('#tab-pbc .level-card').removeClass('active');
    $(this).addClass('active');
    
    loadPBCByLevel(level);
});

// Load PBC tab when activated
$(document).on('shown.bs.tab', 'a[href="#tab-pbc"]', function() {
    const activeLevel = $('#tab-pbc .level-card.active').data('level') || 1;
    loadPBCByLevel(activeLevel);
});
</script>
