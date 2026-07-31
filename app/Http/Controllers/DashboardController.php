<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Task;
use App\Services\ReportService;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected ReportService $reportService;
    protected TaskService $taskService;

    public function __construct(ReportService $reportService, TaskService $taskService)
    {
        $this->reportService = $reportService;
        $this->taskService = $taskService;
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $stats = $this->reportService->getDashboardStats();
            return view('dashboard.index', compact('stats', 'user'));
        }

        // Operatör dashboard
        $todayTasks = $this->taskService->getTodayTasks($user->id);
        $myLeadsCount = Lead::where('atanan_operator_id', $user->id)->count();
        $myTodayLeadsCount = Lead::where('atanan_operator_id', $user->id)
            ->whereDate('created_at', Carbon::today())->count();
        $myPendingTasks = Task::where('operator_id', $user->id)
            ->where('durum', 'bekliyor')->count();
        $recentLeads = Lead::with('status')
            ->where('atanan_operator_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'user', 'todayTasks', 'myLeadsCount', 
            'myTodayLeadsCount', 'myPendingTasks', 'recentLeads'
        ));
    }
}
