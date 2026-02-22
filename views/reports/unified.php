<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<style>
.filter-panel {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.summary-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

.status-badge {
    font-size: 0.85rem;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
}

.table-container {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.table-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    padding: 16px 20px;
}

.search-box {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 8px 12px;
    transition: all 0.2s;
}

.search-box:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.pagination-controls {
    border-top: 1px solid #e2e8f0;
    padding: 16px 20px;
    background: #f8fafc;
}

.sortable {
    cursor: pointer;
    user-select: none;
}

.sortable:hover {
    background-color: rgba(79, 70, 229, 0.05);
}

.sort-indicator {
    font-size: 0.8rem;
    margin-left: 4px;
}

.student-dropdown-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
}

.student-dropdown-item:hover,
.student-dropdown-item.active {
    background-color: #f8f9fa;
}

.student-dropdown-item:last-child {
    border-bottom: none;
}

.student-dropdown-item .student-name {
    font-weight: 500;
    color: #333;
}

.student-dropdown-item .student-number {
    font-size: 0.85rem;
    color: #666;
}

.student-dropdown-item .student-section {
    font-size: 0.8rem;
    color: #888;
    margin-top: 2px;
}

.student-filter-disabled {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}

.student-filter-disabled::placeholder {
    color: #adb5bd;
}

