<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GamoQuestion;
use App\Models\GamoObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $maturityLevels = [1, 2, 3, 4, 5];

        return view('questions.index', compact('questions', 'gamoObjectives', 'categories', 'maturityLevels'));
    }

    /**
     * Show the form for creating a new question
     */
    public function create()
    {
        $gamoObjectives = GamoObjective::where('is_active', true)
            ->orderBy('category')
            ->orderBy('objective_order')
            ->get();
        
        $questionTypes = ['text', 'rating', 'multiple_choice', 'yes_no', 'evidence'];
        $maturityLevels = [1, 2, 3, 4, 5];

        return view('questions.create', compact('gamoObjectives', 'questionTypes', 'maturityLevels'));
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:gamo_questions,code',
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'question_text' => 'required|string',
            'guidance' => 'nullable|string',
            'evidence_requirement' => 'nullable|string',
            'question_type' => 'required|in:text,rating,multiple_choice,yes_no,evidence',
            'maturity_level' => 'required|integer|min:1|max:5',
            'required' => 'boolean',
            'question_order' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Set defaults
        $validated['required'] = $request->has('required');
        $validated['is_active'] = $request->has('is_active') ? true : ($request->filled('is_active') ? $request->is_active : true);

        $question = GamoQuestion::create($validated);

        return redirect()
            ->route('questions.index')
            ->with('success', 'Question created successfully!');
    }

    /**
     * Display the specified question
     */
    public function show(GamoQuestion $question)
    {
        $question->load('gamoObjective');
        
        // Count usages
        $usageCount = $question->answers()->count();
        $assessmentCount = $question->answers()->distinct('assessment_id')->count('assessment_id');

        return view('questions.show', compact('question', 'usageCount', 'assessmentCount'));
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
        $maturityLevels = [1, 2, 3, 4, 5];

        return view('questions.edit', compact('question', 'gamoObjectives', 'questionTypes', 'maturityLevels'));
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, GamoQuestion $question)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:gamo_questions,code,' . $question->id,
            'gamo_objective_id' => 'required|exists:gamo_objectives,id',
            'question_text' => 'required|string',
            'guidance' => 'nullable|string',
            'evidence_requirement' => 'nullable|string',
            'question_type' => 'required|in:text,rating,multiple_choice,yes_no,evidence',
            'maturity_level' => 'required|integer|min:1|max:5',
            'required' => 'boolean',
            'question_order' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Set defaults
        $validated['required'] = $request->has('required');
        $validated['is_active'] = $request->has('is_active') ? true : ($request->filled('is_active') ? $request->is_active : true);

        $question->update($validated);

        return redirect()
            ->route('questions.index')
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
            ->route('questions.index')
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
}
