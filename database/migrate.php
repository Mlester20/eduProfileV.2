<?php
/**
 * Migration runner — tracks which database/migrations/*.sql files have
 * already been applied (in a `migrations` table) so you only ever run
 * whatever is new, instead of reimporting the whole database every time
 * you pull changes that add a migration.
 *
 * Usage:
 *   php database/migrate.php                Run every migration not yet
 *                                            recorded as applied.
 *   php database/migrate.php --mark-applied  Record every migration file
 *                                            that currently exists as
 *                                            already applied, WITHOUT
 *                                            running any SQL. Use this
 *                                            once on a database that
 *                                            already has all the current
 *                                            migrations' changes (e.g.
 *                                            applied by hand before this
 *                                            tool existed) — after that,
 *                                            normal runs only pick up
 *                                            migrations added later.
 */

require_once __DIR__ . '/config/config.php';

$con->query("
    CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL UNIQUE,
        applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
");

$files = glob(__DIR__ . '/migrations/*.sql');
sort($files, SORT_STRING); // zero-padded 001_, 002_, ... filenames sort correctly as plain strings

$applied = [];
$result = $con->query("SELECT migration FROM migrations");
while($row = $result->fetch_assoc()){
    $applied[$row['migration']] = true;
}

$markOnly = in_array('--mark-applied', $argv, true);
$pending = array_filter($files, function($path) use ($applied){
    return !isset($applied[basename($path)]);
});

if(empty($pending)){
    echo "Already up to date -- nothing to run.\n";
    exit(0);
}

$ran = 0;
foreach($pending as $filePath){
    $filename = basename($filePath);

    if($markOnly){
        echo "Marking {$filename} as applied (not executing)...\n";
    }else{
        echo "Running {$filename}...\n";
        $sql = file_get_contents($filePath);
        try{
            $con->multi_query($sql);
            do{
                if($res = $con->store_result()){
                    $res->free();
                }
            }while($con->more_results() && $con->next_result());
        }catch(mysqli_sql_exception $e){
            echo "  FAILED: " . $e->getMessage() . "\n";
            echo "\nStopped at {$filename}. Fix the issue above, then re-run -- already-applied migrations won't be repeated.\n";
            exit(1);
        }
    }

    $stmt = $con->prepare("INSERT INTO migrations (migration) VALUES (?)");
    $stmt->bind_param("s", $filename);
    $stmt->execute();
    echo "  OK\n";
    $ran++;
}

echo "\nDone. " . ($markOnly ? "Marked" : "Ran") . " {$ran} migration(s).\n";
