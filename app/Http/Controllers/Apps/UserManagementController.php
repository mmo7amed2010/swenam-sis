<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\AdminService;
use App\Traits\HandlesDataTableRequests;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    use HandlesDataTableRequests;

    /**
     * Searchable columns for DataTables.
     */
    protected array $searchableColumns = ['name', 'email'];

    /**
     * Filters configuration for DataTables.
     */
    protected array $filters = [];

    /**
     * Orderable columns configuration.
     */
    protected array $orderableColumns = [
        0 => 'name',
        1 => 'email',
        3 => 'created_at',
    ];

    public function __construct(
        private AdminService $adminService
    ) {}

    /**
     * Display a listing of admin users.
     */
    public function index(Request $request)
    {
        // Handle DataTables AJAX request
        if ($this->isDataTableRequest($request)) {
            $query = User::query()
                ->where('user_type', 'admin');

            return $this->dataTableResponse(
                query: $query,
                request: $request,
                transformer: fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'is_super_admin' => $user->is_super_admin ?? false,
                    'profile_photo_url' => $user->profile_photo_url,
                    'last_login_at' => $user->last_login_at?->diffForHumans() ?? 'Never',
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $user->created_at->format('d M Y, h:i a'),
                    'show_url' => route('user-management.users.show', $user),
                    'can_delete' => $this->adminService->canDelete($user),
                ],
                searchableColumns: $this->searchableColumns,
                filters: $this->filters,
                orderableColumns: $this->orderableColumns
            );
        }

        // Regular page load - return view with stats
        $stats = $this->adminService->getStatistics();

        return view('pages.admin.user-management.users.index', [
            'totalAdmins' => $stats['total'],
            'activeAdmins' => $stats['active'],
            'newThisMonth' => $stats['new_this_month'],
        ]);
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(UserRequest $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $data = $request->validated();

                // Handle file upload
                if ($request->hasFile('avatar')) {
                    $data['avatar'] = $request->file('avatar');
                }

                $user = $this->adminService->createAdmin($data);
                $counts = $this->adminService->getCounts();

                return response()->json([
                    'success' => true,
                    'message' => __('Admin created successfully!'),
                    'admin' => $user,
                    'total_admins' => $counts['total'],
                    'active_admins' => $counts['active'],
                    'new_this_month' => $counts['new_this_month'],
                ], 201);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => __('Failed to create admin: ').$e->getMessage(),
                ], 422);
            }
        }

        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar');
        }

        $this->adminService->createAdmin($data);

        return redirect()->route('user-management.users.index')
            ->with('success', 'Admin created successfully!');
    }

    /**
     * Display the specified admin.
     */
    public function show(User $user)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_super_admin' => $user->is_super_admin ?? false,
                    'profile_photo_url' => $user->profile_photo_url,
                ],
            ]);
        }

        return redirect()->route('user-management.users.index');
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $user)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_super_admin' => $user->is_super_admin ?? false,
                    'profile_photo_url' => $user->profile_photo_url,
                ],
            ]);
        }

        return redirect()->route('user-management.users.index');
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $data = $request->validated();

                // Handle file upload
                if ($request->hasFile('avatar')) {
                    $data['avatar'] = $request->file('avatar');
                }

                $user = $this->adminService->updateAdmin($user, $data);
                $counts = $this->adminService->getCounts();

                return response()->json([
                    'success' => true,
                    'message' => __('Admin updated successfully!'),
                    'admin' => $user,
                    'total_admins' => $counts['total'],
                    'active_admins' => $counts['active'],
                    'new_this_month' => $counts['new_this_month'],
                ]);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => __('Failed to update admin: ').$e->getMessage(),
                ], 422);
            }
        }

        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar');
        }

        $this->adminService->updateAdmin($user, $data);

        return redirect()->route('user-management.users.index')
            ->with('success', 'Admin updated successfully!');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // Check if admin can be deleted
        if (! $this->adminService->canDelete($user)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('You cannot delete yourself.'),
                ], 422);
            }

            return redirect()->route('user-management.users.index')
                ->with('error', 'You cannot delete yourself.');
        }

        $this->adminService->deleteAdmin($user);
        $counts = $this->adminService->getCounts();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Admin deleted successfully!'),
                'total_admins' => $counts['total'],
                'active_admins' => $counts['active'],
                'new_this_month' => $counts['new_this_month'],
            ]);
        }

        return redirect()->route('user-management.users.index')
            ->with('success', 'Admin deleted successfully!');
    }

    /**
     * Show the form for creating a new admin (not used - modal instead).
     */
    public function create()
    {
        return redirect()->route('user-management.users.index');
    }
}
