<?php

namespace App\Services;

use App\Models\ContractTemplate;
use Illuminate\Support\Facades\Log;

class ContractTemplateService
{
    /**
     * Create a new contract template.
     */
    public function create(array $data): ContractTemplate
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $template = ContractTemplate::create($data);

        Log::info('Contract template created', [
            'template_id' => $template->id,
            'name' => $template->name,
            'created_by' => auth()->id(),
        ]);

        return $template;
    }

    /**
     * Update an existing contract template.
     */
    public function update(ContractTemplate $template, array $data): ContractTemplate
    {
        $data['updated_by'] = auth()->id();

        $template->update($data);

        Log::info('Contract template updated', [
            'template_id' => $template->id,
            'name' => $template->name,
            'updated_by' => auth()->id(),
        ]);

        return $template->fresh();
    }

    /**
     * Delete a contract template (prevent if contracts exist).
     */
    public function delete(ContractTemplate $template): void
    {
        if ($template->studentContracts()->exists()) {
            throw new \Exception('Cannot delete template that has been used to issue contracts.');
        }

        Log::info('Contract template deleted', [
            'template_id' => $template->id,
            'name' => $template->name,
            'deleted_by' => auth()->id(),
        ]);

        $template->delete();
    }

    /**
     * Get active templates for a specific program.
     */
    public function getTemplatesForProgram(int $programId)
    {
        return ContractTemplate::active()
            ->forProgram($programId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get SIS placeholder definitions with descriptions.
     */
    public function getSisPlaceholderDefinitions(): array
    {
        return ContractTemplate::getSisPlaceholders();
    }
}
