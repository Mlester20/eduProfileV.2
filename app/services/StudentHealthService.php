<?php
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/teacher/StudentHealthModel.php';

class StudentHealthService extends Model {
    protected $healthModel;
    protected $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    public function __construct($con) {
        parent::__construct($con);
        $this->healthModel = new StudentHealthModel($con);
    }

    // Simplified adult BMI cutoffs (not DepEd's BMI-for-age percentile chart).
    // Swap this for an age/sex-based lookup if that's ever required — the
    // enum values ('Severely Wasted'...'Obese') stay the same either way.
    private function computeBmi($height_cm, $weight_kg) {
        $heightM = $height_cm / 100;
        $bmi = round($weight_kg / ($heightM * $heightM), 2);

        if($bmi < 16.0){
            $classification = 'Severely Wasted';
        }elseif($bmi < 18.5){
            $classification = 'Wasted';
        }elseif($bmi < 25.0){
            $classification = 'Normal';
        }elseif($bmi < 30.0){
            $classification = 'Overweight';
        }else{
            $classification = 'Obese';
        }

        return ['bmi' => $bmi, 'bmi_classification' => $classification];
    }

    private function validate($data, $teacher_id, $excludeId = null) {
        $student_id = $data['student_id'] ?? null;
        $school_year_id = $data['school_year_id'] ?? null;

        if(!is_numeric($student_id) || (int) $student_id != $student_id){
            return ['success' => false, 'message' => 'A valid student is required.'];
        }
        if(!is_numeric($school_year_id) || (int) $school_year_id != $school_year_id){
            return ['success' => false, 'message' => 'A valid school year is required.'];
        }
        if(!$this->healthModel->belongsToTeacher((int) $student_id, $teacher_id)){
            return ['success' => false, 'message' => 'You do not have permission to record a health profile for this student.'];
        }
        if($this->healthModel->hasHealthProfile((int) $student_id, $excludeId)){
            return ['success' => false, 'message' => 'This student already has a health profile on record. Edit the existing record instead of creating a new one.'];
        }

        $height_cm = $data['height_cm'] ?? null;
        $weight_kg = $data['weight_kg'] ?? null;
        if(!is_numeric($height_cm) || $height_cm < 50 || $height_cm > 250){
            return ['success' => false, 'message' => 'Height must be a number between 50 and 250 cm.'];
        }
        if(!is_numeric($weight_kg) || $weight_kg < 5 || $weight_kg > 200){
            return ['success' => false, 'message' => 'Weight must be a number between 5 and 200 kg.'];
        }

        $blood_type = $data['blood_type'] ?? null;
        if($blood_type !== null && $blood_type !== '' && !in_array($blood_type, $this->bloodTypes, true)){
            return ['success' => false, 'message' => 'Invalid blood type.'];
        }

        return ['success' => true];
    }

    private function buildPayload($data) {
        $bmiResult = $this->computeBmi((float) $data['height_cm'], (float) $data['weight_kg']);

        return [
            'student_id' => (int) $data['student_id'],
            'school_year_id' => (int) $data['school_year_id'],
            'height_cm' => (float) $data['height_cm'],
            'weight_kg' => (float) $data['weight_kg'],
            'bmi' => $bmiResult['bmi'],
            'bmi_classification' => $bmiResult['bmi_classification'],
            'blood_type' => !empty($data['blood_type']) ? $data['blood_type'] : null,
            'allergies' => $data['allergies'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'vision_screening_result' => $data['vision_screening_result'] ?? null,
            'hearing_screening_result' => $data['hearing_screening_result'] ?? null,
            'immunization_status' => $data['immunization_status'] ?? null,
            'recorded_by' => (int) $data['recorded_by']
        ];
    }

    public function create($data, $teacher_id) {
        try{
            $validation = $this->validate($data, $teacher_id);
            if(!$validation['success']){
                return $validation;
            }

            if($this->healthModel->create($this->buildPayload($data))){
                return ['success' => true, 'message' => 'Health profile recorded successfully.'];
            }
            return ['success' => false, 'message' => 'Something went wrong recording the health profile.'];
        }catch(Exception $e){
            error_log("Error in StudentHealthService::create: " . $e->getMessage());
            return ['success' => false, 'message' => 'Something went wrong recording the health profile.'];
        }
    }

    public function update($id, $data, $teacher_id) {
        try{
            if(!$this->healthModel->getById($id, $teacher_id)){
                return ['success' => false, 'message' => 'Health profile record not found.'];
            }

            $validation = $this->validate($data, $teacher_id, (int) $id);
            if(!$validation['success']){
                return $validation;
            }

            if($this->healthModel->update($id, $this->buildPayload($data))){
                return ['success' => true, 'message' => 'Health profile updated successfully.'];
            }
            return ['success' => false, 'message' => 'Something went wrong updating the health profile.'];
        }catch(Exception $e){
            error_log("Error in StudentHealthService::update: " . $e->getMessage());
            return ['success' => false, 'message' => 'Something went wrong updating the health profile.'];
        }
    }

    public function delete($id, $teacher_id) {
        try{
            if(!$this->healthModel->getById($id, $teacher_id)){
                return ['success' => false, 'message' => 'Health profile record not found.'];
            }

            if($this->healthModel->delete($id)){
                return ['success' => true, 'message' => 'Health profile deleted successfully.'];
            }
            return ['success' => false, 'message' => 'Something went wrong deleting the health profile.'];
        }catch(Exception $e){
            error_log("Error in StudentHealthService::delete: " . $e->getMessage());
            return ['success' => false, 'message' => 'Something went wrong deleting the health profile.'];
        }
    }
}
