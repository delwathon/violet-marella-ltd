<?php

namespace App\Http\Controllers;

use App\Services\SystemUpdater;
use Illuminate\Http\RedirectResponse;
use Throwable;

class SystemUpdateController extends Controller
{
    public function run(SystemUpdater $systemUpdater): RedirectResponse
    {
        try {
            $result = $systemUpdater->run();

            return redirect()
                ->back()
                ->with('success', "System update completed successfully. Database backup saved to {$result['backup_file']}.");
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->back()
                ->with('error', 'System update failed: ' . $exception->getMessage());
        }
    }
}
