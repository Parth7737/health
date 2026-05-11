<div id="hrx-panel-payroll">
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fas fa-money-check-alt" style="color:#4a148c"></i> Payroll Processing - {{ $payrollMonthLabel ?? now()->format('F Y') }}</span>
                <div class="hrx-pay-actions">
                    <button type="button" class="hrx-pay-btn success" id="hrxPayrollProcess"><i class="fas fa-play"></i> Process Payroll</button>
                    <button type="button" class="hrx-pay-btn outline" id="hrxPayrollExport"><i class="fas fa-download"></i> Export</button>
                    <button type="button" class="hrx-pay-btn outline" id="hrxPayrollBulkEmail"><i class="fas fa-envelope"></i> Bulk Email</button>
                    <button type="button" class="hrx-pay-btn outline" id="hrxPayrollBulkPaid"><i class="fas fa-check-double"></i> Mark Selected Paid</button>
                    <span id="hrxPayrollSelectedCount" class="hrx-selected-chip" aria-live="polite" style="display:none">Selected: 0</span>
                </div>
            </div>
            <div class="card-body" style="padding:0">
                <div class="card-header" style="border-bottom:1px solid #eef3f8">
                    <div class="hrx-pay-filters">
                        <input type="month" id="hrxPayrollMonth" class="hrx-pay-input" value="{{ now()->subMonth()->format('Y-m') }}" max="{{ now()->subMonth()->format('Y-m') }}">
                        <input type="text" id="hrxPayrollSearch" class="hrx-pay-input" placeholder="Search staff or employee ID">
                        <select id="hrxPayrollStatus" class="hrx-pay-select">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="generated">Generated</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                </div>
                <div class="hrx-table-wrap">
                    <table id="hrxPayrollTable" class="hrx-table">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="hrxPayrollSelectAll"></th>
                            <th>Emp ID</th>
                            <th>Slip No</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Basic</th>
                            <th>Allowances</th>
                            <th>Deductions</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="hrx-payslip-shell" id="hrxPayslipShell">
        <div class="hrx-payslip-empty" id="hrxPayslipEmpty">
            <i class="fas fa-file-invoice-dollar"></i>
            <h4>Select Payslip</h4>
        </div>
        <div id="hrxPayslipContainer" style="display:none"></div>
    </div>
</div>
