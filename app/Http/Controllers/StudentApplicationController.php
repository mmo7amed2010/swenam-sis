<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentApplicationStepFiveRequest;
use App\Http\Requests\StudentApplicationStepFourRequest;
use App\Http\Requests\StudentApplicationStepOneRequest;
use App\Http\Requests\StudentApplicationStepThreeRequest;
use App\Http\Requests\StudentApplicationStepTwoRequest;
use App\Jobs\CreateImmediateStudentAccountJob;
use App\Models\StudentApplication;
use App\Services\ApplicationDocumentService;
use App\Services\LmsApiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class StudentApplicationController extends Controller
{
    protected ApplicationDocumentService $documentService;

    protected LmsApiService $lmsApiService;

    public function __construct(
        ApplicationDocumentService $documentService,
        LmsApiService $lmsApiService
    ) {
        $this->documentService = $documentService;
        $this->lmsApiService = $lmsApiService;
    }

    /**
     * Show Step 1 - Program Information
     *
     * Programs and intakes are fetched from LMS API (master system).
     * Cached for 5 minutes to reduce API calls.
     */
    public function showStepOne()
    {
        // Fetch programs from LMS with caching
        $programs = Cache::remember('lms_programs', 300, function () {
            return collect($this->lmsApiService->getPrograms());
        });

        // Fetch intakes from LMS with caching
        $intakes = Cache::remember('lms_intakes', 300, function () {
            return collect($this->lmsApiService->getIntakes());
        });

        $data = Session::get('application', []);

        return view('application.step1', compact('programs', 'intakes', 'data'));
    }

    /**
     * Store Step 1 and proceed to Step 2
     */
    public function storeStepOne(StudentApplicationStepOneRequest $request)
    {
        $application = Session::get('application', []);
        $validated = $request->validated();

        // If intake_id is provided, also set preferred_intake for backward compatibility
        if (! empty($validated['intake_id'])) {
            // Get intake data from cached LMS response
            $intakes = Cache::remember('lms_intakes', 300, function () {
                return collect($this->lmsApiService->getIntakes());
            });

            $intake = $intakes->firstWhere('id', (int) $validated['intake_id']);
            if ($intake) {
                $validated['preferred_intake'] = $intake['name'] ?? null;
            }
        }

        $application = array_merge($application, $validated);
        Session::put('application', $application);

        return redirect()->route('application.step2');
    }

    /**
     * Show Step 2 - Personal Information
     */
    public function showStepTwo()
    {
        if (! Session::has('application.program_id')) {
            return redirect()->route('application.step1');
        }

        $data = Session::get('application', []);

        return view('application.step2', compact('data'));
    }

    /**
     * Store Step 2 and proceed to Step 3
     */
    public function storeStepTwo(StudentApplicationStepTwoRequest $request)
    {
        $application = Session::get('application', []);
        $validated = $request->validated();

        // Remove email_confirmation as it's not stored
        unset($validated['email_confirmation']);

        $application = array_merge($application, $validated);
        Session::put('application', $application);

        return redirect()->route('application.step3');
    }

    /**
     * Show Step 3 - Education History
     */
    public function showStepThree()
    {
        if (! Session::has('application.email')) {
            return redirect()->route('application.step1');
        }

        $data = Session::get('application', []);

        return view('application.step3', compact('data'));
    }

    /**
     * Store Step 3 and proceed to Step 4
     */
    public function storeStepThree(StudentApplicationStepThreeRequest $request)
    {
        $application = Session::get('application', []);
        $application = array_merge($application, $request->validated());
        Session::put('application', $application);

        return redirect()->route('application.step4');
    }

    /**
     * Show Step 4 - Work History
     */
    public function showStepFour()
    {
        if (! Session::has('application.education_field')) {
            return redirect()->route('application.step1');
        }

        $data = Session::get('application', []);

        return view('application.step4', compact('data'));
    }

    /**
     * Store Step 4 and proceed to Step 5
     */
    public function storeStepFour(StudentApplicationStepFourRequest $request)
    {
        $application = Session::get('application', []);
        $application = array_merge($application, $request->validated());
        Session::put('application', $application);

        return redirect()->route('application.step5');
    }

    /**
     * Show Step 5 - Supporting Documents
     */
    public function showStepFive()
    {
        if (! Session::has('application.has_work_experience')) {
            return redirect()->route('application.step1');
        }

        $data = Session::get('application', []);

        return view('application.step5', compact('data'));
    }

    /**
     * Submit the complete application
     */
    public function submit(StudentApplicationStepFiveRequest $request)
    {
        $applicationData = Session::get('application', []);

        // Generate reference number
        $referenceNumber = StudentApplication::generateReferenceNumber();
        $applicationData['reference_number'] = $referenceNumber;
        $applicationData['status'] = 'pending';

        // Upload documents
        if ($request->hasFile('degree_certificate')) {
            $applicationData['degree_certificate_path'] = $this->documentService->upload(
                $request->file('degree_certificate'),
                $referenceNumber,
                'degree_certificate'
            );
        }

        if ($request->hasFile('transcripts')) {
            $applicationData['transcripts_path'] = $this->documentService->upload(
                $request->file('transcripts'),
                $referenceNumber,
                'transcripts'
            );
        }

        if ($request->hasFile('cv')) {
            $applicationData['cv_path'] = $this->documentService->upload(
                $request->file('cv'),
                $referenceNumber,
                'cv'
            );
        }

        if ($request->hasFile('english_test')) {
            $applicationData['english_test_path'] = $this->documentService->upload(
                $request->file('english_test'),
                $referenceNumber,
                'english_test'
            );
        }

        // Create application record
        $application = StudentApplication::create($applicationData);

        // Clear session
        Session::forget('application');

        // Create student account immediately so they can login and track their application
        // This also sends the account created email with login credentials
        CreateImmediateStudentAccountJob::dispatchSync($application);

        return redirect()->route('application.confirmation', $application->reference_number);
    }

    /**
     * Show confirmation page
     */
    public function confirmation($referenceNumber)
    {
        $application = StudentApplication::where('reference_number', $referenceNumber)->firstOrFail();

        return view('application.confirmation', compact('application'));
    }

    /**
     * Go back to previous step
     */
    public function back($step)
    {
        return redirect()->route("application.step{$step}");
    }
}
