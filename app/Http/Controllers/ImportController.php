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

        // Store rows & analysis in lightweight /tmp JSON file to keep session tiny & prevent cookie overflow logout
        $jsonPath = $tmpDir . '/import_payload_' . time() . '_' . Str::random(8) . '.json';
        file_put_contents($jsonPath, json_encode([
            'original_name' => $originalName,
            'file_path' => $fullPath,
            'rows' => $rows,
            'analysis' => $analysis,
        ]));

        $request->session()->put('import_json_payload', $jsonPath);

        return redirect()->route('import.preview');
    }

    public function preview(Request $request)
    {
        $jsonPath = $request->session()->get('import_json_payload');
        if (!$jsonPath || !file_exists($jsonPath)) {
            return redirect()->route('import.index')->with('error', 'Önizleme verisi bulunamadı.');
        }

        $payload = json_decode(file_get_contents($jsonPath), true);
        $rows = $payload['rows'] ?? [];
        $analysis = $payload['analysis'] ?? [];

        if (empty($rows)) {
            return redirect()->route('import.index')->with('error', 'Önizleme verisi okunamadı.');
        }

        $previewRows = array_slice($rows, 0, 20);
        
        return view('imports.preview', [
            'totalCount' => count($rows),
            'newCount' => count($analysis['new'] ?? []),
            'duplicateCount' => count($analysis['duplicates'] ?? []),
            'errorCount' => count($analysis['errors'] ?? []),
            'errors' => array_slice($analysis['errors'] ?? [], 0, 10),
            'previewRows' => $previewRows,
            'duplicates' => array_slice($analysis['duplicates'] ?? [], 0, 10),
        ]);
    }

    public function process(Request $request)
    {
        $request->validate([
            'duplicate_action' => 'required|in:skip,update,create_new'
        ]);

        $jsonPath = $request->session()->get('import_json_payload');
        if (!$jsonPath || !file_exists($jsonPath)) {
            return redirect()->route('import.index')->with('error', 'İçe aktarma verisi bulunamadı.');
        }

        $payload = json_decode(file_get_contents($jsonPath), true);
        $rows = $payload['rows'] ?? [];
        $originalName = $payload['original_name'] ?? ('import_' . date('Y-m-d_H-i-s') . '.csv');

        if (empty($rows)) {
            return redirect()->route('import.index')->with('error', 'İçe aktarma verisi bulunamadı.');
        }

        $import = $this->importService->processImport($rows, $request->duplicate_action, Auth::id(), $originalName);

        $request->session()->forget('import_json_payload');

        // Cleanup temporary files
        if (file_exists($jsonPath)) {
            @unlink($jsonPath);
        }
        $filePath = $payload['file_path'] ?? null;
        if ($filePath && file_exists($filePath)) {
            @unlink($filePath);
        }

        return view('imports.result', [
            'import' => $import,
        ]);
    }
}
