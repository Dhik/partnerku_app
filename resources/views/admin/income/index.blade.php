{{-- Income View (admin/income/index.blade.php) --}}
@extends('adminlte::page')

@section('title', 'Income Management')

@section('content_header')
    <h1>Income Management</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Income Data</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" onclick="createIncome()">
                        <i class="fas fa-plus"></i> Add New Income
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="incomeTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client Name</th>
                            <th>Revenue Contract</th>
                            <th>Service</th>
                            <th>Team In Charge</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="incomeModal" tabindex="-1" role="dialog" aria-labelledby="incomeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incomeModalLabel">Add New Income</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="incomeForm">
                <div class="modal-body">
                    <input type="hidden" id="incomeId" name="income_id">
                    <div class="form-group">
                        <label for="nama_client">Client Name</label>
                        <input type="text" class="form-control" id="nama_client" name="nama_client" required>
                    </div>
                    <div class="form-group">
                        <label for="revenue_contract">Revenue Contract</label>
                        <input type="number" step="0.01" class="form-control" id="revenue_contract" name="revenue_contract" required>
                    </div>
                    <div class="form-group">
                        <label for="service">Service</label>
                        <input type="text" class="form-control" id="service" name="service" required>
                    </div>
                    <div class="form-group">
                        <label for="team_in_charge">Team In Charge <span class="required">*</span></label>
                        <select class="form-control" id="team_in_charge" name="team_in_charge[]" multiple required style="display: none !important;">
                            <!-- Options will be loaded via AJAX -->
                        </select>
                        <small class="form-text text-muted">Select multiple team members</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Income</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Show Modal -->
<div class="modal fade" id="showIncomeModal" tabindex="-1" role="dialog" aria-labelledby="showIncomeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showIncomeModalLabel">Income Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Client Name:</strong>
                        <p id="show_nama_client"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Revenue Contract:</strong>
                        <p id="show_revenue_contract"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Service:</strong>
                        <p id="show_service"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Team In Charge:</strong>
                        <p id="show_team_in_charge"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <style>
    .required {
        color: #dc2626;
    }

    /* Hide original select elements */
    #team_in_charge {
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
@stop

@section('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
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
    
    // Method to clear all selections
    clearAll() {
        this.selectedValues = [];
        this.selectedItemsContainer.innerHTML = '';
        this.hiddenInputsContainer.innerHTML = '';
        this.options.forEach(option => {
            option.classList.remove('selected');
        });
        this.updateDisplay();
    }
    
    // Method to set values programmatically
    setValues(values) {
        this.clearAll();
        values.forEach(value => {
            const option = this.options.find(opt => opt.getAttribute('data-value') === value);
            if (option) {
                const text = option.getAttribute('data-text');
                this.addValue(value, text);
            }
        });
        this.updateDisplay();
    }
    
    // Method to get selected values
    getSelectedValues() {
        return this.selectedValues;
    }
}

let teamMultiSelect = null;

$(document).ready(function() {
    // Users data is already available from the controller
    const usersData = @json($users ?? []);
    
    // Initialize custom multiselect with users data
    initializeCustomMultiSelect(usersData);
    
    // Initialize DataTable
    $('#incomeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("income.data") }}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'nama_client', name: 'nama_client'},
            {data: 'revenue_contract', name: 'revenue_contract'},
            {data: 'service', name: 'service'},
            {data: 'team_in_charge', name: 'team_in_charge'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ]
    });

    // Form submission
    $('#incomeForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get selected team members from custom multiselect
        let selectedTeam = teamMultiSelect ? teamMultiSelect.getSelectedValues() : [];
        
        // Get form data as object
        let formData = {
            'nama_client': $('#nama_client').val(),
            'revenue_contract': $('#revenue_contract').val(),
            'service': $('#service').val(),
            'team_in_charge': selectedTeam, // This will be an array
            '_token': '{{ csrf_token() }}'
        };
        
        let incomeId = $('#incomeId').val();
        let url = incomeId ? '{{ route("income.update", ":id") }}'.replace(':id', incomeId) : '{{ route("income.store") }}';
        let method = incomeId ? 'PUT' : 'POST';

        if (method === 'PUT') {
            formData['_method'] = 'PUT';
        }

        console.log('Submitting data:', formData); // Debug log

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                console.log('Success response:', response); // Debug log
                if (response.success) {
                    $('#incomeModal').modal('hide');
                    $('#incomeTable').DataTable().ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    resetForm();
                }
            },
            error: function(xhr) {
                console.log('Error response:', xhr.responseJSON); // Debug log
                handleFormErrors(xhr);
            }
        });
    });
});

