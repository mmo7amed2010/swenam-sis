<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Seeder;

class DepartmentsAndCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Departments
        $departments = [
            ['name' => 'Computer Science', 'code' => 'CS', 'description' => 'Computing, programming, and software development'],
            ['name' => 'Mathematics', 'code' => 'MATH', 'description' => 'Pure and applied mathematics'],
            ['name' => 'Physics', 'code' => 'PHYS', 'description' => 'Physical sciences and natural phenomena'],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'description' => 'Chemical sciences and laboratory work'],
            ['name' => 'Biology', 'code' => 'BIOL', 'description' => 'Life sciences and biological systems'],
            ['name' => 'Engineering', 'code' => 'ENG', 'description' => 'Engineering disciplines and applied sciences'],
            ['name' => 'Business Administration', 'code' => 'BA', 'description' => 'Business management and administration'],
            ['name' => 'Economics', 'code' => 'ECON', 'description' => 'Economic theory and practice'],
            ['name' => 'Psychology', 'code' => 'PSY', 'description' => 'Human behavior and mental processes'],
            ['name' => 'Sociology', 'code' => 'SOC', 'description' => 'Social structures and human societies'],
            ['name' => 'History', 'code' => 'HIST', 'description' => 'Historical events and civilizations'],
            ['name' => 'Literature', 'code' => 'LIT', 'description' => 'Literary works and analysis'],
            ['name' => 'Philosophy', 'code' => 'PHIL', 'description' => 'Philosophical thought and ethics'],
            ['name' => 'Art & Design', 'code' => 'ART', 'description' => 'Visual arts and creative design'],
            ['name' => 'Music', 'code' => 'MUS', 'description' => 'Musical theory and performance'],
        ];

        foreach ($departments as $dept) {
            Department::create([
                'name' => $dept['name'],
                'code' => $dept['code'],
                'description' => $dept['description'],
                'is_active' => true,
            ]);
        }

        // Seed Programs
        $programs = [
            ['name' => 'Undergraduate', 'description' => 'Bachelor degree programs'],
            ['name' => 'Graduate', 'description' => 'Master and doctoral degree programs'],
            ['name' => 'Certificate', 'description' => 'Professional certification programs'],
            ['name' => 'Diploma', 'description' => 'Diploma and associate degree programs'],
            ['name' => 'Professional Development', 'description' => 'Continuing education and professional training'],
            ['name' => 'Online Program', 'description' => 'Fully online degree and certificate programs'],
            ['name' => 'Hybrid Program', 'description' => 'Blended online and on-campus programs'],
            ['name' => 'Executive Education', 'description' => 'Executive MBA and leadership programs'],
        ];

        foreach ($programs as $prog) {
            Program::create([
                'name' => $prog['name'],
                'description' => $prog['description'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Departments and Programs seeded successfully!');
    }
}
