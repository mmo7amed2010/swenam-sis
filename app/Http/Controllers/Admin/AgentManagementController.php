<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRequest;
use App\Models\User;
use App\Services\AgentService;
use App\Services\LmsApiService;
use App\Traits\HandlesDataTableRequests;
use Illuminate\Http\Request;

class AgentManagementController extends Controller
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
        2 => 'agent_applications_count',
        3 => 'created_at',
    ];

    public function __construct(
        private AgentService $agentService
    ) {}

    /**
     * Display a listing of agent users.
     */
    public function index(Request $request)
    {
        // Handle DataTables AJAX request
        if ($this->isDataTableRequest($request)) {
            $query = User::query()
                ->where('user_type', 'agent')
                ->withCount('agentApplications');

            return $this->dataTableResponse(
                query: $query,
                request: $request,
                transformer: fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                    'last_login_at' => $user->last_login_at?->diffForHumans() ?? 'Never',
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $user->created_at->format('d M Y, h:i a'),
                    'applications_count' => $user->agent_applications_count,
                    'applications_url' => route('admin.applications.index', ['agent_id' => $user->id]),
                    'show_url' => route('admin.agents.show', $user),
                    'can_delete' => $this->agentService->canDelete($user),
                ],
                searchableColumns: $this->searchableColumns,
                filters: $this->filters,
                orderableColumns: $this->orderableColumns
            );
        }

        // Regular page load - return view with stats
        $stats = $this->agentService->getStatistics();

        return view('pages.admin.agents.index', [
            'totalAgents' => $stats['total'],
            'activeAgents' => $stats['active'],
            'newThisMonth' => $stats['new_this_month'],
        ]);
    }

    /**
     * Store a newly created agent in storage.
     */
    public function store(AgentRequest $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $data = $request->validated();

                // Handle file upload
                if ($request->hasFile('avatar')) {
                    $data['avatar'] = $request->file('avatar');
                }

                $user = $this->agentService->createAgent($data);
                $counts = $this->agentService->getCounts();

                return response()->json([
                    'success' => true,
                    'message' => __('Agent created successfully!'),
                    'agent' => $user,
                    'total_agents' => $counts['total'],
                    'active_agents' => $counts['active'],
                    'new_this_month' => $counts['new_this_month'],
                ], 201);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => __('Failed to create agent: ').$e->getMessage(),
                ], 422);
            }
        }

        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar');
        }

        $this->agentService->createAgent($data);

        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent created successfully!');
    }

    /**
     * Display the specified agent.
     */
    public function show(User $agent, LmsApiService $lmsApiService)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'email' => $agent->email,
                    'profile_photo_url' => $agent->profile_photo_url,
                ],
            ]);
        }

        $agentApplications = $agent->agentApplications()->latest()->get();

        // Pre-fetch programs map to avoid N+1 API calls
        $programsMap = collect($lmsApiService->getPrograms())->keyBy('id');

        $agentApplications->each(function ($app) use ($programsMap) {
            $app->resolved_program_name = $programsMap->get($app->program_id)['name'] ?? 'N/A';
        });

        $statusCounts = [
            'total' => $agentApplications->count(),
            'pending' => $agentApplications->where('status', 'pending')->count(),
            'initial_approved' => $agentApplications->whereIn('status', ['initial_approved', 'contract_sent', 'contract_uploaded', 'contract_approved', 'payment_pending', 'payment_uploaded', 'payment_approved'])->count(),
            'approved' => $agentApplications->where('status', 'approved')->count(),
            'rejected' => $agentApplications->where('status', 'rejected')->count(),
        ];

        return view('pages.admin.agents.show', [
            'agent' => $agent,
            'agentApplications' => $agentApplications,
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Show the form for editing the specified agent.
     */
    public function edit(User $agent)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'email' => $agent->email,
                    'profile_photo_url' => $agent->profile_photo_url,
                ],
            ]);
        }

        return redirect()->route('admin.agents.index');
    }

    /**
     * Update the specified agent in storage.
     */
    public function update(AgentRequest $request, User $agent)
    {
        if ($request->ajax() || $request->wantsJson()) {
            try {
                $data = $request->validated();

                // Handle file upload
                if ($request->hasFile('avatar')) {
                    $data['avatar'] = $request->file('avatar');
                }

                $agent = $this->agentService->updateAgent($agent, $data);
                $counts = $this->agentService->getCounts();

                return response()->json([
                    'success' => true,
                    'message' => __('Agent updated successfully!'),
                    'agent' => $agent,
                    'total_agents' => $counts['total'],
                    'active_agents' => $counts['active'],
                    'new_this_month' => $counts['new_this_month'],
                ]);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'success' => false,
                    'message' => __('Failed to update agent: ').$e->getMessage(),
                ], 422);
            }
        }

        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar');
        }

        $this->agentService->updateAgent($agent, $data);

        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent updated successfully!');
    }

    /**
     * Remove the specified agent from storage.
     */
    public function destroy(Request $request, User $agent)
    {
        // Check if agent can be deleted
        if (! $this->agentService->canDelete($agent)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('You cannot delete yourself.'),
                ], 422);
            }

            return redirect()->route('admin.agents.index')
                ->with('error', 'You cannot delete yourself.');
        }

        $this->agentService->deleteAgent($agent);
        $counts = $this->agentService->getCounts();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Agent deleted successfully!'),
                'total_agents' => $counts['total'],
                'active_agents' => $counts['active'],
                'new_this_month' => $counts['new_this_month'],
            ]);
        }

        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent deleted successfully!');
    }

    /**
     * Show the form for creating a new agent (not used - modal instead).
     */
    public function create()
    {
        return redirect()->route('admin.agents.index');
    }
}
