<?php
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/teacher/StudentsModel.php';

class StudentService extends Model {
    protected $model;
    protected $students = 'students';
    protected $sections = 'sections';
    protected $grade_levels = 'grade_levels';

    public function __construct($con, ?StudentsModel $model = null) {
        parent::__construct($con);
        $this->model = $model;
    }

    /**
     * Students belonging to the given teacher's advisory section(s), optionally
     * scoped to a school year. Returns [] if the teacher advises no section.
     */
    public function getStudentsByAdviser(int $teacherId, ?int $schoolYearId = null): array {
        try{
            $query = "SELECT
                s.*,
                gl.grade_name AS grade_name,
                ss.section_name AS section_name
                FROM {$this->students} s
                LEFT JOIN {$this->sections} ss ON s.section_id = ss.id
                LEFT JOIN {$this->grade_levels} gl ON ss.grade_level_id = gl.id
                WHERE ss.adviser_id = ? AND s.status = 'active'";
            if($schoolYearId !== null){
                $query .= " AND s.school_year_id = ?";
            }
            $stmt = $this->con->prepare($query);
            if($schoolYearId !== null){
                $stmt->bind_param('ii', $teacherId, $schoolYearId);
            }else{
                $stmt->bind_param('i', $teacherId);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }catch(Exception $e){
            error_log("Error fetching students by adviser: " . $e->getMessage());
            return [];
        }
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

    public function getPaginatedStudents(int $teacherId, int $page = 1, int $perPage = 10): array {
        $page   = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $rows  = $this->model->getPage($perPage, $offset, $teacherId);
        $total = $this->model->countAll($teacherId);

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