@media print {
    .filter-panel, .no-print {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>

<div class="filter-panel">
    <div class="row g-3">
        <div class="col-12 col-md-3">
            <label class="form-label fw-medium">Section</label>
            <select id="sectionFilter" class="form-select">
                <option value="">All Sections</option>
                <?php foreach ($sections as $section): ?>
                    <option value="<?= $section['id'] ?>" <?= ($filters['sectionId'] ?? '') == $section['id'] ? 'selected' : '' ?>>
                        <?= e($section['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="col-12 col-md-3">
            <label class="form-label fw-medium">Student</label>
            <div class="position-relative">
                <input type="text" id="studentFilter" class="form-control" placeholder="Search students..." autocomplete="off">
                <input type="hidden" id="studentFilterId" value="">
                <div id="studentDropdown" class="position-absolute w-100 bg-white border border-top-0 rounded-bottom shadow-sm" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"></div>
            </div>
            <small class="text-muted"></small>
        </div>
        
        <div class="col-12 col-md-6">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">From Date</label>
                    <input type="date" id="dateFromFilter" class="form-control" value="<?= e($filters['dateFrom'] ?? '') ?>">
                </div>
                
                <div class="col-12 col-md-6">
                    <label class="form-label fw-medium">To Date</label>
                    <input type="date" id="dateToFilter" class="form-control" value="<?= e($filters['dateTo'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex gap-2">
                <button id="applyFilters" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Apply Filters
                </button>
                <button id="resetFilters" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-3">
        <div class="card summary-card h-100 border-0">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-check-circle text-success fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-success"><?= $summary['present'] ?></h3>
                <div class="text-muted">Present</div>
                <div class="small text-success mt-1"><?= $summary['attendance_rate'] ?>% Rate</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card summary-card h-100 border-0">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-x-circle text-danger fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-danger"><?= $summary['absent'] ?></h3>
                <div class="text-muted">Absent</div>
                <div class="small text-danger mt-1"><?= $summary['total'] > 0 ? round(($summary['absent']/$summary['total'])*100, 1) : 0 ?>%</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card summary-card h-100 border-0">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-clock text-warning fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-warning"><?= $summary['late'] ?></h3>
                <div class="text-muted">Late</div>
                <div class="small text-warning mt-1"><?= $summary['total'] > 0 ? round(($summary['late']/$summary['total'])*100, 1) : 0 ?>%</div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card summary-card h-100 border-0">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-people text-info fs-3"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1 text-info"><?= $summary['total'] ?></h3>
                <div class="text-muted">Total Records</div>
                <div class="small text-info mt-1">Filtered Results</div>
            </div>
        </div>
    </div>
</div>

<!-- Date Range Summary -->
<?php if (empty($filters['studentId'])): ?>
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="table-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-range me-2"></i>
                    Summary
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($dateRangeSummary['daily_breakdown']) && count($dateRangeSummary['daily_breakdown']) <= 31): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="dateRangeTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th>Total</th>
                                    <th>Attendance Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dateRangeSummary['daily_breakdown'] as $day): ?>
                                    <tr>
                                        <td class="fw-medium"><?= date('M j, Y', strtotime($day['date'])) ?></td>
                                        <td><span class="badge bg-success bg-opacity-10 text-success"><?= $day['present'] ?></span></td>
                                        <td><span class="badge bg-danger bg-opacity-10 text-danger"><?= $day['absent'] ?></span></td>
                                        <td><span class="badge bg-warning bg-opacity-10 text-warning"><?= $day['late'] ?></span></td>
                                        <td><?= $day['total'] ?></td>
                                        <td>
                                            <?php if ($day['total'] > 0): ?>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2"><?= round(($day['present']/$day['total'])*100, 1) ?>%</div>
                                                    <div style="width: 80px;">
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-success" style="width: <?= ($day['present']/$day['total'])*100 ?>%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">No data</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 mb-3"></i>
                        <p class="mb-0">Detailed daily breakdown available for periods up to 31 days</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Student Breakdown Table -->
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="table-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i>
                    Student Attendance Records
                </h5>
                <div class="d-flex gap-2">
                    <select id="recordsPerPage" class="form-select" style="width: auto;">
                        <option value="10">10 per page</option>
                        <option value="25" selected>25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="studentTable">
                        <thead class="table-light">
                            <tr>
                                <th class="sortable" data-sort="student_name">
                                    Student Name <span class="sort-indicator"></span>
                                </th>
                                <th class="sortable" data-sort="section_name">
                                    Section <span class="sort-indicator"></span>
                                </th>
                                <th class="sortable" data-sort="date">
                                    Date <span class="sort-indicator"></span>
                                </th>
                                <th class="sortable" data-sort="status">
                                    Status <span class="sort-indicator"></span>
                                </th>
                                <th>Time Marked</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                            <?php foreach ($studentBreakdown as $record): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                <i class="bi bi-person text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium"><?= e($record['student_name']) ?></div>
                                                <div class="small text-muted">#<?= e($record['student_number']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            <?= e($record['section_name']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($record['date'])) ?></td>
                                    <td>
                                        <?php
                                            $statusClass = '';
                                            switch ($record['status']) {
                                                case 'Present': $statusClass = 'success'; break;
                                                case 'Absent': $statusClass = 'danger'; break;
                                                case 'Late': $statusClass = 'warning'; break;
                                            }
                                        ?>
                                        <span class="status-badge bg-<?= $statusClass ?> bg-opacity-10 text-<?= $statusClass ?>">
                                            <?= e($record['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <?= e($record['time_marked']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination-controls d-flex justify-content-between align-items-center">
                    <div>
                        <span id="paginationInfo">Showing 1 to <?= min(25, count($studentBreakdown)) ?> of <?= count($studentBreakdown) ?> records</span>
                    </div>
                    <div>
                        <nav>
                            <ul class="pagination mb-0" id="pagination">
                                <!-- Pagination will be generated by JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for table sorting and pagination
let currentData = <?= json_encode($studentBreakdown) ?>;
let currentSort = { column: 'date', direction: 'desc' };
let currentPage = 1;
let recordsPerPage = 25;
let searchQuery = '';
let allStudents = []; // Cache for all students
let filteredStudents = []; // Students filtered by section
let selectedStudent = null; // Currently selected student

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    // Initialize student data
    allStudents = <?= json_encode($studentOptions) ?>;
    filteredStudents = allStudents;
    
    // Set up event listeners
    setupEventListeners();
    
    // Initialize student filter state
    updateStudentFilterState();
    
    // Initialize table
    renderTable();
    setupPagination();
});

function setupEventListeners() {
    // Filter controls
    const sectionFilter = document.getElementById('sectionFilter');
    sectionFilter.addEventListener('change', updateStudentDropdown);
    
    // Student autocomplete
    const studentFilter = document.getElementById('studentFilter');
    studentFilter.addEventListener('input', handleStudentInput);
    studentFilter.addEventListener('focus', handleStudentFocus);
    studentFilter.addEventListener('blur', handleStudentBlur);
    studentFilter.addEventListener('keydown', handleStudentKeydown);
    
    // Other controls
    document.getElementById('applyFilters').addEventListener('click', applyFilters);
    document.getElementById('resetFilters').addEventListener('click', resetFilters);
    
    // Pagination
    document.getElementById('recordsPerPage').addEventListener('change', function(e) {
        recordsPerPage = parseInt(e.target.value);
        currentPage = 1;
        renderTable();
        setupPagination();
    });
    
    // Date range validation
    const dateFrom = document.getElementById('dateFromFilter');
    const dateTo = document.getElementById('dateToFilter');
    
    dateFrom.addEventListener('change', function() {
        if (dateFrom.value && dateTo.value && dateFrom.value > dateTo.value) {
            dateTo.value = dateFrom.value;
        }
    });
    
    dateTo.addEventListener('change', function() {
        if (dateFrom.value && dateTo.value && dateTo.value < dateFrom.value) {
            dateFrom.value = dateTo.value;
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#studentFilter') && !e.target.closest('#studentDropdown')) {
            hideStudentDropdown();
        }
    });
}

function updateStudentDropdown() {
    const sectionId = document.getElementById('sectionFilter').value;
    
    // Clear selected student
    document.getElementById('studentFilterId').value = '';
    document.getElementById('studentFilter').value = '';
    
    // Update student filter state
    updateStudentFilterState();
    
    // Load students for selected section
    loadStudentsBySection(sectionId);
}

function updateStudentFilterState() {
    const sectionId = document.getElementById('sectionFilter').value;
    const studentFilter = document.getElementById('studentFilter');
    
    // Always enable student filter - search will work with all students or section-specific students
    studentFilter.classList.remove('student-filter-disabled');
    studentFilter.placeholder = 'Search students...';
    studentFilter.disabled = false;
}

function loadStudentsBySection(sectionId) {
    fetch('get_students_by_section.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `section_id=${sectionId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            filteredStudents = data.students;
            console.log('Loaded students for section:', filteredStudents.length);
        } else {
            console.error('Error loading students:', data.error);
            filteredStudents = [];
        }
    })
    .catch(error => {
        console.error('Error:', error);
        filteredStudents = [];
    });
}

