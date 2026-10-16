@props(['network'])
<span class="social-icon social-icon--{{ $network }}" aria-hidden="true">
<svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor" focusable="false">
@switch($network)
@case('facebook')<path d="M14 22v-9h3l.5-4H14V7c0-1.1.3-2 2-2h2V1.5A25 25 0 0 0 15 1c-3 0-5 1.8-5 5v3H7v4h3v9z"/>@break
@case('x')<path d="M18.9 2H22l-6.8 7.8L23 22h-6.3l-4.9-7.5L5.2 22H2l8.3-9.5L1 2h6.5l4.4 6.8L18.9 2ZM17.8 20h1.7L6.5 4H4.7z"/>@break
@case('linkedin')<circle cx="4" cy="4" r="2"/><path d="M2 8h4v14H2zM9 8h4v2c1-1.5 2.2-2.3 4-2.3 3.5 0 5 2.1 5 6.2V22h-4v-7.2c0-2.1-.5-3.3-2.2-3.3-1.8 0-2.8 1.3-2.8 3.3V22H9z"/>@break
@case('youtube')<path d="M22.5 6.2a3 3 0 0 0-2.1-2.1C18.5 3.6 12 3.6 12 3.6s-6.5 0-8.4.5a3 3 0 0 0-2.1 2.1C1 8.1 1 12 1 12s0 3.9.5 5.8a3 3 0 0 0 2.1 2.1c1.9.5 8.4.5 8.4.5s6.5 0 8.4-.5a3 3 0 0 0 2.1-2.1c.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8ZM9.8 15.6V8.4L16 12z"/>@break
@case('instagram')<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2"/>@break
@case('tiktok')<path d="M14 2h4c.2 2.4 1.5 4 4 4.5V10a10 10 0 0 1-4-1.3v7.4a6.5 6.5 0 1 1-7-6.5v3.8a2.8 2.8 0 1 0 3 2.8z"/>@break
@endswitch
</svg>
</span>
