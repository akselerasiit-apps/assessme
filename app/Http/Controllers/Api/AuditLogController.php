<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs with filtering
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Authorization check
        if (!$user->hasPermissionTo('audit.view')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view audit logs'
            ], 403);
        }

        // Build query
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Role-based filtering
        if ($user->hasRole('Super Admin')) {
            // Super Admin sees all logs
        } elseif ($user->hasRole('Admin')) {
            // Admin sees company-wide logs
            $query->where(function ($q) use ($user) {
                $q->whereHas('causer', function ($sq) use ($user) {
                    $sq->where('company_id', $user->company_id);
                })
                ->orWhereHas('subject', function ($sq) use ($user) {
                    // Filter by company_id if subject has company relationship
                    if (method_exists($sq->getModel(), 'company')) {
                        $sq->where('company_id', $user->company_id);
                    }
                });
            });
        } elseif ($user->hasRole('Manager')) {
            // Manager sees limited logs (own actions + team actions)
            $query->where(function ($q) use ($user) {
                $q->where('causer_id', $user->id)
                  ->orWhere('causer_type', get_class($user));
            });
        } else {
            // Other roles see only their own logs
            $query->where('causer_id', $user->id)
                  ->where('causer_type', get_class($user));
        }

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('description', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhereHas('causer', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Pagination
        $perPage = $request->input('per_page', 20);
        $logs = $query->paginate($perPage);

        // Transform data
        $logs->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'log_name' => $log->log_name,
                'description' => $log->description,
                'subject_type' => class_basename($log->subject_type),
                'subject_id' => $log->subject_id,
                'causer' => $log->causer ? [
                    'id' => $log->causer->id,
                    'name' => $log->causer->name,
                    'email' => $log->causer->email,
                ] : null,
                'properties' => $log->properties,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $log->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    /**
     * Display a specific audit log
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user->hasPermissionTo('audit.view')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view audit logs'
            ], 403);
        }

        $log = Activity::with(['causer', 'subject'])->findOrFail($id);

        // Authorization check based on role
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            if ($log->causer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this audit log'
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'log_name' => $log->log_name,
                'description' => $log->description,
                'subject_type' => $log->subject_type,
                'subject_id' => $log->subject_id,
                'subject' => $log->subject,
                'causer' => $log->causer ? [
                    'id' => $log->causer->id,
                    'name' => $log->causer->name,
                    'email' => $log->causer->email,
                ] : null,
                'properties' => $log->properties,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $log->created_at->diffForHumans(),
            ]
        ]);
    }

    /**
     * Get audit log statistics
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasPermissionTo('audit.view')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view audit statistics'
            ], 403);
        }

        // Base query with role-based filtering
        $query = Activity::query();

        if ($user->hasRole('Admin') && !$user->hasRole('Super Admin')) {
            $query->whereHas('causer', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });
        } elseif ($user->hasRole('Manager')) {
            $query->where('causer_id', $user->id);
        }

        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Calculate statistics
        $stats = [
            'total_logs' => (clone $query)->count(),
            'logs_today' => (clone $query)->whereDate('created_at', today())->count(),
            'logs_this_week' => (clone $query)->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'logs_this_month' => (clone $query)->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        // Activity by type
        $byType = (clone $query)
            ->select('log_name', DB::raw('count(*) as count'))
            ->groupBy('log_name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->log_name => $item->count];
            });

        // Activity by user (top 10)
        $byUser = (clone $query)
            ->with('causer:id,name,email')
            ->select('causer_id', DB::raw('count(*) as count'))
            ->whereNotNull('causer_id')
            ->groupBy('causer_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'user' => $item->causer ? [
                        'id' => $item->causer->id,
                        'name' => $item->causer->name,
                        'email' => $item->causer->email,
                    ] : null,
                    'count' => $item->count,
                ];
            });

        // Activity by subject type
        $bySubjectType = (clone $query)
            ->select('subject_type', DB::raw('count(*) as count'))
            ->whereNotNull('subject_type')
            ->groupBy('subject_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [class_basename($item->subject_type) => $item->count];
            });

        // Daily activity (last 7 days)
        $dailyActivity = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->count];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats,
                'by_type' => $byType,
                'by_user' => $byUser,
                'by_subject_type' => $bySubjectType,
                'daily_activity' => $dailyActivity,
            ]
        ]);
    }

    /**
     * Export audit logs to Excel
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasPermissionTo('audit.export')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to export audit logs'
            ], 403);
        }

        // Build query with same filters as index
        $query = Activity::with(['causer'])->orderBy('created_at', 'desc');

        // Apply role-based filtering (same as index method)
        if ($user->hasRole('Admin') && !$user->hasRole('Super Admin')) {
            $query->whereHas('causer', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });
        } elseif ($user->hasRole('Manager')) {
            $query->where('causer_id', $user->id);
        }

        // Apply request filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->limit(5000)->get(); // Limit to prevent memory issues

        // Create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Audit Logs');

        // Headers
        $headers = ['ID', 'Date/Time', 'User', 'Email', 'Action', 'Module', 'Subject Type', 'Subject ID', 'Details'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
            $sheet->getStyle("{$col}1")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD9D9D9');
            $col++;
        }

        // Data
        $row = 2;
        foreach ($logs as $log) {
            $sheet->setCellValue("A{$row}", $log->id);
            $sheet->setCellValue("B{$row}", $log->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue("C{$row}", $log->causer->name ?? '-');
            $sheet->setCellValue("D{$row}", $log->causer->email ?? '-');
            $sheet->setCellValue("E{$row}", $log->description);
            $sheet->setCellValue("F{$row}", $log->log_name);
            $sheet->setCellValue("G{$row}", class_basename($log->subject_type ?? '-'));
            $sheet->setCellValue("H{$row}", $log->subject_id ?? '-');
            $sheet->setCellValue("I{$row}", json_encode($log->properties));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Save to file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = "Audit-Logs-" . date('Ymd-His') . ".xlsx";
        $tempFile = storage_path("app/temp/{$filename}");

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Get recent activities for a specific user
     * 
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function userActivities(Request $request, $userId)
    {
        $user = Auth::user();

        if (!$user->hasPermissionTo('audit.view')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view audit logs'
            ], 403);
        }

        // Authorization: Only Super Admin/Admin can view other users' activities
        if ($userId != $user->id && !$user->hasRole(['Super Admin', 'Admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this user activities'
            ], 403);
        }

        $query = Activity::where('causer_id', $userId)
            ->where('causer_type', 'App\\Models\\User')
            ->with('subject')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = $request->input('per_page', 20);
        $activities = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }
}
