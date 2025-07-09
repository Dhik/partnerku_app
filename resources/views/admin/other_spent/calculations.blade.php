@extends('adminlte::page')

@section('title', 'Cashflow Advice')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Cashflow Advice</h1>
        </div>
        <div class="col-sm-6">
            <div class="float-sm-right">
                <div class="form-group mb-0">
                    <label for="monthFilter" class="sr-only">Select Month</label>
                    <input type="month" id="monthFilter" class="form-control" style="width: 200px;">
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<!-- KPI Cards Row -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 id="totalRevenue">Rp 0</h3>
                <p>Total Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3 id="totalRecommended">Rp 0</h3>
                <p>Recommended Budget</p>
            </div>
            <div class="icon">
                <i class="fas fa-calculator"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" id="actualSpendingCard">
            <div class="inner">
                <h3 id="totalActual">Rp 0</h3>
                <p>Actual Spending</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" id="budgetUtilizationCard">
            <div class="inner">
                <h3 id="budgetUtilization">0%</h3>
                <p>Budget Utilization</p>
            </div>
            <div class="icon">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
    </div>
</div>

<!-- Month Display -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    Financial Overview for <span id="currentMonth">Current Month</span>
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Expense Categories Comparison -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Recommended vs Actual Expenses
                </h3>
            </div>
            <div class="card-body">
                <div class="row" id="expenseCards">
                    <!-- Expense cards will be dynamically generated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Breakdown Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-2"></i>
                    Detailed Breakdown
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="expenseTable">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Percentage</th>
                                <th>Recommended Amount</th>
                                <th>Actual Amount</th>
                                <th>Difference</th>
                                <th>Status</th>
                                <th>Utilization</th>
                            </tr>
                        </thead>
                        <tbody id="expenseTableBody">
                            <!-- Table rows will be dynamically generated here -->
                        </tbody>
                        <tfoot id="expenseTableFooter">
                            <!-- Footer totals will be dynamically generated here -->
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
.expense-card {
    transition: transform 0.2s;
}

.expense-card:hover {
    transform: translateY(-5px);
}

.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.bg-on-target {
    background-color: #28a745 !important;
}

.bg-under-budget {
    background-color: #007bff !important;
}

.bg-over-budget {
    background-color: #dc3545 !important;
}

.progress-bar-on-target {
    background-color: #28a745;
}

.progress-bar-under-budget {
    background-color: #007bff;
}

.progress-bar-over-budget {
    background-color: #dc3545;
}

.card-expense {
    border-left: 4px solid #6c757d;
}

.card-expense.on-target {
    border-left-color: #28a745;
}

.card-expense.under-budget {
    border-left-color: #007bff;
}

.card-expense.over-budget {
    border-left-color: #dc3545;
}

.metric-value {
    font-size: 1.1rem;
    font-weight: bold;
}

.metric-label {
    font-size: 0.85rem;
    color: #6c757d;
}

.difference-positive {
    color: #dc3545;
}

.difference-negative {
    color: #007bff;
}

.difference-zero {
    color: #28a745;
}

@media (max-width: 768px) {
    .expense-card {
        margin-bottom: 1rem;
    }
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Set default month to current month
    const currentMonth = new Date().toISOString().slice(0, 7);
    $('#monthFilter').val(currentMonth);
    
    // Load initial data
    loadCalculationsData();
    
    // Handle month filter change
    $('#monthFilter').on('change', function() {
        loadCalculationsData();
    });
});

function loadCalculationsData() {
    const month = $('#monthFilter').val();
    
    $.ajax({
        url: '{{ route("otherSpent.calculationsData") }}',
        method: 'GET',
        data: { month: month },
        beforeSend: function() {
            // Show loading state
            $('#totalRevenue, #totalRecommended, #totalActual, #budgetUtilization').html('<i class="fas fa-spinner fa-spin"></i>');
        },
        success: function(response) {
            if (response.success) {
                updateKPICards(response.data);
                updateExpenseCards(response.data.expenseData);
                updateExpenseTable(response.data);
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load calculations data'
            });
        }
    });
}

function updateKPICards(data) {
    // Update month display
    $('#currentMonth').text(data.monthName);
    
    // Update KPI values
    $('#totalRevenue').text(formatCurrency(data.totalRevenue));
    $('#totalRecommended').text(formatCurrency(data.totalRecommended));
    $('#totalActual').text(formatCurrency(data.totalActualSpending));
    $('#budgetUtilization').text(Math.round(data.budgetUtilization) + '%');
    
    // Update card colors based on performance
    updateActualSpendingCardColor(data.totalDifference);
    updateBudgetUtilizationCardColor(data.budgetUtilization);
}

