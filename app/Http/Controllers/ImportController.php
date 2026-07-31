<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportLeadRequest;
use App\Services\ImportService;
use App\Models\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $originalName = $file->getClientOriginalName();

        $rows = $this->importService->parseCSV($file->getRealPath());
        
        if (empty($rows)) {
            return back()->with('error', 'CSV dosyası boş veya okunamadı.');
        }

        $validation = $this->importService->validateHeaders(array_keys($rows[0]));
        if (!$validation['valid']) {
            return back()->with('error', 'Eksik başlıklar: ' . implode(', ', $validation['missing']));
        }

        $analysis = $this->importService->checkDuplicates($rows);

        // Render preview view directly in the same request to guarantee 100% serverless compatibility
        $previewRows = array_slice($rows, 0, 10);

        return view('imports.preview', [
            'originalName' => $originalName,
            'totalCount' => count($rows),
            'newCount' => count($analysis['new']),
            'duplicateCount' => count($analysis['duplicates']),
            'errorCount' => count($analysis['errors']),
            'errors' => array_slice($analysis['errors'], 0, 10),
            'previewRows' => $previewRows,
            'duplicates' => array_slice($analysis['duplicates'], 0, 10),
            'rawPayload' => base64_encode(gzcompress(json_encode([
                'rows' => $rows,
                'originalName' => $originalName,
            ]))),
        ]);
    }

    public function preview(Request $request)
    {
        return redirect()->route('import.index');
    }

    public function process(Request $request)
    {
        $request->validate([
            'duplicate_action' => 'required|in:skip,update,create_new',
            'raw_payload' => 'required|string',
        ]);

        $decompressed = @gzuncompress(base64_decode($request->input('raw_payload')));
        if (!$decompressed) {
            return redirect()->route('import.index')->with('error', 'İçe aktarma verisi okunamadı. Lütfen dosyayı tekrar yükleyin.');
        }

        $payload = json_decode($decompressed, true);
        $rows = $payload['rows'] ?? [];
        $originalName = $payload['originalName'] ?? ('import_' . date('Y-m-d_H-i-s') . '.csv');

        if (empty($rows)) {
            return redirect()->route('import.index')->with('error', 'İçe aktarma verisi bulunamadı.');
        }

        $import = $this->importService->processImport($rows, $request->duplicate_action, Auth::id(), $originalName);

        return view('imports.result', [
            'import' => $import,
        ]);
    }
}
