<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Section.php';

final class StudentController
{
    public static function index(): array
    {
        $error = '';
        $success = '';

        $sections = Section::all();
        $sectionId = (int)get('section_id', '0');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = post('action', 'create_student');

            if ($action === 'create_section') {
                $name = post('section_name');
                if ($name === '') {
                    $error = 'Section name is required.';
                } else {
                    try {
                        Section::create($name);
                        $success = 'Section created.';
                    } catch (PDOException $e) {
                        $error = 'Failed to create section. Please try again.';
                    }
                }
            } else {
                $firstName = post('first_name');
                $middleName = post('middle_name');
                $lastName = post('last_name');
                $address = post('address');
                $email = post('email');
                $contactNumber = post('contact_number');
                $sectionId = (int)post('section_id', '0');

                if ($firstName === '' || $lastName === '' || $address === '' || $email === '' || $contactNumber === '') {
                    $error = 'Please fill in all required fields.';
                } elseif ($sectionId <= 0) {
                    $error = 'Please select a section.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email.';
                } else {
                    try {
                        $newStudentId = Student::create($firstName, $middleName, $lastName, $address, $email, $contactNumber);
                        Section::addStudent($sectionId, $newStudentId);
                        $success = 'Student added.';
                    } catch (PDOException $e) {
                        $error = 'Failed to add student. Please try again.';
                    }
                }
            }

            $sections = Section::all();
        }

        $students = Student::all($sectionId > 0 ? $sectionId : null);

        return compact('students', 'sections', 'sectionId', 'error', 'success');
    }

    public static function edit(int $id): array
    {
        $error = '';
        $success = '';

        $student = Student::findById($id);
        if (!$student) {
            redirect('students.php');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = post('first_name');
            $middleName = post('middle_name');
            $lastName = post('last_name');
            $address = post('address');
            $email = post('email');
            $contactNumber = post('contact_number');

            if ($firstName === '' || $lastName === '' || $address === '' || $email === '' || $contactNumber === '') {
                $error = 'Please fill in all required fields.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email.';
            } else {
                try {
                    Student::update($id, $firstName, $middleName, $lastName, $address, $email, $contactNumber);
                    $success = 'Student updated.';
                    $student = Student::findById($id) ?: $student;
                } catch (PDOException $e) {
                    $error = 'Failed to update student.';
                }
            }
        }

        return compact('student', 'error', 'success');
    }

    public static function delete(int $id): void
    {
        Student::delete($id);
        redirect('students.php');
    }
}
