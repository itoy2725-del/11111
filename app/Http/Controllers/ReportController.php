<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $stats = $this->reportService->getDashboardStats(); // Global stats
        $opPerformance = $this->reportService->getOperatorPerformance($dateFrom, $dateTo);
        $campaignStats = $this->reportService->getCampaignStats($dateFrom, $dateTo);

        return view('reports.index', compact('stats', 'opPerformance', 'campaignStats', 'dateFrom', 'dateTo'));
    }
}