function initializeCustomMultiSelect(users) {
    const $select = $('#team_in_charge');
    const $formGroup = $select.closest('.form-group');
    
    // Clear existing options and add users from controller
    $select.empty();
    users.forEach(function(user) {
        $select.append(`<option value="${user.id}">${user.name}</option>`);
    });
    
    // Create custom multi-select HTML
    let customMultiSelectHtml = `
        <div class="custom-multiselect" data-name="team_in_charge" data-placeholder="Select team members...">
            <div class="multiselect-container">
                <div class="multiselect-input-container">
                    <div class="selected-items"></div>
                    <input type="text" class="multiselect-search" placeholder="Select team members..." autocomplete="off">
                    <i class="fas fa-chevron-down multiselect-arrow"></i>
                </div>
                <div class="multiselect-dropdown">
                    <div class="multiselect-options">
    `;
    
    // Add options from users data
    users.forEach(function(user) {
        customMultiSelectHtml += `
            <div class="multiselect-option" data-value="${user.id}" data-text="${user.name}">
                <span class="option-text">${user.name}</span>
                <i class="fas fa-check option-check"></i>
            </div>
        `;
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
    const customElement = $formGroup.find('.custom-multiselect')[0];
    teamMultiSelect = new CustomMultiSelect(customElement);
}

function createIncome() {
    resetForm();
    $('#incomeModalLabel').text('Add New Income');
    $('#incomeModal').modal('show');
}

function editIncome(id) {
    $.ajax({
        url: '{{ route("income.edit", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#incomeId').val(data.id);
                $('#nama_client').val(data.nama_client);
                $('#revenue_contract').val(data.revenue_contract);
                $('#service').val(data.service);
                
                // Set selected team members using custom multiselect
                if (Array.isArray(data.team_in_charge) && teamMultiSelect) {
                    teamMultiSelect.setValues(data.team_in_charge.map(String));
                }
                
                $('#incomeModalLabel').text('Edit Income');
                $('#incomeModal').modal('show');
            }
        }
    });
}

function showIncome(id) {
    $.ajax({
        url: '{{ route("income.show", ":id") }}'.replace(':id', id),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let data = response.data;
                $('#show_nama_client').text(data.nama_client);
                $('#show_revenue_contract').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.revenue_contract));
                $('#show_service').text(data.service);
                
                // Display team members names
                if (data.team_members_names && data.team_members_names.length > 0) {
                    $('#show_team_in_charge').text(data.team_members_names.join(', '));
                } else {
                    $('#show_team_in_charge').text('No team assigned');
                }
                
                $('#showIncomeModal').modal('show');
            }
        }
    });
}

function deleteIncome(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("income.destroy", ":id") }}'.replace(':id', id),
                method: 'DELETE',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#incomeTable').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON.message || 'Something went wrong!'
                    });
                }
            });
        }
    });
}

function resetForm() {
    $('#incomeForm')[0].reset();
    $('#incomeId').val('');
    if (teamMultiSelect) {
        teamMultiSelect.clearAll();
    }
}

function handleFormErrors(xhr) {
    let errors = xhr.responseJSON.errors;
    if (errors) {
        let errorMessage = '';
        Object.keys(errors).forEach(function(key) {
            errorMessage += errors[key][0] + '\n';
        });
        Swal.fire({
            icon: 'error',
            title: 'Validation Error!',
            text: errorMessage
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: xhr.responseJSON.message || 'Something went wrong!'
        });
    }
}
</script>
@stop