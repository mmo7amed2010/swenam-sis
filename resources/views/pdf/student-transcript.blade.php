<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Transcript - {{ $student->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        .header h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
            color: #1e40af;
        }
        .header h2 {
            font-size: 18px;
            margin: 0;
            color: #666;
            font-weight: normal;
        }
        .student-info {
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 5px 10px;
        }
        .student-info .label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .summary {
            margin-bottom: 25px;
            padding: 15px;
            background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
            color: white;
            border-radius: 5px;
            text-align: center;
        }
        .summary .gpa {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .summary .average {
            font-size: 18px;
            margin: 5px 0;
        }
        .semester-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .semester-header {
            background: #1e40af;
            color: white;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        table.grades {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.grades th {
            background: #e5e7eb;
            color: #374151;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #9ca3af;
        }
        table.grades td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        table.grades tr:last-child td {
            border-bottom: none;
        }
        table.grades tr:nth-child(even) {
            background: #f9fafb;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .grade-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 12px;
        }
        .grade-excellent {
            background: #10b981;
            color: white;
        }
        .grade-good {
            background: #3b82f6;
            color: white;
        }
        .grade-satisfactory {
            background: #f59e0b;
            color: white;
        }
        .grade-passing {
            background: #fb923c;
            color: white;
        }
        .grade-at-risk {
            background: #ef4444;
            color: white;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .footer .generated {
            margin-top: 10px;
        }
        .legend {
            margin-top: 20px;
            padding: 10px;
            background: #f9fafb;
            border-radius: 3px;
            font-size: 10px;
        }
        .legend h4 {
            margin: 0 0 10px 0;
            font-size: 12px;
            color: #374151;
        }
        .legend-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>ACADEMIC TRANSCRIPT</h1>
        <h2>Official Student Grade Report</h2>
    </div>

    {{-- Student Information --}}
    <div class="student-info">
        <table>
            <tr>
                <td class="label">Student Name:</td>
                <td>{{ $student->name }}</td>
                <td class="label">Student ID:</td>
                <td>{{ $student->id }}</td>
            </tr>
            <tr>
                <td class="label">Email:</td>
                <td>{{ $student->email }}</td>
                <td class="label">Generated:</td>
                <td>{{ $generatedAt->format('F d, Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- Academic Summary --}}
    <div class="summary">
        <div style="font-size: 14px; margin-bottom: 10px;">Academic Performance Summary</div>
        <div class="gpa">GPA: {{ $gpa }}</div>
        <div class="average">Overall Average: {{ number_format($overallAverage, 2) }}%</div>
        <div style="font-size: 12px; margin-top: 10px;">Based on {{ $grades->count() }} course(s)</div>
    </div>

    {{-- Grades by Semester --}}
    @if($gradesBySemester->count() > 0)
        @foreach($gradesBySemester as $semester => $semesterGrades)
            <div class="semester-section">
                <div class="semester-header">
                    {{ $semester }}
                </div>
                <table class="grades">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th class="text-center">Credits</th>
                            <th class="text-center">Points</th>
                            <th class="text-center">Grade (%)</th>
                            <th class="text-center">Items</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($semesterGrades as $grade)
                            @php
                                $statusClass = match(true) {
                                    $grade->percentage >= 90 => 'grade-excellent',
                                    $grade->percentage >= 80 => 'grade-good',
                                    $grade->percentage >= 70 => 'grade-satisfactory',
                                    $grade->percentage >= 60 => 'grade-passing',
                                    default => 'grade-at-risk'
                                };
                                $statusText = match(true) {
                                    $grade->percentage >= 90 => 'Excellent',
                                    $grade->percentage >= 80 => 'Good',
                                    $grade->percentage >= 70 => 'Satisfactory',
                                    $grade->percentage >= 60 => 'Passing',
                                    default => 'At Risk'
                                };
                            @endphp
                            <tr>
                                <td>{{ $grade->course->course_code }}</td>
                                <td>{{ $grade->course->name }}</td>
                                <td class="text-center">{{ $grade->course->credits ?? 'N/A' }}</td>
                                <td class="text-center">{{ number_format($grade->points_earned, 2) }} / {{ number_format($grade->points_total, 2) }}</td>
                                <td class="text-center"><strong>{{ number_format($grade->percentage, 2) }}%</strong></td>
                                <td class="text-center">{{ $grade->graded_items_count }}</td>
                                <td class="text-center">
                                    <span class="grade-badge {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                            </tr>
                        @endforeach
                        {{-- Semester Summary --}}
                        <tr style="background: #f3f4f6; font-weight: bold;">
                            <td colspan="2">Semester Average</td>
                            <td class="text-center">{{ $semesterGrades->sum(fn($g) => $g->course->credits ?? 0) }}</td>
                            <td class="text-center">{{ number_format($semesterGrades->sum('points_earned'), 2) }} / {{ number_format($semesterGrades->sum('points_total'), 2) }}</td>
                            <td class="text-center">{{ number_format($semesterGrades->avg('percentage'), 2) }}%</td>
                            <td class="text-center">{{ $semesterGrades->sum('graded_items_count') }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <div style="text-align: center; padding: 40px; background: #f9fafb; border-radius: 5px;">
            <p style="margin: 0; color: #666;">No grades available at this time.</p>
        </div>
    @endif

    {{-- Legend --}}
    <div class="legend">
        <h4>Grade Legend</h4>
        <div class="legend-item">
            <span class="grade-badge grade-excellent">Excellent</span> 90-100%
        </div>
        <div class="legend-item">
            <span class="grade-badge grade-good">Good</span> 80-89%
        </div>
        <div class="legend-item">
            <span class="grade-badge grade-satisfactory">Satisfactory</span> 70-79%
        </div>
        <div class="legend-item">
            <span class="grade-badge grade-passing">Passing</span> 60-69%
        </div>
        <div class="legend-item">
            <span class="grade-badge grade-at-risk">At Risk</span> Below 60%
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p><strong>This is an official academic transcript generated by the Learning Management System.</strong></p>
        <p>For verification purposes, please contact the Registrar's Office.</p>
        <p class="generated">
            Generated on: {{ $generatedAt->format('F d, Y \a\t g:i A') }}<br>
            Document ID: TRANS-{{ $student->id }}-{{ $generatedAt->format('YmdHis') }}
        </p>
    </div>
</body>
</html>