function hideStudentDropdown() {
    const dropdown = document.getElementById('studentDropdown');
    if (dropdown) {
        dropdown.style.display = 'none';
    }
}

function handleStudentInput(e) {
    const query = e.target.value.toLowerCase().trim();
    
    if (query.length < 2) {
        hideStudentDropdown();
        return;
    }
    
    const sectionId = document.getElementById('sectionFilter').value;
    
    // Use allStudents if no section is selected, otherwise use filteredStudents
    const studentsToSearch = sectionId === '' ? allStudents : filteredStudents;
    
    if (studentsToSearch.length === 0) return;
    
    // Filter students based on query
    const matches = studentsToSearch.filter(student => 
        student.name.toLowerCase().includes(query) ||
        student.student_number.toLowerCase().includes(query)
    );
    
    showStudentDropdown(matches);
}

function handleStudentFocus(e) {
    const query = e.target.value.toLowerCase().trim();
    const sectionId = document.getElementById('sectionFilter').value;
    
    if (query.length < 2) return;
    
    // Use allStudents if no section is selected, otherwise use filteredStudents
    const studentsToShow = sectionId === '' ? allStudents : filteredStudents;
    
    // Show all students if there aren't too many
    if (studentsToShow.length <= 20) {
        showStudentDropdown(studentsToShow);
    }
}

function handleStudentBlur(e) {
    // Delay hiding to allow clicking on dropdown items
    setTimeout(hideStudentDropdown, 150);
}

