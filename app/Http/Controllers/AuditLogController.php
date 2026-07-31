<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('kayit_tipi')) {
            $query->where('kayit_tipi', $request->kayit_tipi);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);
        $users = User::pluck('isim', 'id');
        $types = AuditLog::select('kayit_tipi')->whereNotNull('kayit_tipi')->distinct()->pluck('kayit_tipi');

        return view('audit.index', compact('logs', 'users', 'types'));
    }
}
