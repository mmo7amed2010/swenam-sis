<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Support\Facades\Log;

class DepartmentService
{
    /**
     * Create a new department.
     *
     * @param  array  $data  Department data
     * @return Department Newly created department
     */
    public function createDepartment(array $data): Department
    {
        $department = Department::create([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
            'active' => $data['active'] ?? true,
        ]);

        Log::info('Department created', [
            'department_id' => $department->id,
            'user_id' => auth()->id(),
            'name' => $department->name,
            'code' => $department->code,
        ]);

        return $department;
    }

    /**
     * Update an existing department.
     *
     * @param  Department  $department  Department to update
     * @param  array  $data  Update data
     * @return Department Updated department
     */
    public function updateDepartment(Department $department, array $data): Department
    {
        $department->update([
            'name' => $data['name'],
            'code' => $data['code'],
            'description' => $data['description'] ?? null,
            'active' => $data['active'] ?? true,
        ]);

        Log::info('Department updated', [
            'department_id' => $department->id,
            'user_id' => auth()->id(),
            'changes' => $department->getChanges(),
        ]);

        return $department->fresh();
    }

    /**
     * Delete a department.
     *
     * @param  Department  $department  Department to delete
     * @return bool Success status
     */
    public function deleteDepartment(Department $department): bool
    {
        Log::warning('Department deleted', [
            'department_id' => $department->id,
            'user_id' => auth()->id(),
            'name' => $department->name,
            'code' => $department->code,
        ]);

        return $department->delete();
    }

    /**
     * Check if a department can be deleted.
     *
     * @param  Department  $department  Department to check
     * @return bool True if can be deleted
     */
    public function canDelete(Department $department): bool
    {
        return ! $department->courses()->exists();
    }
}
