<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GamoQuestion;
use App\Models\GamoObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuestionWebController extends Controller
{
    /**
     * Display a listing of questions
     */
    public function index(Request $request)
    {
        $query = GamoQuestion::with('gamoObjective')
            ->when($request->gamo_id, function($q) use ($request) {
                return $q->where('gamo_objective_id', $request->gamo_id);
            })
            ->when($request->category, function($q) use ($request) {
                return $q->whereHas('gamoObjective', function($query) use ($request) {
                    $query->where('category', $request->category);
                });
            })
            ->when($request->maturity_level, function($q) use ($request) {
                return $q->where('maturity_level', $request->maturity_level);
            })
            ->when($request->search, function($q) use ($request) {
                return $q->where(function($query) use ($request) {
                    $query->where('code', 'like', '%' . $request->search . '%')
                          ->orWhere('question_text', 'like', '%' . $request->search . '%')
                          ->orWhere('guidance', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->is_active !== null, function($q) use ($request) {
                return $q->where('is_active', $request->is_active);
            })
            ->orderBy('gamo_objective_id')
            ->orderBy('maturity_level')
            ->orderBy('question_order');

        $questions = $query->paginate($request->get('perPage', 15));
        
        // Get filter options
        $gamoObjectives = GamoObjective::where('is_active', true)
            ->orderBy('category')
            ->orderBy('objective_order')
            ->get();
        
        $categories = ['EDM', 'APO', 'BAI', 'DSS', 'MEA'];
        $maturityLevels = [2, 3, 4, 5]; // COBIT 2019: Level 2-5 only

        // Get statistics
        $totalQuestions = GamoQuestion::count();
        $activeCount = GamoQuestion::where('is_active', true)->count();
        $inactiveCount = GamoQuestion::where('is_active', false)->count();
        $gamoCount = GamoObjective::where('is_active', true)->count();

        return view('questions.index', compact('questions', 'gamoObjectives', 'categories', 'maturityLevels', 'totalQuestions', 'activeCount', 'inactiveCount', 'gamoCount'));
    }

    /**
     * Show the form for creating a new question
     */
    public function create(Request $request)
    {
        $gamoObjectives = GamoObjective::where('is_active', true)
            ->orderBy('category')
            ->orderBy('objective_order')
            ->get();
        
        $questionTypes = ['text', 'rating', 'multiple_choice', 'yes_no', 'evidence'];
        $maturityLevels = [2, 3, 4, 5]; // COBIT 2019: Level 2-5 only
        
        // Preselect GAMO if passed from GAMO Objectives page
        $selectedGamoId = $request->get('gamo_id');

        return view('questions.create', compact('gamoObjectives', 'questionTypes', 'maturityLevels', 'selectedGamoId'));
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'question_text_en' => 'required|string',
            'question_text_id' => 'required|string',
            'guidance' => 'nullable|string',
            'evidence_requirement' => 'nullable|string',
            'maturity_level' => 'required|integer|min:1|max:5',
            'required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Merge EN and ID into single field with separator
        $validated['question_text'] = trim($validated['question_text_en']) . ' | ' . trim($validated['question_text_id']);
        unset($validated['question_text_en'], $validated['question_text_id']);

        // Set defaults
        $validated['question_type'] = 'rating'; // Default to rating
        $validated['required'] = $request->has('required');
        $validated['is_active'] = $request->boolean('is_active');

        $question = DB::transaction(function () use ($validated) {
            $validated['code'] = $this->generateNextQuestionCode($validated['gamo_objective_id']);
            return GamoQuestion::create($validated);
        });

        return redirect()
            ->route('master-data.questions.index')
            ->with('success', 'Question created successfully!');
    }

    /**
     * Display the specified question
     */
    public function show(GamoQuestion $question)
    {
        $question->load('gamoObjective');
        
        // Count usages
        $usageStats = [
            'answer_count' => $question->answers()->count(),
            'assessment_count' => $question->answers()->distinct('assessment_id')->count('assessment_id')
        ];

        return view('questions.show', compact('question', 'usageStats'));
    }

    /**
     * Show the form for editing the specified question
     */
    public function edit(GamoQuestion $question)
    {
        $gamoObjectives = GamoObjective::where('is_active', true)
            ->orderBy('category')
            ->orderBy('objective_order')
            ->get();
        
        $questionTypes = ['text', 'rating', 'multiple_choice', 'yes_no', 'evidence'];
        $maturityLevels = [2, 3, 4, 5]; // COBIT 2019: Level 2-5 only

        return view('questions.edit', compact('question', 'gamoObjectives', 'questionTypes', 'maturityLevels'));
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, GamoQuestion $question)
    {
        $validated = $request->validate([
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'question_text_en' => 'required|string',
            'question_text_id' => 'required|string',
            'guidance' => 'nullable|string',
            'evidence_requirement' => 'nullable|string',
            'maturity_level' => 'required|integer|min:1|max:5',
            'required' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Merge EN and ID into single field with separator
        $validated['question_text'] = trim($validated['question_text_en']) . ' | ' . trim($validated['question_text_id']);
        unset($validated['question_text_en'], $validated['question_text_id']);

        // Set defaults
        $validated['question_type'] = 'rating'; // Default to rating
        $validated['required'] = $request->has('required');
        $validated['is_active'] = $request->boolean('is_active');

        DB::transaction(function () use ($validated, $question) {
            // Regenerate code only when GAMO objective changes.
            if ((int) $validated['gamo_objective_id'] !== (int) $question->gamo_objective_id) {
                $validated['code'] = $this->generateNextQuestionCode($validated['gamo_objective_id'], $question->id);
            }

            $question->update($validated);
        });

        return redirect()
            ->route('master-data.questions.index')
            ->with('success', 'Question updated successfully!');
    }

    /**
     * Remove the specified question
     */
    public function destroy(GamoQuestion $question)
    {
        // Check if question is used in assessments
        $answersCount = $question->answers()->count();
        
        if ($answersCount > 0) {
            return redirect()
                ->back()
                ->with('error', "Cannot delete question. It has {$answersCount} answer(s) in assessments.");
        }

        $question->delete();

        return redirect()
            ->route('master-data.questions.index')
            ->with('success', 'Question deleted successfully!');
    }

    /**
     * Toggle question active status
     */
    public function toggleActive(GamoQuestion $question)
    {
        $question->update(['is_active' => !$question->is_active]);

        $status = $question->is_active ? 'activated' : 'deactivated';
        
        return redirect()
            ->back()
            ->with('success', "Question {$status} successfully!");
    }

    /**
     * Download official Excel template for question import
     */
    public function downloadImportTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'No',
            'GAMO Objective',
            'Kode',
            'Aktifitas',
            'Terjemahan',
            'Penjelasan Detail',
            'Kebutuhan Dokumen',
            'Level',
        ];

        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray([
            1,
            'EDM01',
            'EDM01.01',
            'Analyze and identify the internal and external environmental factors...',
            'Analisis dan identifikasi faktor lingkungan internal dan eksternal...',
            'Uraian detail aktivitas dan konteks pelaksanaan.',
            '- Dokumen Analisis Lingkungan\n- IT Governance Context',
            2,
        ], null, 'A2');

        $sheet->fromArray([
            2,
            'EDM01',
            'EDM01.01',
            'Determine the significance of I&T and its role with respect to the business.',
            'Menentukan signifikansi I&T dan perannya terhadap bisnis.',
            'Uraian detail aktivitas kedua.',
            '- Dokumen IT Strategy\n- Peta keterkaitan tujuan bisnis-TI',
            2,
        ], null, 'A3');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'template-import-aktifitas.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Import questions from Excel/CSV file (WEB)
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        try {
            $spreadsheet = IOFactory::load($validated['file']->getRealPath());
            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

            if (count($rows) < 2) {
                return $this->importResponse($request, false, 'File tidak memiliki data untuk diimpor.');
            }

            $headers = array_map(fn ($h) => $this->normalizeHeader((string) $h), $rows[0]);

            $idxNo = $this->findHeaderIndex($headers, ['no']);
            $idxGamo = $this->findHeaderIndex($headers, ['gamoobjective', 'gamo']);
            $idxKode = $this->findHeaderIndex($headers, ['kode', 'code']);
            $idxActivity = $this->findHeaderIndex($headers, ['aktifitas', 'aktivitas', 'activity']);
            $idxTerjemahan = $this->findHeaderIndex($headers, ['terjemahan', 'translation']);
            $idxDetail = $this->findHeaderIndex($headers, ['penjelasandetail', 'detail', 'guidance']);
            $idxDokumen = $this->findHeaderIndex($headers, ['kebutuhandokumen', 'documentrequirements', 'dokumen']);
            $idxLevel = $this->findHeaderIndex($headers, ['level', 'maturitylevel']);

            if ($idxGamo === null || $idxActivity === null || $idxLevel === null) {
                return $this->importResponse(
                    $request,
                    false,
                    'Header wajib tidak ditemukan. Pastikan ada kolom: GAMO Objective, Aktifitas, Level.'
                );
            }

            $gamoObjectiveMap = GamoObjective::query()
                ->select('id', 'code')
                ->get()
                ->keyBy('code');

            $imported = 0;
            $failed = [];
            $errorFileUrl = null;

            DB::transaction(function () use (
                $rows,
                $idxNo,
                $idxGamo,
                $idxKode,
                $idxActivity,
                $idxTerjemahan,
                $idxDetail,
                $idxDokumen,
                $idxLevel,
                $gamoObjectiveMap,
                &$imported,
                &$failed
            ) {
                foreach ($rows as $i => $row) {
                    if ($i === 0) {
                        continue;
                    }

                    $lineNumber = $i + 1;
                    $rawGamo = trim((string) ($row[$idxGamo] ?? ''));
                    $rawKode = $idxKode !== null ? trim((string) ($row[$idxKode] ?? '')) : '';
                    $activity = trim((string) ($row[$idxActivity] ?? ''));
                    $translation = $idxTerjemahan !== null ? trim((string) ($row[$idxTerjemahan] ?? '')) : '';
                    $guidance = $idxDetail !== null ? trim((string) ($row[$idxDetail] ?? '')) : '';
                    $documentRequirements = $idxDokumen !== null ? trim((string) ($row[$idxDokumen] ?? '')) : '';
                    $rawLevel = trim((string) ($row[$idxLevel] ?? ''));
                    $rawNo = $idxNo !== null ? trim((string) ($row[$idxNo] ?? '')) : '';

                    if ($rawGamo === '' && $activity === '' && $rawLevel === '' && $rawKode === '') {
                        continue;
                    }

                    $gamoCode = strtoupper($rawGamo);
                    if ($gamoCode === '' && $rawKode !== '') {
                        if (preg_match('/^([A-Z]{3}\d{2})\./i', $rawKode, $m)) {
                            $gamoCode = strtoupper($m[1]);
                        }
                    }

                    if ($gamoCode === '' || !isset($gamoObjectiveMap[$gamoCode])) {
                        $failed[] = [
                            'line' => $lineNumber,
                            'no' => $rawNo,
                            'gamo_objective' => $rawGamo,
                            'kode' => $rawKode,
                            'aktifitas' => $activity,
                            'level' => $rawLevel,
                            'reason' => "GAMO Objective tidak valid ({$rawGamo}).",
                        ];
                        continue;
                    }

                    if ($activity === '') {
                        $failed[] = [
                            'line' => $lineNumber,
                            'no' => $rawNo,
                            'gamo_objective' => $rawGamo,
                            'kode' => $rawKode,
                            'aktifitas' => $activity,
                            'level' => $rawLevel,
                            'reason' => 'Aktifitas wajib diisi.',
                        ];
                        continue;
                    }

                    $level = (int) $rawLevel;
                    if (!is_numeric($rawLevel) || $level < 1 || $level > 5) {
                        $failed[] = [
                            'line' => $lineNumber,
                            'no' => $rawNo,
                            'gamo_objective' => $rawGamo,
                            'kode' => $rawKode,
                            'aktifitas' => $activity,
                            'level' => $rawLevel,
                            'reason' => 'Level harus angka 1-5.',
                        ];
                        continue;
                    }

                    $questionText = $activity . ' | ' . $translation;
                    $questionOrder = is_numeric($rawNo) ? (int) $rawNo : null;

                    $gamoObjectiveId = (int) $gamoObjectiveMap[$gamoCode]->id;

                    GamoQuestion::create([
                        'code' => $this->generateNextQuestionCode($gamoObjectiveId),
                        'gamo_objective_id' => $gamoObjectiveId,
                        'question_text' => $questionText,
                        'guidance' => $guidance !== '' ? $guidance : null,
                        'document_requirements' => $documentRequirements !== '' ? $documentRequirements : null,
                        'evidence_requirement' => $documentRequirements !== '' ? $documentRequirements : null,
                        'question_type' => 'rating',
                        'maturity_level' => $level,
                        'required' => true,
                        'question_order' => $questionOrder,
                        'is_active' => true,
                    ]);

                    $imported++;
                }
            });

            $message = "Import selesai. Berhasil: {$imported}";
            if (!empty($failed)) {
                $message .= ', Gagal: ' . count($failed);
                $errorFileUrl = $this->buildImportErrorCsv($failed);
            }

            if (!empty($failed)) {
                $message .= '. Gunakan file error untuk perbaikan lalu import ulang.';
            }

            return $this->importResponse($request, true, $message, [
                'imported_count' => $imported,
                'failed_count' => count($failed),
                'failed_rows' => $failed,
                'error_file_url' => $errorFileUrl,
            ]);
        } catch (\Throwable $e) {
            return $this->importResponse($request, false, 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Generate next available WEB code with format GAMO.XX
     */
    private function generateNextQuestionCode(int $gamoObjectiveId, ?int $excludeQuestionId = null): string
    {
        $gamoObjective = GamoObjective::select('id', 'code')->findOrFail($gamoObjectiveId);
        $prefix = $gamoObjective->code . '.';

        $query = GamoQuestion::where('gamo_objective_id', $gamoObjectiveId)
            ->lockForUpdate();

        if ($excludeQuestionId) {
            $query->where('id', '!=', $excludeQuestionId);
        }

        $existingCodes = $query->pluck('code');
        $usedSequences = [];

        foreach ($existingCodes as $code) {
            if (preg_match('/^' . preg_quote($gamoObjective->code, '/') . '\\.(\\d{1,2})(?:\\.|$)/', $code, $matches)) {
                $sequence = (int) $matches[1];
                if ($sequence >= 1 && $sequence <= 99) {
                    $usedSequences[$sequence] = true;
                }
            }
        }

        for ($sequence = 1; $sequence <= 99; $sequence++) {
            if (!isset($usedSequences[$sequence])) {
                return $prefix . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
            }
        }

        throw new \RuntimeException("Sequence for {$gamoObjective->code} is full (01-99).");
    }

    private function normalizeHeader(string $header): string
    {
        $header = strtolower(trim($header));
        return preg_replace('/[^a-z0-9]/', '', $header) ?? '';
    }

    private function findHeaderIndex(array $headers, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            $normalized = $this->normalizeHeader($candidate);
            $index = array_search($normalized, $headers, true);
            if ($index !== false) {
                return (int) $index;
            }
        }

        return null;
    }

    private function importResponse(Request $request, bool $ok, string $message, array $extra = [])
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(array_merge(['success' => $ok, 'message' => $message], $extra), $ok ? 200 : 422);
        }

        $redirect = redirect()
            ->route('master-data.questions.index')
            ->with($ok ? 'success' : 'error', $message);

        foreach ($extra as $key => $value) {
            $redirect->with($key, $value);
        }

        return $redirect;
    }

    private function buildImportErrorCsv(array $failedRows): string
    {
        $stream = fopen('php://temp', 'r+');

        fputcsv($stream, ['line', 'no', 'gamo_objective', 'kode', 'aktifitas', 'level', 'reason']);

        foreach ($failedRows as $failed) {
            fputcsv($stream, [
                $failed['line'] ?? '',
                $failed['no'] ?? '',
                $failed['gamo_objective'] ?? '',
                $failed['kode'] ?? '',
                $failed['aktifitas'] ?? '',
                $failed['level'] ?? '',
                $failed['reason'] ?? '',
            ]);
        }

        rewind($stream);
        $content = stream_get_contents($stream) ?: '';
        fclose($stream);

        $fileName = 'import-errors/questions-import-errors-' . now()->format('Ymd-His') . '.csv';
        Storage::disk('public')->put($fileName, $content);

        return Storage::url($fileName);
    }
}
