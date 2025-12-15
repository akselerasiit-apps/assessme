<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;

class AssessmentProgressController extends Controller
{
    /**
     * Show detailed progress for an assessment
     */
    public function show(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        // Get all GAMO objectives for this assessment
        $gamoObjectives = $assessment->gamoObjectives()
            ->with(['questions' => function($query) {
                $query->where('is_active', true)->orderBy('maturity_level')->orderBy('question_order');
            }])
            ->get();

        $progressData = [];
        $totalQuestions = 0;
        $answeredQuestions = 0;
        $totalRequired = 0;
        $answeredRequired = 0;

        foreach ($gamoObjectives as $gamo) {
            $gamoTotal = $gamo->questions->count();
            $gamoAnswered = $assessment->answers()
                ->whereIn('question_id', $gamo->questions->pluck('id'))
                ->whereNotNull('answer_text')
                ->count();
            
            $gamoRequired = $gamo->questions->where('required', true)->count();
            $gamoRequiredAnswered = $assessment->answers()
                ->whereIn('question_id', $gamo->questions->where('required', true)->pluck('id'))
                ->whereNotNull('answer_text')
                ->count();

            $progressData[] = [
                'gamo' => $gamo,
                'total_questions' => $gamoTotal,
                'answered_questions' => $gamoAnswered,
                'required_questions' => $gamoRequired,
                'required_answered' => $gamoRequiredAnswered,
                'progress_percentage' => $gamoTotal > 0 ? round(($gamoAnswered / $gamoTotal) * 100) : 0,
                'required_complete' => $gamoRequired > 0 && $gamoRequiredAnswered >= $gamoRequired,
            ];

            $totalQuestions += $gamoTotal;
            $answeredQuestions += $gamoAnswered;
            $totalRequired += $gamoRequired;
            $answeredRequired += $gamoRequiredAnswered;
        }

        $overallProgress = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
        $requiredProgress = $totalRequired > 0 ? round(($answeredRequired / $totalRequired) * 100) : 0;

        $stats = [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'unanswered_questions' => $totalQuestions - $answeredQuestions,
            'overall_progress' => $overallProgress,
            'total_required' => $totalRequired,
            'answered_required' => $answeredRequired,
            'required_progress' => $requiredProgress,
            'is_complete' => $answeredQuestions >= $totalQuestions,
            'required_complete' => $answeredRequired >= $totalRequired,
        ];

        return view('assessments.progress', compact('assessment', 'progressData', 'stats'));
    }
}
