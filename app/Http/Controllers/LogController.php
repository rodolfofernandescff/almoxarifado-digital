<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    /**
     * Display a listing of the system logs.
     */
    public function index(): Response
    {
        $logs = Activity::with('causer')
            ->latest()
            ->paginate(20)
            ->through(fn ($log) => [
                'id' => $log->id,
                'log_name' => $log->log_name,
                'description' => $log->description,
                'subject_type' => class_basename($log->subject_type),
                'subject_id' => $log->subject_id,
                'causer_name' => $log->causer ? $log->causer->name : 'Sistema',
                'properties' => $log->properties,
                'created_at' => $log->created_at->format('d/m/Y H:i:s'),
            ]);

        return Inertia::render('Admin/Logs/Index', [
            'logs' => $logs,
        ]);
    }
}
