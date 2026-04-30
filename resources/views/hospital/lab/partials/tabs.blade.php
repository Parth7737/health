<div class="tabs-bar" style="padding:0 18px;margin-bottom:0" id="labTabBar">
    <button class="tab-btn active" data-lab-tab-target="sampleQueuePane" type="button" onclick="switchLabTab('sampleQueuePane', this)">🧫 Sample Queue <span class="tab-count" id="labTabCountQueue">0</span></button>
    <button class="tab-btn" data-lab-tab-target="urgentPane" type="button" onclick="switchLabTab('urgentPane', this)">🚨 Urgent/STAT <span class="tab-count" id="labTabCountUrgent">0</span></button>
    <button class="tab-btn" data-lab-tab-target="resultEntryPane" type="button" onclick="switchLabTab('resultEntryPane', this)">📊 Result Entry</button>
    <button class="tab-btn" data-lab-tab-target="criticalPane" type="button" onclick="switchLabTab('criticalPane', this)">⚠️ Critical Values <span class="tab-count" id="labTabCountCritical" style="background:var(--danger-light);color:var(--danger)">0</span></button>
    <button class="tab-btn" data-lab-tab-target="reportsPane" type="button" onclick="switchLabTab('reportsPane', this)">📄 Reports</button>
    <button class="tab-btn" data-lab-tab-target="tatPane" type="button" onclick="switchLabTab('tatPane', this)">⏱️ TAT Analytics</button>
    <button class="tab-btn" data-lab-tab-target="analyzerPane" type="button" onclick="switchLabTab('analyzerPane', this)">🔬 Analyzer Config</button>
</div>
