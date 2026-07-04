<?php
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/teacher/StudentsModel.php';

class StudentService extends Model {
    protected $model;

    public function __construct($con, StudentsModel $model) {
        parent::__construct($con);
        $this->model = $model;
    }

    public function formatFullName(array $student): string {
        $first  = trim($student['first_name']  ?? '');
        $middle = trim($student['middle_name'] ?? '');
        $last   = trim($student['last_name']   ?? '');
        $suffix = trim($student['suffix']      ?? '');

        // Format: Last, First [Middle] [Suffix]
        $name = $last . ', ' . $first;
        if ($middle !== '') {
            $name .= ' ' . $middle;
        }
        if ($suffix !== '') {
            $name .= ' ' . $suffix;
        }
        return $name;
    }

    public function getPaginatedStudents(int $page = 1, int $perPage = 10): array {
        $page   = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $rows  = $this->model->getPage($perPage, $offset);
        $total = $this->model->countAll();

        $data = array_map(function (array $student): array {
            $student['full_name'] = $this->formatFullName($student);
            return $student;
        }, $rows);

        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'total_pages'  => (int) ceil($total / $perPage),
        ];
    }
}