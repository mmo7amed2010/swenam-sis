@extends('application.layout')

@section('content')
    {{-- Progress Indicator --}}
    <x-application-progress currentStep="1" />

    <form method="POST" action="{{ route('application.step1.store') }}" class="mt-10">
        @csrf

        {{-- Section Title --}}
        <div class="mb-10">
            <h2 class="text-gray-900 fw-bold fs-2 mb-2">Program Information</h2>
            <p class="text-gray-600 fs-6">Select your preferred program and intake period</p>
        </div>

        {{-- Choose Your Program --}}
        <div class="mb-8">
            <label class="form-label required fs-6 fw-semibold mb-3">
                <i class="ki-outline ki-book fs-5 text-primary me-2"></i>
                Choose Your Program
            </label>
            <select name="program_id" id="program_id" class="form-select form-select-lg @error('program_id') is-invalid @enderror" required>
                <option value="">Select a Program</option>
                @foreach($programs as $program)
                    <option value="{{ $program['id'] }}" {{ old('program_id', $data['program_id'] ?? '') == $program['id'] ? 'selected' : '' }}>
                        {{ $program['name'] }}
                    </option>
                @endforeach
            </select>
            @error('program_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Preferred Intake --}}
        <div class="mb-8">
            <label class="form-label required fs-6 fw-semibold mb-3">
                <i class="ki-outline ki-calendar fs-5 text-primary me-2"></i>
                Preferred Intake
            </label>
            <select name="intake_id" id="intake_id" class="form-select form-select-lg @error('intake_id') is-invalid @enderror" required>
                <option value="">Select an Intake</option>
                @forelse($intakes as $intake)
                    <option value="{{ $intake['id'] }}" {{ old('intake_id', $data['intake_id'] ?? '') == $intake['id'] ? 'selected' : '' }}>
                        {{ $intake['name'] }}
                    </option>
                @empty
                    <option value="" disabled>No intakes currently available</option>
                @endforelse
            </select>
            @error('intake_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        {{-- Agency Referral --}}
        @php
            $hasReferralValue = old('has_referral', $data['has_referral'] ?? null);
            $showReferral = $hasReferralValue === '1' || $hasReferralValue === 1 || $hasReferralValue === true;
        @endphp
        <div class="mb-8" x-data="{ hasReferral: {{ $showReferral ? 'true' : 'false' }} }">
            <label class="form-label required fs-6 fw-semibold mb-3">
                <i class="ki-outline ki-people fs-5 text-primary me-2"></i>
                Have you been referred by one of our approved agencies?
            </label>
            <div class="d-flex gap-6">
                <label class="form-check form-check-custom form-check-solid form-check-lg">
                    <input type="radio"
                           name="has_referral"
                           value="1"
                           class="form-check-input @error('has_referral') is-invalid @enderror"
                           x-on:change="hasReferral = true"
                           {{ $showReferral ? 'checked' : '' }}
                           required />
                    <span class="form-check-label fw-semibold text-gray-700">Yes</span>
                </label>
                <label class="form-check form-check-custom form-check-solid form-check-lg">
                    <input type="radio"
                           name="has_referral"
                           value="0"
                           class="form-check-input @error('has_referral') is-invalid @enderror"
                           x-on:change="hasReferral = false"
                           {{ $hasReferralValue === '0' || $hasReferralValue === 0 || $hasReferralValue === false ? 'checked' : '' }}
                           required />
                    <span class="form-check-label fw-semibold text-gray-700">No</span>
                </label>
            </div>
            @error('has_referral')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            {{-- Conditional Agency Name Field --}}
            <div class="mt-5" x-show="hasReferral" x-transition x-cloak>
                <label class="form-label required fs-6 fw-semibold mb-3">
                    <i class="ki-outline ki-building fs-5 text-primary me-2"></i>
                    Name of Referring Agency
                </label>
                <input type="text"
                       name="referral_agency_name"
                       class="form-control form-control-lg @error('referral_agency_name') is-invalid @enderror"
                       placeholder="Enter the agency name"
                       value="{{ old('referral_agency_name', $data['referral_agency_name'] ?? '') }}"
                       maxlength="255"
                       x-bind:required="hasReferral" />
                @error('referral_agency_name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-end mt-10 pt-8 border-top border-gray-300">
            <button type="submit" class="btn btn-primary btn-lg fw-semibold px-8">
                Next
                <i class="ki-outline ki-arrow-right fs-3 ms-2"></i>
            </button>
        </div>
    </form>
@endsection
