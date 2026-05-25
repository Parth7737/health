@extends('layouts.state-admin.dashboard.app')
@section('title', 'State Health Command Centre')
@section('content')
<!-- ══════════ OVERVIEW TAB ══════════ -->
<div class="tab-panel active" id="tab-overview">

    <!-- Alert Ticker -->
    <div class="alert-ticker">
        <i class="fas fa-bell at-icon"></i>
        <div class="at-scroll">
            <div class="at-inner">
                <span class="at-item"><span class="at-tag">ALERT</span>Dengue cases surge +34% in Haridwar —
                    47 new cases this week</span>
                <span class="at-item"><span class="at-tag">CRITICAL</span>Blood O− stock critically low in 3
                    facilities: AIIMS Rishikesh, Doon Hospital, Haldwani</span>
                <span class="at-item"><span class="at-tag">ALERT</span>Chamoli CHC oxygen supply below 20% —
                    resupply scheduled 14 Apr</span>
                <span class="at-item"><span class="at-tag">INFO</span>Ayushman Bharat claims pending >30
                    days: 1,247 cases requiring SHA review</span>
                <span class="at-item"><span class="at-tag">ALERT</span>Dengue cases surge +34% in Haridwar —
                    47 new cases this week</span>
                <span class="at-item"><span class="at-tag">CRITICAL</span>Blood O− stock critically low in 3
                    facilities</span>
                <span class="at-item"><span class="at-tag">INFO</span>Ayushman Bharat claims pending >30
                    days: 1,247 cases</span>
            </div>
        </div>
    </div>

    <!-- KPI Row 1 -->
    <div class="kpi-grid">
        <div class="kpi-card sky">
            <div class="k-label">Total Facilities</div>
            <div class="k-value">1,847</div>
            <div class="k-trend"><span class="up">↑ 23</span>&nbsp;onboarded this year</div><i
                class="fas fa-hospital k-icon"></i>
        </div>
        <div class="kpi-card teal">
            <div class="k-label">OPD Today (State)</div>
            <div class="k-value">68,241</div>
            <div class="k-trend"><span class="up">↑ 4.2%</span>&nbsp;vs yesterday</div><i
                class="fas fa-user-md k-icon"></i>
        </div>
        <div class="kpi-card green">
            <div class="k-label">IPD Admissions</div>
            <div class="k-value">12,847</div>
            <div class="k-trend"><span class="neutral">Bed occupancy 74%</span></div><i class="fas fa-bed k-icon"></i>
        </div>
        <div class="kpi-card orange">
            <div class="k-label">Emergencies (24h)</div>
            <div class="k-value">3,429</div>
            <div class="k-trend"><span class="down">↑ 8.1%</span>&nbsp;vs last week</div><i
                class="fas fa-ambulance k-icon"></i>
        </div>
        <div class="kpi-card gold">
            <div class="k-label">Revenue (MTD)</div>
            <div class="k-value">₹42.7Cr</div>
            <div class="k-trend"><span class="up">↑ 12%</span>&nbsp;vs last month</div><i
                class="fas fa-rupee-sign k-icon"></i>
        </div>
        <div class="kpi-card purple">
            <div class="k-label">AB Beneficiaries</div>
            <div class="k-value">8,341</div>
            <div class="k-trend"><span class="neutral">This month</span></div><i
                class="fas fa-hospital-user k-icon"></i>
        </div>
        <div class="kpi-card red">
            <div class="k-label">Maternal Deaths (MTD)</div>
            <div class="k-value">4</div>
            <div class="k-trend"><span class="down">↓ 3</span>&nbsp;vs last month</div><i
                class="fas fa-heart k-icon"></i>
        </div>
        <div class="kpi-card cyan">
            <div class="k-label">Active Doctors</div>
            <div class="k-value">4,218</div>
            <div class="k-trend"><span class="neutral">On duty today</span></div><i class="fas fa-user-md k-icon"></i>
        </div>
        <div class="kpi-card pink">
            <div class="k-label">108 Calls (24h)</div>
            <div class="k-value">2,147</div>
            <div class="k-trend"><span class="neutral">Avg resp: 11.4 min</span></div><i
                class="fas fa-phone k-icon"></i>
        </div>
        <div class="kpi-card teal">
            <div class="k-label">Lab Tests (24h)</div>
            <div class="k-value">31,480</div>
            <div class="k-trend"><span class="up">↑ 6.3%</span>&nbsp;vs yesterday</div><i
                class="fas fa-flask k-icon"></i>
        </div>
        <div class="kpi-card sky">
            <div class="k-label">Deliveries (MTD)</div>
            <div class="k-value">3,847</div>
            <div class="k-trend"><span class="up">Inst. delivery 87%</span></div><i class="fas fa-baby k-icon"></i>
        </div>
        <div class="kpi-card gold">
            <div class="k-label">AB Claims (Pending)</div>
            <div class="k-value">1,247</div>
            <div class="k-trend"><span class="down">₹18.4Cr</span>&nbsp;value</div><i
                class="fas fa-file-invoice k-icon"></i>
        </div>
    </div>

    <!-- Chart Row 1 -->
    <div class="chart-grid-3">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-line" style="color:#60a5fa"></i>State OPD
                        Trend (12 Months)</div>
                    <div class="cc-sub">Total outpatient visits across all facilities</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="opdTrendChart" height="200"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-bed" style="color:#4db6ac"></i>Bed Occupancy by
                        District</div>
                    <div class="cc-sub">% occupancy — all facility types</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="bedOccChart" height="200"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-rupee-sign" style="color:#ffca28"></i>Revenue by
                        Facility Type</div>
                    <div class="cc-sub">Monthly revenue distribution</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="revTypeChart" height="200"></canvas></div>
        </div>
    </div>

    <!-- Chart Row 2 -->
    <div class="chart-grid-5050">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-sun" style="color:#ffca28"></i>Health Budget —
                        Sunburst Breakdown</div>
                    <div class="cc-sub">State health expenditure drill-down by category → programme → item
                    </div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="sunburstChart" style="height:360px"></div>
            </div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-pie" style="color:#ce93d8"></i>Disease
                        Burden Distribution</div>
                    <div class="cc-sub">Top 10 disease categories — state-wide OPD diagnosis</div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="diseaseChart" style="height:360px"></div>
            </div>
        </div>
    </div>

    <!-- Chart Row 3 -->
    <div class="chart-grid-4">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-baby" style="color:#f48fb1"></i>MCH Indicators
                    </div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="mchRadar" style="height:220px"></div>
            </div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-syringe" style="color:#81c784"></i>Immunisation
                        Coverage</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="immChart" height="220"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-ambulance" style="color:#ffb74d"></i>108 Response
                        Time</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="ambChart" height="220"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-hospital-user" style="color:#60a5fa"></i>AB Claim
                        Status</div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="abPieChart" style="height:220px"></div>
            </div>
        </div>
    </div>

    <!-- Chart Row 4 -->
    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-bar" style="color:#4dd0e1"></i>Top
                        Performing Districts — OPD Load</div>
                    <div class="cc-sub">Ranked by total outpatient visits this month</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="distOpdChart" height="260"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-area" style="color:#f48fb1"></i>Maternal &
                        Child Health Trend</div>
                    <div class="cc-sub">ANC, deliveries, immunisation — 12-month</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="mchTrendChart" height="260"></canvas></div>
        </div>
    </div>

</div><!-- /overview -->

<!-- ══════════ MAP TAB ══════════ -->
<div class="tab-panel" id="tab-map">
    <div class="section-hdr">
        <div class="section-title">State Health Heatmap — Facility Distribution & Performance</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <select class="filter-select" onchange="updateMapLayer(this.value)">
                <option value="facilities">All Facilities</option>
                <option value="opd">OPD Load</option>
                <option value="bed">Bed Occupancy</option>
                <option value="disease">Disease Burden</option>
                <option value="amb">Ambulance Coverage</option>
            </select>
            <button class="tb-btn" onclick="toggleHeatmap()"><i class="fas fa-fire"></i> Heatmap</button>
            <button class="tb-btn" onclick="fitBounds()"><i class="fas fa-compress-arrows-alt"></i> Reset
                View</button>
        </div>
    </div>
    <div class="map-card">
        <div class="mc-hdr">
            <div class="mc-title"><i class="fas fa-map-marked-alt" style="color:#60a5fa"></i>Uttarakhand —
                13 Districts · 1,847 Facilities</div>
            <div class="map-legend">
                <div class="ml-item">
                    <div class="ml-dot" style="background:#ef5350"></div>Medical College
                </div>
                <div class="ml-item">
                    <div class="ml-dot" style="background:#42a5f5"></div>District Hospital
                </div>
                <div class="ml-item">
                    <div class="ml-dot" style="background:#26a69a"></div>CHC
                </div>
                <div class="ml-item">
                    <div class="ml-dot" style="background:#66bb6a"></div>PHC
                </div>
                <div class="ml-item">
                    <div class="ml-dot" style="background:#ffca28"></div>Sub-Centre
                </div>
            </div>
        </div>
        <div id="stateMap"></div>
    </div>
</div>

<!-- ══════════ DISTRICT SCORECARD TAB ══════════ -->
<div class="tab-panel" id="tab-districts">
    <div class="section-hdr">
        <div class="section-title">District-wise Performance Scorecard</div>
        <div style="display:flex;gap:8px">
            <select class="filter-select">
                <option>Sort by: OPD Volume</option>
                <option>Sort by: Bed Occupancy</option>
                <option>Sort by: AB Claims</option>
                <option>Sort by: MMR</option>
            </select>
            <button class="tb-btn primary"><i class="fas fa-download"></i> Export CSV</button>
        </div>
    </div>
    <div class="chart-card" style="margin-bottom:16px">
        <div class="dist-row dist-hdr">
            <div>District</div>
            <div>Facilities</div>
            <div>OPD/day</div>
            <div>Bed Occ%</div>
            <div>MMR</div>
            <div>AB Claims</div>
            <div>Score</div>
        </div>
        <div id="districtRows"></div>
    </div>
    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-bar" style="color:#60a5fa"></i>District OPD
                        Comparison</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="distCompChart" height="280"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-scatter" style="color:#f48fb1"></i>Bed
                        Occupancy vs Revenue</div>
                    <div class="cc-sub">Bubble size = AB beneficiary count</div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="scatterChart" style="height:280px"></div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ OPD/IPD TAB ══════════ -->
