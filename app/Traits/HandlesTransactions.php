<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandlesTransactions
{
    /**
     * Execute a database operation within a transaction.
     *
     * @param  callable  $operation  The operation to execute
     * @param  string  $successMessage  Message to display on success
     * @param  string  $errorMessage  Message to display on error
     * @param  string|callable|null  $redirectRoute  Route name, callable that returns RedirectResponse, or null for back()
     * @param  array  $logContext  Additional context for error logging
     */
    protected function executeInTransaction(
        callable $operation,
        string $successMessage,
        string $errorMessage,
        string|callable|null $redirectRoute = null,
        array $logContext = []
    ): RedirectResponse {
        try {
            DB::beginTransaction();

            $result = $operation();

            DB::commit();

            if ($redirectRoute === null) {
                $redirect = back();
            } elseif (is_callable($redirectRoute)) {
                $redirect = $redirectRoute($result);
            } else {
                $redirect = redirect()->route($redirectRoute, $result);
            }

            return $redirect->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($errorMessage, array_merge([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'timestamp' => now()->toDateTimeString(),
            ], $logContext));

            return back()
                ->withInput()
                ->with('error', $errorMessage.': '.$e->getMessage());
        }
    }
}
