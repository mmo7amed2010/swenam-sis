<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\StudentApplication;
use App\Services\LmsApiService;
use App\Traits\HandlesDataTableRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AgentApplicationController extends Controller
{
    use HandlesDataTableRequests;

    /**
     * List agent's applications with DataTable support.
     */
    public function index(Request $request)
    {
        if ($this->isDataTableRequest($request)) {
            $query = StudentApplication::query()
                ->where('agent_id', auth()->id());

            $lmsApiService = app(LmsApiService::class);
            $programs = collect($lmsApiService->getPrograms());
            $programsMap = $programs->keyBy('id');

            return $this->dataTableResponse(
                query: $query,
                request: $request,
                transformer: function ($application) use ($programsMap) {
                    $program = $programsMap->get($application->program_id);

                    return [
                        'id' => $application->id,
                        'reference_number' => $application->reference_number,
                        'full_name' => $application->full_name,
                        'first_name' => $application->first_name,
                        'last_name' => $application->last_name,
                        'email' => $application->email,
                        'program_name' => $program['name'] ?? 'N/A',
                        'status' => $application->status,
                        'created_at' => $application->created_at->format('M d, Y H:i'),
                        'created_at_human' => $application->created_at->diffForHumans(),
                        'show_url' => route('agent.applications.show', $application),
                    ];
                },
                searchableColumns: ['reference_number', 'first_name', 'last_name', 'email'],
                filters: [
                    'status' => fn ($q, $val) => $val !== 'all' ? $q->where('status', $val) : $q,
                    'from' => fn ($q, $val) => $q->whereDate('created_at', '>=', $val),
                    'to' => fn ($q, $val) => $q->whereDate('created_at', '<=', $val),
                ],
                orderableColumns: [
                    0 => 'reference_number',
                    1 => 'first_name',
                    2 => 'email',
                    3 => 'created_at',
                    4 => 'status',
                ]
            );
        }

        $agentApplications = StudentApplication::where('agent_id', auth()->id());

        $stats = [
            'total' => $agentApplications->count(),
            'pending' => (clone $agentApplications)->where('status', 'pending')->count(),
            'approved' => (clone $agentApplications)->where('status', 'approved')->count(),
            'rejected' => (clone $agentApplications)->where('status', 'rejected')->count(),
        ];

        return view('pages.agent.applications.index', compact('stats'));
    }

    /**
     * Show application detail (scoped to agent's applications).
     */
    public function show(StudentApplication $application)
    {
        // Ensure the application belongs to the authenticated agent
        if ($application->agent_id !== auth()->id()) {
            abort(403, 'You do not have permission to view this application.');
        }

        return view('pages.agent.applications.show', compact('application'));
    }

    /**
     * Set agent session flag and redirect to the student application form.
     */
    public function createApplication()
    {
        Session::put('agent_submission', auth()->id());

        return redirect()->route('agent.application.step1');
    }

    /**
     * Show confirmation page.
     */
    public function confirmation($referenceNumber)
    {
        $application = StudentApplication::where('reference_number', $referenceNumber)
            ->where('agent_id', auth()->id())
            ->firstOrFail();

        return view('pages.agent.applications.confirmation', compact('application'));
    }
}
