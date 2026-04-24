/* assets/js/script.js */

document.addEventListener('DOMContentLoaded', () => {
    const loanForm = document.getElementById('loan-form');
    const amountInput = document.getElementById('input-amount');
    const percentageInput = document.getElementById('input-percentage');
    const previewInterest = document.getElementById('preview-interest');
    const previewTotal = document.getElementById('preview-total');
    const totalRepaymentInput = document.getElementById('input-total-repayment');
    const loansTbody = document.getElementById('loans-tbody');
    const currentDateEl = document.getElementById('current-date');
    const searchInput = document.getElementById('table-search');
    const exportBtn = document.getElementById('export-csv');

    let allLoans = []; // Store loans for filtering

    // Set current date in header
    currentDateEl.textContent = new Date().toLocaleDateString('en-US', { 
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
    });

    // Helper for currency formatting
    const formatCurrency = (val) => {
        const num = parseFloat(val) || 0;
        return Math.round(num).toLocaleString() + ' rwf';
    };

    // Real-time calculation
    const calculateTotals = () => {
        const amount = parseFloat(amountInput.value) || 0;
        const percentage = parseFloat(percentageInput.value) || 0;
        const interest = (amount * percentage) / 100;
        const total = amount + interest;

        previewInterest.textContent = formatCurrency(interest);
        previewTotal.textContent = formatCurrency(total);
        totalRepaymentInput.value = total.toFixed(2);
    };

    amountInput.addEventListener('input', calculateTotals);
    percentageInput.addEventListener('input', calculateTotals);

    // Fetch Stats
    const fetchStats = async () => {
        try {
            const response = await fetch('api.php?action=stats');
            const stats = await response.json();
            document.getElementById('stat-total-lent').textContent = formatCurrency(stats.total_lent);
            document.getElementById('stat-expected-return').textContent = formatCurrency(stats.expected_return);
            document.getElementById('stat-total-remaining').textContent = formatCurrency(stats.total_remaining);
            document.getElementById('stat-pending-count').textContent = stats.pending_count;
        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    };

    // Fetch and Render Loans
    const fetchLoans = async () => {
        try {
            const response = await fetch('api.php?action=list');
            allLoans = await response.json();
            renderLoans(allLoans);
        } catch (error) {
            toastr.error('Failed to load loans');
        }
    };

    const renderLoans = (loans) => {
        loansTbody.innerHTML = loans.map(loan => {
            const statusBadge = `<span class="badge badge-${loan.status.toLowerCase()}">${loan.status}</span>`;
            const statusToggle = loan.status === 'Pending' ? `
                <button class="action-btn" onclick="updateStatus(${loan.id}, 'Paid')" title="Mark as Paid">
                    <i class="fas fa-check-circle"></i>
                </button>
            ` : `
                <button class="action-btn" onclick="updateStatus(${loan.id}, 'Pending')" title="Mark as Pending" style="color: #f39c12;">
                    <i class="fas fa-clock-rotate-left"></i>
                </button>
            `;

            return `
                <tr>
                    <td>
                        <div style="font-weight: 600;">${loan.borrower_name}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">${new Date(loan.created_at).toLocaleDateString()}</div>
                    </td>
                    <td style="font-size: 0.9rem; color: var(--text-muted);">${loan.lender_name || 'N/A'}</td>
                    <td>${formatCurrency(loan.amount)}</td>
                    <td>${loan.percentage}%</td>
                    <td style="font-weight: 700;">${formatCurrency(loan.total_repayment)}</td>
                    <td style="font-weight: 700; color: ${loan.status === 'Paid' ? 'var(--text-muted)' : 'var(--secondary)'};">
                        ${formatCurrency(parseFloat(loan.total_repayment) - parseFloat(loan.amount_paid || 0))}
                    </td>
                    <td>${loan.payment_date}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="actions">
                            ${statusToggle}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    };

    // Form Submission
    loanForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(loanForm);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('api.php?action=add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.success) {
                toastr.success(result.message);
                loanForm.reset();
                calculateTotals();
                fetchLoans();
                fetchStats();
            }
        } catch (error) {
            toastr.error('Error saving loan');
        }
    });

    // Global Functions for Actions
    window.updateStatus = async (id, status) => {
        try {
            const response = await fetch('api.php?action=update_status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, status })
            });
            if (response.ok) {
                toastr.success(`Marked as ${status}`);
                fetchLoans();
                fetchStats();
            }
        } catch (error) {
            toastr.error('Update failed');
        }
    };

    window.deleteLoan = async (id) => {
        if (!confirm('Are you sure you want to delete this record?')) return;
        try {
            const response = await fetch(`api.php?action=delete&id=${id}`);
            if (response.ok) {
                toastr.info('Record deleted');
                fetchLoans();
                fetchStats();
            }
        } catch (error) {
            toastr.error('Delete failed');
        }
    };

    // Search logic
    searchInput.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        const filtered = allLoans.filter(loan => 
            loan.borrower_name.toLowerCase().includes(term)
        );
        renderLoans(filtered);
    });

    // CSV Export
    exportBtn.addEventListener('click', () => {
        if (allLoans.length === 0) return toastr.warning('No data to export');
        
        const headers = ['Borrower', 'Lender', 'Principal', 'Interest%', 'Total Repayment', 'Remaining', 'Due Date', 'Status'];
        const rows = allLoans.map(l => [
            l.borrower_name, l.lender_name, l.amount, l.percentage, l.total_repayment, 
            (parseFloat(l.total_repayment) - parseFloat(l.amount_paid || 0)),
            l.payment_date, l.status
        ]);

        let csvContent = "data:text/csv;charset=utf-8," 
            + headers.join(",") + "\n"
            + rows.map(e => e.join(",")).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        const dateStr = new Date().toISOString().split('T')[0];
        const fileName = "moola_export_" + dateStr + ".csv";
        
        link.setAttribute("download", fileName);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        toastr.success('Exported successfully');
    });

    // Initial Load
    fetchLoans();
    fetchStats();
});
