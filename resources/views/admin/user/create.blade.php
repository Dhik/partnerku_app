@extends('adminlte::page')

@section('title', trans('labels.user'))

@section('content_header')
    <h1>{{ trans('labels.add') }} {{ trans('labels.user') }}</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf
                            
                            <!-- Name -->
                            <div class="form-group row">
                                <label for="name" class="col-md-3 col-form-label text-md-right">
                                    {{ trans('labels.name') }}<span class="required">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group row">
                                <label for="email" class="col-md-3 col-form-label text-md-right">
                                    {{ trans('labels.email') }}<span class="required">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="form-group row">
                                <label for="phone_number" class="col-md-3 col-form-label text-md-right">
                                    {{ trans('labels.phone_number') }}<span class="required">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required>
                                    @error('phone_number')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Position -->
                            <div class="form-group row">
                                <label for="position" class="col-md-3 col-form-label text-md-right">
                                    {{ trans('labels.position') }}<span class="required">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input id="position" type="text" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position') }}" required>
                                    @error('position')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="form-group row">
                                <label for="password" class="col-md-3 col-form-label text-md-right">
                                    {{ trans('labels.password') }}<span class="required">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password Confirmation -->
                            <div class="form-group row">
                                <label for="password_confirmation" class="col-md-3 col-form-label text-md-right">
                                    {{ trans('labels.password_confirmation') }}<span class="required">*</span>
                                </label>
                                <div class="col-md-6">
                                    <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
                                    @error('password_confirmation')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Roles -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-md-right">
                                    {{ trans('labels.roles') }}<span class="required">*</span>
                                </label>
                                <div class="col-md-6">
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                                        @if(isset($roles) && count($roles) > 0)
                                            @foreach($roles as $role)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role['id'] }}" id="role_{{ $role['id'] }}" {{ in_array($role['id'], old('roles', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role_{{ $role['id'] }}">
                                                        {{ $role['label'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted">No roles available</p>
                                        @endif
                                    </div>
                                    @error('roles')
                                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tenants -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label text-md-right">
                                    {{ trans('labels.tenant') }}
                                </label>
                                <div class="col-md-6">
                                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                                        @if(isset($tenants) && count($tenants) > 0)
                                            @foreach($tenants as $tenant)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="tenants[]" value="{{ $tenant['id'] }}" id="tenant_{{ $tenant['id'] }}" {{ in_array($tenant['id'], old('tenants', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="tenant_{{ $tenant['id'] }}">
                                                        {{ $tenant['name'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted">No tenants available</p>
                                        @endif
                                    </div>
                                    @error('tenants')
                                        <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('buttons.save') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
.form-check {
    margin-bottom: 0.5rem;
}

.form-check-input:checked {
    background-color: #1e3a8a;
    border-color: #1e3a8a;
}

.form-check-label {
    font-weight: 500;
    cursor: pointer;
}

.required {
    color: #dc2626;
}
</style>
@endsection