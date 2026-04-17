<?php

namespace App\Services\Ofi;

use App\Models\Assessment;
use App\Models\GamoObjective;

class OfiPromptBuilder
{
    public function buildPerActivity(Assessment $assessment, GamoObjective $gamo, array $templateData, array $activity, int $position, int $total): array
    {
        $systemPrompt = implode("\n", [
            'Anda adalah konsultan COBIT 2019 yang membantu menyusun Opportunity for Improvement (OFI).',
            'Tugas Anda adalah membuat satu rekomendasi yang konkret, dapat dijalankan, dan relevan untuk satu activity prioritas.',
            'Anda wajib menjawab dalam satu objek JSON valid tanpa markdown, tanpa code fence, tanpa HTML, dan tanpa penjelasan tambahan.',
            'Jangan tampilkan chain-of-thought, reasoning internal, atau tag seperti <think>.',
            'Gunakan bahasa Indonesia yang formal dan operasional.',
        ]);

        $activityName = $activity['translated_text'] ?? $activity['activity_name'] ?? $activity['activity_code'];

        $userPrompt = implode("\n", [
            'Buat satu rekomendasi OFI AI untuk activity prioritas berikut.',
            '',
            'Assessment:',
            '- code: '.$assessment->code,
            '- title: '.$assessment->title,
            '',
            'GAMO:',
            '- code: '.$gamo->code,
            '- name: '.$gamo->name,
            '',
            'Capability:',
            '- current_level: '.$templateData['current_level'],
            '- target_level: '.$templateData['target_level'],
            '- gap: '.$templateData['gap_score'],
            '',
            'Activity ke-'.$position.' dari '.$total.':',
            '- activity_code: '.$activity['activity_code'],
            '- activity_name: '.$activityName,
            '- level: '.$activity['level'],
            '- current_compliance: '.$activity['current_compliance'].'%',
            '- capability_rating: '.($activity['capability_rating'] ?? '-'),
            '- guidance: '.($activity['guidance'] ?: '-'),
            '- document_requirements: '.($activity['document_requirements'] ?: '-'),
            '',
            'Aturan output:',
            '- Jawab hanya satu objek JSON final.',
            '- activity_code wajib sama persis dengan '.$activity['activity_code'].'.',
            '- Field wajib: activity_code, issue, objective, recommended_action, expected_evidence, success_indicator, priority.',
            '- priority hanya boleh: low, medium, high, critical.',
            '- Semua isi harus spesifik dan dapat ditindaklanjuti. Jangan gunakan placeholder seperti ..., XX, TBD, dummy.',
            '',
            'Contoh bentuk JSON yang benar:',
            '{',
            '  "activity_code": "'.$activity['activity_code'].'",',
            '  "issue": "Aktivitas belum memiliki mekanisme evaluasi manfaat yang terdokumentasi dan belum dijalankan secara konsisten.",',
            '  "objective": "Membangun proses evaluasi manfaat yang konsisten dan dapat diaudit.",',
            '  "recommended_action": "Tetapkan prosedur evaluasi manfaat, tentukan PIC, dan lakukan review berkala atas realisasi manfaat investasi I&T.",',
            '  "expected_evidence": "SOP evaluasi manfaat, notulen review, daftar PIC, laporan realisasi manfaat.",',
            '  "success_indicator": "Review manfaat dilakukan berkala dan terdokumentasi untuk investasi utama.",',
            '  "priority": "high"',
            '}',
        ]);

        return [
            'system_prompt' => $systemPrompt,
            'user_prompt' => $userPrompt,
            'allowed_activity_codes' => [$activity['activity_code']],
            'prompt_preview' => trim($systemPrompt."\n\n".$userPrompt),
        ];
    }

