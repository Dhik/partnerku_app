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
                            @include('admin.user._form_input', ['edit' => false])
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
class CustomMultiSelect {
    constructor(element) {
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
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updatePlaceholder();
        
        // Load old values if any
        this.loadOldValues();
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
        if (!this.selectedValues.includes(value)) {
            this.selectedValues.push(value);
            this.createSelectedItem(value, text);
            this.createHiddenInput(value);
            this.updateOptionState(value, true);
        }
    }
    
    removeValue(value) {
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
    
    loadOldValues() {
        // Load old values from Laravel old() helper if they exist
        const oldRoles = @json(old('roles', []));
        const oldTenants = @json(old('tenants', []));
        
        if (this.name === 'roles' && oldRoles && oldRoles.length > 0) {
            oldRoles.forEach(value => {
                const option = this.options.find(opt => opt.getAttribute('data-value') === value);
                if (option) {
                    const text = option.getAttribute('data-text');
                    this.addValue(value, text);
                }
            });
            this.updateDisplay();
        }
        
        if (this.name === 'tenants' && oldTenants && oldTenants.length > 0) {
            oldTenants.forEach(value => {
                const option = this.options.find(opt => opt.getAttribute('data-value') === value);
                if (option) {
                    const text = option.getAttribute('data-text');
                    this.addValue(value, text);
                }
            });
            this.updateDisplay();
        }
    }
}

$(document).ready(function() {
    // Replace the original select elements with custom multi-select
    replaceSelectWithCustomMultiSelect('#roles', 'roles', 'Select roles...');
    replaceSelectWithCustomMultiSelect('#tenants', 'tenants', 'Select tenants...');
    
    function replaceSelectWithCustomMultiSelect(selectId, fieldName, placeholder) {
        const $select = $(selectId);
        const $container = $select.closest('.col-md-6');
        
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
        
        // Initialize the custom multi-select
        const customElement = $container.find('.custom-multiselect')[0];
        new CustomMultiSelect(customElement);
    }
});
</script>
@endsection