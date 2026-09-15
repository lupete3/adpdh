<?php
require __DIR__.'/../vendor/autoload.php';
$app=require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\{DB,Artisan};
$backup=$argv[1]??'';
if(!preg_match('/^cms-[0-9]{8}-[0-9]{6}$/D',$backup))throw new RuntimeException('Expected a backup directory name.');
$folder=storage_path('app/private/backups/'.$backup);
$counts=json_decode(file_get_contents($folder.'/counts.json'),true,512,JSON_THROW_ON_ERROR);
if(config('database.default')!=='mysql'||DB::connection()->getDatabaseName()!=='adpdh')throw new RuntimeException('Expected local adpdh MySQL connection.');
$pdo=DB::connection()->getPdo();
$users=DB::table('users')->orderBy('id')->get()->toJson();
$temp='adpdh_cms_verify_'.date('YmdHis');
$pdo->exec('CREATE DATABASE `'.$temp.'`');
try {
 $pdo->exec('USE `'.$temp.'`');
 $pdo->exec(file_get_contents($folder.'/database.sql'));
 foreach($counts as $table=>$count)if((int)$pdo->query('SELECT COUNT(*) FROM `'.str_replace('`','``',$table).'`')->fetchColumn()!==$count)throw new RuntimeException('Backup count mismatch: '.$table);
 if(json_encode($pdo->query('SELECT * FROM users ORDER BY id')->fetchAll(PDO::FETCH_OBJ))!==$users)throw new RuntimeException('Backup account mismatch');
 config(['database.connections.cms_verification'=>array_merge(config('database.connections.mysql'),['database'=>$temp]),'database.default'=>'cms_verification']);
 DB::setDefaultConnection('cms_verification');
 if(Artisan::call('migrate',['--force'=>true])!==0)throw new RuntimeException(Artisan::output());
 if(Artisan::call('db:seed',['--force'=>true])!==0)throw new RuntimeException(Artisan::output());
 if(Artisan::call('db:seed',['--force'=>true])!==0)throw new RuntimeException(Artisan::output());
 if(DB::table('cms_sections')->count()!==43||DB::table('indicators')->count()!==6||DB::table('indicator_values')->count()!==6)throw new RuntimeException('Unexpected structured content counts');
 if(DB::table('users')->orderBy('id')->get()->toJson()!==$users)throw new RuntimeException('Migration altered accounts');
 file_put_contents($folder.'/structured-verified.txt',date(DATE_ATOM)." Backup restored; migration and repeated seeding verified on isolated MySQL database; accounts unchanged.\n");
 echo "Backup restore, MySQL migration and repeated seeding verified; accounts unchanged.\n";
} finally {
 DB::purge('cms_verification');
 $pdo->exec('USE `adpdh`');
 if(!preg_match('/^adpdh_cms_verify_[0-9]{14}$/D',$temp))throw new RuntimeException('Invalid verification database');
 $pdo->exec('DROP DATABASE `'.$temp.'`');
}
