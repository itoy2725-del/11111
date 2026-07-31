<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportLeadRequest;
use App\Services\ImportService;
use App\Models\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    protected ImportService $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    public function index()
    {
        $imports = Import::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('imports.index', compact('imports'));
    }

    public function upload(ImportLeadRequest $request)
    {
        $file = $request->file('csv_file');
        
        // Vercel serverless /tmp writable storage compatibility
        $tmpDir = is_dir('/tmp') ? '/tmp' : storage_path('app');
        $originalName = $file->getClientOriginalName();
        $fileName = 'import_' . time() . '_' . Str::random(6) . '.csv';
        $fullPath = $tmpDir . '/' . $fileName;

        @copy($file->getRealPath(), $fullPath);
        
        $rows = $this->importService->parseCSV($fullPath);
        
        if (empty($rows)) {
            @unlink($fullPath);
            return back()->with('error', 'CSV dosyası boş veya okunamadı.');
        }

        $validation = $this->importService->validateHeaders(array_keys($rows[0]));
        if (!$validation['valid']) {
            @unlink($fullPath);
            return back()->with('error', 'Eksik başlıklar: ' . implode(', ', $validation['missing']));
        }

        $analysis = $this->importService->checkDuplicates($rows);

        $request->session()->put('import_original_name', $originalName);
        $request->session()->put('import_file_path', $fullPath);
        $request->session()->put('import_rows', $rows);
        $request->session()->put('import_analysis', $analysis);

        return redirect()->route('import.preview');
    }

    public function preview(Request $request)
    {
        $rows = $request->session()->get('import_rows');
        $analysis = $request->session()->get('import_analysis');
        
        if (!$rows || !$analysis) {
            return redirect()->route('import.index')->with('error', 'Önizleme verisi bulunamadı.');
        }

        $previewRows = array_slice($rows, 0, 20);
        
        return view('imports.preview', [
            'totalCount' => count($rows),
            'newCount' => count($analysis['new']),
            'duplicateCount' => count($analysis['duplicates']),
            'errorCount' => count($analysis['errors']),
            'errors' => array_slice($analysis['errors'], 0, 10),
            'previewRows' => $previewRows,
            'duplicates' => array_slice($analysis['duplicates'], 0, 10),
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'duplicate_action' => 'required|in:skip,update,create_new'
        ]);

        $rows = $request->session()->get('import_rows');
        if (!$rows) {
            return redirect()->route('import.index')->with('error', 'İçe aktarma verisi bulunamadı.');
        }

        $originalName = $request->session()->get('import_original_name', 'import_' . date('Y-m-d_H-i-s') . '.csv');
        $import = $this->importService->processImport($rows, $request->duplicate_action, Auth::id(), $originalName);

        $request->session()->forget(['import_rows', 'import_analysis', 'import_original_name']);
        
        $path = $request->session()->get('import_file_path');
        if ($path && file_exists($path)) {
            @unlink($path);
            $request->session()->forget('import_file_path');
        }

        return view('imports.result', [
            'import' => $import,
        ]);
    }
}
