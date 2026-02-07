<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContractTemplateRequest;
use App\Http\Requests\UpdateContractTemplateRequest;
use App\Models\ContractTemplate;
use App\Services\ContractTemplateService;
use App\Services\LmsApiService;
use Illuminate\Http\Request;

class ContractTemplateController extends Controller
{
    public function __construct(
        private ContractTemplateService $templateService,
        private LmsApiService $lmsApiService
    ) {}

    /**
     * Display a listing of contract templates.
     */
    public function index(Request $request)
    {
        $templates = ContractTemplate::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Pre-fetch programs for display
        $programs = collect($this->lmsApiService->getPrograms());
        $programsMap = $programs->keyBy('id');

        return view('pages.admin.contract-templates.index', compact('templates', 'programsMap'));
    }

    /**
     * Show the form for creating a new contract template.
     */
    public function create()
    {
        $programs = $this->lmsApiService->getPrograms();
        $sisPlaceholders = $this->templateService->getSisPlaceholderDefinitions();

        return view('pages.admin.contract-templates.create', compact('programs', 'sisPlaceholders'));
    }

    /**
     * Store a newly created contract template.
     */
    public function store(StoreContractTemplateRequest $request)
    {
        $this->templateService->create($request->validated());

        return redirect()
            ->route('admin.contract-templates.index')
            ->with('success', 'Contract template created successfully.');
    }

    /**
     * Display the specified contract template.
     */
    public function show(ContractTemplate $contractTemplate)
    {
        $contractTemplate->load('creator', 'updater');

        return view('pages.admin.contract-templates.show', compact('contractTemplate'));
    }

    /**
     * Show the form for editing the specified contract template.
     */
    public function edit(ContractTemplate $contractTemplate)
    {
        $programs = $this->lmsApiService->getPrograms();
        $sisPlaceholders = $this->templateService->getSisPlaceholderDefinitions();

        return view('pages.admin.contract-templates.edit', compact('contractTemplate', 'programs', 'sisPlaceholders'));
    }

    /**
     * Update the specified contract template.
     */
    public function update(UpdateContractTemplateRequest $request, ContractTemplate $contractTemplate)
    {
        $this->templateService->update($contractTemplate, $request->validated());

        return redirect()
            ->route('admin.contract-templates.index')
            ->with('success', 'Contract template updated successfully.');
    }

    /**
     * Remove the specified contract template.
     */
    public function destroy(ContractTemplate $contractTemplate)
    {
        try {
            $this->templateService->delete($contractTemplate);

            return redirect()
                ->route('admin.contract-templates.index')
                ->with('success', 'Contract template deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
