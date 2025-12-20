<?php

namespace Database\Factories;

use App\Models\Intake;
use App\Models\Program;
use App\Models\StudentApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentApplication>
 */
class StudentApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StudentApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference_number' => 'APP-'.date('Ymd').'-'.strtoupper($this->faker->bothify('??##')),
            'status' => 'pending',

            // Program Information
            'program_id' => Program::factory(),
            'intake_id' => Intake::factory(),
            'preferred_intake' => $this->faker->randomElement(['January 2025', 'May 2025', 'September 2025']),
            'has_referral' => $this->faker->boolean(30), // 30% chance of having referral
            'referral_agency_name' => fn (array $attributes) => $attributes['has_referral'] ? $this->faker->company() : null,

            // Personal Information
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'country_of_citizenship' => $this->faker->country(),
            'residency_status' => $this->faker->randomElement(['Citizen', 'Permanent Resident', 'International Student']),
            'primary_language' => $this->faker->randomElement(['English', 'French', 'Spanish', 'Mandarin']),
            'address_line1' => $this->faker->streetAddress(),
            'address_line2' => $this->faker->optional()->secondaryAddress(),
            'city' => $this->faker->city(),
            'state_province' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),

            // Education History
            'highest_education_level' => $this->faker->randomElement(['High School', 'Associate Degree', "Bachelor's Degree", "Master's Degree", 'Doctorate']),
            'education_field' => $this->faker->randomElement(['Computer Science', 'Business', 'Engineering', 'Arts', 'Science']),
            'institution_name' => $this->faker->company().' University',
            'education_completed' => $this->faker->randomElement(['yes', 'no', 'still_studying']),
            'education_country' => $this->faker->country(),
            'has_disciplinary_action' => $this->faker->boolean(10), // 10% chance of true

            // Work History
            'has_work_experience' => $this->faker->boolean(70), // 70% chance of having work experience
            'position_level' => $this->faker->randomElement(['Entry-Level', 'Mid-Level', 'Senior-Level', 'Management']),
            'position_title' => $this->faker->jobTitle(),
            'organization_name' => $this->faker->company(),
            'work_start_date' => $this->faker->date('Y-m-d', '-5 years'),
            'work_end_date' => $this->faker->optional()->date('Y-m-d', '-1 year'),
            'years_of_experience' => $this->faker->numberBetween(0, 20),

            // Supporting Documents (optional paths)
            'degree_certificate_path' => null,
            'transcripts_path' => null,
            'cv_path' => null,
            'english_test_path' => null,

            // Review/Approval
            'reviewed_by' => null,
            'reviewed_at' => null,
            'rejection_reason' => null,
            'admin_notes' => null,
            'created_user_id' => null,
        ];
    }

    /**
     * Indicate that the application has been approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Indicate that the application has been rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewed_at' => now(),
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the application has document paths.
     */
    public function withDocuments(): static
    {
        return $this->state(function (array $attributes) {
            $reference = $attributes['reference_number'];

            return [
                'degree_certificate_path' => "applications/{$reference}/degree_certificate.pdf",
                'transcripts_path' => "applications/{$reference}/transcripts.pdf",
                'cv_path' => "applications/{$reference}/cv.pdf",
                'english_test_path' => "applications/{$reference}/english_test.pdf",
            ];
        });
    }
}
