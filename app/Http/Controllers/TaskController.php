<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Services\TaskService;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        $filters = $request->all();
        $operatorId = Auth::user()->isOperator() ? Auth::id() : null;
        
        $tasks = $this->taskService->getTasks($filters, $operatorId);
        $operators = User::where('rol', 'operator')->where('aktif', true)->get();
        $leads = Lead::select('id', 'ad_soyad', 'telefon')->orderByDesc('id')->limit(100)->get();

        return view('tasks.index', compact('tasks', 'filters', 'operators', 'leads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'nullable|exists:leads,id',
            'baslik' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'tarih' => 'required|date',
            'operator_id' => 'required|exists:users,id'
        ]);

        if (Auth::user()->isOperator() && $request->operator_id != Auth::id()) {
            abort(403);
        }

        $this->taskService->createTask($request->all());
        
        return redirect()->back()->with('success', 'Görev oluşturuldu.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['durum' => 'required|in:bekliyor,tamamlandi,iptal']);
        
        $task = Task::findOrFail($id);
        
        if (Auth::user()->isOperator() && $task->operator_id !== Auth::id()) {
            abort(403);
        }

        $this->taskService->updateTaskStatus($task, $request->durum);
        
        return redirect()->back()->with('success', 'Görev durumu güncellendi.');
    }
}
