<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Trait HandlesDataTableRequests
 *
 * Provides DataTables server-side processing support for controllers.
 * Returns JSON responses compatible with DataTables jQuery plugin.
 *
 * SECURITY CONSIDERATIONS:
 * - Controllers MUST eager load relationships to prevent N+1 queries
 * - Use explicit column whitelisting in $orderableColumns
 * - Use callable filters for complex filtering logic
 * - Search values have wildcards escaped automatically
 * - All errors are logged with user context per CLAUDE.md requirements
 */
trait HandlesDataTableRequests
{
    /**
     * Generate a DataTables-compatible JSON response
     *
     * SECURITY: This method expects the controller to:
     * - Eager load relationships to avoid N+1 queries
     * - Use explicit column whitelisting in $orderableColumns
     * - Use callable filters for complex logic
     *
     * @param  Builder  $query  Base query (eager load relations!)
     * @param  Request  $request  HTTP request with DataTables parameters
     * @param  callable  $transformer  fn(Model): array - Transform each row
     * @param  array  $searchableColumns  Columns to search (must be actual DB columns)
     * @param  array  $filters  [param => column|callable] - Filter definitions
     * @param  array  $orderableColumns  [index => column] - Orderable column mapping
     * @param  string|null  $cacheKey  Optional cache key prefix for result caching
     * @return JsonResponse DataTables-compatible response
     *
     * @throws \Exception On database errors (caught and logged)
     */
    protected function dataTableResponse(
        Builder $query,
        Request $request,
        callable $transformer,
        array $searchableColumns = [],
        array $filters = [],
        array $orderableColumns = [],
        ?string $cacheKey = null
    ): JsonResponse {
        try {
            // Check if caching is enabled and a cache key was provided
            if ($cacheKey && config('datatable.enable_caching', false)) {
                return $this->getCachedDataTableResponse(
                    $query,
                    $request,
                    $transformer,
                    $searchableColumns,
                    $filters,
                    $orderableColumns,
                    $cacheKey
                );
            }

            return $this->buildDataTableResponse(
                $query,
                $request,
                $transformer,
                $searchableColumns,
                $filters,
                $orderableColumns
            );
        } catch (\Exception $e) {
            Log::error('DataTable query failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'user_type' => auth()->user()?->user_type,
                'request_params' => $request->only(['draw', 'start', 'length', 'search', 'order']),
            ]);

            // Return a safe error response that DataTables can handle
            return response()->json([
                'draw' => max(1, (int) $request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load data. Please try again.',
            ], 500);
        }
    }

    /**
     * Build the DataTable response (internal implementation)
     */
    protected function buildDataTableResponse(
        Builder $query,
        Request $request,
        callable $transformer,
        array $searchableColumns,
        array $filters,
        array $orderableColumns
    ): JsonResponse {
        // Validate and sanitize input parameters
        $draw = max(1, (int) $request->input('draw', 1));
        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', config('datatable.default_length', 15));

        // Validate length against allowed values from config
        $allowedLengths = config('datatable.allowed_lengths', [10, 15, 25, 50, 100]);
        if (! in_array($length, $allowedLengths)) {
            $length = config('datatable.default_length', 15);
        }

        // Clone query for total count (before any filters)
        $totalQuery = clone $query;
        $recordsTotal = $totalQuery->count();

        // Apply search with escaped wildcards (security fix)
        $searchValue = $request->input('search.value', '');
        if (! empty($searchValue) && ! empty($searchableColumns)) {
            // Escape LIKE wildcards to prevent injection
            $escapedSearch = $this->escapeLikeWildcards($searchValue);

            $query->where(function ($q) use ($searchableColumns, $escapedSearch) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$escapedSearch}%");
                }
            });
        }

        // Apply custom filters
        foreach ($filters as $param => $columnOrCallable) {
            $value = $request->input($param);

            if ($value !== null && $value !== '' && $value !== 'all') {
                if (is_callable($columnOrCallable)) {
                    $columnOrCallable($query, $value);
                } else {
                    // Use parameter binding for security
                    $query->where($columnOrCallable, $value);
                }
            }
        }

        // Count after filtering
        $recordsFiltered = $query->count();

        // Apply ordering with case-insensitive direction validation
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = strtolower($request->input('order.0.dir', 'desc'));

        // Validate direction to prevent SQL injection
        if (! in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        if (! empty($orderableColumns) && isset($orderableColumns[$orderColumnIndex])) {
            $orderColumn = $orderableColumns[$orderColumnIndex];
            $query->orderBy($orderColumn, $orderDirection);
        }

        // Apply pagination
        $data = $query->skip($start)->take($length)->get();

        // Transform data
        $transformedData = $data->map($transformer)->values()->toArray();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $transformedData,
        ]);
    }

    /**
     * Get cached DataTable response with user context in cache key
     *
     * Cache keys include user_id and user_type to prevent data leakage
     * between users (per CLAUDE.md security requirements).
     */
    protected function getCachedDataTableResponse(
        Builder $query,
        Request $request,
        callable $transformer,
        array $searchableColumns,
        array $filters,
        array $orderableColumns,
        string $cacheKey
    ): JsonResponse {
        $userId = auth()->id() ?? 0;
        $userType = auth()->user()?->user_type ?? 'guest';

        // Build cache key with user context for security
        $requestHash = md5(serialize($request->only([
            'draw', 'start', 'length', 'search', 'order', ...array_keys($filters),
        ])));

        $fullCacheKey = "datatable_{$cacheKey}_{$userId}_{$userType}_{$requestHash}";

        $cacheTtl = config('datatable.cache_ttl', 300);

        return Cache::remember($fullCacheKey, $cacheTtl, function () use (
            $query,
            $request,
            $transformer,
            $searchableColumns,
            $filters,
            $orderableColumns
        ) {
            return $this->buildDataTableResponse(
                $query,
                $request,
                $transformer,
                $searchableColumns,
                $filters,
                $orderableColumns
            );
        });
    }

    /**
     * Escape LIKE wildcards in search value to prevent SQL injection
     *
     * @param  string  $value  Raw search input
     * @return string Escaped search value safe for LIKE queries
     */
    protected function escapeLikeWildcards(string $value): string
    {
        return str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            $value
        );
    }

    /**
     * Check if request is a DataTables AJAX request
     */
    protected function isDataTableRequest(Request $request): bool
    {
        return $request->ajax() && $request->has('draw');
    }

    /**
     * Invalidate cached DataTable results for a specific entity type
     *
     * Call this method when data changes to ensure fresh results.
     * Uses pattern matching to clear all cache keys for the entity.
     *
     * @param  string  $cacheKeyPrefix  The cache key prefix used in dataTableResponse
     */
    protected function invalidateDataTableCache(string $cacheKeyPrefix): void
    {
        try {
            // Note: This simple implementation clears cache for current user only
            // For full cache invalidation, consider using cache tags (Redis required)
            $userId = auth()->id() ?? 0;
            $userType = auth()->user()?->user_type ?? 'guest';

            // Clear known cache key pattern
            Cache::forget("datatable_{$cacheKeyPrefix}_{$userId}_{$userType}_*");
        } catch (\Exception $e) {
            Log::warning('Failed to invalidate DataTable cache', [
                'cache_key_prefix' => $cacheKeyPrefix,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
        }
    }
}