function updateActualSpendingCardColor(difference) {
    const card = $('#actualSpendingCard');
    card.removeClass('bg-success bg-info bg-danger');
    
    if (difference === 0) {
        card.addClass('bg-success');
    } else if (difference < 0) {
        card.addClass('bg-info');
    } else {
        card.addClass('bg-danger');
    }
}

function updateBudgetUtilizationCardColor(utilization) {
    const card = $('#budgetUtilizationCard');
    card.removeClass('bg-success bg-info bg-danger');
    
    if (utilization <= 100) {
        if (utilization >= 90) {
            card.addClass('bg-success');
        } else {
            card.addClass('bg-info');
        }
    } else {
        card.addClass('bg-danger');
    }
}

function updateExpenseCards(expenseData) {
    const container = $('#expenseCards');
    container.empty();
    
    expenseData.forEach(function(expense) {
        const cardHtml = `
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="card card-expense ${expense.status} expense-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">${expense.type}</h6>
                            <span class="badge bg-${expense.status} status-badge">${getStatusText(expense.status)}</span>
                        </div>
                        
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="metric-value text-warning">${formatCurrency(expense.recommended)}</div>
                                <div class="metric-label">Recommended</div>
                            </div>
                            <div class="col-6">
                                <div class="metric-value text-${getStatusColorClass(expense.status)}">${formatCurrency(expense.actual)}</div>
                                <div class="metric-label">Actual</div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Utilization</small>
                                <small>${Math.round(expense.percentage_used)}%</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar progress-bar-${expense.status}" 
                                     role="progressbar" 
                                     style="width: ${Math.min(expense.percentage_used, 100)}%">
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <small class="difference-${getDifferenceClass(expense.difference)}">
                                ${expense.difference >= 0 ? '+' : ''}${formatCurrency(expense.difference)}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.append(cardHtml);
    });
}

function updateExpenseTable(data) {
    const tbody = $('#expenseTableBody');
    const tfoot = $('#expenseTableFooter');
    
    tbody.empty();
    tfoot.empty();
    
    // Add expense rows
    data.expenseData.forEach(function(expense) {
        const rowHtml = `
            <tr>
                <td><strong>${expense.type}</strong></td>
                <td class="text-center">${expense.percentage}%</td>
                <td class="text-right">${formatCurrency(expense.recommended)}</td>
                <td class="text-right text-${getStatusColorClass(expense.status)}">${formatCurrency(expense.actual)}</td>
                <td class="text-right difference-${getDifferenceClass(expense.difference)}">
                    ${expense.difference >= 0 ? '+' : ''}${formatCurrency(expense.difference)}
                </td>
                <td class="text-center">
                    <span class="badge bg-${expense.status}">${getStatusText(expense.status)}</span>
                </td>
                <td class="text-center">${Math.round(expense.percentage_used)}%</td>
            </tr>
        `;
        tbody.append(rowHtml);
    });
    
    // Add totals footer
    const footerHtml = `
        <tr class="bg-light font-weight-bold">
            <td><strong>TOTAL</strong></td>
            <td class="text-center">40%</td>
            <td class="text-right">${formatCurrency(data.totalRecommended)}</td>
            <td class="text-right text-${getStatusColorClass(data.totalDifference >= 0 ? 'over-budget' : 'under-budget')}">
                ${formatCurrency(data.totalActualSpending)}
            </td>
            <td class="text-right difference-${getDifferenceClass(data.totalDifference)}">
                ${data.totalDifference >= 0 ? '+' : ''}${formatCurrency(data.totalDifference)}
            </td>
            <td class="text-center">
                <span class="badge bg-${data.budgetUtilization <= 100 ? 'success' : 'danger'}">
                    ${Math.round(data.budgetUtilization)}%
                </span>
            </td>
            <td class="text-center">${Math.round(data.budgetUtilization)}%</td>
        </tr>
    `;
    tfoot.append(footerHtml);
}

function formatCurrency(amount) {
    if (amount === 0) return 'Rp 0';
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(amount));
}

function getStatusText(status) {
    switch(status) {
        case 'on-target': return 'On Target';
        case 'under-budget': return 'Under Budget';
        case 'over-budget': return 'Over Budget';
        default: return 'Unknown';
    }
}

function getStatusColorClass(status) {
    switch(status) {
        case 'on-target': return 'success';
        case 'under-budget': return 'info';
        case 'over-budget': return 'danger';
        default: return 'secondary';
    }
}

function getDifferenceClass(difference) {
    if (difference > 0) return 'positive';
    if (difference < 0) return 'negative';
    return 'zero';
}
</script>
@stop