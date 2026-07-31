<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lead;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OperatorController extends Controller
{
    public function index()
    {
        $operators = User::where('rol', 'operator')->withCount('leads')->get();
        return view('operators.index', compact('operators'));
    }

    public function show(int $id)
    {
        $operator = User::where('rol', 'operator')->withCount('leads')->findOrFail($id);
        $leads = Lead::with('status')->where('atanan_operator_id', $id)->orderByDesc('created_at')->paginate(20);
        $completedCount = Lead::where('atanan_operator_id', $id)
            ->whereHas('status', fn($q) => $q->where('isim', 'Tamamlandı'))->count();
        $pendingCount = Lead::where('atanan_operator_id', $id)
            ->whereHas('status', fn($q) => $q->whereNotIn('isim', ['Tamamlandı', 'Kapatıldı']))->count();

        return view('operators.show', compact('operator', 'leads', 'completedCount', 'pendingCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'isim' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8'
        ]);

        $user = User::create([
            'isim' => $request->isim,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => 'operator',
            'aktif' => true
        ]);

        AuditService::logStatic('Operatör oluşturuldu', 'User', $user->id);

        return redirect()->back()->with('success', 'Operatör eklendi.');
    }

    public function update(Request $request, int $id)
    {
        $operator = User::findOrFail($id);
        
        $request->validate([
            'isim' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($operator->id)],
            'password' => 'nullable|min:8'
        ]);

        $data = [
            'isim' => $request->isim,
            'email' => $request->email,
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $operator->update($data);
        AuditService::logStatic('Operatör güncellendi', 'User', $operator->id);

        return redirect()->back()->with('success', 'Operatör güncellendi.');
    }

    public function toggleActive(int $id)
    {
        $operator = User::findOrFail($id);
        $operator->update(['aktif' => !$operator->aktif]);
        
        AuditService::logStatic(
            $operator->aktif ? 'Operatör aktif edildi' : 'Operatör pasif edildi',
            'User',
            $operator->id
        );
        
        return redirect()->back()->with('success', $operator->aktif ? 'Operatör aktif edildi.' : 'Operatör pasif edildi.');
    }
}
