<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoolaTracker | Money Lending Dashboard</title>
    
    <!-- External Resources -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <h1>MoolaTracker</h1>
                <p style="color: var(--text-muted); font-size: 0.8rem;">Manage your loans with precision</p>
            </div>
            <div class="header-actions">
                <span id="current-date" style="color: var(--text-muted); font-size: 0.9rem;"></span>
            </div>
        </header>

        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Amount Lent</h3>
                <div class="value" id="stat-total-lent">0 rwf</div>
            </div>
            <div class="stat-card">
                <h3>Expected Returns</h3>
                <div class="value" id="stat-expected-return" style="color: var(--secondary);">0 rwf</div>
            </div>
            <div class="stat-card">
                <h3>Total Remaining</h3>
                <div class="value" id="stat-total-remaining" style="color: #f59e0b;">0 rwf</div>
            </div>
            <div class="stat-card">
                <h3>Pending Collections</h3>
                <div class="value" id="stat-pending-count">0</div>
            </div>
        </div>

        <div class="main-grid">
            <!-- Loan Entry Form -->
            <aside>
                <div class="form-card">
                    <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">New Loan Request</h2>
                    <form id="loan-form">
                        <div class="form-group">
                            <label>Borrower Name</label>
                            <input type="text" name="borrower_name" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label>Lender Name</label>
                            <input type="text" name="lender_name" placeholder="Me / Bank Name" required>
                        </div>
                        <div class="form-group">
                            <label>Principal Amount (rwf)</label>
                            <input type="number" name="amount" id="input-amount" placeholder="1000" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Interest Percentage (%)</label>
                            <input type="number" name="percentage" id="input-percentage" placeholder="10" step="0.1" required>
                        </div>
                        <div class="form-group">
                            <label>Request Date</label>
                            <input type="date" name="request_date" required>
                        </div>
                        <div class="form-group">
                            <label>Payment Due Date</label>
                            <input type="date" name="payment_date" required>
                        </div>
                        
                        <div class="calculation-preview" style="background: var(--glass); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-muted);">
                                <span>Interest:</span>
                                <span id="preview-interest">0 rwf</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-weight: 700; margin-top: 0.5rem;">
                                <span>Total to Pay:</span>
                                <span id="preview-total">0 rwf</span>
                            </div>
                        </div>

                        <input type="hidden" name="total_repayment" id="input-total-repayment">
                        <button type="submit" class="btn btn-primary">Record Loan</button>
                    </form>
                </div>
            </aside>

            <!-- Loans List -->
            <main>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div style="position: relative; width: 300px;">
                        <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                        <input type="text" id="table-search" placeholder="Search borrowers..." style="padding-left: 2.5rem;">
                    </div>
                    <button id="export-csv" class="btn" style="width: auto; background: var(--glass); border: 1px solid var(--border); color: white;">
                        <i class="fas fa-file-export" style="margin-right: 0.5rem;"></i> Export CSV
                    </button>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Borrower</th>
                                <th>Lender</th>
                                <th>Principal</th>
                                <th>Interest (%)</th>
                                <th>Total Due</th>
                                <th>Remaining</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="loans-tbody">
                            <!-- Loaded via JS -->
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
