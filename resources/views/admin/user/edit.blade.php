@extends('adminlte::page')

@section('title', trans('labels.user'))

@section('content_header')
    <h1>{{ trans('labels.edit') }} {{ trans('labels.user') }}</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="post" action="{{ route('users.update', $user->id) }}">
                            @method('put')
                            @include('admin.user._form', ['edit' => true])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
.required {
    color: #dc2626;
}

/* Hide original select elements */
#roles, #tenants {
    display: none !important;
}

/* Custom Multi-Select Styles */
.custom-multiselect {
    position: relative;
    width: 100%;
}

.multiselect-container {
    position: relative;
}

.multiselect-input-container {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    background-color: #fff;
    min-height: 38px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.multiselect-input-container:hover {
    border-color: #86b7fe;
}

.multiselect-input-container.active {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.selected-items {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    flex: 1;
}

.selected-item {
    background-color: #1e3a8a;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.selected-item .remove-item {
    cursor: pointer;
    font-size: 0.75rem;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.selected-item .remove-item:hover {
    opacity: 1;
}

.multiselect-search {
    border: none;
    outline: none;
    padding: 0.25rem;
    flex: 1;
    min-width: 100px;
    background: transparent;
}

.multiselect-search::placeholder {
    color: #6c757d;
}

.multiselect-arrow {
    color: #6c757d;
    transition: transform 0.2s;
    margin-left: 0.5rem;
}

.multiselect-input-container.active .multiselect-arrow {
    transform: rotate(180deg);
}

.multiselect-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.multiselect-dropdown.show {
    display: block;
}

.multiselect-options {
    padding: 0.25rem 0;
}

.multiselect-option {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: background-color 0.2s;
}

.multiselect-option:hover {
    background-color: #f8f9fa;
}

.multiselect-option.selected {
    background-color: #e3f2fd;
    color: #1e3a8a;
}

.multiselect-option .option-check {
    display: none;
    color: #1e3a8a;
}

.multiselect-option.selected .option-check {
    display: block;
}

.multiselect-option.hidden {
    display: none;
}

/* No results message */
.no-results {
    padding: 0.75rem;
    text-align: center;
    color: #6c757d;
    font-style: italic;
}

/* Responsive */
@media (max-width: 576px) {
    .multiselect-search {
        min-width: 80px;
    }
    
    .selected-item {
        font-size: 0.8rem;
        padding: 0.2rem 0.4rem;
    }
}
</style>
@endsection

@section('js')
<script>
// Debug: First, let's see what data we have
console.log('=== DEBUG: User Data ===');
console.log('User exists:', @json(isset($user)));
@if(isset($user))
console.log('User ID:', @json($user->id));
console.log('User roles:', @json($user->roles->toArray()));
console.log('User tenants:', @json($user->tenants->toArray()));
console.log('Role IDs:', @json($user->roles->pluck('id')->toArray()));
console.log('Tenant IDs:', @json($user->tenants->pluck('id')->toArray()));
@endif
console.log('Old roles:', @json(old('roles', [])));
console.log('Old tenants:', @json(old('tenants', [])));

class CustomMultiSelect {
    constructor(element, existingData = []) {
        this.element = element;
        this.name = element.getAttribute('data-name');
        this.placeholder = element.getAttribute('data-placeholder');
        this.container = element.querySelector('.multiselect-input-container');
        this.searchInput = element.querySelector('.multiselect-search');
        this.dropdown = element.querySelector('.multiselect-dropdown');
        this.selectedItemsContainer = element.querySelector('.selected-items');
        this.hiddenInputsContainer = element.querySelector('.multiselect-hidden-inputs');
        this.arrow = element.querySelector('.multiselect-arrow');
        this.options = Array.from(element.querySelectorAll('.multiselect-option'));
        this.selectedValues = [];
        this.existingData = existingData;
        
        console.log('=== DEBUG: CustomMultiSelect Init ===');
        console.log('Field name:', this.name);
        console.log('Available options:', this.options.map(opt => ({
            value: opt.getAttribute('data-value'),
            text: opt.getAttribute('data-text')
        })));
        console.log('Existing data passed:', existingData);
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updatePlaceholder();
        
        // Add delay to ensure DOM is ready
        setTimeout(() => {
            this.loadExistingValues();
        }, 100);
    }
    
    bindEvents() {
        // Toggle dropdown
        this.container.addEventListener('click', (e) => {
            if (e.target !== this.searchInput) {
                this.toggleDropdown();
            }
        });
        
        // Search functionality
        this.searchInput.addEventListener('input', (e) => {
            this.filterOptions(e.target.value);
        });
        
        this.searchInput.addEventListener('focus', () => {
            this.openDropdown();
        });
        
        // Option selection
        this.options.forEach(option => {
            option.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleOption(option);
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.element.contains(e.target)) {
                this.closeDropdown();
            }
        });
        
        // Keyboard navigation
        this.searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeDropdown();
            }
        });
    }
    
    toggleDropdown() {
        if (this.dropdown.classList.contains('show')) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }
    
    openDropdown() {
        this.dropdown.classList.add('show');
        this.container.classList.add('active');
        this.searchInput.focus();
    }
    
    closeDropdown() {
        this.dropdown.classList.remove('show');
        this.container.classList.remove('active');
        this.searchInput.value = '';
        this.filterOptions('');
    }
    
    toggleOption(option) {
        const value = option.getAttribute('data-value');
        const text = option.getAttribute('data-text');
        
        if (this.selectedValues.includes(value)) {
            this.removeValue(value);
        } else {
            this.addValue(value, text);
        }
        
        this.updateDisplay();
        this.searchInput.focus();
    }
    
    addValue(value, text) {
        console.log('Adding value:', value, text);
        if (!this.selectedValues.includes(value)) {
            this.selectedValues.push(value);
            this.createSelectedItem(value, text);
            this.createHiddenInput(value);
            this.updateOptionState(value, true);
        }
    }
    
    removeValue(value) {
        console.log('Removing value:', value);
        const index = this.selectedValues.indexOf(value);
        if (index > -1) {
            this.selectedValues.splice(index, 1);
            this.removeSelectedItem(value);
            this.removeHiddenInput(value);
            this.updateOptionState(value, false);
        }
    }
    
    createSelectedItem(value, text) {
        const item = document.createElement('div');
        item.className = 'selected-item';
        item.setAttribute('data-value', value);
        item.innerHTML = `
            <span>${text}</span>
            <i class="fas fa-times remove-item" data-value="${value}"></i>
        `;
        
        // Add remove functionality
        item.querySelector('.remove-item').addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeValue(value);
            this.updateDisplay();
        });
        
        this.selectedItemsContainer.appendChild(item);
        console.log('Created selected item for:', value, text);
    }
    
    removeSelectedItem(value) {
        const item = this.selectedItemsContainer.querySelector(`[data-value="${value}"]`);
        if (item) {
            item.remove();
        }
    }
    
    createHiddenInput(value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `${this.name}[]`;
        input.value = value;
        input.setAttribute('data-value', value);
        this.hiddenInputsContainer.appendChild(input);
        console.log('Created hidden input for:', this.name, value);
    }
    
    removeHiddenInput(value) {
        const input = this.hiddenInputsContainer.querySelector(`[data-value="${value}"]`);
        if (input) {
            input.remove();
        }
    }
    
    updateOptionState(value, selected) {
        const option = this.options.find(opt => opt.getAttribute('data-value') === value);
        if (option) {
            if (selected) {
                option.classList.add('selected');
            } else {
                option.classList.remove('selected');
            }
        }
    }
    
    updateDisplay() {
        this.updatePlaceholder();
    }
    
    updatePlaceholder() {
        if (this.selectedValues.length === 0) {
            this.searchInput.placeholder = this.placeholder;
            this.searchInput.style.display = 'block';
        } else {
            this.searchInput.placeholder = '';
            this.searchInput.style.display = 'block';
        }
    }
    
    filterOptions(searchText) {
        const text = searchText.toLowerCase();
        let hasVisibleOptions = false;
        
        this.options.forEach(option => {
            const optionText = option.getAttribute('data-text').toLowerCase();
            if (optionText.includes(text)) {
                option.classList.remove('hidden');
                hasVisibleOptions = true;
            } else {
                option.classList.add('hidden');
            }
        });
        
        // Show/hide no results message
        this.toggleNoResults(!hasVisibleOptions && searchText.length > 0);
    }
    
    toggleNoResults(show) {
        let noResultsDiv = this.dropdown.querySelector('.no-results');
        
        if (show && !noResultsDiv) {
            noResultsDiv = document.createElement('div');
            noResultsDiv.className = 'no-results';
            noResultsDiv.textContent = 'No results found';
            this.dropdown.querySelector('.multiselect-options').appendChild(noResultsDiv);
        } else if (!show && noResultsDiv) {
            noResultsDiv.remove();
        }
    }
    
    loadExistingValues() {
        console.log('=== DEBUG: Loading existing values ===');
        console.log('Field name:', this.name);
        console.log('Existing data:', this.existingData);
        
        // Use existing data passed from constructor
        if (this.existingData && this.existingData.length > 0) {
            console.log('Processing existing data:', this.existingData);
            
            this.existingData.forEach(value => {
                const valueStr = String(value);
                console.log('Looking for option with value:', valueStr);
                
                const option = this.options.find(opt => {
                    const optValue = opt.getAttribute('data-value');
                    console.log('Comparing', optValue, '===', valueStr, ':', optValue === valueStr);
                    return optValue === valueStr;
                });
                
                if (option) {
                    const text = option.getAttribute('data-text');
                    console.log('Found option, adding value:', valueStr, text);
                    this.addValue(valueStr, text);
                } else {
                    console.log('No option found for value:', valueStr);
                    console.log('Available option values:', this.options.map(opt => opt.getAttribute('data-value')));
                }
            });
            
            this.updateDisplay();
            console.log('Final selected values:', this.selectedValues);
        } else {
            console.log('No existing data to load');
        }
    }
}

