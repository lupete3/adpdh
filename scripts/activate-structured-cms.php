<?php
require __DIR__.'/../vendor/autoload.php';
$app=require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\{DB,Artisan};
$backup=$argv[1]??'';
if(!preg_match('/^cms-[0-9]{8}-[0-9]{6}$/D',$backup)||!is_file(storage_path('app/private/backups/'.$backup.'/structured-verified.txt')))throw new RuntimeException('A verified structured CMS backup is required.');
if(config('database.default')!=='mysql'||DB::connection()->getDatabaseName()!=='adpdh')throw new RuntimeException('Expected local adpdh MySQL connection.');
$users=DB::table('users')->orderBy('id')->get()->toJson();
$titles=DB::table('cms_titles')->orderBy('id')->get()->toJson();
if(Artisan::call('migrate',['--force'=>true])!==0)throw new RuntimeException(Artisan::output());
echo Artisan::output();
if(Artisan::call('db:seed',['--force'=>true])!==0)throw new RuntimeException(Artisan::output());
echo Artisan::output();
if(DB::table('users')->orderBy('id')->get()->toJson()!==$users||DB::table('cms_titles')->orderBy('id')->get()->toJson()!==$titles)throw new RuntimeException('Existing account or title changed unexpectedly.');
$counts=[];foreach(['cms_sections','pillars','intervention_axes','indicators','indicator_values','media_assets','galleries'] as $table)$counts[$table]=DB::table($table)->count();
echo json_encode(['counts'=>$counts,'accounts_and_legacy_titles'=>'unchanged'],JSON_PRETTY_PRINT)."\n";
