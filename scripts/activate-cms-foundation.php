<?php
require __DIR__.'/../vendor/autoload.php';
$app=require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
if (DB::connection()->getDatabaseName() !== 'adpdh' || !is_file(storage_path('app/private/backups/cms-20260911-081020/restore-verified.txt'))) throw new RuntimeException('Expected database and verified backup required.');
$before=DB::table('users')->orderBy('id')->get(['id','email','password'])->toJson();
if (!DB::table('users')->where('email','admin@adpdh.org')->exists()) throw new RuntimeException('Existing administrator missing');
foreach ([['migrate',['--force'=>true]],['adpdh:grant-admin',['email'=>'admin@adpdh.org']],['db:seed',['--class'=>'DatabaseSeeder','--force'=>true]]] as [$command,$arguments]) {
 $code=Artisan::call($command,$arguments);
 echo Artisan::output();
 if($code!==0) throw new RuntimeException('Failed: '.$command);
}
$after=DB::table('users')->orderBy('id')->get(['id','email','password'])->toJson();
if($before!==$after) throw new RuntimeException('Unexpected user credential changes');
echo "User IDs, emails and password hashes preserved.\n";
echo 'CMS pages: '.DB::table('cms_pages')->count().'; title fields: '.DB::table('cms_titles')->count()."\n";
