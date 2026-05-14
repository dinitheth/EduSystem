@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your school data')

@section('content')

<style>
    .dashboard-grid-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 14px rgba(15, 23, 42, 0.06);
        overflow: hidden;
        height: 100%;
        background: #fff;
    }
    .dashboard-grid-header {
        padding: 18px 20px 14px;
        border-bottom: 1px solid #eef2f7;
    }
    .dashboard-grid-title {
        margin: 0;
        font-size: 0.98rem;
        font-weight: 700;
        color: #0f172a;
    }
    .dashboard-grid-subtitle {
        margin-top: 4px;
        font-size: 0.8rem;
        color: #64748b;
    }
    .dashboard-grid-body {
        padding: 18px 20px 20px;
    }
    .stat-card {
        min-height: 208px;
    }
    .kpi-note {
        margin-top: 10px;
        font-size: 0.78rem;
        opacity: 0.86;
    }
    .chart-shell {
        position: relative;
        height: 300px;
    }
    .chart-shell-sm {
        position: relative;
        height: 320px;
    }
    .chart-legend-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 18px;
    }
    .chart-legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        font-size: 0.82rem;
        color: #475569;
    }
    .chart-legend-left {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .chart-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .chart-legend-value {
        font-weight: 700;
        color: #111827;
    }
    .mini-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 18px;
    }
    .mini-summary-tile {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 14px;
        background: #f8fafc;
    }
    .mini-summary-label {
        font-size: 0.76rem;
        color: #64748b;
        margin-bottom: 5px;
    }
    .mini-summary-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #111827;
        line-height: 1.1;
    }
    .mini-summary-meta {
        margin-top: 4px;
        font-size: 0.74rem;
        color: #94a3b8;
    }
    .mini-summary-list {
        margin-top: 8px;
        font-size: 0.76rem;
        color: #475569;
        line-height: 1.45;
        min-height: 32px;
    }
    .mini-summary-actions {
        margin-top: 10px;
        display: flex;
        justify-content: flex-end;
    }
    .mini-summary-button {
        border: 0;
        background: transparent;
        color: #2563eb;
        font-size: 0.76rem;
        font-weight: 600;
        padding: 0;
    }
    .mini-summary-button:hover {
        color: #1d4ed8;
    }
    .gap-modal-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .gap-modal-item {
        padding: 10px 12px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        font-size: 0.84rem;
        color: #334155;
    }
    @media (max-width: 1199.98px) {
        .chart-shell,
        .chart-shell-sm {
            height: 280px;
        }
    }
</style>