function handleStudentKeydown(e) {
    const dropdown = document.getElementById('studentDropdown');
    const items = dropdown.querySelectorAll('.student-dropdown-item');
    const activeItem = dropdown.querySelector('.student-dropdown-item.active');
    
    let currentIndex = activeItem ? Array.from(items).indexOf(activeItem) : -1;
    
    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            currentIndex = Math.min(currentIndex + 1, items.length - 1);
            setActiveStudentItem(items, currentIndex);
            break;
        case 'ArrowUp':
            e.preventDefault();
            currentIndex = Math.max(currentIndex - 1, 0);
            setActiveStudentItem(items, currentIndex);
            break;
        case 'Enter':
            e.preventDefault();
            if (activeItem) {
                selectStudent(activeItem.dataset.studentId, activeItem.dataset.studentName);
            }
            break;
        case 'Escape':
            hideStudentDropdown();
            break;
    }
}

function showStudentDropdown(students) {
    const dropdown = document.getElementById('studentDropdown');
    
    if (students.length === 0) {
        dropdown.innerHTML = '<div class="student-dropdown-item text-muted">No students found</div>';
    } else {
        dropdown.innerHTML = students.map(student => `
            <div class="student-dropdown-item" 
                 data-student-id="${student.id}" 
                 data-student-name="${student.name}"
                 onclick="selectStudent(${student.id}, '${escapeHtml(student.name)}')">
                <div class="student-name">${escapeHtml(student.name)}</div>
                <div class="student-number">#${escapeHtml(student.student_number)}</div>
            </div>
        `).join('');
    }
    
    dropdown.style.display = 'block';
}

function setActiveStudentItem(items, index) {
    items.forEach(item => item.classList.remove('active'));
    if (items[index]) {
        items[index].classList.add('active');
        items[index].scrollIntoView({ block: 'nearest' });
    }
}

function selectStudent(studentId, studentName) {
    selectedStudent = { id: studentId, name: studentName };
    document.getElementById('studentFilterId').value = studentId;
    document.getElementById('studentFilter').value = studentName;
    hideStudentDropdown();
    
    // Auto-detect and set section if student is selected first
    autoSetStudentSection(studentId);
}

function autoSetStudentSection(studentId) {
    fetch('get_student_section.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `student_id=${studentId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.student.section_ids.length > 0) {
            // Set section to the first section the student belongs to
            const sectionId = data.student.section_ids[0];
            document.getElementById('sectionFilter').value = sectionId;
            
            // Reload students for this section
            loadStudentsBySection(sectionId);
            
            // Update filter state
            updateStudentFilterState();
        }
    })
    .catch(error => {
        console.error('Error getting student section:', error);
    });
}

function applyFilters() {
    const sectionId = document.getElementById('sectionFilter').value;
    const studentId = document.getElementById('studentFilterId').value; // Use hidden input
    const dateFrom = document.getElementById('dateFromFilter').value;
    const dateTo = document.getElementById('dateToFilter').value;
    
    console.log('Applying filters:', { sectionId, studentId, dateFrom, dateTo });
    
    // Show loading state
    const applyBtn = document.getElementById('applyFilters');
    const originalText = applyBtn.innerHTML;
    applyBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Loading...';
    applyBtn.disabled = true;
    
    // Make AJAX request to get filtered data
    fetch('unified_report_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `section_id=${sectionId}&student_id=${studentId}&date_from=${dateFrom}&date_to=${dateTo}`
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Received data:', data);
        if (!data.success) {
            console.error('Server error:', data.error);
            alert('Error: ' + data.error);
            return;
        }
        
        // Update all data
        currentData = data.studentBreakdown;
        updateSummaryCards(data.summary);
        updateDateRangeSummary(data.dateRangeSummary);
        
        // Reset pagination and search
        currentPage = 1;
        searchQuery = '';
        
        // Re-render table
        renderTable();
        setupPagination();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error applying filters. Please try again.');
    })
    .finally(() => {
        applyBtn.innerHTML = originalText;
        applyBtn.disabled = false;
    });
}

