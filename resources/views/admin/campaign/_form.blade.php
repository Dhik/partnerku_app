@csrf

<div class="form-group row">
    <label for="title" class="col-md-3 col-form-label text-md-right">
        {{ trans('labels.title') }}<span class="required">*</span>
    </label>

    <div class="col-md-6">
        <input
            id="title"
            type="text"
            class="form-control @error('title') is-invalid @enderror"
            name="title"
            value="{{ old('title', $edit ? $campaign->title : '') }}"
            placeholder="{{ trans('placeholder.input', ['field' => trans('labels.title')]) }}"
            autocomplete="title"
            autofocus>

        @error('title')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="title" class="col-md-3 col-form-label text-md-right">
        {{ trans('labels.period') }}<span class="required">*</span>
    </label>

    <div class="col-md-6">
        <input
            id="period"
            type="text"
            class="form-control rangeDateNoLimit @error('period') is-invalid @enderror"
            name="period"
            value="{{ old('period', $edit ? $campaign->period : '') }}"
            placeholder="{{ trans('placeholder.input', ['field' => trans('labels.period')]) }}"
            autocomplete="title"
            autofocus>

        @error('period')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="description" class="col-md-3 col-form-label text-md-right">
        {{ trans('labels.description') }}<span class="required">*</span>
    </label>

    <div class="col-md-6">
        <textarea
            class="form-control  @error('description') is-invalid @enderror"
            id="description"
            name="description"
            placeholder="{{ trans('placeholder.input', ['field' => trans('labels.description')]) }}"
            autofocus>{{ old('description', $edit ? $campaign->description : '') }}</textarea>

        @error('description')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

{{-- Add CPM Benchmark field --}}
<div class="form-group row">
    <label for="cpm_benchmark" class="col-md-3 col-form-label text-md-right">
        CPM Benchmark
    </label>

    <div class="col-md-6">
        <input
            id="cpm_benchmark"
            type="number"
            step="0.01"
            min="0"
            class="form-control @error('cmp_benchmark') is-invalid @enderror"
            name="cpm_benchmark"
            value="{{ old('cpm_benchmark', $edit ? $campaign->cpm_benchmark : '0.00') }}"
            placeholder="Enter CPM benchmark value"
            autocomplete="cpm_benchmark">

        @error('cpm_benchmark')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        
        <small class="form-text text-muted">
            Set the CPM benchmark for this campaign (default: 0.00)
        </small>
    </div>
</div>

<div class="form-group row mb-0">
    <div class="col-md-6 offset-md-3">
        <button type="submit" class="btn btn-primary">
            {{ $edit ? trans('buttons.update') : trans('buttons.save') }}
        </button>
    </div>
</div>