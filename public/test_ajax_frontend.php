<?php
require_once __DIR__ . '/../config/auth.php';
require_auth();
$pageTitle = 'AJAX Test';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>AJAX Test Page</h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5>Test Parameters</h5>
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Section ID</label>
                        <input type="number" id="sectionId" class="form-control" value="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Student ID</label>
                        <input type="number" id="studentId" class="form-control" value="">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" id="dateFrom" class="form-control" value="2026-02-01">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" id="dateTo" class="form-control" value="2026-02-22">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label">Status</label>
                        <select id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Late">Late</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button id="testBtn" class="btn btn-primary">Test AJAX Request</button>
                    <button id="resetBtn" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h5>Response</h5>
                <div id="response" class="bg-light p-3 rounded">
                    <em>Click "Test AJAX Request" to see results</em>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('testBtn').addEventListener('click', function() {
        const sectionId = document.getElementById('sectionId').value;
        const studentId = document.getElementById('studentId').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        const status = document.getElementById('status').value;
        
        const responseDiv = document.getElementById('response');
        responseDiv.innerHTML = '<div class="text-info">Sending request...</div>';
        
        console.log('Sending request with params:', {sectionId, studentId, dateFrom, dateTo, status});
        
        fetch('unified_report_ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `section_id=${sectionId}&student_id=${studentId}&date_from=${dateFrom}&date_to=${dateTo}&status=${status}`
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', [...response.headers.entries()]);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            responseDiv.innerHTML = '<pre class="mb-0">' + JSON.stringify(data, null, 2) + '</pre>';
        })
        .catch(error => {
            console.error('Error:', error);
            responseDiv.innerHTML = '<div class="text-danger"><strong>Error:</strong> ' + error.message + '</div>';
        });
    });
    
    document.getElementById('resetBtn').addEventListener('click', function() {
        document.getElementById('sectionId').value = '1';
        document.getElementById('studentId').value = '';
        document.getElementById('dateFrom').value = '2026-02-01';
        document.getElementById('dateTo').value = '2026-02-22';
        document.getElementById('status').value = '';
        document.getElementById('response').innerHTML = '<em>Click "Test AJAX Request" to see results</em>';
    });
    </script>
</body>
</html>