<div class="stats-grid mb-20">
    <div class="stat-card stat-blue">
        <div class="stat-icon">🧫</div>
        <div class="stat-info">
            <div class="stat-value lab-kpi-value loading" id="labKpiTotal">0</div>
            <div class="stat-label">Samples In Queue</div>
            <div class="lab-stat-note">Live from pathology worklist</div>
        </div>
    </div>
    <div class="stat-card stat-red">
        <div class="stat-icon">🚨</div>
        <div class="stat-info">
            <div class="stat-value lab-kpi-value loading" id="labKpiUrgent">0</div>
            <div class="stat-label">Urgent / STAT</div>
            <div class="lab-stat-note">Priority flagged lab orders</div>
        </div>
    </div>
    <div class="stat-card stat-orange">
        <div class="stat-icon">⚠️</div>
        <div class="stat-info">
            <div class="stat-value lab-kpi-value loading" id="labKpiCritical">0</div>
            <div class="stat-label">Critical Values</div>
            <div class="lab-stat-note">Derived from flagged reports</div>
        </div>
    </div>
    <div class="stat-card stat-green">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
            <div class="stat-value lab-kpi-value loading" id="labKpiCompleted">0</div>
            <div class="stat-label">Completed</div>
            <div class="lab-stat-note">Status = completed</div>
        </div>
    </div>
    <div class="stat-card stat-teal">
        <div class="stat-icon">⏱️</div>
        <div class="stat-info">
            <div class="stat-value" id="labKpiTat">--</div>
            <div class="stat-label">Avg TAT</div>
            <div class="lab-stat-note">Calculated from listed rows</div>
        </div>
    </div>
    <div class="stat-card stat-purple">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
            <div class="stat-value" id="labKpiQuality">--</div>
            <div class="stat-label">Quality Score</div>
            <div class="lab-stat-note">Completed/Total ratio</div>
        </div>
    </div>
</div>
