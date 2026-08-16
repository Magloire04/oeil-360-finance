{{-- Métadonnées PWA (installation, thème, iOS) + enregistrement du service worker. Inclus dans chaque <head>. --}}
<link rel="manifest" href="/manifest.webmanifest">
<meta name="theme-color" content="#1a2e4a">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Oeil360">
<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
</script>
