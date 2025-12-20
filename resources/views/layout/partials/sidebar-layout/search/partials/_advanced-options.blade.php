<!--begin::Preferences-->
<form data-kt-search-element="advanced-options-form" class="pt-1 d-none">
	<!--begin::Heading-->
	<h3 class="fw-semibold text-gray-900 mb-7">{{ __('Advanced Search') }}</h3>
	<!--end::Heading-->
	<!--begin::Input group-->
	<div class="mb-5">
		<input type="text" class="form-control form-control-sm form-control-solid" placeholder="{{ __('Contains the word') }}" name="query" />
	</div>
	<!--end::Input group-->
	<!--begin::Input group-->
	<div class="mb-5">
		<!--begin::Radio group-->
		<div class="nav-group nav-group-fluid">
			<!--begin::Option-->
			<label>
				<input type="radio" class="btn-check" name="type" value="has" checked="checked" />
				<span class="btn btn-sm btn-color-muted btn-active btn-active-primary">{{ __('All') }}</span>
			</label>
			<!--end::Option-->
			<!--begin::Option-->
			<label>
				<input type="radio" class="btn-check" name="type" value="users" />
				<span class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">{{ __('Users') }}</span>
			</label>
			<!--end::Option-->
			<!--begin::Option-->
			<label>
				<input type="radio" class="btn-check" name="type" value="orders" />
				<span class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">{{ __('Orders') }}</span>
			</label>
			<!--end::Option-->
			<!--begin::Option-->
			<label>
				<input type="radio" class="btn-check" name="type" value="projects" />
				<span class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">{{ __('Projects') }}</span>
			</label>
			<!--end::Option-->
		</div>
		<!--end::Radio group-->
	</div>
	<!--end::Input group-->
	<!--begin::Input group-->
	<div class="mb-5">
		<input type="text" name="assignedto" class="form-control form-control-sm form-control-solid" placeholder="{{ __('Assigned to') }}" value="" />
	</div>
	<!--end::Input group-->
	<!--begin::Input group-->
	<div class="mb-5">
		<input type="text" name="collaborators" class="form-control form-control-sm form-control-solid" placeholder="{{ __('Collaborators') }}" value="" />
	</div>
	<!--end::Input group-->
	<!--begin::Input group-->
	<div class="mb-5">
		<!--begin::Radio group-->
		<div class="nav-group nav-group-fluid">
			<!--begin::Option-->
			<label>
				<input type="radio" class="btn-check" name="attachment" value="has" checked="checked" />
				<span class="btn btn-sm btn-color-muted btn-active btn-active-primary">{{ __('Has attachment') }}</span>
			</label>
			<!--end::Option-->
			<!--begin::Option-->
			<label>
				<input type="radio" class="btn-check" name="attachment" value="any" />
				<span class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">{{ __('Any') }}</span>
			</label>
			<!--end::Option-->
		</div>
		<!--end::Radio group-->
	</div>
	<!--end::Input group-->
	<!--begin::Input group-->
	<div class="mb-5">
		<select name="timezone" aria-label="{{ __('Select a Timezone') }}" data-control="select2" data-placeholder="{{ __('date_period') }}" class="form-select form-select-sm form-select-solid">
			<option value="next">{{ __('Within the next') }}</option>
			<option value="last">{{ __('Within the last') }}</option>
			<option value="between">{{ __('Between') }}</option>
			<option value="on">{{ __('On') }}</option>
		</select>
	</div>
	<!--end::Input group-->
	<!--begin::Input group-->
	<div class="row mb-8">
		<!--begin::Col-->
		<div class="col-6">
			<input type="number" name="date_number" class="form-control form-control-sm form-control-solid" placeholder="{{ __('Length') }}" value="" />
		</div>
		<!--end::Col-->
		<!--begin::Col-->
		<div class="col-6">
			<select name="date_typer" aria-label="{{ __('Select a Timezone') }}" data-control="select2" data-placeholder="{{ __('Period') }}" class="form-select form-select-sm form-select-solid">
				<option value="days">{{ __('Days') }}</option>
				<option value="weeks">{{ __('Weeks') }}</option>
				<option value="months">{{ __('Months') }}</option>
				<option value="years">{{ __('Years') }}</option>
			</select>
		</div>
		<!--end::Col-->
	</div>
	<!--end::Input group-->
	<!--begin::Actions-->
	<div class="d-flex justify-content-end">
		<button type="reset" class="btn btn-sm btn-light fw-bold btn-active-light-primary me-2" data-kt-search-element="advanced-options-form-cancel">{{ __('Cancel') }}</button>
		<a href="#" class="btn btn-sm fw-bold btn-primary" data-kt-search-element="advanced-options-form-search">{{ __('Search') }}</a>
	</div>
	<!--end::Actions-->
</form>
<!--end::Preferences-->
