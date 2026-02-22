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
    $sectionId = $_POST['section_id'] ?? '';
    
    // Convert empty string to null for "all sections"
    $sectionId = $sectionId ? (int)$sectionId : null;
    
    if ($sectionId) {
        // Validate that section exists
        $section = Section::findById($sectionId);
        if (!$section) {
            echo json_encode(['success' => false, 'error' => 'Section not found']);
            exit;
        }
        
        $students = Student::findBySection($sectionId);
    } else {
        $students = Student::all();
    }
    
    // Build student options for dropdown
    $studentOptions = [];
    foreach ($students as $student) {
        $studentOptions[] = [
            'id' => (int)$student['id'],
            'name' => trim($student['first_name'] . ' ' . $student['last_name']),
            'student_number' => $student['student_number'],
            'section_id' => (int)$student['section_id'] ?? null
        ];
    }
    
    echo json_encode([
        'success' => true,
        'students' => $studentOptions
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'An error occurred: ' . $e->getMessage()
    ]);
}