function resetFilters() {
    document.getElementById('sectionFilter').value = '';
    document.getElementById('studentFilterId').value = ''; // Clear hidden input
    document.getElementById('studentFilter').value = '';
    document.getElementById('dateFromFilter').value = '';
    document.getElementById('dateToFilter').value = '';
    
    // Reload page to reset to default state
    window.location.href = 'attendance_report.php';
}

function updateSummaryCards(summary) {
    // Get all summary cards
    const cards = document.querySelectorAll('.summary-card');
    
    if (cards.length >= 4) {
        try {
            // Update Present card (1st card)
            const presentH3 = cards[0].querySelector('h3');
            const presentSmall = cards[0].querySelector('.small');
            if (presentH3) presentH3.textContent = summary.present;
            if (presentSmall) {
                const presentRate = summary.total > 0 ? ((summary.present/summary.total)*100).toFixed(1) : 0;
                presentSmall.textContent = presentRate + '% Rate';
            }
            
            // Update Absent card (2nd card)
            const absentH3 = cards[1].querySelector('h3');
            const absentSmall = cards[1].querySelector('.small');
            if (absentH3) absentH3.textContent = summary.absent;
            if (absentSmall) {
                const absentRate = summary.total > 0 ? ((summary.absent/summary.total)*100).toFixed(1) : 0;
                absentSmall.textContent = absentRate + '%';
            }
            
            // Update Late card (3rd card)
            const lateH3 = cards[2].querySelector('h3');
            const lateSmall = cards[2].querySelector('.small');
            if (lateH3) lateH3.textContent = summary.late;
            if (lateSmall) {
                const lateRate = summary.total > 0 ? ((summary.late/summary.total)*100).toFixed(1) : 0;
                lateSmall.textContent = lateRate + '%';
            }
            
            // Update Total card (4th card)
            const totalH3 = cards[3].querySelector('h3');
            if (totalH3) totalH3.textContent = summary.total;
        } catch (error) {
            console.error('Error updating summary cards:', error);
        }
    } else {
        console.warn('Expected 4 summary cards, found:', cards.length);
    }
}

function updateDateRangeSummary(dateData) {
    // Check if a student is selected
    const studentId = document.getElementById('studentFilterId').value;
    
    // Find the date range summary row (the row containing the date range table)
    let dateRangeRow = null;
    const rows = document.querySelectorAll('.row');
    for (let row of rows) {
        if (row.querySelector('#dateRangeTable')) {
            dateRangeRow = row;
            break;
        }
    }
    
    if (studentId) {
        // Hide the date range summary when a student is selected
        if (dateRangeRow) {
            dateRangeRow.style.display = 'none';
        }
        return;
    }
    
    // Show the date range summary when no student is selected
    if (dateRangeRow) {
        dateRangeRow.style.display = '';
    }
    
    // Update the date range summary table
    const tableBody = document.querySelector('#dateRangeTable tbody');
    if (!tableBody) return;
    
    tableBody.innerHTML = '';
    
    if (dateData.daily_breakdown && dateData.daily_breakdown.length > 0) {
        dateData.daily_breakdown.forEach(day => {
            const row = document.createElement('tr');
            const attendanceRate = day.total > 0 ? ((day.present/day.total)*100).toFixed(1) : 0;
            row.innerHTML = `
                <td class="fw-medium">${new Date(day.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
                <td><span class="badge bg-success bg-opacity-10 text-success">${day.present}</span></td>
                <td><span class="badge bg-danger bg-opacity-10 text-danger">${day.absent}</span></td>
                <td><span class="badge bg-warning bg-opacity-10 text-warning">${day.late}</span></td>
                <td>${day.total}</td>
                <td>
                    ${day.total > 0 ? 
                        `<div class="d-flex align-items-center">
                            <div class="me-2">${attendanceRate}%</div>
                            <div style="width: 80px;">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: ${attendanceRate}%"></div>
                                </div>
                            </div>
                        </div>` : 
                        '<span class="text-muted">No data</span>'}
                </td>
            `;
            tableBody.appendChild(row);
        });
    } else {
        const row = document.createElement('tr');
        row.innerHTML = '<td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1 mb-3"></i><p class="mb-0">Detailed daily breakdown available for periods up to 31 days</p></td>';
        tableBody.appendChild(row);
    }
}

