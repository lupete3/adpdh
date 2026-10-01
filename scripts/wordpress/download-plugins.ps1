$ErrorActionPreference = 'Stop'
$adpdhRoot = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
$adpdhPlugins = @('advanced-custom-fields', 'capability-manager-enhanced', 'press-permit-core', 'fluent-smtp')
foreach ($adpdhPlugin in $adpdhPlugins) {
    $adpdhZip = Join-Path $env:TEMP ('adpdh-' + $adpdhPlugin + '.zip')
    Invoke-WebRequest -Uri ('https://downloads.wordpress.org/plugin/' + $adpdhPlugin + '.latest-stable.zip') -OutFile $adpdhZip -TimeoutSec 60
    Expand-Archive -LiteralPath $adpdhZip -DestinationPath "$adpdhRoot/wordpress-runtime/wordpress/wp-content/plugins" -Force
    Write-Output "Téléchargé : $adpdhPlugin"
}