$(document).ready(function() {
    console.log('=== DEBUG: Document ready ===');
    
    // Get existing data from Laravel
    const existingRoles = @json(isset($user) ? $user->roles->pluck('name')->toArray() : []);
    const existingTenants = @json(isset($user) ? $user->tenants->pluck('id')->toArray() : []);
    const oldRoles = @json(old('roles', []));
    const oldTenants = @json(old('tenants', []));
    
    console.log('Existing roles from user (using names):', existingRoles);
    console.log('Existing tenants from user:', existingTenants);
    console.log('Old roles from validation:', oldRoles);
    console.log('Old tenants from validation:', oldTenants);
    
    // Use old values if they exist (after validation error), otherwise use existing values
    const rolesToUse = oldRoles && oldRoles.length > 0 ? oldRoles : existingRoles;
    const tenantsToUse = oldTenants && oldTenants.length > 0 ? oldTenants : existingTenants;
    
    console.log('Final roles to use:', rolesToUse);
    console.log('Final tenants to use:', tenantsToUse);
    
    // Replace the original select elements with custom multi-select
    replaceSelectWithCustomMultiSelect('#roles', 'roles', 'Select roles...', rolesToUse);
    replaceSelectWithCustomMultiSelect('#tenants', 'tenants', 'Select tenants...', tenantsToUse);
    
    function replaceSelectWithCustomMultiSelect(selectId, fieldName, placeholder, existingData = []) {
        console.log('=== DEBUG: Replacing select ===');
        console.log('Select ID:', selectId);
        console.log('Field name:', fieldName);
        console.log('Existing data:', existingData);
        
        const $select = $(selectId);
        const $container = $select.closest('.col-md-6');
        
        console.log('Original select found:', $select.length > 0);
        const originalOptions = $select.find('option').map(function() {
            return { value: $(this).val(), text: $(this).text(), selected: $(this).is(':selected') };
        }).get();
        console.log('Original select options:', originalOptions);
        
        // Also check what's currently selected in the original select
        const currentlySelected = $select.find('option:selected').map(function() {
            return $(this).val();
        }).get();
        console.log('Currently selected in original select:', currentlySelected);
        
        // Create custom multi-select HTML
        let customMultiSelectHtml = `
            <div class="custom-multiselect" data-name="${fieldName}" data-placeholder="${placeholder}">
                <div class="multiselect-container">
                    <div class="multiselect-input-container">
                        <div class="selected-items"></div>
                        <input type="text" class="multiselect-search" placeholder="${placeholder}" autocomplete="off">
                        <i class="fas fa-chevron-down multiselect-arrow"></i>
                    </div>
                    <div class="multiselect-dropdown">
                        <div class="multiselect-options">
        `;
        
        // Add options from the original select
        $select.find('option').each(function() {
            const value = $(this).val();
            const text = $(this).text();
            
            if (value && value !== "") {
                customMultiSelectHtml += `
                    <div class="multiselect-option" data-value="${value}" data-text="${text}">
                        <span class="option-text">${text}</span>
                        <i class="fas fa-check option-check"></i>
                    </div>
                `;
            }
        });
        
        customMultiSelectHtml += `
                        </div>
                    </div>
                </div>
                <!-- Hidden inputs for form submission -->
                <div class="multiselect-hidden-inputs"></div>
            </div>
        `;
        
        // Insert the custom multi-select after the original select
        $select.after(customMultiSelectHtml);
        
        // Initialize the custom multi-select with existing data
        const customElement = $container.find('.custom-multiselect')[0];
        console.log('Custom element found:', customElement !== undefined);
        
        if (customElement) {
            new CustomMultiSelect(customElement, existingData);
        } else {
            console.error('Custom multiselect element not found!');
        }
    }
});
</script>
@endsection