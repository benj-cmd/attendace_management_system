<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Section.php';

require_auth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    $studentId = $_POST['student_id'] ?? '';
    
    if (!$studentId) {
        echo json_encode(['success' => false, 'error' => 'Student ID required']);
        exit;
    }
    
    $studentId = (int)$studentId;
    
    // Get student info with sections
    $students = Student::all(); // This includes section_names
    $studentInfo = null;
    
    foreach ($students as $student) {
        if ((int)$student['id'] === $studentId) {
            $studentInfo = $student;
            break;
        }
    }
    
    if (!$studentInfo) {
        echo json_encode(['success' => false, 'error' => 'Student not found']);
        exit;
    }
    
    // Parse section names to find section IDs
    $sectionNames = $studentInfo['section_names'] ?? '';
    $sectionIds = [];
    
    if ($sectionNames) {
        // Get all sections to map names to IDs
        $allSections = Section::all();
        $nameToIdMap = [];
        
        foreach ($allSections as $section) {
            $nameToIdMap[$section['name']] = (int)$section['id'];
        }
        
        // Split section names and map to IDs
        $sectionNameArray = array_map('trim', explode(',', $sectionNames));
        foreach ($sectionNameArray as $name) {
            if (isset($nameToIdMap[$name])) {
                $sectionIds[] = $nameToIdMap[$name];
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'student' => [
            'id' => (int)$studentInfo['id'],
            'name' => trim($studentInfo['first_name'] . ' ' . $studentInfo['last_name']),
            'student_number' => $studentInfo['student_number'],
            'section_ids' => $sectionIds,
            'section_names' => $sectionNames
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred: ' . $e->getMessage()
    ]);
}
