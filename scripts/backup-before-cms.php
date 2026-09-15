<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
$folder = storage_path('app/private/backups/cms-'.date('Ymd-His'));
Illuminate\Support\Facades\File::ensureDirectoryExists($folder);
$pdo = $db->getPdo();
$tables = $db->select('SHOW TABLES');
$out = fopen($folder.'/database.sql', 'wb');
fwrite($out, "SET FOREIGN_KEY_CHECKS=0;\n");
$counts = [];
$db->beginTransaction();
foreach ($tables as $item) {
    $name = array_values((array)$item)[0];
    $quoted = '`'.str_replace('`','``',$name).'`';
    $schema = (array)$db->selectOne('SHOW CREATE TABLE '.$quoted);
    fwrite($out, array_values($schema)[1].";\n");
    $rows = $db->table($name)->get();
    $counts[$name] = $rows->count();
    foreach ($rows as $row) {
        $values = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote((string)$v), (array)$row);
        fwrite($out, 'INSERT INTO '.$quoted.' VALUES ('.implode(',', $values).");\n");
    }
}
$db->commit();
fwrite($out, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($out);
if (is_dir(storage_path('app/public'))) Illuminate\Support\Facades\File::copyDirectory(storage_path('app/public'), $folder.'/uploads');
file_put_contents($folder.'/counts.json', json_encode($counts, JSON_PRETTY_PRINT));
echo json_encode(['backup'=>$folder,'counts'=>$counts,'admin_exists'=>$db->table('users')->where('email','admin@adpdh.org')->exists()],JSON_PRETTY_PRINT);
