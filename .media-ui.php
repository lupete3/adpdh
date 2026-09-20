<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (($argv[1] ?? '') === 'create') {
    if (App\Models\User::where('email', 'codex-media-review@example.invalid')->exists()) throw new RuntimeException('Review account already exists');
    $user = App\Models\User::create(['name'=>'Revue médiathèque temporaire','email'=>'codex-media-review@example.invalid','password'=>Illuminate\Support\Facades\Hash::make('Local-Media-Review-2026!')]);
    $user->forceFill(['is_admin'=>true,'email_verified_at'=>now()])->save();
    file_put_contents(__DIR__.'/.media-review-user-id', (string)$user->id);
    $image = imagecreatetruecolor(180,120);
    $color = imagecolorallocate($image,35,85,120);
    imagefill($image,0,0,$color);
    imagepng($image,__DIR__.'/.media-review.png');
    imagedestroy($image);
    echo "Temporary local review account ready.\n";
} elseif (($argv[1] ?? '') === 'cleanup') {
    $id = (int)file_get_contents(__DIR__.'/.media-review-user-id');
    App\Models\User::whereKey($id)->where('email','codex-media-review@example.invalid')->delete();
    echo "Temporary review account removed.\n";
}
