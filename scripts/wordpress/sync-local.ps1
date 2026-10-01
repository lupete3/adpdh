$ErrorActionPreference = 'Stop'
$adpdhRoot = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
Copy-Item -LiteralPath "$adpdhRoot/wordpress/wp-content/plugins/adpdh-core" -Destination "$adpdhRoot/wordpress-runtime/wordpress/wp-content/plugins" -Recurse -Force
Copy-Item -LiteralPath "$adpdhRoot/wordpress/wp-content/themes/adpdh" -Destination "$adpdhRoot/wordpress-runtime/wordpress/wp-content/themes" -Recurse -Force
