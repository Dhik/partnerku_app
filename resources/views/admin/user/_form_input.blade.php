@csrf
<div class="form-group row">
    <label for="name" class="col-md-3 col-form-label text-md-right">
        {{ trans('labels.name') }}<span class="required">*</span>
    </label>
    <div class="col-md-6">
        <input
            id="name"
            type="text"
            class="form-control @error('name') is-invalid @enderror"
            name="name"
            value="{{ old('name', $edit ? $user->name : '') }}"
            placeholder="{{ trans('placeholder.input', ['field' => trans('labels.name')]) }}"
            autocomplete="name"
            required
        >
        @error('name')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="email" class="col-md-3 col-form-label text-md-right">
        {{ trans('labels.email') }}<span class="required">*</span>
    </label>
    <div class="col-md-6">
        <input
            id="email"
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            name="email"
            value="{{ old('email', $edit ? $user->email : '') }}"
            placeholder="{{ trans('placeholder.input', ['field' => trans('labels.email')]) }}"
            autocomplete="email"
            required
        >
        @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="phone_number" class="col-md-3 col-form-label text-md-right">
        {{ trans('labels.phone_number') }}<span class="required">*</span>
    </label>
    <div class="col-md-6">
        <input
            id="phone_number"
            type="text"
            class="form-control @error('phone_number') is-invalid @enderror"
            name="phone_number"
            value="{{ old('phone_number', $edit ? $user->phone_number : '') }}"
            placeholder="{{ trans('placeholder.input', ['field' => trans('labels.phone_number')]) }}"
            autocomplete="tel"
            required
        >
        @error('phone_number')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="position" class="col-md-3 col-form-label text-md-right">
        {{ trans('labels.position') }}<span class="required">*</span>
    </label>
    <div class="col-md-6">
        <input
            id="position"
            type="text"
            class="form-control @error('position') is-invalid @enderror"
            name="position"
            value="{{ old('position', $edit ? $user->position : '') }}"
            placeholder="{{ trans('placeholder.input', ['field' => trans('labels.position')]) }}"
            autocomplete="organization-title"
            required
        >
        @error('position')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

@if(!$edit)
    <div class="form-group row">
        <label for="password" class="col-md-3 col-form-label text-md-right">
            {{ trans('labels.password') }}<span class="required">*</span>
        </label>
        <div class="col-md-6">
            <input
                id="password"
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password"
                placeholder="{{ trans('placeholder.input', ['field' => trans('labels.password')]) }}"
                autocomplete="new-password"
                required
            >
            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label for="password_confirmation" class="col-md-3 col-form-label text-md-right">
            {{ trans('labels.password_confirmation') }}<span class="required">*</span>
        </label>
        <div class="col-md-6">
            <input
                id="password_confirmation"
                type="password"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                name="password_confirmation"
                placeholder="{{ trans('placeholder.input', ['field' => trans('labels.password_confirmation')]) }}"
                autocomplete="new-password"
                required
            >
            @error('password_confirmation')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
    </div>
@endif

<div class="form-group row">
    <label for="roles" class="col-md-3 col-form-label text-md-right">
        {{ trans('labels.roles') }}<span class="required">*</span>
    </label>
    <div class="col-md-6">
        <select
            id="roles"
            name="roles[]"
            multiple="multiple"
            class="form-control @error('roles') is-invalid @enderror"
            style="width: 100%;"
        >
            @if(isset($roles))
                @foreach($roles as $role)
                    <option value="{{ $role['id'] }}" 
                        {{ in_array($role['id'], old('roles', $edit ? $user->roles->pluck('name')->toArray() : [])) ? 'selected' : '' }}>
                        {{ $role['label'] }}
                    </option>
                @endforeach
            @endif
        </select>
        @error('roles')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row">
    <label for="tenants" class="col-md-3 col-form-label text-md-right">
        {{ trans('labels.tenant') }}
    </label>
    <div class="col-md-6">
        <select
            id="tenants"
            name="tenants[]"
            multiple="multiple"
            class="form-control @error('tenants') is-invalid @enderror"
            style="width: 100%;"
        >
            @if(isset($tenants))
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant['id'] }}" 
                        {{ in_array($tenant['id'], old('tenants', $edit ? $user->tenants()->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                        {{ $tenant['name'] }}
                    </option>
                @endforeach
            @else
                <option value="">No tenants available</option>
            @endif
        </select>
        @error('tenants')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>

<div class="form-group row mb-0">
    <div class="col-md-6 offset-md-3">
        <button type="submit" class="btn btn-primary">
            {{ $edit ? trans('buttons.update') : trans('buttons.save') }}
        </button>
    </div>
</div>