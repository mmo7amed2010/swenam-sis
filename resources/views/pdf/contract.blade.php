<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Contract - {{ $application->full_name }}</title>
    <style>
        @page {
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            line-height: 1.4;
            padding: 15mm 12mm 15mm 12mm;
        }

        /* ========== HEADER ========== */
        .document-header {
            border-bottom: 3px double #1a365d;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logo {
            width: 80px;
            vertical-align: middle;
            padding-right: 12px;
        }
        .header-logo img {
            height: 80px;
            width: auto;
        }
        .header-center {
            vertical-align: top;
        }
        .institution-name {
            font-size: 13px;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 4px 0;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 4px;
        }
        .student-info-header {
            font-size: 10px;
            color: #4a5568;
        }
        .header-right {
            width: 130px;
            vertical-align: top;
            text-align: right;
            font-size: 8px;
            color: #4a5568;
            border-left: 1px solid #e2e8f0;
            padding-left: 10px;
        }
        .header-right-row {
            margin-bottom: 4px;
        }
        .header-right-label {
            font-weight: bold;
            color: #2d3748;
        }

        /* ========== SECTION TITLES ========== */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #1a365d;
            border-left: 4px solid #1a365d;
            padding: 5px 0 5px 10px;
            margin-bottom: 12px;
            margin-top: 18px;
            background-color: #f7fafc;
        }
        .section-title:first-of-type {
            margin-top: 0;
        }

        /* ========== STUDENT INFO TABLE ========== */
        .student-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .student-info-table td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        .student-info-table .info-label {
            background-color: #f7fafc;
            font-weight: bold;
            color: #2d3748;
            width: 120px;
        }

        /* ========== CONTRACT BODY ========== */
        .contract-body {
            margin: 20px 0;
            line-height: 1.8;
            font-size: 10px;
        }
        .contract-body h1 {
            font-size: 16px;
            color: #1a365d;
            margin: 15px 0 8px 0;
        }
        .contract-body h2 {
            font-size: 13px;
            color: #1a365d;
            margin: 12px 0 6px 0;
        }
        .contract-body h3 {
            font-size: 11px;
            color: #1a365d;
            margin: 10px 0 5px 0;
        }
        .contract-body p {
            margin-bottom: 8px;
        }
        .contract-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .contract-body table th,
        .contract-body table td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        .contract-body table th {
            background-color: #f7fafc;
            font-weight: bold;
            color: #2d3748;
            text-align: left;
        }
        .contract-body ul, .contract-body ol {
            margin: 8px 0 8px 20px;
        }
        .contract-body li {
            margin-bottom: 4px;
        }

        /* ========== SIGNATURE BLOCK ========== */
        .signature-block {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            padding: 8px 0;
            vertical-align: top;
        }
        .signature-table td.spacer {
            width: 10%;
        }
        .signature-line {
            border-bottom: 1px solid #2d3748;
            margin-bottom: 4px;
            height: 35px;
        }
        .signature-label {
            font-size: 9px;
            color: #4a5568;
            font-weight: bold;
        }
        .signature-sublabel {
            font-size: 8px;
            color: #718096;
        }
        .signature-date {
            margin-top: 15px;
        }

        /* ========== FOOTER ========== */
        .document-footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 2px solid #1a365d;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-left {
            font-size: 8px;
            color: #718096;
            vertical-align: bottom;
        }
        .footer-center {
            text-align: center;
            font-size: 8px;
            color: #718096;
            vertical-align: bottom;
        }
        .footer-right {
            text-align: right;
            font-size: 8px;
            color: #718096;
            vertical-align: bottom;
        }
        .footer-notice {
            font-size: 9px;
            color: #1a365d;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    {{-- Document Header --}}
    <div class="document-header">
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <img src="{{ public_path('assets/media/logos/swenam_vertical_logo.png') }}" alt="Institution Logo">
                </td>
                <td class="header-center">
                    <div class="document-title">Enrollment Contract</div>
                    <div class="student-info-header">
                        <strong>Student:</strong> {{ $application->full_name }} &nbsp;|&nbsp;
                        <strong>Ref:</strong> {{ $application->reference_number }}
                    </div>
                </td>
                <td class="header-right">
                    <div class="header-right-row">
                        <span class="header-right-label">Issue Date:</span><br>
                        {{ $generatedAt->format('M d, Y') }}
                    </div>
                    <div class="header-right-row">
                        <span class="header-right-label">Program:</span><br>
                        {{ $application->program_name ?? 'N/A' }}
                    </div>
                    <div class="header-right-row">
                        <span class="header-right-label">Document ID:</span><br>
                        CT-{{ $application->reference_number }}-{{ $generatedAt->format('ymd') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>



    {{-- Contract Body (rendered from template) --}}
    <div class="contract-body">
        {!! $renderedBody !!}
    </div>

</body>
</html>
