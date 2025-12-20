<?php

namespace App\Rules;

use App\Services\LmsApiService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;

/**
 * Validates that a value exists in LMS API data.
 *
 * Since programs and intakes are managed in LMS (master system),
 * we validate against cached API data instead of local database.
 */
class ExistsInLms implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  string  $type  Either 'programs' or 'intakes'
     */
    public function __construct(
        protected string $type
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $lmsApiService = app(LmsApiService::class);

        // Get cached data from LMS
        $items = match ($this->type) {
            'programs' => collect($lmsApiService->getPrograms()),
            'intakes' => collect($lmsApiService->getIntakes()),
            default => collect(),
        };

        // Check if the value exists in the items
        $exists = $items->contains(function ($item) use ($value) {
            return (int) ($item['id'] ?? 0) === (int) $value;
        });

        if (! $exists) {
            $label = $this->type === 'programs' ? 'program' : 'intake';
            $fail("The selected {$label} is not available.");
        }
    }
}
