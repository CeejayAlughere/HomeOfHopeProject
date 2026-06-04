/**
 * Admin Dashboard JavaScript
 */

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeTableSorting();
    initializeTooltips();
});

/**
 * Initialize table sorting
 */
function initializeTableSorting() {
    const tableHeaders = document.querySelectorAll('table th');
    tableHeaders.forEach((header, columnIndex) => {
        // Skip action columns
        if (header.textContent.toLowerCase() !== 'actions') {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                sortTable(columnIndex);
            });
        }
    });
}

/**
 * Sort table by column
 */
function sortTable(columnIndex) {
    const table = document.querySelector('table');
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Determine sort direction
    const header = table.querySelectorAll('th')[columnIndex];
    const isAscending = !header.classList.contains('sort-asc');
    
    // Clear previous sort indicators
    table.querySelectorAll('th').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
    });
    
    // Add sort indicator
    header.classList.add(isAscending ? 'sort-asc' : 'sort-desc');
    
    // Sort rows
    rows.sort((rowA, rowB) => {
        const cellA = rowA.cells[columnIndex].textContent.trim();
        const cellB = rowB.cells[columnIndex].textContent.trim();
        
        // Try to parse as numbers
        const numA = parseFloat(cellA.replace(/[$,]/g, ''));
        const numB = parseFloat(cellB.replace(/[$,]/g, ''));
        
        let comparison = 0;
        
        if (!isNaN(numA) && !isNaN(numB)) {
            comparison = numA - numB;
        } else {
            comparison = cellA.localeCompare(cellB);
        }
        
        return isAscending ? comparison : -comparison;
    });
    
    // Rebuild table
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Initialize Bootstrap tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

/**
 * Format date
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

/**
 * Confirm deletion
 */
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toastHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    toastContainer.innerHTML = toastHTML;
    
    document.body.appendChild(toastContainer);
    
    const toastElement = toastContainer.querySelector('.toast');
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    // Remove container after toast hides
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastContainer.remove();
    });
}

/**
 * Export table to CSV
 */
function exportTableToCSV(filename) {
    const csv = [];
    const rows = document.querySelectorAll('table tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = [];
        
        cols.forEach(col => {
            csvRow.push('"' + col.textContent.trim().replace(/"/g, '""') + '"');
        });
        
        csv.push(csvRow.join(','));
    });
    
    downloadCSV(csv.join('\n'), filename);
}

/**
 * Download CSV file
 */
function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.href = URL.createObjectURL(csvFile);
    downloadLink.download = filename;
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

/**
 * Filter table by status
 */
function filterTableByStatus(statusValue) {
    const table = document.querySelector('table');
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const statusCell = row.querySelector('td:nth-child(5)'); // Adjust column index as needed
        
        if (!statusCell) return;
        
        if (statusValue === '' || statusCell.textContent.includes(statusValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * Search table
 */
function searchTable(searchTerm) {
    const table = document.querySelector('table');
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    const lowerCaseTerm = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(lowerCaseTerm) ? '' : 'none';
    });
}

/**
 * Real-time search
 */
function setupRealtimeSearch(inputSelector) {
    const searchInput = document.querySelector(inputSelector);
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            searchTable(this.value);
        });
    }
}

/**
 * Validate email
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Clear form
 */
function clearForm(formSelector) {
    const form = document.querySelector(formSelector);
    if (form) {
        form.reset();
    }
}

/**
 * Enable/Disable form inputs
 */
function setFormInputsDisabled(formSelector, disabled = true) {
    const form = document.querySelector(formSelector);
    if (form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.disabled = disabled;
        });
    }
}

/**
 * Show loading spinner
 */
function showLoadingSpinner(message = 'Loading...') {
    const spinnerHTML = `
        <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
            <div>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">${message}</p>
            </div>
        </div>
    `;
    
    return spinnerHTML;
}

/**
 * Trigger modal with data
 */
function openModal(modalSelector, data = {}) {
    const modal = new bootstrap.Modal(document.querySelector(modalSelector));
    
    // Populate form fields with data
    Object.keys(data).forEach(key => {
        const input = document.querySelector(`[name="${key}"]`);
        if (input) {
            input.value = data[key];
        }
    });
    
    modal.show();
}

/**
 * Close modal
 */
function closeModal(modalSelector) {
    const modal = bootstrap.Modal.getInstance(document.querySelector(modalSelector));
    if (modal) {
        modal.hide();
    }
}

/**
 * Auto-hide alerts after 5 seconds
 */
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-dismissible')) {
            setTimeout(() => {
                const alertInstance = new bootstrap.Alert(alert);
                alertInstance.close();
            }, 5000);
        }
    });
});

/**
 * Prevent form double submission
 */
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            }
        });
    });
});
