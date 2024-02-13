<?php

namespace App\Http\Controllers;

use App\Models\Redirect;
use App\Models\RedirectLog;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function showStats($redirect)
    {
        $redirectId = resolve('App\Services\HashidsService')->decode($redirect);
        $redirect = Redirect::find($redirectId);

        if (!$redirect) {
            return response()->json(['message' => 'Redirect not found'], 404);
        }

        $totalAccesses = $redirect->logs()->count();
        $uniqueAccesses = $redirect->logs()->distinct('ip')->count('ip');
        $topReferrers = $redirect->logs()
                            ->select('referer', \DB::raw('count(*) as total'))
                            ->groupBy('referer')
                            ->orderByDesc('total')
                            ->limit(5)
                            ->get();

        $accessesLast10Days = $redirect->logs()
                                ->select(\DB::raw('DATE(created_at) as date'), \DB::raw('count(*) as total'))
                                ->where('created_at', '>=', now()->subDays(10))
                                ->groupBy('date')
                                ->orderBy('date')
                                ->get();

        return response()->json([
            'total_accesses' => $totalAccesses,
            'unique_accesses' => $uniqueAccesses,
            'top_referrers' => $topReferrers,
            'accesses_last_10_days' => $accessesLast10Days,
        ]);
    }

    public function showLogs($redirect)
    {
        $redirectId = resolve('App\Services\HashidsService')->decode($redirect);
        $redirect = Redirect::find($redirectId);

        if (!$redirect) {
            return response()->json(['message' => 'Redirect not found'], 404);
        }

        $logs = $redirect->logs()->get();

        return response()->json($logs);
    }
}