    public function build(Assessment $assessment, GamoObjective $gamo, array $templateData): array
    {
        $activities = collect($templateData['recommendations'])
            ->sortBy('current_compliance')
            ->values()
            ->take(4)
            ->map(function (array $activity) {
                return [
                    'activity_code' => $activity['activity_code'],
                    'activity_name' => $activity['translated_text'] ?: $activity['activity_name'],
                    'level' => $activity['level'],
                    'current_compliance' => $activity['current_compliance'],
                    'capability_rating' => $activity['capability_rating'],
                    'guidance' => $activity['guidance'],
                    'document_requirements' => $activity['document_requirements'],
                ];
            })
            ->all();

        $allowedActivityCodes = array_values(array_unique(array_map(
            fn (array $activity) => $activity['activity_code'],
            $activities
        )));

        $exampleActivityCode = $allowedActivityCodes[0] ?? $gamo->code;
        $isLocalProvider = config('services.ofi_ai.default_provider') === 'local';
        $requiredRecommendationCount = $isLocalProvider
            ? max(2, min(count($allowedActivityCodes), 2))
            : max(2, min(count($allowedActivityCodes), 4));

        $systemPrompt = implode("\n", [
            'Anda adalah konsultan COBIT 2019 yang membantu menyusun Opportunity for Improvement (OFI).',
            'Tugas Anda adalah membuat rekomendasi yang konkret, dapat dijalankan, dan relevan dengan gap capability level.',
            'Anda wajib menjawab dalam satu objek JSON valid tanpa markdown, tanpa code fence, tanpa HTML, dan tanpa penjelasan tambahan.',
            'Jangan tampilkan chain-of-thought, reasoning internal, atau tag seperti <think>.',
            'Gunakan bahasa Indonesia yang formal dan operasional.',
        ]);

        $activityLines = collect($activities)
            ->map(function (array $activity) {
                return sprintf(
                    '- [%s] %s | level=%s | compliance=%s%% | rating=%s | guidance=%s | evidence=%s',
                    $activity['activity_code'],
                    $activity['activity_name'],
                    $activity['level'],
                    $activity['current_compliance'],
                    $activity['capability_rating'] ?? '-',
                    $activity['guidance'] ?: '-',
                    $activity['document_requirements'] ?: '-'
                );
            })
            ->implode("\n");

        $userPrompt = implode("\n", [
            'Buat OFI berbasis AI dari konteks assessment berikut.',
            '',
            'Assessment:',
            '- id: '.$assessment->id,
            '- code: '.$assessment->code,
            '- title: '.$assessment->title,
            '',
            'GAMO:',
            '- id: '.$gamo->id,
            '- code: '.$gamo->code,
            '- name: '.$gamo->name,
            '',
            'Capability:',
            '- current_level: '.$templateData['current_level'],
            '- target_level: '.$templateData['target_level'],
            '- gap: '.$templateData['gap_score'],
            '',
            'Baseline title:',
            '- '.$templateData['title'],
            '',
            'Aktivitas prioritas:',
            $activityLines,
            '',
            'Aturan output:',
            '- Jawab hanya dengan satu objek JSON final.',
            '- Jangan tulis markdown, code fence, penjelasan, atau kata pembuka.',
            '- Isi field wajib: title, summary, priority, rationale, recommendations.',
            '- priority hanya boleh: low, medium, high, critical.',
            '- recommendations harus berupa array object.',
            '- Jumlah recommendations harus tepat '.$requiredRecommendationCount.' item.',
            '- Setiap item recommendations wajib memiliki field: activity_code, issue, objective, recommended_action, expected_evidence, success_indicator, priority.',
            '- Setiap recommendation harus fokus pada activity_code yang berbeda.',
            '- activity_code wajib memakai kode aktivitas nyata dari daftar Aktivitas prioritas. Jangan membuat kode placeholder, jangan gunakan XX, TBD, dummy, atau ...',
            '- issue, recommended_action, dan expected_evidence harus spesifik. Jangan gunakan teks generik seperti "...", "dan lain-lain", atau placeholder serupa.',
            '- objective dan success_indicator juga harus spesifik dan dapat diverifikasi.',
            '',
            'Contoh bentuk JSON yang benar:',
            '{',
            '  "title": "Rekomendasi peningkatan kapabilitas '.$gamo->code.'",',
            '  "summary": "Fokus perbaikan diarahkan pada beberapa aktivitas prioritas dengan kepatuhan terendah agar target level dapat dicapai.",',
            '  "priority": "high",',
            '  "rationale": "Gap level memerlukan penguatan kontrol, dokumentasi, penetapan PIC, dan review berkala pada aktivitas prioritas.",',
            '  "recommendations": [',
            '    {',
            '      "activity_code": "'.$exampleActivityCode.'",',
            '      "issue": "Aktivitas belum memiliki mekanisme evaluasi manfaat yang terdokumentasi dan belum dijalankan secara konsisten.",',
            '      "objective": "Membangun proses evaluasi manfaat yang konsisten dan dapat diaudit.",',
            '      "recommended_action": "Tetapkan prosedur evaluasi manfaat, tentukan PIC, dan lakukan review berkala atas realisasi manfaat investasi I&T.",',
            '      "expected_evidence": "SOP evaluasi manfaat, notulen review, daftar PIC, laporan realisasi manfaat.",',
            '      "success_indicator": "Review manfaat dilakukan berkala dan terdokumentasi untuk investasi utama.",',
            '      "priority": "high"',
            '    }',
            '  ]',
            '}',
        ]);

        return [
            'system_prompt' => $systemPrompt,
            'user_prompt' => $userPrompt,
            'allowed_activity_codes' => $allowedActivityCodes,
            'activities' => $activities,
            'required_recommendation_count' => $requiredRecommendationCount,
            'prompt_preview' => trim($systemPrompt."\n\n".$userPrompt),
        ];
    }
}