<div class="tab-panel" id="tab-opd">
    <div class="mini-stat-row">
        <div class="mini-stat">
            <div class="ms-v">68,241</div>
            <div class="ms-l">OPD Today</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">12,847</div>
            <div class="ms-l">IPD Active</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">74%</div>
            <div class="ms-l">Bed Occupancy</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">4.2d</div>
            <div class="ms-l">Avg LOS</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">3,429</div>
            <div class="ms-l">Emergencies (24h)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">842</div>
            <div class="ms-l">ICU Beds Used</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">18.4 min</div>
            <div class="ms-l">Avg Wait (OPD)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">2,341</div>
            <div class="ms-l">Discharges Today</div>
        </div>
    </div>
    <div class="chart-grid-3">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-bar" style="color:#60a5fa"></i>OPD by
                        Department</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="opdDeptChart" height="220"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-line" style="color:#4db6ac"></i>Admission
                        Trend (30 days)</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="admTrendChart" height="220"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-pie" style="color:#ce93d8"></i>Discharge
                        Type Split</div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="dischargePie" style="height:220px"></div>
            </div>
        </div>
    </div>
    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-bed" style="color:#81c784"></i>Bed Occupancy by
                        Ward Type</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="bedWardChart" height="260"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-clock" style="color:#ffca28"></i>Length of Stay
                        Distribution</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="losChart" height="260"></canvas></div>
        </div>
    </div>
</div>

<!-- ══════════ DISEASE TAB ══════════ -->
<div class="tab-panel" id="tab-disease">
    <div class="mini-stat-row">
        <div class="mini-stat">
            <div class="ms-v" style="color:#ef9a9a">47</div>
            <div class="ms-l">Dengue New (7d)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#ffb74d">23</div>
            <div class="ms-l">Malaria New (7d)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#f48fb1">8</div>
            <div class="ms-l">Tuberculosis New</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#81c784">142</div>
            <div class="ms-l">Hypertension (Active)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#4db6ac">96</div>
            <div class="ms-l">Diabetes New (MTD)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#ce93d8">12</div>
            <div class="ms-l">Cancer (Referred)</div>
        </div>
    </div>
    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-virus" style="color:#ef9a9a"></i>Communicable
                        Disease Trend</div>
                    <div class="cc-sub">Weekly new cases — last 12 weeks</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="diseaseLineChart" height="260"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-sun" style="color:#ffca28"></i>Disease Burden
                        Sunburst</div>
                    <div class="cc-sub">Category → Disease → Severity drill-down</div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="diseaseSunburst" style="height:260px"></div>
            </div>
        </div>
    </div>
    <div class="chart-grid-3">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-map" style="color:#4dd0e1"></i>District Disease
                        Heatmap</div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="districtHeatmap" style="height:260px"></div>
            </div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-bar" style="color:#60a5fa"></i>NCD vs
                        Communicable</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="ncdChart" height="260"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-area" style="color:#f48fb1"></i>Under-5
                        Morbidity</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="childMorbChart" height="260"></canvas></div>
        </div>
    </div>
</div>

<!-- ══════════ MCH TAB ══════════ -->
<div class="tab-panel" id="tab-mch">
    <div class="mini-stat-row">
        <div class="mini-stat">
            <div class="ms-v">3,847</div>
            <div class="ms-l">Deliveries (MTD)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">87%</div>
            <div class="ms-l">Institutional Delivery</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">4</div>
            <div class="ms-l">Maternal Deaths (MTD)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">62</div>
            <div class="ms-l">MMR (per 100k LB)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">18</div>
            <div class="ms-l">IMR (per 1000 LB)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">94%</div>
            <div class="ms-l">ANC 4+ Coverage</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">91%</div>
            <div class="ms-l">Full Immunisation</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">28%</div>
            <div class="ms-l">C-Section Rate</div>
        </div>
    </div>
    <div class="chart-grid-3">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-radar" style="color:#f48fb1"></i>MCH Radar
                        Dashboard</div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="mchRadarBig" style="height:280px"></div>
            </div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-line" style="color:#81c784"></i>Delivery
                        Trend (12 Months)</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="delivTrendChart" height="280"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-syringe" style="color:#4db6ac"></i>Immunisation
                        Coverage by Vaccine</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="vaccChart" height="280"></canvas></div>
        </div>
    </div>
</div>

<!-- ══════════ REVENUE TAB ══════════ -->
<div class="tab-panel" id="tab-revenue">
    <div class="ab-summary">
        <div class="ab-row">
            <div class="ab-cell">
                <div class="av">₹42.7Cr</div>
                <div class="al">Revenue MTD</div>
            </div>
            <div class="ab-cell">
                <div class="av">₹18.4Cr</div>
                <div class="al">AB Claims Pending</div>
            </div>
            <div class="ab-cell">
                <div class="av">₹9.2Cr</div>
                <div class="al">CGHS/ESI (MTD)</div>
            </div>
            <div class="ab-cell">
                <div class="av">₹6.1Cr</div>
                <div class="al">OPD Collections</div>
            </div>
            <div class="ab-cell">
                <div class="av">₹8.9Cr</div>
                <div class="al">IPD Collections</div>
            </div>
        </div>
    </div>
    <div class="chart-grid-3">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-bar" style="color:#ffca28"></i>Revenue by
                        District (MTD)</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="revDistChart" height="260"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-line" style="color:#60a5fa"></i>Revenue
                        Trend (12 Months)</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="revTrendChart" height="260"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-pie" style="color:#4db6ac"></i>Revenue Mix
                        by Payer</div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="payerMixChart" style="height:260px"></div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ AB TAB ══════════ -->
<div class="tab-panel" id="tab-ab">
    <div class="section-hdr">
        <div class="section-title">Ayushman Bharat — State-wide Performance</div>
        <a href="ab-claims.html" class="tb-btn primary"><i class="fas fa-external-link-alt"></i> Open AB
            Claims Portal</a>
    </div>
    <div class="mini-stat-row">
        <div class="mini-stat">
            <div class="ms-v" style="color:#60a5fa">8,341</div>
            <div class="ms-l">Beneficiaries (MTD)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#81c784">6,847</div>
            <div class="ms-l">Claims Approved</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#ffb74d">1,247</div>
            <div class="ms-l">Claims Pending</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#ef9a9a">247</div>
            <div class="ms-l">Claims Rejected</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#ffca28">₹61.2Cr</div>
            <div class="ms-l">Claim Value (MTD)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#4db6ac">₹48.7Cr</div>
            <div class="ms-l">Paid by SHA</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#ce93d8">5.2d</div>
            <div class="ms-l">Avg TAT (Claim)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v" style="color:#f48fb1">94.2%</div>
            <div class="ms-l">Eligibility Hit Rate</div>
        </div>
    </div>
    <div class="chart-grid-2">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-bar" style="color:#60a5fa"></i>AB Claims by
                        District</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="abDistChart" height="280"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-sun" style="color:#ffca28"></i>AB Expenditure by
                        Package Category</div>
                    <div class="cc-sub">Drill-down: Specialty → Package → Procedure</div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="abSunburst" style="height:280px"></div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ FACILITIES TAB ══════════ -->
<div class="tab-panel" id="tab-facilities">
    <div class="section-hdr">
        <div class="section-title">Facility Register — All 1,847 Facilities</div>
        <div style="display:flex;gap:8px">
            <input type="text" placeholder="Search facility..." class="filter-select" style="width:200px">
            <a href="onboarding.html" class="tb-btn primary"><i class="fas fa-plus"></i> Onboard New</a>
        </div>
    </div>
    <div class="mini-stat-row">
        <div class="mini-stat">
            <div class="ms-v">12</div>
            <div class="ms-l">Medical Colleges</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">13</div>
            <div class="ms-l">District Hospitals</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">89</div>
            <div class="ms-l">CHCs</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">348</div>
            <div class="ms-l">PHCs</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">1,385</div>
            <div class="ms-l">Sub-Centres</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">96.2%</div>
            <div class="ms-l">HMIS Connected</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">83%</div>
            <div class="ms-l">AB Empanelled</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">23</div>
            <div class="ms-l">Onboarded This Year</div>
        </div>
    </div>
    <div class="chart-card">
        <div class="cc-hdr">
            <div>
                <div class="cc-title"><i class="fas fa-table" style="color:#60a5fa"></i>Facility Directory
                    (Top 25)</div>
            </div>
        </div>
        <div class="cc-body">
            <div class="data-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Facility Name</th>
                            <th>Type</th>
                            <th>District</th>
                            <th>Beds</th>
                            <th>Doctors</th>
                            <th>OPD/Day</th>
                            <th>AB Status</th>
                            <th>HMIS</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="facilityTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ HR TAB ══════════ -->
<div class="tab-panel" id="tab-hr">
    <div class="mini-stat-row">
        <div class="mini-stat">
            <div class="ms-v">24,847</div>
            <div class="ms-l">Total Staff</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">4,218</div>
            <div class="ms-l">Doctors</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">8,341</div>
            <div class="ms-l">Nurses/ANM</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">1,847</div>
            <div class="ms-l">Para-Medical</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">1,241</div>
            <div class="ms-l">Vacancies</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">5.1%</div>
            <div class="ms-l">Absenteeism Rate</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">₹84.2Cr</div>
            <div class="ms-l">Payroll (MTD)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">187</div>
            <div class="ms-l">On Training</div>
        </div>
    </div>
    <div class="chart-grid-3">
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-users" style="color:#60a5fa"></i>Staff by
                        Category</div>
                </div>
            </div>
            <div class="cc-body">
                <div class="echart-box" id="staffPie" style="height:240px"></div>
            </div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-chart-bar" style="color:#81c784"></i>Vacancy by
                        Department</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="vacancyChart" height="240"></canvas></div>
        </div>
        <div class="chart-card">
            <div class="cc-hdr">
                <div>
                    <div class="cc-title"><i class="fas fa-map-marker" style="color:#ffb74d"></i>Staff
                        Distribution by District</div>
                </div>
            </div>
            <div class="cc-body"><canvas id="staffDistChart" height="240"></canvas></div>
        </div>
    </div>
</div>

