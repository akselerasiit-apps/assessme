<!-- PBC (Provided By Client) List -->
<div class="row">
    <div class="col-12">
        <h3>Provided By Client (PBC) List</h3>
        <p class="text-muted">List of evidence and documentation required from the client for {{ $gamo->code }} assessment.</p>
        
        <div class="alert alert-info">
            <h4 class="alert-title">What is PBC?</h4>
            <div class="text-muted">
                PBC (Provided By Client) refers to documents, evidence, and information that need to be collected from the organization 
                to support the capability assessment. This ensures proper validation of process implementation.
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Required Evidence by Level</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Required Evidence Types</th>
                            <th>Min. Documents</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $capabilityLevels = \App\Models\GamoCapabilityDefinition::where('gamo_objective_id', $gamo->id)
                                ->orderBy('level')
                                ->get();
                        @endphp
                        @foreach($capabilityLevels as $levelDef)
                        <tr>
                            <td>
                                <span class="badge bg-primary">Level {{ $levelDef->level }}</span>
                                <div class="small text-muted">{{ $levelDef->level_name }}</div>
                            </td>
                            <td>
                                <ul class="mb-0 ps-3">
                                    @if($levelDef->level == 0)
                                        <li>Basic process documentation</li>
                                        <li>Evidence of process execution</li>
                                    @elseif($levelDef->level == 1)
                                        <li>Process procedures</li>
                                        <li>Work products</li>
                                        <li>Performance indicators</li>
                                    @elseif($levelDef->level == 2)
                                        <li>Process management procedures</li>
                                        <li>Planning documents</li>
                                        <li>Resource assignment records</li>
                                        <li>Monitoring reports</li>
                                    @elseif($levelDef->level == 3)
                                        <li>Standardized process definition</li>
                                        <li>Process deployment evidence</li>
                                        <li>Training records</li>
                                        <li>Process tailoring guidelines</li>
                                    @elseif($levelDef->level == 4)
                                        <li>Process measurement system</li>
                                        <li>Process performance baselines</li>
                                        <li>Statistical analysis reports</li>
                                        <li>Control charts</li>
                                    @else
                                        <li>Process innovation records</li>
                                        <li>Improvement proposals</li>
                                        <li>Optimization analysis</li>
                                        <li>Lessons learned repository</li>
                                    @endif
                                </ul>
                            </td>
                            <td>
                                <strong>{{ $levelDef->required_evidence_count ?? 3 }}</strong>
                            </td>
                            <td>
                                @php
                                    $evidenceCount = 0; // TODO: Calculate actual evidence count
                                    $required = $levelDef->required_evidence_count ?? 3;
                                @endphp
                                @if($evidenceCount >= $required)
                                    <span class="badge bg-success">Complete</span>
                                @elseif($evidenceCount > 0)
                                    <span class="badge bg-warning">Partial ({{ $evidenceCount }}/{{ $required }})</span>
                                @else
                                    <span class="badge bg-secondary">Not Started</span>
                                @endif
                            </td>
                            <td class="text-muted">
                                @if($levelDef->additional_requirements)
                                    {{ Str::limit($levelDef->additional_requirements, 50) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('assessments.evidence.create', $assessment) }}?gamo={{ $gamo->id }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                Upload Evidence
            </a>
        </div>
    </div>
</div>
