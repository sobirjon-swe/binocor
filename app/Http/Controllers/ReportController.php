<?php

namespace App\Http\Controllers;

use App\Services\ReportService;

class ReportController extends Controller
{
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
