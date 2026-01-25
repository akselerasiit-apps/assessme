<!-- Level Cards -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between" style="gap: 1rem;">
            @for($level = 2; $level <= 5; $level++)
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
                <div class="text-black small" id="level-{{ $level }}-count">0 Activities</div>
            </div>
            @if($level < 5)
            <div class="level-connector"></div>
            @endif
            @endfor
        </div>
    </div>
</div>

<!-- Level Title and Action Menu -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 id="currentLevelTitle">Level 2</h3>
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="ti ti-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="#" onclick="showHistoryModal(); return false;">
                    <i class="ti ti-history me-2"></i>Lihat History Perubahan Data
                </a>
            </li>
            @can('answer', $assessment)
            <li>
                <a class="dropdown-item" href="#" onclick="showNotesModal(); return false;">
                    <i class="ti ti-notes me-2"></i>Lihat Daftar Catatan / Note Penilaian
                </a>
            </li>
            @endcan
        </ul>
    </div>
</div>

<!-- Activities Table -->
<div class="card">
    <div class="table-responsive" style="min-height:500px">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Urutan</th>
                    <th>Management Practice Code</th>
                    <th>Activities</th>
                    <th>Translate</th>
                    <th class="text-center" style="width: 80px;">Evidence</th>
                    <th class="text-center" style="width: 120px;">Asesmen</th>
                    <th class="text-center" style="width: 80px;">Weight</th>
                    {{-- <th class="text-center" style="width: 80px;">Aksi</th> --}}
                </tr>
            </thead>
            <tbody id="activitiesTableBody">
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
                        <span class="text-muted ms-2">Loading activities...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Summary Section -->
<div class="card mt-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                    <strong>Total Weight</strong>
                    <span id="totalWeight">0</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                    <strong>Compliance</strong>
                    <span id="totalCompliances">0.00%</span>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <div class="d-flex justify-content-between">
                    <strong>Completed</strong>
                    <span id="completedCount">0/0</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between">
                    <strong>Total Score</strong>
                    <span id="totalValues">0.00</span>
                </div>
            </div>
        </div>
    </div>
</div>
