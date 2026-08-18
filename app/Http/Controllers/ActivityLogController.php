<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Payment;
use App\Models\Property;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = Activity::with('causer', 'subject')
            ->whereIn('subject_type', [Contract::class, Payment::class, Property::class])
            ->latest('id')
            ->paginate(30);

        return view('activity-log.index', compact('activities'));
    }
}