<!-- ══════════ AMBULANCE TAB ══════════ -->
<div class="tab-panel" id="tab-ambulance">
    <div class="mini-stat-row">
        <div class="mini-stat">
            <div class="ms-v">847</div>
            <div class="ms-l">Total Ambulances</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">641</div>
            <div class="ms-l">Active / Online</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">2,147</div>
            <div class="ms-l">Calls (24h)</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">11.4 min</div>
            <div class="ms-l">Avg Response</div>
        </div>
        <div class="mini-stat">
            <div class="ms-v">94%</div>
            <div class="ms-l">On-time (<15 min)</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">47</div>
                <div class="ms-l">Under Maintenance</div>
            </div>
        </div>
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="cc-hdr">
                    <div>
                        <div class="cc-title"><i class="fas fa-chart-bar" style="color:#ffb74d"></i>Response
                            Time by District</div>
                    </div>
                </div>
                <div class="cc-body"><canvas id="ambDistChart" height="280"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="cc-hdr">
                    <div>
                        <div class="cc-title"><i class="fas fa-chart-line" style="color:#60a5fa"></i>108
                            Call Volume Trend</div>
                    </div>
                </div>
                <div class="cc-body"><canvas id="ambTrendChart" height="280"></canvas></div>
            </div>
        </div>
    </div>

    <!-- ══════════ INVENTORY TAB ══════════ -->
    <div class="tab-panel" id="tab-inventory">
        <div class="mini-stat-row">
            <div class="mini-stat">
                <div class="ms-v">1,241</div>
                <div class="ms-l">Items Tracked</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v" style="color:#ef9a9a">87</div>
                <div class="ms-l">Critical Stock</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v" style="color:#ffb74d">142</div>
                <div class="ms-l">Low Stock</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v" style="color:#ffca28">234</div>
                <div class="ms-l">Expiring in 30d</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">₹18.4Cr</div>
                <div class="ms-l">Stock Value</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">47</div>
                <div class="ms-l">Pending POs</div>
            </div>
        </div>
        <div class="chart-grid-3">
            <div class="chart-card">
                <div class="cc-hdr">
                    <div>
                        <div class="cc-title"><i class="fas fa-chart-pie" style="color:#60a5fa"></i>Stock
                            Value by Category</div>
                    </div>
                </div>
                <div class="cc-body">
                    <div class="echart-box" id="stockPie" style="height:240px"></div>
                </div>
            </div>
            <div class="chart-card">
                <div class="cc-hdr">
                    <div>
                        <div class="cc-title"><i class="fas fa-chart-bar" style="color:#ef9a9a"></i>Critical
                            Drug Shortage</div>
                    </div>
                </div>
                <div class="cc-body"><canvas id="drugShortChart" height="240"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="cc-hdr">
                    <div>
                        <div class="cc-title"><i class="fas fa-chart-line" style="color:#81c784"></i>Consumption Trend
                        </div>
                    </div>
                </div>
                <div class="cc-body"><canvas id="consumptionChart" height="240"></canvas></div>
            </div>
        </div>
    </div>

    <!-- ══════════ LAB TAB ══════════ -->
    <div class="tab-panel" id="tab-lab">
        <div class="mini-stat-row">
            <div class="mini-stat">
                <div class="ms-v">31,480</div>
                <div class="ms-l">Tests (24h)</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">94.7%</div>
                <div class="ms-l">TAT Compliance</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">847</div>
                <div class="ms-l">Critical Values</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">2.4h</div>
                <div class="ms-l">Avg TAT</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">98.1%</div>
                <div class="ms-l">Quality Pass Rate</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">12</div>
                <div class="ms-l">NABL Labs</div>
            </div>
        </div>
        <div class="chart-grid-2">
            <div class="chart-card">
                <div class="cc-hdr">
                    <div>
                        <div class="cc-title"><i class="fas fa-chart-bar" style="color:#4dd0e1"></i>Test
                            Volume by Category</div>
                    </div>
                </div>
                <div class="cc-body"><canvas id="labCatChart" height="280"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="cc-hdr">
                    <div>
                        <div class="cc-title"><i class="fas fa-chart-line" style="color:#60a5fa"></i>TAT
                            Performance Trend</div>
                    </div>
                </div>
                <div class="cc-body"><canvas id="tatChart" height="280"></canvas></div>
            </div>
        </div>
    </div>

    <!-- ══════════ PHARMA TAB ══════════ -->
    <div class="tab-panel" id="tab-pharma">
        <div class="mini-stat-row">
            <div class="mini-stat">
                <div class="ms-v">84,241</div>
                <div class="ms-l">Prescriptions (MTD)</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">96.8%</div>
                <div class="ms-l">Generic Rx Rate</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">₹4.2Cr</div>
                <div class="ms-l">Drug Cost (MTD)</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">234</div>
                <div class="ms-l">Near-Expiry Items</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">12</div>
                <div class="ms-l">Adverse Drug Events</div>
            </div>
            <div class="mini-stat">
                <div class="ms-v">87%</div>
                <div class="ms-l">BPL Free Medicines</div>
            </div>
        </div>
        <div class="chart-grid-3">
            <div class="chart-card">
                <div class="cc-hdr">
                    <div>
                        <div class="cc-title"><i class="fas fa-pills" style="color:#ce93d8"></i>Top 10 Drugs
                            Dispensed</div>
                    </div>
                </div>
                <div class="cc-body"><canvas id="topDrugChart" height="240"></canvas></div>
            </div>
            <div class="chart-card">
                <div class="cc-hdr">
                    <div>
                        <div class="cc-title"><i class="fas fa-chart-pie" style="color:#60a5fa"></i>Prescription Type
                            Split</div>
                    </div>
                </div>
                <div class="cc-body">
                    <div class="echart-box" id="rxPie" style="height:240px"></div>
                </div>
            </div>
            <div class="chart-card">
                <div class="cc-hdr">
                    <div>
                        <div class="cc-title"><i class="fas fa-chart-line" style="color:#81c784"></i>Drug
                            Consumption Trend</div>
                    </div>
                </div>
                <div class="cc-body"><canvas id="drugTrendChart" height="240"></canvas></div>
            </div>
        </div>
    </div>



    @endsection
    @push('scripts')
    <script>
    // ═══════════════════════════════════════
    //  DATA
    // ═══════════════════════════════════════
    const DISTRICTS = ['Dehradun', 'Haridwar', 'Nainital', 'US Nagar', 'Almora', 'Chamoli', 'Champawat',
        'Bageshwar', 'Pithoragarh', 'Rudraprayag', 'Tehri', 'Uttarkashi', 'Pauri'
    ];
    const MONTHS = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];

    const DIST_DATA = [{
            name: 'Dehradun',
            dist: 'Dehradun',
            fac: 187,
            opd: 14200,
            bed: 76,
            mmr: 58,
            ab: 1420,
            score: 91
        },
        {
            name: 'Haridwar',
            dist: 'Haridwar',
            fac: 142,
            opd: 11800,
            bed: 71,
            mmr: 64,
            ab: 1180,
            score: 85
        },
        {
            name: 'Nainital',
            dist: 'Nainital',
            fac: 128,
            opd: 9200,
            bed: 68,
            mmr: 71,
            ab: 920,
            score: 82
        },
        {
            name: 'US Nagar',
            dist: 'US Nagar',
            fac: 154,
            opd: 10400,
            bed: 65,
            mmr: 79,
            ab: 1040,
            score: 78
        },
        {
            name: 'Almora',
            dist: 'Almora',
            fac: 94,
            opd: 4200,
            bed: 58,
            mmr: 84,
            ab: 420,
            score: 72
        },
        {
            name: 'Chamoli',
            dist: 'Chamoli',
            fac: 67,
            opd: 2800,
            bed: 52,
            mmr: 91,
            ab: 280,
            score: 65
        },
        {
            name: 'Tehri',
            dist: 'Tehri',
            fac: 88,
            opd: 3600,
            bed: 56,
            mmr: 87,
            ab: 360,
            score: 68
        },
        {
            name: 'Pauri',
            dist: 'Pauri',
            fac: 82,
            opd: 3400,
            bed: 54,
            mmr: 89,
            ab: 340,
            score: 66
        },
        {
            name: 'Uttarkashi',
            dist: 'Uttarkashi',
            fac: 61,
            opd: 2100,
            bed: 49,
            mmr: 96,
            ab: 210,
            score: 61
        },
        {
            name: 'Champawat',
            dist: 'Champawat',
            fac: 48,
            opd: 1700,
            bed: 47,
            mmr: 98,
            ab: 170,
            score: 59
        },
        {
            name: 'Bageshwar',
            dist: 'Bageshwar',
            fac: 44,
            opd: 1600,
            bed: 45,
            mmr: 102,
            ab: 160,
            score: 57
        },
        {
            name: 'Pithoragarh',
            dist: 'Pithoragarh',
            fac: 71,
            opd: 2600,
            bed: 50,
            mmr: 93,
            ab: 260,
            score: 63
        },
        {
            name: 'Rudraprayag',
            dist: 'Rudraprayag',
            fac: 42,
            opd: 1420,
            bed: 44,
            mmr: 105,
            ab: 140,
            score: 55
        },
    ];

    const FACILITIES = [{
            name: 'AIIMS Rishikesh',
            type: 'Medical College',
            dist: 'Dehradun',
            beds: 1500,
            docs: 420,
            opd: 1800,
            ab: 'Empanelled',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'Doon Medical College',
            type: 'Medical College',
            dist: 'Dehradun',
            beds: 1200,
            docs: 380,
            opd: 1400,
            ab: 'Empanelled',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'Government Medical College Haldwani',
            type: 'Medical College',
            dist: 'Nainital',
            beds: 1100,
            docs: 310,
            opd: 1200,
            ab: 'Empanelled',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'Sushila Tiwari Hospital',
            type: 'District Hospital',
            dist: 'Nainital',
            beds: 800,
            docs: 210,
            opd: 980,
            ab: 'Empanelled',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'District Hospital Haridwar',
            type: 'District Hospital',
            dist: 'Haridwar',
            beds: 600,
            docs: 180,
            opd: 820,
            ab: 'Empanelled',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'District Hospital Roorkee',
            type: 'District Hospital',
            dist: 'Haridwar',
            beds: 400,
            docs: 120,
            opd: 640,
            ab: 'Empanelled',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'CHC Ramnagar',
            type: 'CHC',
            dist: 'Nainital',
            beds: 100,
            docs: 12,
            opd: 280,
            ab: 'Empanelled',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'CHC Kashipur',
            type: 'CHC',
            dist: 'US Nagar',
            beds: 80,
            docs: 10,
            opd: 240,
            ab: 'Pending',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'PHC Kedarnath',
            type: 'PHC',
            dist: 'Rudraprayag',
            beds: 30,
            docs: 4,
            opd: 80,
            ab: 'Not Eligible',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'PHC Badrinath',
            type: 'PHC',
            dist: 'Chamoli',
            beds: 30,
            docs: 4,
            opd: 70,
            ab: 'Not Eligible',
            hmis: 'Offline',
            status: 'Operational'
        },
        {
            name: 'CHC Lansdowne',
            type: 'CHC',
            dist: 'Pauri',
            beds: 60,
            docs: 8,
            opd: 160,
            ab: 'Empanelled',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'District Hospital Almora',
            type: 'District Hospital',
            dist: 'Almora',
            beds: 350,
            docs: 90,
            opd: 520,
            ab: 'Empanelled',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'CHC Munsiyari',
            type: 'CHC',
            dist: 'Pithoragarh',
            beds: 50,
            docs: 6,
            opd: 120,
            ab: 'Pending',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'PHC Joshimath',
            type: 'PHC',
            dist: 'Chamoli',
            beds: 20,
            docs: 3,
            opd: 60,
            ab: 'Not Eligible',
            hmis: 'Active',
            status: 'Operational'
        },
        {
            name: 'Base Hospital Srinagar',
            type: 'District Hospital',
            dist: 'Pauri',
            beds: 300,
            docs: 80,
            opd: 420,
            ab: 'Empanelled',
            hmis: 'Active',
            status: 'Operational'
        },
    ];

    const DARK_COLORS = ['#60a5fa', '#4db6ac', '#81c784', '#ffb74d', '#ef9a9a', '#ce93d8', '#ffca28', '#4dd0e1',
        '#f48fb1', '#a5d6a7', '#90caf9', '#b0bec5', '#ff8a65'
    ];

    // ═══════════════════════════════════════
    //  TAB NAVIGATION
    // ═══════════════════════════════════════
    function showTab(id) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        const panel = document.getElementById('tab-' + id);
        if (panel) {
            panel.classList.add('active');
            initTabCharts(id);
        }
        event.target.closest('.nav-item')?.classList.add('active');
        // show map if needed
        if (id === 'map') setTimeout(() => {
            if (stateMap) stateMap.invalidateSize();
        }, 200);
    }

    // ═══════════════════════════════════════
    //  DISTRICT ROWS
    // ═══════════════════════════════════════
    function renderDistrictRows() {
        const el = document.getElementById('districtRows');
        if (!el) return;
        el.innerHTML = DIST_DATA.map(d => {
            const scoreColor = d.score >= 80 ? '#81c784' : d.score >= 65 ? '#ffb74d' : '#ef9a9a';
            return `<div class="dist-row" onclick="drillDistrict('${d.name}')">
    <div><div class="dr-name">${d.name}</div><div class="dr-sub">${d.fac} facilities</div></div>
    <div>
    <div style="font-size:11px;color:#fff;margin-bottom:2px">${d.fac} total</div>
    <div class="prog-bar"><div class="prog-fill" style="width:${(d.fac/200*100).toFixed(0)}%;background:#60a5fa"></div></div>
    </div>
    <div style="color:#fff;font-weight:700">${(d.opd/1000).toFixed(1)}k</div>
    <div>
    <div style="color:${d.bed>70?'#81c784':d.bed>55?'#ffb74d':'#ef9a9a'};font-weight:700">${d.bed}%</div>
    </div>
    <div style="color:${d.mmr<70?'#81c784':d.mmr<90?'#ffb74d':'#ef9a9a'};font-weight:700">${d.mmr}</div>
    <div style="color:#fff;font-weight:700">${d.ab.toLocaleString()}</div>
    <div>
    <span style="background:${scoreColor}22;color:${scoreColor};padding:3px 8px;border-radius:8px;font-size:12px;font-weight:800">${d.score}</span>
    </div>
</div>`;
        }).join('');
    }

    function drillDistrict(name) {
        alert('Opening detailed drill-down for: ' + name);
    }

    function exportReport() {
        alert('Generating state health report PDF...');
    }

    function refreshData() {
        /* live filter would reload charts */
    }

    // ═══════════════════════════════════════
    //  FACILITY TABLE
    // ═══════════════════════════════════════
    function renderFacilities() {
        const tbody = document.getElementById('facilityTableBody');
        if (!tbody) return;
        tbody.innerHTML = FACILITIES.map((f, i) => {
            const abBadge = f.ab === 'Empanelled' ? 'badge-green' : f.ab === 'Pending' ? 'badge-orange' :
                'badge-gray';
            const hmisBadge = f.hmis === 'Active' ? 'badge-teal' : 'badge-red';
            return `<tr>
    <td><span class="tbl-rank">${i+1}</span></td>
    <td><span class="strong">${f.name}</span></td>
    <td><span class="badge badge-blue">${f.type}</span></td>
    <td>${f.dist}</td>
    <td>${f.beds.toLocaleString()}</td>
    <td>${f.docs}</td>
    <td>${f.opd.toLocaleString()}</td>
    <td><span class="badge ${abBadge}">${f.ab}</span></td>
    <td><span class="badge ${hmisBadge}">${f.hmis}</span></td>
    <td><span class="badge badge-green">${f.status}</span></td>
</tr>`;
        }).join('');
    }

    // ═══════════════════════════════════════
    //  MAP
    // ═══════════════════════════════════════
    let stateMap = null;

    function initMap() {
        if (stateMap) return;
        stateMap = L.map('stateMap', {
            zoomControl: true
        }).setView([30.1, 79.2], 7);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 17,
            className: 'dark-tiles'
        }).addTo(stateMap);

        // District centres
        const distCentres = [{
                name: 'Dehradun',
                lat: 30.316,
                lon: 78.032,
                opd: 14200,
                type: 'District Hospital',
                color: '#42a5f5'
            },
            {
                name: 'Haridwar',
                lat: 29.945,
                lon: 78.164,
                opd: 11800,
                type: 'CHC',
                color: '#26a69a'
            },
            {
                name: 'Nainital',
                lat: 29.392,
                lon: 79.463,
                opd: 9200,
                type: 'Medical College',
                color: '#ef5350'
            },
            {
                name: 'US Nagar',
                lat: 28.999,
                lon: 79.518,
                opd: 10400,
                type: 'District Hospital',
                color: '#42a5f5'
            },
            {
                name: 'Almora',
                lat: 29.597,
                lon: 79.659,
                opd: 4200,
                type: 'District Hospital',
                color: '#42a5f5'
            },
            {
                name: 'Chamoli',
                lat: 30.415,
                lon: 79.342,
                opd: 2800,
                type: 'CHC',
                color: '#26a69a'
            },
            {
                name: 'Tehri',
                lat: 30.378,
                lon: 78.480,
                opd: 3600,
                type: 'PHC',
                color: '#66bb6a'
            },
            {
                name: 'Pauri',
                lat: 30.145,
                lon: 78.779,
                opd: 3400,
                type: 'District Hospital',
                color: '#42a5f5'
            },
            {
                name: 'Uttarkashi',
                lat: 30.726,
                lon: 78.449,
                opd: 2100,
                type: 'CHC',
                color: '#26a69a'
            },
            {
                name: 'Champawat',
                lat: 29.334,
                lon: 80.091,
                opd: 1700,
                type: 'PHC',
                color: '#66bb6a'
            },
            {
                name: 'Bageshwar',
                lat: 29.836,
                lon: 79.769,
                opd: 1600,
                type: 'PHC',
                color: '#66bb6a'
            },
            {
                name: 'Pithoragarh',
                lat: 29.582,
                lon: 80.218,
                opd: 2600,
                type: 'CHC',
                color: '#26a69a'
            },
            {
                name: 'Rudraprayag',
                lat: 30.285,
                lon: 78.982,
                opd: 1420,
                type: 'PHC',
                color: '#66bb6a'
            },
        ];
        distCentres.forEach(d => {
            const r = Math.max(10, Math.sqrt(d.opd / 10));
            L.circleMarker([d.lat, d.lon], {
                    radius: r,
                    color: d.color,
                    fillColor: d.color,
                    fillOpacity: .6,
                    weight: 2
                })
                .addTo(stateMap)
                .bindPopup(`<b>${d.name}</b><br>OPD/day: ${d.opd.toLocaleString()}<br>Type: ${d.type}`);
        });
    }

    function updateMapLayer(v) {
        /* filter map markers */
    }

    function toggleHeatmap() {
        /* toggle leaflet heatmap */
    }

    function fitBounds() {
        if (stateMap) stateMap.setView([30.1, 79.2], 7);
    }

    // ═══════════════════════════════════════
    //  CHART INIT — track which charts drawn
    // ═══════════════════════════════════════
    const drawnCharts = {};

    function initTabCharts(id) {
        if (id === 'map') {
            initMap();
            return;
        }
        if (id === 'districts') {
            renderDistrictRows();
            if (!drawnCharts.distComp) {
                buildDistComp();
                buildScatter();
                drawnCharts.distComp = 1;
            }
            return;
        }
        if (id === 'facilities') {
            renderFacilities();
            return;
        }
        if (drawnCharts[id]) return;
        drawnCharts[id] = 1;
        ({
            overview: buildOverviewCharts,
            opd: buildOpdCharts,
            disease: buildDiseaseCharts,
            mch: buildMchCharts,
            revenue: buildRevenueCharts,
            ab: buildAbCharts,
            hr: buildHrCharts,
            ambulance: buildAmbCharts,
            inventory: buildInvCharts,
            lab: buildLabCharts,
            pharma: buildPharmaCharts,
        } [id] || (() => {}))();
    }

    // ─── helpers ───────────────────────────
    function mkChart(id, cfg) {
        const c = document.getElementById(id);
        if (!c) return null;
        if (c._chartInst) c._chartInst.destroy();
        const inst = new Chart(c.getContext('2d'), cfg);
        c._chartInst = inst;
        return inst;
    }

    function mkEchart(id, opt) {
        const el = document.getElementById(id);
        if (!el) return;
        const inst = echarts.init(el, 'dark');
        inst.setOption(opt);
        window.addEventListener('resize', () => inst.resize());
        return inst;
    }
    const gridDark = {
        color: 'rgba(255,255,255,.06)'
    };
    const axDark = {
        ticks: {
            color: '#7b9bbf',
            font: {
                size: 11
            }
        },
        grid: {
            color: 'rgba(255,255,255,.06)'
        }
    };

    function rnd(n, lo, hi) {
        return Array.from({
            length: n
        }, () => Math.round(lo + Math.random() * (hi - lo)))
    }

    // ═══════════════════════════════════════
    //  OVERVIEW CHARTS
    // ═══════════════════════════════════════
    function buildOverviewCharts() {
        // OPD Trend
        mkChart('opdTrendChart', {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{
                    label: 'OPD Visits',
                    data: [58000, 61000, 65000, 63000, 67000, 64000, 66000, 68000, 70000, 67000,
                        65000, 68241
                    ],
                    fill: true,
                    borderColor: '#60a5fa',
                    backgroundColor: 'rgba(96,165,250,.1)',
                    tension: .4,
                    pointRadius: 3
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });

        // Bed Occ
        mkChart('bedOccChart', {
            type: 'bar',
            data: {
                labels: DISTRICTS.slice(0, 8),
                datasets: [{
                    label: 'Occupancy %',
                    data: DIST_DATA.slice(0, 8).map(d => d.bed),
                    backgroundColor: DIST_DATA.slice(0, 8).map(d => d.bed > 70 ? '#81c784' : d.bed >
                        55 ? '#ffb74d' : '#ef9a9a'),
                    borderRadius: 6
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            maxRotation: 45
                        }
                    },
                    y: {
                        ...axDark,
                        max: 100
                    }
                },
                responsive: true
            }
        });

        // Rev Type
        mkChart('revTypeChart', {
            type: 'doughnut',
            data: {
                labels: ['Medical College', 'District Hosp', 'CHC', 'PHC', 'Sub-Centre'],
                datasets: [{
                    data: [42, 31, 14, 9, 4],
                    backgroundColor: ['#60a5fa', '#4db6ac', '#81c784', '#ffca28', '#ce93d8'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#a0bbd8',
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                cutout: '65%',
                responsive: true
            }
        });

        // Sunburst — Budget
        mkEchart('sunburstChart', {
            backgroundColor: 'transparent',
            series: [{
                type: 'sunburst',
                data: [{
                        name: 'Clinical Services',
                        value: null,
                        itemStyle: {
                            color: '#1565c0'
                        },
                        children: [{
                                name: 'OPD',
                                value: 1400,
                                itemStyle: {
                                    color: '#1976d2'
                                }
                            },
                            {
                                name: 'IPD',
                                value: 2200,
                                itemStyle: {
                                    color: '#1e88e5'
                                },
                                children: [{
                                    name: 'General Ward',
                                    value: 800
                                }, {
                                    name: 'ICU',
                                    value: 600
                                }, {
                                    name: 'Surgery',
                                    value: 500
                                }, {
                                    name: 'Maternity',
                                    value: 300
                                }]
                            },
                            {
                                name: 'Emergency',
                                value: 600,
                                itemStyle: {
                                    color: '#2196f3'
                                }
                            },
                            {
                                name: 'Diagnostics',
                                value: 900,
                                itemStyle: {
                                    color: '#42a5f5'
                                }
                            },
                        ]
                    },
                    {
                        name: 'Preventive',
                        value: null,
                        itemStyle: {
                            color: '#00695c'
                        },
                        children: [{
                                name: 'Immunisation',
                                value: 480,
                                itemStyle: {
                                    color: '#00796b'
                                }
                            },
                            {
                                name: 'MCH',
                                value: 620,
                                itemStyle: {
                                    color: '#00897b'
                                }
                            },
                            {
                                name: 'NVBDCP',
                                value: 280,
                                itemStyle: {
                                    color: '#009688'
                                }
                            },
                            {
                                name: 'TB/Leprosy',
                                value: 320,
                                itemStyle: {
                                    color: '#26a69a'
                                }
                            },
                        ]
                    },
                    {
                        name: 'Infrastructure',
                        value: null,
                        itemStyle: {
                            color: '#e65100'
                        },
                        children: [{
                                name: 'Civil Works',
                                value: 1200,
                                itemStyle: {
                                    color: '#ef6c00'
                                }
                            },
                            {
                                name: 'Equipment',
                                value: 800,
                                itemStyle: {
                                    color: '#f57c00'
                                }
                            },
                            {
                                name: 'IT/HMIS',
                                value: 240,
                                itemStyle: {
                                    color: '#fb8c00'
                                }
                            },
                        ]
                    },
                    {
                        name: 'Human Resources',
                        value: null,
                        itemStyle: {
                            color: '#6a1b9a'
                        },
                        children: [{
                                name: 'Salaries',
                                value: 3400,
                                itemStyle: {
                                    color: '#7b1fa2'
                                }
                            },
                            {
                                name: 'Training',
                                value: 180,
                                itemStyle: {
                                    color: '#8e24aa'
                                }
                            },
                            {
                                name: 'Incentives',
                                value: 220,
                                itemStyle: {
                                    color: '#9c27b0'
                                }
                            },
                        ]
                    },
                    {
                        name: 'Medicines',
                        value: null,
                        itemStyle: {
                            color: '#827717'
                        },
                        children: [{
                                name: 'Essential Drugs',
                                value: 620,
                                itemStyle: {
                                    color: '#9e9d24'
                                }
                            },
                            {
                                name: 'AB Schemes',
                                value: 840,
                                itemStyle: {
                                    color: '#afb42b'
                                }
                            },
                            {
                                name: 'Consumables',
                                value: 280,
                                itemStyle: {
                                    color: '#c0ca33'
                                }
                            },
                        ]
                    },
                ],
                radius: ['15%', '90%'],
                label: {
                    show: true,
                    color: '#ccc',
                    fontSize: 10
                },
                emphasis: {
                    focus: 'ancestor'
                }
            }]
        });

        // Disease Pie
        mkEchart('diseaseChart', {
            backgroundColor: 'transparent',
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                textStyle: {
                    color: '#a0bbd8',
                    fontSize: 10
                }
            },
            series: [{
                type: 'pie',
                radius: ['40%', '70%'],
                data: [{
                        value: 2840,
                        name: 'Respiratory'
                    },
                    {
                        value: 2120,
                        name: 'Gastrointestinal'
                    },
                    {
                        value: 1890,
                        name: 'Cardiovascular'
                    },
                    {
                        value: 1470,
                        name: 'Musculoskeletal'
                    },
                    {
                        value: 1240,
                        name: 'Endocrine/Diabetes'
                    },
                    {
                        value: 980,
                        name: 'Dengue/Malaria'
                    },
                    {
                        value: 840,
                        name: 'Mental Health'
                    },
                    {
                        value: 720,
                        name: 'Injury/Trauma'
                    },
                    {
                        value: 610,
                        name: 'Skin/Derma'
                    },
                    {
                        value: 480,
                        name: 'Others'
                    },
                ],
                emphasis: {
                    itemStyle: {
                        shadowBlur: 10
                    }
                },
                label: {
                    color: '#ccc',
                    fontSize: 10
                }
            }]
        });

        // MCH Radar
        mkEchart('mchRadar', {
            backgroundColor: 'transparent',
            radar: {
                indicator: [{
                    name: 'ANC4+',
                    max: 100
                }, {
                    name: 'Inst.Del',
                    max: 100
                }, {
                    name: 'IMR',
                    max: 100
                }, {
                    name: 'MMR',
                    max: 100
                }, {
                    name: 'Immunise',
                    max: 100
                }],
                splitLine: {
                    lineStyle: {
                        color: 'rgba(255,255,255,.1)'
                    }
                },
                axisLine: {
                    lineStyle: {
                        color: 'rgba(255,255,255,.1)'
                    }
                },
                name: {
                    textStyle: {
                        color: '#a0bbd8',
                        fontSize: 10
                    }
                }
            },
            series: [{
                type: 'radar',
                data: [{
                    value: [94, 87, 82, 88, 91],
                    name: 'State',
                    areaStyle: {
                        opacity: .2,
                        color: '#60a5fa'
                    },
                    lineStyle: {
                        color: '#60a5fa'
                    }
                }]
            }]
        });

        // Immunisation
        mkChart('immChart', {
            type: 'bar',
            data: {
                labels: ['BCG', 'OPV', 'DPT', 'Hep-B', 'Measles', 'MMR', 'Penta', 'PCV'],
                datasets: [{
                    label: 'Coverage%',
                    data: [97, 95, 93, 94, 91, 89, 92, 88],
                    backgroundColor: '#4db6ac',
                    borderRadius: 5
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            maxRotation: 45
                        }
                    },
                    y: {
                        ...axDark,
                        max: 100
                    }
                },
                responsive: true
            }
        });

        // Ambulance response
        mkChart('ambChart', {
            type: 'bar',
            data: {
                labels: DISTRICTS.slice(0, 8),
                datasets: [{
                    label: 'Avg Response (min)',
                    data: [9.2, 10.4, 11.8, 12.1, 15.4, 18.2, 17.8, 16.4],
                    backgroundColor: DIST_DATA.slice(0, 8).map(d => d.bed > 70 ? '#81c784' :
                        '#ffb74d'),
                    borderRadius: 5
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            maxRotation: 45
                        }
                    },
                    y: axDark
                },
                responsive: true
            }
        });

        // AB Pie
        mkEchart('abPieChart', {
            backgroundColor: 'transparent',
            tooltip: {
                trigger: 'item'
            },
            series: [{
                type: 'pie',
                radius: '70%',
                data: [{
                        value: 6847,
                        name: 'Approved',
                        itemStyle: {
                            color: '#81c784'
                        }
                    },
                    {
                        value: 1247,
                        name: 'Pending',
                        itemStyle: {
                            color: '#ffb74d'
                        }
                    },
                    {
                        value: 247,
                        name: 'Rejected',
                        itemStyle: {
                            color: '#ef9a9a'
                        }
                    },
                ],
                label: {
                    color: '#ccc',
                    fontSize: 10
                }
            }]
        });

        // District OPD
        mkChart('distOpdChart', {
            type: 'bar',
            data: {
                labels: DIST_DATA.map(d => d.name),
                datasets: [{
                    label: 'OPD/day',
                    data: DIST_DATA.map(d => d.opd),
                    backgroundColor: DARK_COLORS,
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: axDark,
                    y: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                responsive: true
            }
        });

        // MCH Trend
        mkChart('mchTrendChart', {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{
                        label: 'Deliveries',
                        data: rnd(12, 3200, 4200),
                        borderColor: '#f48fb1',
                        backgroundColor: 'rgba(244,143,177,.08)',
                        tension: .4,
                        fill: true
                    },
                    {
                        label: 'ANC Reg',
                        data: rnd(12, 4200, 5200),
                        borderColor: '#81c784',
                        backgroundColor: 'rgba(129,199,132,.08)',
                        tension: .4,
                        fill: true
                    },
                    {
                        label: 'Immunisation',
                        data: rnd(12, 12000, 16000),
                        borderColor: '#60a5fa',
                        backgroundColor: 'rgba(96,165,250,.08)',
                        tension: .4,
                        fill: true
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8',
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });
    }

    // ═══════════════════════════════════════
    //  DISTRICT CHARTS
    // ═══════════════════════════════════════
    function buildDistComp() {
        mkChart('distCompChart', {
            type: 'bar',
            data: {
                labels: DIST_DATA.map(d => d.name),
                datasets: [{
                        label: 'OPD/day',
                        data: DIST_DATA.map(d => d.opd),
                        backgroundColor: '#60a5fa',
                        borderRadius: 6
                    },
                    {
                        label: 'Bed Occ%',
                        data: DIST_DATA.map(d => d.bed * 100),
                        backgroundColor: '#4db6ac',
                        borderRadius: 6
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8'
                        }
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            maxRotation: 45
                        }
                    },
                    y: axDark
                },
                responsive: true
            }
        });

        mkEchart('scatterChart', {
            backgroundColor: 'transparent',
            tooltip: {
                formatter: p =>
                    `<b>${p.data[3]}</b><br>Bed Occ: ${p.data[0]}%<br>Revenue: ₹${p.data[1]}Cr<br>AB: ${p.data[2]}`
            },
            xAxis: {
                name: 'Bed Occupancy%',
                nameTextStyle: {
                    color: '#7b9bbf'
                },
                axisLabel: {
                    color: '#7b9bbf'
                },
                splitLine: {
                    lineStyle: {
                        color: 'rgba(255,255,255,.06)'
                    }
                }
            },
            yAxis: {
                name: 'Revenue (₹Cr)',
                nameTextStyle: {
                    color: '#7b9bbf'
                },
                axisLabel: {
                    color: '#7b9bbf'
                },
                splitLine: {
                    lineStyle: {
                        color: 'rgba(255,255,255,.06)'
                    }
                }
            },
            series: [{
                type: 'scatter',
                data: DIST_DATA.map((d, i) => [d.bed, Math.round(d.opd / 400), d.ab / 100, d.name]),
                symbolSize: d => d[2] * 3,
                label: {
                    show: false
                },
                itemStyle: {
                    color: d => DARK_COLORS[DIST_DATA.findIndex(x => x.name === d.data[3])] ||
                        '#60a5fa'
                }
            }]
        });
    }

    // ═══════════════════════════════════════
    //  OPD CHARTS
    // ═══════════════════════════════════════
    function buildOpdCharts() {
        mkChart('opdDeptChart', {
            type: 'bar',
            data: {
                labels: ['Medicine', 'Surgery', 'Ortho', 'Gynae', 'Paeds', 'ENT', 'Ophtha', 'Skin',
                    'Dental', 'Psych'
                ],
                datasets: [{
                    label: 'OPD',
                    data: [18400, 12200, 8400, 9200, 7800, 4200, 3800, 3200, 2800, 1800],
                    backgroundColor: DARK_COLORS,
                    borderRadius: 6
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            maxRotation: 45
                        }
                    },
                    y: axDark
                },
                responsive: true
            }
        });

        mkChart('admTrendChart', {
            type: 'line',
            data: {
                labels: Array.from({
                    length: 30
                }, (_, i) => `${i+1}`),
                datasets: [{
                    label: 'Admissions',
                    data: rnd(30, 380, 480),
                    borderColor: '#4db6ac',
                    backgroundColor: 'rgba(77,182,172,.1)',
                    tension: .4,
                    fill: true
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });

        mkEchart('dischargePie', {
            backgroundColor: 'transparent',
            tooltip: {
                trigger: 'item'
            },
            series: [{
                type: 'pie',
                radius: '70%',
                data: [{
                        value: 7841,
                        name: 'Normal',
                        itemStyle: {
                            color: '#81c784'
                        }
                    },
                    {
                        value: 1240,
                        name: 'LAMA',
                        itemStyle: {
                            color: '#ffb74d'
                        }
                    },
                    {
                        value: 847,
                        name: 'Referred',
                        itemStyle: {
                            color: '#60a5fa'
                        }
                    },
                    {
                        value: 320,
                        name: 'Transfer',
                        itemStyle: {
                            color: '#ce93d8'
                        }
                    },
                    {
                        value: 84,
                        name: 'Death',
                        itemStyle: {
                            color: '#ef9a9a'
                        }
                    },
                ],
                label: {
                    color: '#ccc',
                    fontSize: 10
                }
            }]
        });

        mkChart('bedWardChart', {
            type: 'bar',
            data: {
                labels: ['General', 'Private', 'ICU', 'Maternity', 'Paeds', 'Ortho', 'Surgical', 'Burns'],
                datasets: [{
                        label: 'Occupied',
                        data: [72, 68, 94, 81, 76, 70, 74, 82],
                        backgroundColor: '#4db6ac',
                        borderRadius: 5
                    },
                    {
                        label: 'Available',
                        data: [28, 32, 6, 19, 24, 30, 26, 18],
                        backgroundColor: 'rgba(77,182,172,.2)',
                        borderRadius: 5
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8'
                        }
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        stacked: true
                    },
                    y: {
                        ...axDark,
                        stacked: true,
                        max: 100
                    }
                },
                responsive: true
            }
        });

        mkChart('losChart', {
            type: 'bar',
            data: {
                labels: ['<1d', '1-2d', '2-4d', '4-7d', '7-14d', '>14d'],
                datasets: [{
                    label: 'Patients',
                    data: [3420, 4280, 6840, 3210, 1840, 420],
                    backgroundColor: '#ce93d8',
                    borderRadius: 5
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });
    }

    // ═══════════════════════════════════════
    //  DISEASE CHARTS
    // ═══════════════════════════════════════
    function buildDiseaseCharts() {
        mkChart('diseaseLineChart', {
            type: 'line',
            data: {
                labels: Array.from({
                    length: 12
                }, (_, i) => `W${i+1}`),
                datasets: [{
                        label: 'Dengue',
                        data: rnd(12, 10, 60),
                        borderColor: '#ef9a9a',
                        tension: .4
                    },
                    {
                        label: 'Malaria',
                        data: rnd(12, 5, 30),
                        borderColor: '#ffb74d',
                        tension: .4
                    },
                    {
                        label: 'Diarrhoea',
                        data: rnd(12, 80, 200),
                        borderColor: '#4db6ac',
                        tension: .4
                    },
                    {
                        label: 'ARI',
                        data: rnd(12, 200, 600),
                        borderColor: '#60a5fa',
                        tension: .4
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8',
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });

        mkEchart('diseaseSunburst', {
            backgroundColor: 'transparent',
            series: [{
                type: 'sunburst',
                data: [{
                        name: 'Communicable',
                        value: null,
                        itemStyle: {
                            color: '#ef5350'
                        },
                        children: [{
                                name: 'Vector-borne',
                                value: null,
                                itemStyle: {
                                    color: '#e57373'
                                },
                                children: [{
                                    name: 'Dengue',
                                    value: 420
                                }, {
                                    name: 'Malaria',
                                    value: 280
                                }, {
                                    name: 'Chikungunya',
                                    value: 84
                                }]
                            },
                            {
                                name: 'Enteric',
                                value: null,
                                itemStyle: {
                                    color: '#ef9a9a'
                                },
                                children: [{
                                    name: 'Diarrhoea',
                                    value: 840
                                }, {
                                    name: 'Typhoid',
                                    value: 180
                                }, {
                                    name: 'Hepatitis A',
                                    value: 60
                                }]
                            },
                            {
                                name: 'Respiratory',
                                value: null,
                                itemStyle: {
                                    color: '#ffcdd2'
                                },
                                children: [{
                                    name: 'TB',
                                    value: 320
                                }, {
                                    name: 'ARI',
                                    value: 1840
                                }, {
                                    name: 'COVID',
                                    value: 42
                                }]
                            },
                        ]
                    },
                    {
                        name: 'NCD',
                        value: null,
                        itemStyle: {
                            color: '#1976d2'
                        },
                        children: [{
                                name: 'CVD',
                                value: null,
                                itemStyle: {
                                    color: '#2196f3'
                                },
                                children: [{
                                    name: 'Hypertension',
                                    value: 2840
                                }, {
                                    name: 'IHD',
                                    value: 840
                                }, {
                                    name: 'Heart Failure',
                                    value: 280
                                }]
                            },
                            {
                                name: 'Metabolic',
                                value: null,
                                itemStyle: {
                                    color: '#42a5f5'
                                },
                                children: [{
                                    name: 'Diabetes',
                                    value: 1840
                                }, {
                                    name: 'Obesity',
                                    value: 420
                                }, {
                                    name: 'Thyroid',
                                    value: 280
                                }]
                            },
                            {
                                name: 'Cancer',
                                value: null,
                                itemStyle: {
                                    color: '#64b5f6'
                                },
                                children: [{
                                    name: 'Cervical',
                                    value: 84
                                }, {
                                    name: 'Oral',
                                    value: 62
                                }, {
                                    name: 'Breast',
                                    value: 71
                                }]
                            },
                        ]
                    },
                    {
                        name: 'Maternal',
                        value: null,
                        itemStyle: {
                            color: '#f06292'
                        },
                        children: [{
                            name: 'Anaemia',
                            value: 420
                        }, {
                            name: 'Eclampsia',
                            value: 84
                        }, {
                            name: 'PPH',
                            value: 62
                        }]
                    },
                ],
                radius: ['10%', '90%'],
                label: {
                    show: true,
                    color: '#ccc',
                    fontSize: 9
                },
                emphasis: {
                    focus: 'ancestor'
                }
            }]
        });

        mkEchart('districtHeatmap', {
            backgroundColor: 'transparent',
            tooltip: {
                position: 'top'
            },
            grid: {
                top: '5%',
                bottom: '15%'
            },
            xAxis: {
                type: 'category',
                data: MONTHS,
                axisLabel: {
                    color: '#7b9bbf',
                    fontSize: 10
                },
                splitLine: {
                    show: false
                }
            },
            yAxis: {
                type: 'category',
                data: ['Dengue', 'Malaria', 'TB', 'Diarrhoea', 'ARI', 'Typhoid'],
                axisLabel: {
                    color: '#7b9bbf',
                    fontSize: 10
                },
                splitLine: {
                    show: false
                }
            },
            visualMap: {
                min: 0,
                max: 200,
                calculable: true,
                inRange: {
                    color: ['#1a2847', '#1565c0', '#ef5350']
                },
                textStyle: {
                    color: '#7b9bbf'
                },
                orient: 'horizontal',
                left: 'center',
                bottom: 0,
                itemHeight: 80
            },
            series: [{
                type: 'heatmap',
                data: (() => {
                    const arr = [];
                    for (let y = 0; y < 6; y++)
                        for (let m = 0; m < 12; m++) arr.push([m, y, Math.round(Math
                            .random() * 180 + 10)]);
                    return arr;
                })(),
                label: {
                    show: false
                }
            }]
        });

        mkChart('ncdChart', {
            type: 'bar',
            data: {
                labels: MONTHS,
                datasets: [{
                        label: 'NCD',
                        data: rnd(12, 12000, 18000),
                        backgroundColor: '#60a5fa',
                        borderRadius: 4
                    },
                    {
                        label: 'Communicable',
                        data: rnd(12, 6000, 10000),
                        backgroundColor: '#ef9a9a',
                        borderRadius: 4
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8'
                        }
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });

        mkChart('childMorbChart', {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{
                        label: 'Pneumonia',
                        data: rnd(12, 400, 800),
                        borderColor: '#60a5fa',
                        tension: .4
                    },
                    {
                        label: 'Diarrhoea',
                        data: rnd(12, 300, 600),
                        borderColor: '#4db6ac',
                        tension: .4
                    },
                    {
                        label: 'Malnutrition',
                        data: rnd(12, 200, 400),
                        borderColor: '#ffb74d',
                        tension: .4
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8',
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });
    }

    // ═══════════════════════════════════════
    //  MCH CHARTS
    // ═══════════════════════════════════════
    function buildMchCharts() {
        mkEchart('mchRadarBig', {
            backgroundColor: 'transparent',
            radar: {
                indicator: [{
                        name: 'ANC 4+ (%)',
                        max: 100
                    }, {
                        name: 'Inst. Delivery',
                        max: 100
                    }, {
                        name: 'IMR (inv)',
                        max: 100
                    },
                    {
                        name: 'Full Imm.',
                        max: 100
                    }, {
                        name: 'Skilled Birth',
                        max: 100
                    }, {
                        name: 'PNC Visit',
                        max: 100
                    },
                ],
                splitLine: {
                    lineStyle: {
                        color: 'rgba(255,255,255,.1)'
                    }
                },
                axisLine: {
                    lineStyle: {
                        color: 'rgba(255,255,255,.1)'
                    }
                },
                name: {
                    textStyle: {
                        color: '#a0bbd8',
                        fontSize: 10
                    }
                }
            },
            legend: {
                data: ['State', 'National Target'],
                textStyle: {
                    color: '#a0bbd8',
                    fontSize: 10
                }
            },
            series: [{
                    type: 'radar',
                    name: 'State',
                    data: [{
                        value: [94, 87, 82, 91, 89, 78]
                    }],
                    areaStyle: {
                        opacity: .2,
                        color: '#60a5fa'
                    },
                    lineStyle: {
                        color: '#60a5fa'
                    }
                },
                {
                    type: 'radar',
                    name: 'National Target',
                    data: [{
                        value: [100, 100, 100, 100, 100, 100]
                    }],
                    lineStyle: {
                        color: '#81c784',
                        type: 'dashed'
                    },
                    areaStyle: {
                        opacity: 0
                    }
                },
            ]
        });

        mkChart('delivTrendChart', {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{
                        label: 'Total Deliveries',
                        data: rnd(12, 3200, 4400),
                        borderColor: '#f48fb1',
                        backgroundColor: 'rgba(244,143,177,.1)',
                        tension: .4,
                        fill: true
                    },
                    {
                        label: 'C-Section',
                        data: rnd(12, 800, 1400),
                        borderColor: '#ffb74d',
                        tension: .4
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8'
                        }
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });

        mkChart('vaccChart', {
            type: 'bar',
            data: {
                labels: ['BCG', 'OPV', 'Penta1', 'Penta3', 'Measles', 'Vit A', 'DPT Booster', 'MR2'],
                datasets: [{
                    label: 'Coverage%',
                    data: [97, 95, 94, 91, 91, 88, 84, 82],
                    backgroundColor: DARK_COLORS.slice(0, 8),
                    borderRadius: 5
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            maxRotation: 45
                        }
                    },
                    y: {
                        ...axDark,
                        max: 100
                    }
                },
                responsive: true
            }
        });
    }

    // ═══════════════════════════════════════
    //  REVENUE CHARTS
    // ═══════════════════════════════════════
    function buildRevenueCharts() {
        mkChart('revDistChart', {
            type: 'bar',
            data: {
                labels: DIST_DATA.map(d => d.name),
                datasets: [{
                    label: 'Revenue (₹Cr)',
                    data: DIST_DATA.map(d => (d.opd / 400).toFixed(1)),
                    backgroundColor: DARK_COLORS,
                    borderRadius: 6
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            maxRotation: 45
                        }
                    },
                    y: axDark
                },
                responsive: true
            }
        });

        mkChart('revTrendChart', {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{
                        label: 'Total Revenue (₹Cr)',
                        data: [31, 33, 36, 34, 38, 36, 39, 41, 44, 40, 38, 43],
                        borderColor: '#ffca28',
                        backgroundColor: 'rgba(255,202,40,.08)',
                        tension: .4,
                        fill: true
                    },
                    {
                        label: 'AB Claims (₹Cr)',
                        data: [18, 19, 21, 20, 23, 21, 22, 24, 26, 23, 22, 25],
                        borderColor: '#60a5fa',
                        tension: .4
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8'
                        }
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });

        mkEchart('payerMixChart', {
            backgroundColor: 'transparent',
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                textStyle: {
                    color: '#a0bbd8',
                    fontSize: 10
                }
            },
            series: [{
                type: 'pie',
                radius: ['40%', '70%'],
                data: [{
                        value: 3820,
                        name: 'Ayushman Bharat',
                        itemStyle: {
                            color: '#60a5fa'
                        }
                    },
                    {
                        value: 1240,
                        name: 'CGHS',
                        itemStyle: {
                            color: '#4db6ac'
                        }
                    },
                    {
                        value: 840,
                        name: 'ESI',
                        itemStyle: {
                            color: '#81c784'
                        }
                    },
                    {
                        value: 620,
                        name: 'BPL Free',
                        itemStyle: {
                            color: '#ffca28'
                        }
                    },
                    {
                        value: 1840,
                        name: 'General',
                        itemStyle: {
                            color: '#ce93d8'
                        }
                    },
                    {
                        value: 340,
                        name: 'Others',
                        itemStyle: {
                            color: '#b0bec5'
                        }
                    },
                ],
                label: {
                    color: '#ccc',
                    fontSize: 10
                }
            }]
        });
    }

    // ═══════════════════════════════════════
    //  AB CHARTS
    // ═══════════════════════════════════════
    function buildAbCharts() {
        mkChart('abDistChart', {
            type: 'bar',
            data: {
                labels: DIST_DATA.map(d => d.name),
                datasets: [{
                        label: 'Approved',
                        data: DIST_DATA.map(d => Math.round(d.ab * .82)),
                        backgroundColor: '#81c784',
                        borderRadius: 5
                    },
                    {
                        label: 'Pending',
                        data: DIST_DATA.map(d => Math.round(d.ab * .12)),
                        backgroundColor: '#ffb74d',
                        borderRadius: 5
                    },
                    {
                        label: 'Rejected',
                        data: DIST_DATA.map(d => Math.round(d.ab * .06)),
                        backgroundColor: '#ef9a9a',
                        borderRadius: 5
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8'
                        }
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        stacked: true,
                        ticks: {
                            ...axDark.ticks,
                            maxRotation: 45
                        }
                    },
                    y: {
                        ...axDark,
                        stacked: true
                    }
                },
                responsive: true
            }
        });

        mkEchart('abSunburst', {
            backgroundColor: 'transparent',
            series: [{
                type: 'sunburst',
                data: [{
                        name: 'Cardiology',
                        value: null,
                        itemStyle: {
                            color: '#ef5350'
                        },
                        children: [{
                            name: 'CABG',
                            value: 320
                        }, {
                            name: 'Valve Replacement',
                            value: 180
                        }, {
                            name: 'Angioplasty',
                            value: 240
                        }]
                    },
                    {
                        name: 'Oncology',
                        value: null,
                        itemStyle: {
                            color: '#ce93d8'
                        },
                        children: [{
                            name: 'Chemo',
                            value: 280
                        }, {
                            name: 'Radiation',
                            value: 140
                        }, {
                            name: 'Surgery',
                            value: 180
                        }]
                    },
                    {
                        name: 'Orthopaedics',
                        value: null,
                        itemStyle: {
                            color: '#60a5fa'
                        },
                        children: [{
                            name: 'Hip Replacement',
                            value: 180
                        }, {
                            name: 'Knee',
                            value: 210
                        }, {
                            name: 'Fracture',
                            value: 320
                        }]
                    },
                    {
                        name: 'Neurology',
                        value: null,
                        itemStyle: {
                            color: '#ffca28'
                        },
                        children: [{
                            name: 'Stroke',
                            value: 240
                        }, {
                            name: 'Brain Surgery',
                            value: 120
                        }, {
                            name: 'Spine',
                            value: 90
                        }]
                    },
                    {
                        name: 'Maternity',
                        value: null,
                        itemStyle: {
                            color: '#f48fb1'
                        },
                        children: [{
                            name: 'Normal Delivery',
                            value: 840
                        }, {
                            name: 'C-Section',
                            value: 420
                        }, {
                            name: 'High Risk',
                            value: 180
                        }]
                    },
                    {
                        name: 'Renal',
                        value: null,
                        itemStyle: {
                            color: '#4db6ac'
                        },
                        children: [{
                            name: 'Dialysis',
                            value: 480
                        }, {
                            name: 'Transplant',
                            value: 24
                        }, {
                            name: 'CAPD',
                            value: 60
                        }]
                    },
                    {
                        name: 'Others',
                        value: null,
                        itemStyle: {
                            color: '#81c784'
                        },
                        children: [{
                            name: 'General Surgery',
                            value: 620
                        }, {
                            name: 'Paediatrics',
                            value: 380
                        }, {
                            name: 'Ophthalmology',
                            value: 180
                        }]
                    },
                ],
                radius: ['15%', '90%'],
                label: {
                    show: true,
                    color: '#ccc',
                    fontSize: 9
                },
                emphasis: {
                    focus: 'ancestor'
                }
            }]
        });
    }

    // ═══════════════════════════════════════
    //  HR CHARTS
    // ═══════════════════════════════════════
    function buildHrCharts() {
        mkEchart('staffPie', {
            backgroundColor: 'transparent',
            tooltip: {
                trigger: 'item'
            },
            series: [{
                type: 'pie',
                radius: '70%',
                data: [{
                        value: 4218,
                        name: 'Doctors',
                        itemStyle: {
                            color: '#60a5fa'
                        }
                    },
                    {
                        value: 8341,
                        name: 'Nurses/ANM',
                        itemStyle: {
                            color: '#4db6ac'
                        }
                    },
                    {
                        value: 3420,
                        name: 'Pharmacists',
                        itemStyle: {
                            color: '#81c784'
                        }
                    },
                    {
                        value: 2840,
                        name: 'Lab Techs',
                        itemStyle: {
                            color: '#ffca28'
                        }
                    },
                    {
                        value: 1847,
                        name: 'Para-Medical',
                        itemStyle: {
                            color: '#ce93d8'
                        }
                    },
                    {
                        value: 4181,
                        name: 'Admin/Support',
                        itemStyle: {
                            color: '#b0bec5'
                        }
                    },
                ],
                label: {
                    color: '#ccc',
                    fontSize: 10
                }
            }]
        });

        mkChart('vacancyChart', {
            type: 'bar',
            data: {
                labels: ['Doctor', 'Nurse', 'Pharmacist', 'Lab Tech', 'Radiographer', 'ANM', 'Admin'],
                datasets: [{
                    label: 'Vacancies',
                    data: [342, 280, 84, 120, 62, 241, 112],
                    backgroundColor: '#ef9a9a',
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: axDark,
                    y: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                responsive: true
            }
        });

        mkChart('staffDistChart', {
            type: 'bar',
            data: {
                labels: DISTRICTS,
                datasets: [{
                    label: 'Staff Count',
                    data: DIST_DATA.map(d => Math.round(d.fac * 13.4)),
                    backgroundColor: DARK_COLORS,
                    borderRadius: 5
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            maxRotation: 45
                        }
                    },
                    y: axDark
                },
                responsive: true
            }
        });
    }

    // ═══════════════════════════════════════
    //  AMBULANCE CHARTS
    // ═══════════════════════════════════════
    function buildAmbCharts() {
        mkChart('ambDistChart', {
            type: 'bar',
            data: {
                labels: DIST_DATA.map(d => d.name),
                datasets: [{
                    label: 'Avg Response (min)',
                    data: [9.2, 10.4, 11.8, 12.1, 15.4, 18.2, 17.8, 16.4, 19.8, 22.4, 21.2, 17.4,
                        20.8
                    ],
                    backgroundColor: d => {
                        const v = d.raw;
                        return v < 12 ? '#81c784' : v < 18 ? '#ffb74d' : '#ef9a9a';
                    },
                    borderRadius: 5
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            maxRotation: 45
                        }
                    },
                    y: axDark
                },
                responsive: true
            }
        });

        mkChart('ambTrendChart', {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{
                        label: 'Calls',
                        data: rnd(12, 1800, 2400),
                        borderColor: '#ffb74d',
                        backgroundColor: 'rgba(255,183,77,.08)',
                        tension: .4,
                        fill: true
                    },
                    {
                        label: 'Completed',
                        data: rnd(12, 1700, 2300),
                        borderColor: '#81c784',
                        tension: .4
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8'
                        }
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });
    }

    // ═══════════════════════════════════════
    //  INVENTORY CHARTS
    // ═══════════════════════════════════════
    function buildInvCharts() {
        mkEchart('stockPie', {
            backgroundColor: 'transparent',
            tooltip: {
                trigger: 'item'
            },
            series: [{
                type: 'pie',
                radius: '70%',
                data: [{
                        value: 4200,
                        name: 'Medicines',
                        itemStyle: {
                            color: '#60a5fa'
                        }
                    },
                    {
                        value: 2800,
                        name: 'Consumables',
                        itemStyle: {
                            color: '#4db6ac'
                        }
                    },
                    {
                        value: 1840,
                        name: 'Diagnostics',
                        itemStyle: {
                            color: '#81c784'
                        }
                    },
                    {
                        value: 1240,
                        name: 'Surgical',
                        itemStyle: {
                            color: '#ffca28'
                        }
                    },
                    {
                        value: 820,
                        name: 'Equipment',
                        itemStyle: {
                            color: '#ce93d8'
                        }
                    },
                    {
                        value: 480,
                        name: 'Others',
                        itemStyle: {
                            color: '#b0bec5'
                        }
                    },
                ],
                label: {
                    color: '#ccc',
                    fontSize: 10
                }
            }]
        });

        mkChart('drugShortChart', {
            type: 'bar',
            data: {
                labels: ['Oxygen', 'IV Fluids', 'Insulin', 'BP Meds', 'Antibiotics', 'Blood Bags', 'Gauze',
                    'Gloves'
                ],
                datasets: [{
                    label: 'Facilities Reporting Shortage',
                    data: [12, 8, 18, 24, 14, 9, 6, 11],
                    backgroundColor: '#ef9a9a',
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: axDark,
                    y: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                responsive: true
            }
        });

        mkChart('consumptionChart', {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{
                    label: 'Consumption (₹Cr)',
                    data: rnd(12, 14, 20),
                    borderColor: '#81c784',
                    backgroundColor: 'rgba(129,199,132,.08)',
                    tension: .4,
                    fill: true
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });
    }

    // ═══════════════════════════════════════
    //  LAB CHARTS
    // ═══════════════════════════════════════
    function buildLabCharts() {
        mkChart('labCatChart', {
            type: 'bar',
            data: {
                labels: ['Haematology', 'Biochemistry', 'Microbiology', 'Serology', 'Urine', 'Radiology',
                    'Cytology', 'Histopathology'
                ],
                datasets: [{
                    label: 'Tests/day',
                    data: [8420, 7240, 4820, 3640, 4210, 2840, 840, 480],
                    backgroundColor: DARK_COLORS,
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: axDark,
                    y: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                responsive: true
            }
        });

        mkChart('tatChart', {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{
                        label: 'Avg TAT (hours)',
                        data: rnd(12, 2.0, 3.5),
                        borderColor: '#60a5fa',
                        tension: .4
                    },
                    {
                        label: 'Target',
                        data: Array(12).fill(2.5),
                        borderColor: '#81c784',
                        borderDash: [5, 5],
                        tension: 0
                    },
                ]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            color: '#a0bbd8'
                        }
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });
    }

    // ═══════════════════════════════════════
    //  PHARMA CHARTS
    // ═══════════════════════════════════════
    function buildPharmaCharts() {
        mkChart('topDrugChart', {
            type: 'bar',
            data: {
                labels: ['Paracetamol', 'Amox 500', 'ORS', 'Metformin', 'Atenolol', 'Omeprazole',
                    'Cetirizine', 'Iron Tabs', 'Amlodipine', 'Azithromycin'
                ],
                datasets: [{
                    label: 'Units Dispensed (k)',
                    data: [184, 142, 128, 96, 88, 84, 76, 74, 68, 62],
                    backgroundColor: DARK_COLORS.slice(0, 10),
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: axDark,
                    y: {
                        ...axDark,
                        ticks: {
                            ...axDark.ticks,
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                responsive: true
            }
        });

        mkEchart('rxPie', {
            backgroundColor: 'transparent',
            tooltip: {
                trigger: 'item'
            },
            series: [{
                type: 'pie',
                radius: '70%',
                data: [{
                        value: 42,
                        name: 'Generic Only',
                        itemStyle: {
                            color: '#81c784'
                        }
                    },
                    {
                        value: 28,
                        name: 'Brand Generic',
                        itemStyle: {
                            color: '#60a5fa'
                        }
                    },
                    {
                        value: 18,
                        name: 'AB Scheme',
                        itemStyle: {
                            color: '#ffca28'
                        }
                    },
                    {
                        value: 8,
                        name: 'BPL Free',
                        itemStyle: {
                            color: '#4db6ac'
                        }
                    },
                    {
                        value: 4,
                        name: 'Paid',
                        itemStyle: {
                            color: '#ce93d8'
                        }
                    },
                ],
                label: {
                    color: '#ccc',
                    fontSize: 10
                }
            }]
        });

        mkChart('drugTrendChart', {
            type: 'line',
            data: {
                labels: MONTHS,
                datasets: [{
                    label: 'Prescriptions (k)',
                    data: rnd(12, 68, 92),
                    borderColor: '#ce93d8',
                    backgroundColor: 'rgba(206,147,216,.08)',
                    tension: .4,
                    fill: true
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: axDark,
                    y: axDark
                },
                responsive: true
            }
        });
    }

    // ═══════════════════════════════════════
    //  INIT
    // ═══════════════════════════════════════
    document.addEventListener('DOMContentLoaded', () => {
        buildOverviewCharts();
        drawnCharts.overview = 1;
        renderDistrictRows();
        renderFacilities();
        // Animate KPI numbers
        document.querySelectorAll('.k-value').forEach(el => {
            const target = el.textContent;
            if (!isNaN(target.replace(/[,₹CrK%]/g, ''))) el.style.opacity = 0;
            setTimeout(() => {
                el.style.transition = 'opacity .5s';
                el.style.opacity = 1;
            }, 300);
        });
    });
    </script>
    @endpush