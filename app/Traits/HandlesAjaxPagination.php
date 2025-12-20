<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Trait HandlesAjaxPagination
 *
 * Provides reusable AJAX pagination functionality for controllers.
 * Automatically detects AJAX requests and returns appropriate responses.
 */
trait HandlesAjaxPagination
{
    /**
     * Handle AJAX pagination requests
     *
     * For AJAX requests, returns only the partial view (table content).
     * For regular requests, returns the full page view.
     *
     * @param  string  $view  Full view name (e.g., 'pages.admin.programs.index')
     * @param  array  $data  Data to pass to view
     * @param  string|null  $partialView  Optional partial view (auto-detected if null)
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    protected function handleAjaxPagination(
        Request $request,
        string $view,
        array $data,
        ?string $partialView = null
    ) {
        if ($request->ajax() || $request->wantsJson()) {
            // For AJAX, return partial view with just the table content
            $partial = $partialView ?? $this->resolvePartialView($view);

            if (view()->exists($partial)) {
                return view($partial, $data);
            }

            // Fallback: return full view (JS will extract container)
            return view($view, $data);
        }

        return view($view, $data);
    }

    /**
     * Resolve partial view name from full view
     *
     * Convention: pages.admin.programs.index -> pages.admin.programs.partials.table
     */
    protected function resolvePartialView(string $view): string
    {
        $parts = explode('.', $view);
        $lastPart = array_pop($parts);

        return implode('.', $parts).'.partials.table';
    }

    /**
     * Apply search filter to query
     *
     * @param  array  $searchableColumns  Columns to search in
     * @param  string|array  $param  Search parameter name(s) to check
     */
    protected function applySearch(
        Builder $query,
        Request $request,
        array $searchableColumns,
        string|array $param = 'search'
    ): Builder {
        // Support multiple search param names for flexibility
        $params = is_array($param) ? $param : [$param, 'search', 'q'];

        $searchTerm = null;
        foreach ($params as $p) {
            if ($request->filled($p)) {
                $searchTerm = $request->input($p);
                break;
            }
        }

        if (! empty($searchTerm)) {
            $query->where(function ($q) use ($searchableColumns, $searchTerm) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$searchTerm}%");
                }
            });
        }

        return $query;
    }

    /**
     * Apply filters to query
     *
     * @param  array  $filters  Array of [param => column] or [param => callable]
     */
    protected function applyFilters(Builder $query, Request $request, array $filters): Builder
    {
        foreach ($filters as $param => $columnOrCallable) {
            $value = $request->input($param);

            if ($value !== null && $value !== '') {
                if (is_callable($columnOrCallable)) {
                    $columnOrCallable($query, $value);
                } else {
                    $query->where($columnOrCallable, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Apply sorting to query
     *
     * @param  string  $defaultColumn  Default sort column
     * @param  string  $defaultDirection  Default sort direction (asc|desc)
     * @param  array  $allowedColumns  Whitelisted columns for sorting
     */
    protected function applySorting(
        Builder $query,
        Request $request,
        string $defaultColumn = 'created_at',
        string $defaultDirection = 'desc',
        array $allowedColumns = []
    ): Builder {
        $sortColumn = $request->input('sort', $defaultColumn);
        $sortDirection = $request->input('dir', $defaultDirection);

        // Validate sort column
        if (! empty($allowedColumns) && ! in_array($sortColumn, $allowedColumns)) {
            $sortColumn = $defaultColumn;
        }

        // Validate direction
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc'])
            ? strtolower($sortDirection)
            : $defaultDirection;

        return $query->orderBy($sortColumn, $sortDirection);
    }

    /**
     * Get per-page value from request with validation
     *
     * @param  int  $default  Default per-page value
     * @param  array  $allowed  Allowed per-page values
     */
    protected function getPerPage(
        Request $request,
        int $default = 15,
        array $allowed = [10, 15, 25, 50, 100]
    ): int {
        $perPage = (int) $request->input('per_page', $default);

        return in_array($perPage, $allowed) ? $perPage : $default;
    }

    /**
     * Build paginated query with all filters
     *
     * @param  string|array  $searchParam  Search parameter name(s)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function buildPaginatedQuery(
        Builder $query,
        Request $request,
        array $searchableColumns = [],
        array $filters = [],
        array $sortingOptions = [],
        string|array $searchParam = 'search'
    ) {
        // Apply search with flexible param names
        if (! empty($searchableColumns)) {
            $this->applySearch($query, $request, $searchableColumns, $searchParam);
        }

        // Apply filters
        if (! empty($filters)) {
            $this->applyFilters($query, $request, $filters);
        }

        // Apply sorting
        $this->applySorting(
            $query,
            $request,
            $sortingOptions['defaultColumn'] ?? 'created_at',
            $sortingOptions['defaultDirection'] ?? 'desc',
            $sortingOptions['allowedColumns'] ?? []
        );

        // Paginate
        $perPage = $this->getPerPage($request);

        return $query->paginate($perPage)->withQueryString();
    }
}
