<?php
require_once __DIR__ . '/../core/Model.php';

    /**
     * Read-only lookups over the bundled PSGC reference tables (see
     * database/migrations/015_create_psgc_address_tables.sql) for the
     * cascading Province -> City/Municipality -> Barangay pickers.
     */

    class PsgcModel extends Model{
        protected $provinces = 'psgc_provinces';
        protected $cities = 'psgc_cities_municipalities';
        protected $barangays = 'psgc_barangays';

        public function getProvinces(){
            try{
                $result = $this->con->query("SELECT code, name FROM {$this->provinces} ORDER BY name ASC");
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching PSGC provinces: " . $e->getMessage());
                return [];
            }
        }

        public function getCitiesByProvince($provinceCode){
            try{
                $stmt = $this->con->prepare("SELECT code, name FROM {$this->cities} WHERE province_code = ? ORDER BY name ASC");
                $stmt->bind_param("s", $provinceCode);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching PSGC cities: " . $e->getMessage());
                return [];
            }
        }

        public function getBarangaysByCity($muncityCode){
            try{
                $stmt = $this->con->prepare("SELECT code, name FROM {$this->barangays} WHERE muncity_code = ? ORDER BY name ASC");
                $stmt->bind_param("s", $muncityCode);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }catch(Exception $e){
                error_log("Error fetching PSGC barangays: " . $e->getMessage());
                return [];
            }
        }

        public function findProvinceByName($name){
            try{
                $stmt = $this->con->prepare("SELECT code, name FROM {$this->provinces} WHERE name = ? LIMIT 1");
                $stmt->bind_param("s", $name);
                $stmt->execute();
                return $stmt->get_result()->fetch_assoc();
            }catch(Exception $e){
                error_log("Error finding PSGC province: " . $e->getMessage());
                return null;
            }
        }

        public function findCityByName($name, $provinceCode){
            try{
                $stmt = $this->con->prepare("SELECT code, name FROM {$this->cities} WHERE name = ? AND province_code = ? LIMIT 1");
                $stmt->bind_param("ss", $name, $provinceCode);
                $stmt->execute();
                return $stmt->get_result()->fetch_assoc();
            }catch(Exception $e){
                error_log("Error finding PSGC city: " . $e->getMessage());
                return null;
            }
        }
    }
