<?php

namespace App\Http\Controllers;

use App\Exports\TopProjectsExport;
use App\Services\ReportService;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function exportTopProjects()
    {
        return Excel::download(new TopProjectsExport, 'loyihalar-reytingi.xlsx');
    }

    public function index(ReportService $reports)
    {
        $monthlySales = $reports->monthlySales();
        $monthlyCollected = $reports->monthlyCollected();
        $propertyStatus = $reports->propertyStatusBreakdown();
        $topProjects = $reports->topProjectsByRevenue();
        $paymentStatus = $reports->paymentStatusBreakdown();

        return view('reports.index', compact(
            'monthlySales',
            'monthlyCollected',
            'propertyStatus',
            'topProjects',
            'paymentStatus',
        ));
    }
}
