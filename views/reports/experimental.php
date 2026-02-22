<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="row g-4 mb-4">
    <!-- Overall Statistics Cards -->
    <div class="col-12">
        <div class="alert alert-primary d-flex align-items-center">
            <i class="bi bi-stars fs-4 me-3"></i>
            <div>
                <h5 class="mb-1">Experimental: Student Performance Overview</h5>
                <p class="mb-0">30-day attendance analytics and performance insights</p>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-people text-primary fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $overallStats['total_students'] ?></h3>
                <div class="text-muted">Total Students</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-graph-up text-success fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $overallStats['avg_attendance_rate'] ?>%</h3>
                <div class="text-muted">Avg. Attendance Rate</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-trophy text-info fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $overallStats['excellent_students'] ?></h3>
                <div class="text-muted">Excellent Students</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-exclamation-triangle text-warning fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $overallStats['needs_improvement'] ?></h3>
                <div class="text-muted">Need Support</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Performance Distribution -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="bi bi-bar-chart me-2 text-primary"></i>
                    Performance Distribution
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div class="text-center">
                        <div class="display-6 text-success fw-bold"><?= $overallStats['excellent_students'] ?></div>
                        <div class="small text-muted">Excellent (90%+)</div>
                    </div>
                    <div class="text-center">
                        <div class="display-6 text-info fw-bold"><?= $overallStats['good_students'] ?></div>
                        <div class="small text-muted">Good (80-89%)</div>
                    </div>
                    <div class="text-center">
                        <div class="display-6 text-warning fw-bold"><?= $overallStats['needs_improvement'] ?></div>
                        <div class="small text-muted">Needs Improvement</div>
                    </div>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: <?= ($overallStats['excellent_students'] / max($overallStats['total_students'], 1)) * 100 ?>%"></div>
                    <div class="progress-bar bg-info" style="width: <?= ($overallStats['good_students'] / max($overallStats['total_students'], 1)) * 100 ?>%"></div>
                    <div class="progress-bar bg-warning" style="width: <?= ($overallStats['needs_improvement'] / max($overallStats['total_students'], 1)) * 100 ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0 d-flex align-items-center">
                    <i class="bi bi-activity me-2 text-primary"></i>
                    Recent Activity (Last 7 Days)
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">Attendance Patterns</span>
                    <span class="badge bg-light text-dark">Legend: P=Present, A=Absent, L=Late</span>
                </div>
                <?php 
                $recentPatterns = array_filter($attendanceData, fn($d) => !empty($d['recent_pattern']));
                $recentPatterns = array_slice($recentPatterns, 0, 5);
                ?>
                <?php foreach ($recentPatterns as $data): ?>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                                <i class="bi bi-person text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-medium"><?= e((string)$data['student']['fullname']) ?></div>
                                <div class="small text-muted">#<?= e((string)$data['student']['student_number']) ?></div>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <?php foreach ($data['recent_pattern'] as $status): ?>
                                <span class="badge <?= 
                                    $status === 'P' ? 'bg-success' : 
                                    ($status === 'A' ? 'bg-danger' : 'bg-warning') 
                                ?> bg-opacity-10 text-<?= 
                                    $status === 'P' ? 'success' : 
                                    ($status === 'A' ? 'danger' : 'warning') 
                                ?> fw-medium" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                    <?= $status ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recentPatterns)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-clipboard-data fs-1 mb-3"></i>
                        <p class="mb-0">No recent attendance data available</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Student Performance Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="bi bi-table me-2 text-primary"></i>
                Detailed Student Performance (30 Days)
            </h5>
            <div class="d-flex gap-2">
                <input type="date" class="form-control" value="<?= e($date) ?>" disabled>
                <button class="btn btn-outline-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print Report
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Student #</th>
                        <th>Attendance Rate</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Late</th>
                        <th>Performance</th>
                        <th>Recent Pattern</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($attendanceData as $data): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-person text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="fw-medium"><?= e((string)$data['student']['fullname']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark fw-normal">
                                <?= e((string)$data['student']['student_number']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-2 fw-bold" style="min-width: 50px;"><?= $data['attendance_rate'] ?>%</div>
                                <div style="width: 80px;">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar <?= 
                                            $data['attendance_rate'] >= 90 ? 'bg-success' : 
                                            ($data['attendance_rate'] >= 80 ? 'bg-info' : 
                                            ($data['attendance_rate'] >= 70 ? 'bg-warning' : 'bg-danger')) 
                                        ?>" style="width: <?= $data['attendance_rate'] ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success fw-medium">
                                <i class="bi bi-check-circle me-1"></i>
                                <?= $data['present'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-danger bg-opacity-10 text-danger fw-medium">
                                <i class="bi bi-x-circle me-1"></i>
                                <?= $data['absent'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-warning bg-opacity-10 text-warning fw-medium">
                                <i class="bi bi-clock me-1"></i>
                                <?= $data['late'] ?>
                            </span>
                        </td>
                        <td>
                            <?php
                                $trendClass = '';
                                $trendText = '';
                                $trendIcon = '';
                                switch ($data['trend']) {
                                    case 'excellent':
                                        $trendClass = 'success';
                                        $trendText = 'Excellent';
                                        $trendIcon = 'trophy';
                                        break;
                                    case 'good':
                                        $trendClass = 'info';
                                        $trendText = 'Good';
                                        $trendIcon = 'check-circle';
                                        break;
                                    case 'fair':
                                        $trendClass = 'warning';
                                        $trendText = 'Fair';
                                        $trendIcon = 'exclamation-triangle';
                                        break;
                                    default:
                                        $trendClass = 'danger';
                                        $trendText = 'Needs Attention';
                                        $trendIcon = 'exclamation-circle';
                                }
                            ?>
                            <span class="badge bg-<?= $trendClass ?> bg-opacity-10 text-<?= $trendClass ?> fw-medium">
                                <i class="bi bi-<?= $trendIcon ?> me-1"></i>
                                <?= $trendText ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <?php foreach ($data['recent_pattern'] as $status): ?>
                                    <span class="badge <?= 
                                        $status === 'P' ? 'bg-success' : 
                                        ($status === 'A' ? 'bg-danger' : 'bg-warning') 
                                    ?> bg-opacity-10 text-<?= 
                                        $status === 'P' ? 'success' : 
                                        ($status === 'A' ? 'danger' : 'warning') 
                                    ?> fw-medium" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                                        <?= $status ?>
                                    </span>
                                <?php endforeach; ?>
                                <?php if (empty($data['recent_pattern'])): ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-medium">No data</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to table rows
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(79, 70, 229, 0.05)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // Add print functionality
    const printButton = document.querySelector('[onclick="window.print()"]');
    if (printButton) {
        printButton.addEventListener('click', function() {
            // Add print-specific styling
            const style = document.createElement('style');
            style.innerHTML = `
                @media print {
                    .sidebar-modern { display: none !important; }
                    .content { margin-left: 0 !important; }
                    .card-header .btn { display: none !important; }
                    .alert { display: none !important; }
                }
            `;
            document.head.appendChild(style);
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>