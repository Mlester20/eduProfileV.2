<?php
/**
 * Standalone Trash auto-purge -- for wiring to Windows Task Scheduler or a
 * cron job for a guaranteed daily purge, instead of relying solely on the
 * lightweight "purge on page load" that already runs whenever a teacher or
 * administrative user opens their Trash page.
 *
 * Usage:
 *   php database/purge-trash.php
 *
 * Windows Task Scheduler: create a daily trigger that runs
 *   C:\xampp\php\php.exe C:\xampp\htdocs\eduProfile\database\purge-trash.php
 *
 * Cron (if ever moved to a Linux host):
 *   0 2 * * * php /path/to/eduProfile/database/purge-trash.php
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/../app/helpers/StudentScope.php';
require_once __DIR__ . '/../app/models/teacher/TrashModel.php';
require_once __DIR__ . '/../app/models/teacher/StudentsModel.php';
require_once __DIR__ . '/../app/helpers/auditLogs.php';

$trashModel = new TrashModel($con);
$studentsModel = new StudentsModel($con);
$auditLogs = new AuditLogs($con);

$days = StudentScope::retentionDays();
$expired = $trashModel->getExpired($days);

if(empty($expired)){
    echo "Nothing to purge -- no student has been in Trash longer than {$days} day(s).\n";
    exit(0);
}

$purged = 0;
foreach($expired as $row){
    $id = (int) $row['id'];
    $con->begin_transaction();
    try{
        if(!$studentsModel->hardDelete($id)){
            throw new Exception('hardDelete returned false');
        }
        $con->commit();

        if(!empty($row['picture'])){
            $fullPath = __DIR__ . '/../' . ltrim($row['picture'], '/');
            if(is_file($fullPath)){
                @unlink($fullPath);
            }
        }

        $auditLogs->log(
            null,
            'system',
            'Auto-purged a student from Trash',
            'Students',
            $id,
            null,
            "System auto-purged student ID {$id} after {$days} days in Trash (purge-trash.php)"
        );

        $purged++;
        echo "Purged student ID {$id}.\n";
    }catch(Exception $e){
        $con->rollback();
        echo "Failed to purge student ID {$id}: " . $e->getMessage() . "\n";
    }
}

echo "Done. Purged {$purged} student(s).\n";
