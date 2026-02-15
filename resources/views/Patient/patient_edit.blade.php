@extends('Layout.master')
@section('title', 'Edit Patient')

@section('content')
{{-- Dual option styles removed (UI no longer supports dual option fields) --}}

    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('patients.list') }}">Patients</a></li>
                            <li class="breadcrumb-item active">Edit Patient</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Patient - {{ $patient->name }}</h4>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Saved Reports functionality removed -->

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm" style="border-radius: 12px; border: 1px solid rgba(37, 99, 235, 0.1);">
                    <div class="card-body" style="padding: 2rem;">
                        <h4 class="header-title mb-4" style="color: var(--text-heading); font-weight: 600;">
                            <i class="fas fa-user-edit text-primary-custom mr-2"></i> Patient Information
                        </h4>

                        <form method="POST" action="{{ route('patients.update', $patient->id) }}" id="patientEditForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="name" class="font-weight-bold">
                                            <i class="fas fa-user text-primary-custom mr-1"></i> Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="name" name="name" placeholder="Enter full name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $patient->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="mobile_phone" class="font-weight-bold">
                                            <i class="fas fa-phone text-primary-custom mr-1"></i> Mobile Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="mobile_phone" name="mobile_phone" placeholder="Enter mobile number"
                                            class="form-control @error('mobile_phone') is-invalid @enderror"
                                            value="{{ old('mobile_phone', $patient->mobile_phone) }}" required>
                                        @error('mobile_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="gender" class="font-weight-bold">
                                            <i class="fas fa-venus-mars text-primary-custom mr-1"></i> Gender <span class="text-danger">*</span>
                                        </label>
                                        <select id="gender" name="gender"
                                            class="form-control @error('gender') is-invalid @enderror" required>
                                            <option value="">Choose One Option</option>
                                            <option value="Male"
                                                {{ old('gender', $patient->gender) === 'Male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="Female"
                                                {{ old('gender', $patient->gender) === 'Female' ? 'selected' : '' }}>Female
                                            </option>
                                            <option value="Other"
                                                {{ old('gender', $patient->gender) === 'Other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                        @error('gender')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    @php
                                        // Prefer explicit DB-backed age parts (if columns exist) otherwise parse stored age string
                                        $ageYears = old('age_years', $patient->age_years ?? null);
                                        $ageMonths = old('age_months', $patient->age_months ?? null);
                                        $ageDays = old('age_days', $patient->age_days ?? null);
                                        if (empty($ageYears) && empty($ageMonths) && empty($ageDays)) {
                                            $ageString = old('age', $patient->age ?? '');
                                            $ageYears = $ageMonths = $ageDays = '';
                                            if (preg_match('/(\d+)\s*Y/i', $ageString, $m)) { $ageYears = $m[1]; }
                                            if (preg_match('/(\d+)\s*M/i', $ageString, $m)) { $ageMonths = $m[1]; }
                                            if (preg_match('/(\d+)\s*D/i', $ageString, $m)) { $ageDays = $m[1]; }
                                            // Fallback: if age is plain numeric assume years
                                            if (empty($ageYears) && empty($ageMonths) && empty($ageDays) && is_numeric(trim($ageString))) {
                                                $ageYears = trim($ageString);
                                            }
                                        }
                                    @endphp
                                    <div class="form-group mb-4">
                                        <label for="age_years" class="font-weight-bold">
                                            <i class="fas fa-birthday-cake text-primary-custom mr-1"></i> Age <span class="text-danger">*</span>
                                        </label>
                                        <div class="row">
                                            <div class="col-4">
                                                <input type="number" id="age_years" name="age_years"
                                                    class="form-control @error('age_years') is-invalid @enderror"
                                                    value="{{ old('age_years', $ageYears) }}" placeholder="Years"
                                                    min="0" max="150">
                                                <small class="text-muted">Years</small>
                                                @error('age_years')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-4">
                                                <input type="number" id="age_months" name="age_months"
                                                    class="form-control @error('age_months') is-invalid @enderror"
                                                    value="{{ old('age_months', $ageMonths) }}" placeholder="Months"
                                                    min="0" max="11">
                                                <small class="text-muted">Months</small>
                                                @error('age_months')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-4">
                                                <input type="number" id="age_days" name="age_days"
                                                    class="form-control @error('age_days') is-invalid @enderror"
                                                    value="{{ old('age_days', $ageDays) }}" placeholder="Days"
                                                    min="0" max="30">
                                                <small class="text-muted">Days</small>
                                                @error('age_days')
                                                    <span class="invalid-feedback">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <input type="hidden" id="age" name="age" value="{{ old('age', $patient->age) }}">
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="blood_group" class="font-weight-bold">
                                            <i class="fas fa-tint text-primary-custom mr-1"></i> Blood Group
                                        </label>
                                        <select id="blood_group" name="blood_group" class="form-control">
                                            <option value="">Choose One Option</option>
                                            @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $group)
                                                <option value="{{ $group }}"
                                                    {{ old('blood_group', $patient->blood_group) === $group ? 'selected' : '' }}>
                                                    {{ $group }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="address" class="font-weight-bold">
                                            <i class="fas fa-map-marker-alt text-primary-custom mr-1"></i> Address <span class="text-danger">*</span>
                                        </label>
                                        <textarea id="address" name="address" rows="3" placeholder="Enter address" class="form-control @error('address') is-invalid @enderror"
                                            required>{{ old('address', $patient->address) }}</textarea>
                                        @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="receiving_date" class="font-weight-bold">
                                            <i class="fas fa-calendar-check text-primary-custom mr-1"></i> Receiving Date & Time <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local" id="receiving_date" name="receiving_date"
                                            class="form-control @error('receiving_date') is-invalid @enderror"
                                            value="{{ old('receiving_date', $patient->receiving_datetime?->format('Y-m-d\TH:i') ?: $patient->receiving_date?->format('Y-m-d\TH:i')) }}"
                                            required>
                                        @error('receiving_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="reporting_date" class="font-weight-bold">
                                            <i class="fas fa-calendar-alt text-primary-custom mr-1"></i> Reporting Date & Time <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local" id="reporting_date" name="reporting_date"
                                            class="form-control @error('reporting_date') is-invalid @enderror"
                                            value="{{ old('reporting_date', $patient->reporting_datetime?->format('Y-m-d\TH:i') ?: $patient->reporting_date?->format('Y-m-d\TH:i')) }}"
                                            required>
                                        @error('reporting_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="referred_by" class="font-weight-bold">
                                            <i class="fas fa-user-md text-primary-custom mr-1"></i> Referred By
                                        </label>
                                        <select id="referred_by" name="referred_by" class="form-control select2-referral">
                                            <option value="">Select Referral</option>
                                            @foreach($referrals as $referral)
                                                <option value="{{ $referral->name }}" {{ old('referred_by', $patient->referred_by) == $referral->name ? 'selected' : '' }}>
                                                    {{ $referral->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="note" class="font-weight-bold">
                                            <i class="fas fa-sticky-note text-primary-custom mr-1"></i> Note
                                        </label>
                                        <textarea id="note" name="note" rows="3" placeholder="Enter any additional notes" class="form-control">{{ old('note', $patient->note) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <a href="{{ route('patients.list') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Patient</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Test Data Edit Section --}}
        @php
            // Debug: Show if variables exist
            // dd([
            //     'selectedTests' => $selectedTests ?? [],
            //     'testsWithData' => $testsWithData ?? [],
            //     'patient_test_category' => $patient->test_category ?? 'null'
            // ]);
        @endphp
        @if (!empty($testsWithData))
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm" style="border-radius: 12px; border: 1px solid rgba(37, 99, 235, 0.1);">
                        <div class="card-body" style="padding: 2rem;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <h4 class="header-title mb-4" style="color: var(--text-heading); font-weight: 600;">
                                <i class="fas fa-flask text-primary-custom mr-2"></i> Edit Test Data
                            </h4>
                                <div>
                                    <button id="btn-print-selected" class="btn btn-outline-primary btn-sm">Print Selected</button>
                                    <button id="btn-print-selected-with-header" class="btn btn-success btn-sm ml-2">Print Selected with Header</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0" style="border-radius: 8px; overflow: hidden;">
                                    <thead class="thead-light" style="background: linear-gradient(135deg, rgba(248, 250, 252, 0.9), rgba(255, 255, 255, 0.9));">
                                        <tr>
                                            <th>Test Name</th>
                                            <th width="80">Select</th>
                                            <th>Status</th>
                                            <th>Last Updated</th>
                                            <th>Preview</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($testsWithData as $test)
                                            @php
                                                $testSlug = \Str::slug($test['name']);
                                                $hasData = $test['has_data'];
                                                $savedData = $test['saved_data'];
                                                $template = $test['template'];
                                                $hasTemplate = $test['has_template'] ?? false;
                                                $isMllpData = $test['is_mllp_data'] ?? false;
                                            @endphp

                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-{{ $isMllpData ? 'microchip' : 'vial' }} text-primary-custom mr-2"
                                                            style="font-size: 16px;"></i>
                                                        <div>
                                                            <strong>{{ $test['name'] }}</strong>
                                                            @if ($isMllpData)
                                                                <br><small class="badge badge-info"><i
                                                                        class="fas fa-microchip"></i> From Analyzer</small>
                                                            @elseif(!$hasTemplate)
                                                                <br><small class="badge badge-info">Generic
                                                                    Template</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="test-select" value="{{ $test['name'] }}" {{ !$hasData ? 'disabled' : '' }}>
                                                </td>
                                                <td>
                                                    @if ($hasData)
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check-circle"></i> Completed
                                                        </span>
                                                    @else
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-clock"></i> Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($hasData)
                                                        <small class="text-muted">
                                                            {{ $savedData['test_date'] ?? ($savedData['reported_at'] ?? 'Unknown') }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted text-danger">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($hasData)
                                                        <div class="d-flex align-items-center">
                                                            <small class="text-muted mr-2">
                                                                @php
                                                                    $preview = '';
                                                                    foreach ($template['fields'] as $field) {
                                                                        $value = $savedData[$field['name']] ?? '';
                                                                        if (!empty($value)) {
                                                                            $preview = substr($value, 0, 40);
                                                                            break;
                                                                        }
                                                                    }
                                                                @endphp
                                                                <!-- {{ $preview ?? '-' }}{{ strlen($preview ?? '') > 40 ? '...' : '' }} -->
                                                            </small>
                                                            <a href="#" onclick="printTest('/patients/{{ $patient->id }}/tests/' + encodeURIComponent({{ json_encode($test['name']) }}) + '/print')"
                                                                class="btn btn-sm btn-outline-secondary"
                                                                title="Print Test Report">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                            <a href="#" onclick="printTest('/patients/{{ $patient->id }}/tests/' + encodeURIComponent({{ json_encode($test['name']) }}) + '/print-with-header')"
                                                                class="btn btn-sm btn-success"
                                                                title="Print with Header">
                                                                <i class="fas fa-print"></i> <i class="fas fa-header"></i>
                                                            </a>
                                                            <!-- Save to System removed -->
                                                        </div>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-toggle="modal" data-target="#testModal_{{ $testSlug }}"
                                                        title="{{ $hasData ? 'Edit Test Data' : 'Add Test Data' }}">
                                                        <i class="fas fa-{{ $hasData ? 'edit' : 'plus' }}"></i>
                                                    </button>
                                                    <!-- PDF button moved to the Preview column -->
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(empty($selectedTests))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle"></i> <strong>No tests registered</strong> - This patient has no tests
                        registered. Please register tests during patient creation to edit test data.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Test Results Summary Section --}}
        @php
            $allReports = json_decode($patient->test_report ?? '[]', true) ?? [];
            // Filter out CBC reports (they have their own section)
            $testResults = array_filter($allReports, function ($report) {
                return !isset($report['test']) || $report['test'] !== 'CBC';
            });
        @endphp

        @if (!empty($testResults))
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm" style="border-radius: 12px; border: 1px solid rgba(37, 99, 235, 0.1);">
                        <div class="card-body" style="padding: 2rem;">
                            <h4 class="header-title mb-4" style="color: var(--text-heading); font-weight: 600;">
                                <i class="fas fa-file-alt text-primary-custom mr-2"></i> Test Results Summary
                            </h4>

                            <div class="accordion" id="testResultsAccordion" style="border-radius: 8px; overflow: hidden;">
                                @foreach ($testResults as $testName => $testData)
                                    @php
                                        $testSlug = \Str::slug($testName);
                                        $collapseId = 'testResult_' . $testSlug;
                                        $headingId = 'heading_' . $testSlug;
                                    @endphp

                                    <div class="card border-0 mb-2" style="border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <div class="card-header p-0 bg-light" id="{{ $headingId }}" style="border-radius: 8px 8px 0 0; background: linear-gradient(135deg, rgba(248, 250, 252, 0.9), rgba(255, 255, 255, 0.9));">
                                            <button class="btn btn-link btn-block text-left p-3" type="button"
                                                data-toggle="collapse" data-target="#{{ $collapseId }}"
                                                aria-expanded="false" aria-controls="{{ $collapseId }}" style="color: var(--text-heading); font-weight: 500;">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <div>
                                                        <h6 class="mb-0">
                                                            <i class="fas fa-chevron-right collapse-icon text-primary-custom"
                                                                style="transition: transform 0.3s;"></i>
                                                            <strong>{{ $testName }}</strong>
                                                        </h6>
                                                        <small class="text-muted">
                                                            Recorded: {{ $testData['test_date'] ?? 'Unknown date' }}
                                                        </small>
                                                    </div>
                                                    <span class="badge badge-success" style="background: linear-gradient(135deg, #28a745, #20c997); border: none;">
                                                        <i class="fas fa-check-circle"></i> Completed
                                                    </span>
                                                </div>
                                            </button>
                                        </div>

                                        <div id="{{ $collapseId }}" class="collapse"
                                            aria-labelledby="{{ $headingId }}" data-parent="#testResultsAccordion">
                                            <div class="card-body">
                                                {{-- Try to find the template to display nicely --}}
                                                @php
                                                    // Prefer template provided in $testsWithData (built from DB parameters)
                                                    $template = null;
                                                    if (!empty($testsWithData)) {
                                                        foreach ($testsWithData as $t) {
                                                            if (isset($t['name']) && $t['name'] === $testName) {
                                                                $template = $t['template'] ?? null;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    // No fallback to pre-defined config templates — use DB-built templates only
                                                @endphp

                                                @if ($template)
                                                    {{-- Display with template fields --}}
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Field</th>
                                                                    <th>Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($template['fields'] as $field)
                                                                    @php
                                                                        $value = $testData[$field['name']] ?? '';
                                                                        $fieldType = $field['type'] ?? 'text';
                                                                    @endphp
                                                                    <tr>
                                                                        <td><strong>{{ $field['label'] }}</strong></td>
                                                                        <td>
                                                                            @if (!empty($value))
                                                                                <pre class="mb-0" style="background: var(--surface); padding: 8px; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word;">{{ $value }}</pre>
                                                                            @else
                                                                                <span class="text-muted">-</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr class="bg-surface">
                                                                    <td><strong>Test Date</strong></td>
                                                                    <td>{{ $testData['test_date'] ?? 'Unknown' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    {{-- Display generic test data --}}
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Field</th>
                                                                    <th>Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($testData as $key => $value)
                                                                    @if ($key !== 'test_date')
                                                                        <tr>
                                                                            <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong>
                                                                            </td>
                                                                            <td>
                                                                                @if (!empty($value))
                                                                                    <pre class="mb-0"
                                                                                        style="background: var(--surface); padding: 8px; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word;">{{ $value }}</pre>
                                                                                @else
                                                                                    <span class="text-muted">-</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                                <tr class="bg-surface">
                                                                    <td><strong>Test Date</strong></td>
                                                                    <td>{{ $testData['test_date'] ?? 'Unknown' }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif

                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        data-toggle="modal" data-target="#testModal_{{ $testSlug }}">
                                                        <i class="fas fa-edit"></i> Edit Test Data
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm" style="border-radius: 12px; border: 1px solid rgba(37, 99, 235, 0.1);">
                    <div class="card-body" style="padding: 2rem;">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="header-title mb-0" style="color: var(--text-heading); font-weight: 600;">
                                <i class="fas fa-chart-bar text-primary-custom mr-2"></i> CBC Test Results
                            </h4>
                            <button type="button" class="btn btn-primary-custom" id="fetchCBCResults" style="border-radius: 8px; padding: 0.5rem 1rem; transition: all 0.3s ease;">
                                <i class="fas fa-sync-alt"></i> Fetch Latest CBC Results
                            </button>
                        </div>

                        <div id="cbcResultsContainer">
                            @php
                                $allReports = json_decode($patient->test_report ?? '[]', true) ?? [];
                                // Filter only CBC reports
                                $cbcReports = array_filter($allReports, function ($report) {
                                    return isset($report['test']) && $report['test'] === 'CBC';
                                });
                            @endphp

                            @forelse($cbcReports as $report)
                                @php
                                    $analytes = $report['analytes'] ?? [];
                                @endphp

                                <div class="card mb-3">
                                    <div class="card-header bg-surface">
                                        <strong>{{ $report['instrument'] ?? 'CBC' }}</strong>
                                        <span class="float-right text-muted">
                                            {{ $report['reported_at'] ?? 'Unknown date' }}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Parameter</th>
                                                        <th>Value</th>
                                                        <th>Units</th>
                                                        <th>Reference Range</th>
                                                        <th>Flag</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($analytes as $analyte)
                                                        <tr>
                                                            <td><strong>{{ $analyte['name'] }}</strong></td>
                                                            <td>{{ $analyte['value'] }}</td>
                                                            <td>{{ $analyte['units'] ?? '-' }}</td>
                                                            <td>{{ $analyte['ref_range'] ?? '-' }}</td>
                                                            <td>
                                                                @if (isset($analyte['flags']) && $analyte['flags'] !== 'N')
                                                                    <span
                                                                        class="badge badge-warning">{{ $analyte['flags'] }}</span>
                                                                @else
                                                                    <span class="badge badge-success">Normal</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No CBC results found for this patient.
                                    Click "Fetch Latest CBC Results" to check for new data.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if (!empty($testsWithData))
        @foreach ($testsWithData as $test)
            @php
                $testSlug = \Str::slug($test['name']);
                $template = $test['template'];
                $saved = $test['saved_data'];
            @endphp
            @if (!empty($template) && !empty($template['fields']))
                <div class="modal fade" id="testModal_{{ $testSlug }}" tabindex="-1" role="dialog"
                    aria-labelledby="testModalLabel_{{ $testSlug }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="testModalLabel_{{ $testSlug }}">
                                    <i class="fas fa-flask"></i> {{ $test['name'] }} - Test Results
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form class="test-data-form" data-test-name="{{ $test['name'] }}"
                                data-patient-id="{{ $patient->id }}">
                                <div class="modal-body">
                                    @foreach ($template['fields'] as $field)
                                        <div class="form-group">
                                            <label for="{{ $testSlug }}_{{ $field['name'] }}">
                                                {{ $field['label'] }}
                                                @if ($field['required'] ?? false)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>

                                            @switch($field['type'] ?? 'text')
                                                @case('textarea')
                                                    <textarea id="{{ $testSlug }}_{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-control"
                                                        rows="4" {{ $field['required'] ?? false ? 'required' : '' }}>{{ $saved[$field['name']] ?? '' }}</textarea>
                                                @break

                                                @case('number')
                                                    <input type="number" id="{{ $testSlug }}_{{ $field['name'] }}"
                                                        name="{{ $field['name'] }}" class="form-control"
                                                        value="{{ $saved[$field['name']] ?? '' }}"
                                                        step="{{ $field['step'] ?? '1' }}"
                                                        {{ $field['required'] ?? false ? 'required' : '' }}>
                                                @break

                                                {{-- Dual option field type removed - fallback to text input --}}

                                                @default
                                                    <input type="text" id="{{ $testSlug }}_{{ $field['name'] }}"
                                                        name="{{ $field['name'] }}" class="form-control"
                                                        value="{{ $saved[$field['name']] ?? '' }}"
                                                        {{ $field['required'] ?? false ? 'required' : '' }}>
                                            @endswitch
                                        </div>
                                    @endforeach

                                    <div class="form-group">
                                        <label for="{{ $testSlug }}_test_date">Test Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" id="{{ $testSlug }}_test_date" name="test_date"
                                            class="form-control" value="{{ $saved['test_date'] ?? now()->format('Y-m-d') }}"
                                            required>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Test Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

@endsection

@section('scripts')
   <script>
    function printTest(url) {
        console.log('Print request:', url);

        // Create a temporary iframe to handle the print
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        iframe.style.visibility = 'hidden';
        document.body.appendChild(iframe);
        
        // Set up the iframe to load the URL
        iframe.src = url;
        
        // Handle the print after iframe loads
        iframe.onload = function() {
            // Add a small delay to ensure content is loaded
            setTimeout(() => {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch (err) {
                    console.error('Print failed', err);
                }
                
                // Remove iframe after a delay
                setTimeout(() => {
                    document.body.removeChild(iframe);
                }, 2000);
            }, 1000);
        };
        
        // Handle potential errors in loading the iframe
        iframe.onerror = function() {
            console.error('Failed to load print content');
            document.body.removeChild(iframe);
        };
    }

    $(document).ready(function () {
        // Print Selected button handler
        $('#btn-print-selected').on('click', function (e) {
            e.preventDefault();
            const checked = $('.test-select:checked');
            if (!checked || checked.length === 0) {
                alert('Please select at least one test to print.');
                return;
            }
            const names = []; checked.each(function () { names.push($(this).val()); });
            const urlBase = '/patients/{{ $patient->id }}/tests/print-multiple';
            const target = urlBase + '/' + names.join('_');
            printTest(target);
        });

        // Print Selected with Header button handler
        $('#btn-print-selected-with-header').on('click', function (e) {
            e.preventDefault();
            const checked = $('.test-select:checked');
            if (!checked || checked.length === 0) {
                alert('Please select at least one test to print.');
                return;
            }
            const names = []; checked.each(function () { names.push($(this).val()); });
            const urlBase = '/patients/{{ $patient->id }}/tests/print-multiple-with-header';
            const target = urlBase + '/' + names.join('_');
            printTest(target);
        });

        /* -------------------------------
           AGE COMBINE LOGIC
        --------------------------------*/
        function combineAgeFieldsEdit() {
            const years = $('#age_years').val() || '0';
            const months = $('#age_months').val() || '0';
            const days = $('#age_days').val() || '0';

            const parts = [];
            if (years !== '0') parts.push(years + 'Y');
            if (months !== '0') parts.push(months + 'M');
            if (days !== '0') parts.push(days + 'D');

            const ageString = parts.length ? parts.join(' ') : '0Y';
            $('#age').val(ageString);
        }

        $('#age_years, #age_months, #age_days').on('input', combineAgeFieldsEdit);
        $('#patientEditForm').on('submit', function () {
            combineAgeFieldsEdit();
        });
        combineAgeFieldsEdit();


        /* -------------------------------
           CBC Reload Button - FIXED
        --------------------------------*/
        $('#fetchCBCResults').on('click', function () {
            const btn = $(this);
            const originalText = btn.html();

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Fetching...');

            // Reload the page to refresh CBC data
            location.reload();
        });


        /* -------------------------------
           SUBMIT TEST DATA (IMPORTANT)
        --------------------------------*/
        $('.test-data-form').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalBtnHtml = submitBtn.html();

            const testName = form.data('test-name');
            const patientId = form.data('patient-id');
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            const formData = new FormData();
            formData.append('patient_id', patientId);
            formData.append('test_name', testName);
            formData.append('_token', csrfToken);

            form.find('input, textarea, select').each(function () {
                const field = $(this);
                const name = field.attr('name');
                if (name) {
                    formData.append(`test_data[${name}]`, field.val());
                }
            });

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving…');

            $.ajax({
                url: "{{ route('patients.storeTestData') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            })
                .done(function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved',
                            text: response.message ?? 'Test data saved.',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        // Close modal and refresh the page to update the test data display
                        form.closest('.modal').modal('hide');
                        location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message ?? 'Failed to save test data.'
                        });
                    }
                })
                .fail(function (xhr) {
                    let message = 'Failed to save test data.';
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join(' ');
                    } else if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                })
                .always(function () {
                    submitBtn.prop('disabled', false).html(originalBtnHtml);
                });
        });

        // Initialize Select2 for referral dropdown
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-referral').select2({
                placeholder: 'Search and select a referral',
                allowClear: true,
                width: '100%',
                theme: 'default',
                language: {
                    noResults: function() {
                        return "No referral found";
                    },
                    searching: function() {
                        return "Searching...";
                    }
                }
            });

            $('.select2-referral').on('select2:open', function() {
                $('.select2-search__field').attr('placeholder', 'Type to search...');
            });
        }
    });
</script>

@endsection
