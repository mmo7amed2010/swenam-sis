<?php

namespace App\Services;

/**
 * Grading Scale Service
 *
 * Provides centralized grading calculation logic for the LMS.
 * Eliminates duplicate grading logic across models and ensures
 * consistent grade calculation throughout the system.
 *
 * Features:
 * - Letter grade calculation from percentage scores
 * - Grade point conversion (4.0 scale)
 * - Configurable grading thresholds via config/grading.php
 * - Environment-based threshold overrides
 */
class GradingScaleService
{
    /**
     * Grading scale configuration
     *
     * @var array<string, float>
     */
    protected array $scale;

    /**
     * Grade points configuration (4.0 scale)
     *
     * @var array<string, float>
     */
    protected array $gradePoints;

    /**
     * Initialize service with configuration
     */
    public function __construct()
    {
        $this->scale = config('grading.scale', $this->getDefaultScale());
        $this->gradePoints = config('grading.grade_points', $this->getDefaultGradePoints());
    }

    /**
     * Calculate letter grade from percentage score
     *
     * Evaluates the percentage against configured thresholds to determine
     * the appropriate letter grade. The scale is evaluated in descending
     * order, so the first matching threshold is returned.
     *
     * @param  float  $percentage  Score percentage (0-100)
     * @return string Letter grade (A, A-, B+, B, B-, C+, C, C-, D+, D, D-, F)
     *
     * @example
     * $service->calculateLetterGrade(95.5)  // Returns: "A"
     * $service->calculateLetterGrade(87.0)  // Returns: "B+"
     * $service->calculateLetterGrade(59.9)  // Returns: "F"
     */
    public function calculateLetterGrade(float $percentage): string
    {
        foreach ($this->scale as $letter => $threshold) {
            if ($percentage >= $threshold) {
                return $letter;
            }
        }

        return 'F';
    }

    /**
     * Get grade point value for a letter grade (4.0 scale)
     *
     * Converts a letter grade to its corresponding grade point value
     * for GPA calculation purposes.
     *
     * @param  string  $letterGrade  Letter grade (A, A-, B+, etc.)
     * @return float Grade point value (0.0 - 4.0)
     *
     * @example
     * $service->getGradePoint('A')   // Returns: 4.0
     * $service->getGradePoint('B+')  // Returns: 3.3
     * $service->getGradePoint('F')   // Returns: 0.0
     */
    public function getGradePoint(string $letterGrade): float
    {
        return $this->gradePoints[$letterGrade] ?? 0.0;
    }

    /**
     * Get the complete grading scale configuration
     *
     * Returns the current grading scale thresholds as configured
     * in config/grading.php or environment variables.
     *
     * @return array<string, float> Array mapping letter grades to minimum percentages
     *
     * @example
     * $service->getScale()  // Returns: ['A' => 93, 'A-' => 90, ...]
     */
    public function getScale(): array
    {
        return $this->scale;
    }

    /**
     * Get the complete grade points configuration
     *
     * Returns the grade point values for all letter grades.
     *
     * @return array<string, float> Array mapping letter grades to grade points
     */
    public function getGradePoints(): array
    {
        return $this->gradePoints;
    }

    /**
     * Check if a letter grade is passing
     *
     * Compares the given letter grade against the configured passing threshold.
     *
     * @param  string  $letterGrade  Letter grade to check
     * @return bool True if the grade is passing, false otherwise
     */
    public function isPassing(string $letterGrade): bool
    {
        $passingGrade = config('grading.passing_grade', 'D-');
        $gradePoint = $this->getGradePoint($letterGrade);
        $passingPoint = $this->getGradePoint($passingGrade);

        return $gradePoint >= $passingPoint;
    }

    /**
     * Calculate GPA from an array of letter grades
     *
     * Computes the grade point average from a collection of letter grades.
     * All grades are weighted equally.
     *
     * @param  array<string>  $letterGrades  Array of letter grades
     * @return float Grade point average (0.0 - 4.0)
     *
     * @example
     * $service->calculateGPA(['A', 'B+', 'B', 'A-'])  // Returns: 3.5
     */
    public function calculateGPA(array $letterGrades): float
    {
        if (empty($letterGrades)) {
            return 0.0;
        }

        $totalPoints = 0.0;
        $count = 0;

        foreach ($letterGrades as $grade) {
            $totalPoints += $this->getGradePoint($grade);
            $count++;
        }

        return $count > 0 ? round($totalPoints / $count, 2) : 0.0;
    }

    /**
     * Get default grading scale (fallback if config is missing)
     *
     * @return array<string, float>
     */
    protected function getDefaultScale(): array
    {
        return [
            'A' => 93,
            'A-' => 90,
            'B+' => 87,
            'B' => 83,
            'B-' => 80,
            'C+' => 77,
            'C' => 73,
            'C-' => 70,
            'D+' => 67,
            'D' => 63,
            'D-' => 60,
            'F' => 0,
        ];
    }

    /**
     * Get default grade points (fallback if config is missing)
     *
     * @return array<string, float>
     */
    protected function getDefaultGradePoints(): array
    {
        return [
            'A' => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C' => 2.0,
            'C-' => 1.7,
            'D+' => 1.3,
            'D' => 1.0,
            'D-' => 0.7,
            'F' => 0.0,
        ];
    }
}