function renderTable() {
    const tableBody = document.getElementById('studentTableBody');
    let filteredData = currentData;
    
    // Apply search filter
    if (searchQuery) {
        filteredData = filteredData.filter(record => 
            record.student_name.toLowerCase().includes(searchQuery) ||
            record.student_number.toLowerCase().includes(searchQuery) ||
            record.section_name.toLowerCase().includes(searchQuery) ||
            record.status.toLowerCase().includes(searchQuery) ||
            (record.time_marked && record.time_marked.toLowerCase().includes(searchQuery))
        );
    }
    
    // Apply sorting
    filteredData.sort((a, b) => {
        let aVal = a[currentSort.column];
        let bVal = b[currentSort.column];
        
        if (currentSort.column === 'date') {
            aVal = new Date(aVal);
            bVal = new Date(bVal);
        }
        
        if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
        if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });
    
    // Apply pagination
    const startIndex = (currentPage - 1) * recordsPerPage;
    const endIndex = startIndex + recordsPerPage;
    const pageData = filteredData.slice(startIndex, endIndex);
    
    // Render table rows
    tableBody.innerHTML = '';
    pageData.forEach(record => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="bi bi-person text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-medium">${escapeHtml(record.student_name)}</div>
                        <div class="small text-muted">#${escapeHtml(record.student_number)}</div>
                    </div>
                </div>
            </td>
            <td>
                <span class="badge bg-info bg-opacity-10 text-info">
                    ${escapeHtml(record.section_name)}
                </span>
            </td>
            <td>${new Date(record.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
            <td>
                <span class="status-badge bg-${getStatusClass(record.status)} bg-opacity-10 text-${getStatusClass(record.status)}">
                    ${escapeHtml(record.status)}
                </span>
            </td>
            <td class="text-muted small">
                ${escapeHtml(record.time_marked)}
            </td>
        `;
        tableBody.appendChild(row);
    });
    
    // Update pagination info
    const totalRecords = filteredData.length;
    const startRecord = totalRecords > 0 ? startIndex + 1 : 0;
    const endRecord = Math.min(endIndex, totalRecords);
    document.getElementById('paginationInfo').textContent = 
        `Showing ${startRecord} to ${endRecord} of ${totalRecords} records`;
}

function setupPagination() {
    const filteredData = getFilteredData();
    const totalPages = Math.ceil(filteredData.length / recordsPerPage);
    const pagination = document.getElementById('pagination');
    
    pagination.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>`;
    pagination.appendChild(prevLi);
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
        pagination.appendChild(li);
    }
    
    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>`;
    pagination.appendChild(nextLi);
}

function changePage(page) {
    const filteredData = getFilteredData();
    const totalPages = Math.ceil(filteredData.length / recordsPerPage);
    
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderTable();
        setupPagination();
    }
}

function getFilteredData() {
    let filteredData = currentData;
    
    if (searchQuery) {
        filteredData = filteredData.filter(record => 
            record.student_name.toLowerCase().includes(searchQuery) ||
            record.student_number.toLowerCase().includes(searchQuery) ||
            record.section_name.toLowerCase().includes(searchQuery) ||
            record.status.toLowerCase().includes(searchQuery)
        );
    }
    
    return filteredData;
}

function getStatusClass(status) {
    switch (status) {
        case 'Present': return 'success';
        case 'Absent': return 'danger';
        case 'Late': return 'warning';
        default: return 'secondary';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Add sortable column functionality
document.querySelectorAll('.sortable').forEach(header => {
    header.addEventListener('click', function() {
        const column = this.getAttribute('data-sort');
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.direction = 'asc';
        }
        
        // Update sort indicators
        document.querySelectorAll('.sort-indicator').forEach(indicator => {
            indicator.textContent = '';
        });
        
        const indicator = this.querySelector('.sort-indicator');
        indicator.textContent = currentSort.direction === 'asc' ? ' ↑' : ' ↓';
        
        renderTable();
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>