<div class="row g-4">
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('students.index') }}" class="stat-card d-block" style="background: linear-gradient(135deg,#6366f1,#818cf8); color:#fff; text-decoration:none;">
            <div class="stat-icon" style="background:rgba(255,255,255,0.2);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-num">{{ $totalStudents }}</div>
            <div class="stat-label">Total Students</div>
            <div class="kpi-note">
                <i class="bi bi-arrow-right-circle me-1"></i>View student records
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('teachers.index') }}" class="stat-card d-block" style="background: linear-gradient(135deg,#0ea5e9,#38bdf8); color:#fff; text-decoration:none;">
            <div class="stat-icon" style="background:rgba(255,255,255,0.2);">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div class="stat-num">{{ $totalTeachers }}</div>
            <div class="stat-label">Total Teachers</div>
            <div class="kpi-note">
                <i class="bi bi-arrow-right-circle me-1"></i>View teacher records
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('subjects.index') }}" class="stat-card d-block" style="background: linear-gradient(135deg,#10b981,#34d399); color:#fff; text-decoration:none;">
            <div class="stat-icon" style="background:rgba(255,255,255,0.2);">
                <i class="bi bi-book-fill"></i>
            </div>
            <div class="stat-num">{{ $totalSubjects }}</div>
            <div class="stat-label">Total Subjects</div>
            <div class="kpi-note">
                <i class="bi bi-arrow-right-circle me-1"></i>View subject records
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="{{ route('students.index') }}" class="stat-card d-block" style="background: linear-gradient(135deg,#0f172a,#334155); color:#fff; text-decoration:none;">
            <div class="stat-icon" style="background:rgba(255,255,255,0.14);">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div class="stat-num">{{ $activeStudents }}</div>
            <div class="stat-label">Active Students</div>
            <div class="kpi-note">
                <i class="bi bi-arrow-right-circle me-1"></i>Current active registrations
            </div>
        </a>
    </div>

    <div class="col-xl-4">
        <div class="dashboard-grid-card">
            <div class="dashboard-grid-header">
                <h6 class="dashboard-grid-title">Student Gender Summary</h6>
                <div class="dashboard-grid-subtitle">Current breakdown of registered students</div>
            </div>
            <div class="dashboard-grid-body">
                <div class="chart-shell">
                    <canvas id="genderDonutChart"></canvas>
                </div>
                <div class="chart-legend-list">
                    <div class="chart-legend-item">
                        <div class="chart-legend-left">
                            <span class="chart-dot" style="background:#6366f1;"></span>
                            <span>Male Students</span>
                        </div>
                        <span class="chart-legend-value">{{ $genderSummary['male'] }}</span>
                    </div>
                    <div class="chart-legend-item">
                        <div class="chart-legend-left">
                            <span class="chart-dot" style="background:#ec4899;"></span>
                            <span>Female Students</span>
                        </div>
                        <span class="chart-legend-value">{{ $genderSummary['female'] }}</span>
                    </div>
                    <div class="chart-legend-item">
                        <div class="chart-legend-left">
                            <span class="chart-dot" style="background:#f59e0b;"></span>
                            <span>Other</span>
                        </div>
                        <span class="chart-legend-value">{{ $genderSummary['other'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="dashboard-grid-card">
            <div class="dashboard-grid-header">
                <h6 class="dashboard-grid-title">Assignment Gaps</h6>
                <div class="dashboard-grid-subtitle">Records and setup areas that still need attention</div>
            </div>
            <div class="dashboard-grid-body">
                <div class="chart-shell-sm">
                    <canvas id="assignmentGapChart"></canvas>
                </div>
                <div class="mini-summary-grid">
                    <div class="mini-summary-tile">
                        <div class="mini-summary-label">Students Missing Subjects</div>
                        <div class="mini-summary-value">{{ $studentsWithoutSubjects }}</div>
                        <div class="mini-summary-meta">Student assignment gaps</div>
                        @if($studentsWithoutSubjectsFullList->count() > 0)
                            <div class="mini-summary-actions">
                                <button
                                    type="button"
                                    class="mini-summary-button gap-detail-trigger"
                                    data-title="Students Missing Subjects"
                                    data-items='@json($studentsWithoutSubjectsFullList->values())'
                                >
                                    View all
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="mini-summary-tile">
                        <div class="mini-summary-label">Teachers Missing Subjects</div>
                        <div class="mini-summary-value">{{ $teachersWithoutSubjects }}</div>
                        <div class="mini-summary-meta">Teacher assignment gaps</div>
                        @if($teachersWithoutSubjectsFullList->count() > 0)
                            <div class="mini-summary-actions">
                                <button
                                    type="button"
                                    class="mini-summary-button gap-detail-trigger"
                                    data-title="Teachers Missing Subjects"
                                    data-items='@json($teachersWithoutSubjectsFullList->values())'
                                >
                                    View all
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="mini-summary-tile">
                        <div class="mini-summary-label">Inactive Subjects</div>
                        <div class="mini-summary-value">{{ $statusSummary['inactive_subjects'] }}</div>
                        <div class="mini-summary-meta">Subjects currently inactive</div>
                        @if($inactiveSubjectsFullList->count() > 0)
                            <div class="mini-summary-actions">
                                <button
                                    type="button"
                                    class="mini-summary-button gap-detail-trigger"
                                    data-title="Inactive Subjects"
                                    data-items='@json($inactiveSubjectsFullList->values())'
                                >
                                    View all
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="mini-summary-tile">
                        <div class="mini-summary-label">Unused Subjects</div>
                        <div class="mini-summary-value">{{ $unusedSubjects }}</div>
                        <div class="mini-summary-meta">No student or teacher links</div>
                        @if($unusedSubjectsFullList->count() > 0)
                            <div class="mini-summary-actions">
                                <button
                                    type="button"
                                    class="mini-summary-button gap-detail-trigger"
                                    data-title="Unused Subjects"
                                    data-items='@json($unusedSubjectsFullList->values())'
                                >
                                    View all
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="dashboard-grid-card">
            <div class="dashboard-grid-header">
                <h6 class="dashboard-grid-title">Most Used Subjects</h6>
                <div class="dashboard-grid-subtitle">Top subject coverage by student and teacher assignments</div>
            </div>
            <div class="dashboard-grid-body">
                <div class="chart-shell">
                    <canvas id="subjectUsageChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="gapDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="gapDetailsTitle">Details</h5>
                    <div class="text-muted small mt-1" id="gapDetailsCount"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div id="gapDetailsList" class="gap-modal-list"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    const genderChartCtx = document.getElementById('genderDonutChart');
    const gapChartCtx = document.getElementById('assignmentGapChart');
    const subjectChartCtx = document.getElementById('subjectUsageChart');

    if (genderChartCtx && window.Chart) {
        new Chart(genderChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female', 'Other'],
                datasets: [{
                    data: [
                        {{ $genderSummary['male'] }},
                        {{ $genderSummary['female'] }},
                        {{ $genderSummary['other'] }}
                    ],
                    backgroundColor: ['#6366f1', '#ec4899', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => `${context.label}: ${context.formattedValue}`
                        }
                    }
                }
            }
        });
    }

    if (gapChartCtx && window.Chart) {
        new Chart(gapChartCtx, {
            type: 'bar',
            data: {
                labels: ['Students Missing Subjects', 'Teachers Missing Subjects', 'Inactive Subjects', 'Unused Subjects'],
                datasets: [{
                    data: [
                        {{ $studentsWithoutSubjects }},
                        {{ $teachersWithoutSubjects }},
                        {{ $statusSummary['inactive_subjects'] }},
                        {{ $unusedSubjects }}
                    ],
                    backgroundColor: ['#f97316', '#fb7185', '#94a3b8', '#475569'],
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 18
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#eef2f7' },
                        ticks: { precision: 0 }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    if (subjectChartCtx && window.Chart) {
        new Chart(subjectChartCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($topSubjects->pluck('subject_name')->values()) !!},
                datasets: [{
                    label: 'Assignments',
                    data: {!! json_encode($topSubjects->pluck('total_assignments')->values()) !!},
                    backgroundColor: ['#0ea5e9', '#38bdf8', '#34d399', '#6366f1', '#f59e0b'],
                    borderRadius: 10,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                return label.length > 14 ? label.slice(0, 14) + '...' : label;
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#eef2f7' },
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    const gapDetailsModalEl = document.getElementById('gapDetailsModal');
    const gapDetailsTitleEl = document.getElementById('gapDetailsTitle');
    const gapDetailsCountEl = document.getElementById('gapDetailsCount');
    const gapDetailsListEl = document.getElementById('gapDetailsList');

    if (gapDetailsModalEl && window.bootstrap) {
        const gapDetailsModal = new bootstrap.Modal(gapDetailsModalEl);

        document.querySelectorAll('.gap-detail-trigger').forEach((button) => {
            button.addEventListener('click', () => {
                const title = button.dataset.title || 'Details';
                const items = JSON.parse(button.dataset.items || '[]');

                gapDetailsTitleEl.textContent = title;
                gapDetailsCountEl.textContent = `${items.length} record(s)`;
                gapDetailsListEl.innerHTML = items.length
                    ? items.map((item) => `<div class="gap-modal-item">${item}</div>`).join('')
                    : '<div class="gap-modal-item">No records found.</div>';

                gapDetailsModal.show();
            });
        });
    }
</script>
@endpush
