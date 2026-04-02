<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Test;
use App\Models\TestAssignment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'positions'   => Position::where('is_active', true)->count(),
            'tests'       => Test::where('is_active', true)->count(),
            'candidates'  => Candidate::where('status', 'active')->count(),
            'completed'   => TestAssignment::where('status', 'completed')->count(),
            'in_progress' => TestAssignment::where('status', 'in_progress')->count(),
            'pending'     => TestAssignment::where('status', 'pending')->count(),
        ];

        $recentAssignments = TestAssignment::with(['candidate', 'test', 'result'])
            ->whereIn('status', ['completed', 'in_progress'])
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        return view('dashboard', compact('stats', 'recentAssignments'));
    }
}
