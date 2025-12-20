<?php

namespace App\Repositories;

use App\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DepartmentRepository
{
    /**
     * Get departments with search and filter.
     *
     * @param  string|null  $search  Search term
     * @param  bool|null  $active  Filter by active status
     * @param  int  $perPage  Items per page
     * @return LengthAwarePaginator Paginated departments
     */
    public function getDepartments(?string $search = null, ?bool $active = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = Department::query();

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by active status
        if ($active !== null) {
            $query->where('active', $active);
        }

        // Get departments with course count
        return $query->withCount('courses')
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get a department by ID with relationships.
     *
     * @param  int  $id  Department ID
     * @return Department|null Department or null
     */
    public function findById(int $id): ?Department
    {
        return Department::with('courses')->find($id);
    }
}
