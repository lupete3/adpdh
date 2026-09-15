<?php
require __DIR__.'/../vendor/autoload.php';
$app=require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$folder=storage_path('app/private/backups/cms-20260911-081020');
$counts=json_decode(file_get_contents($folder.'/counts.json'),true,512,JSON_THROW_ON_ERROR);
$db=Illuminate\Support\Facades\DB::connection();
$pdo=$db->getPdo();
$original=$db->getDatabaseName();
$users=$db->table('users')->orderBy('id')->get()->toJson();
$temp='adpdh_cms_restore_'.date('YmdHis');
$pdo->exec('CREATE DATABASE `'.$temp.'`');
try {
 $pdo->exec('USE `'.$temp.'`');
 $pdo->exec(file_get_contents($folder.'/database.sql'));
 foreach($counts as $table=>$count) {
  $actual=(int)$pdo->query('SELECT COUNT(*) FROM `'.str_replace('`','``',$table).'`')->fetchColumn();
  if($actual !== $count) throw new RuntimeException('Mismatch: '.$table);
 }
 $restored=json_encode($pdo->query('SELECT * FROM users ORDER BY id')->fetchAll(PDO::FETCH_OBJ));
 if($restored !== $users) throw new RuntimeException('User restoration mismatch');
 echo "Restore verified: all table counts and full user records match.\n";
 file_put_contents($folder.'/restore-verified.txt',date(DATE_ATOM)." All counts and full user records verified on a separate temporary MySQL database.\n");
} finally {
 $pdo->exec('USE `'.str_replace('`','``',$original).'`');
 $pdo->exec('DROP DATABASE `'.$temp.'`');
}
