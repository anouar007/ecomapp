<!-- Unified Multi-Platform Tracking -->
@php
    $fbPixel = setting('facebook_pixel_id');
    $gtagId  = setting('google_tag_id');
    $ttPixel = setting('tiktok_pixel_id');
    $scPixel = setting('snapchat_pixel_id');
    $pnTag   = setting('pinterest_tag_id');
    $currency = 'MAD';
@endphp

<!-- Meta Pixel -->
@if($fbPixel)
<script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $fbPixel }}');
    fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $fbPixel }}&ev=PageView&noscript=1"/></noscript>
@endif

<!-- Google Tag -->
@if($gtagId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gtagId }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $gtagId }}', { 'send_page_view': true });
</script>
@endif

<!-- TikTok Pixel -->
@if($ttPixel)
<script>
    !function (w, d, t) {
        w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndLog=function(t,e){return function(){var n=Math.random().toString(36).substring(7);if(w.Analytics){w.Analytics.instance(t).setAndLog(e,n);return}ttq.push([t,e,n])}};for(var i=0;i<ttq.methods.length;i++)ttq[ttq.methods[i]]=ttq.setAndLog(ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)e[ttq.methods[n]]=ttq.setAndLog(t,ttq.methods[n]);return e};ttq.load=function(e,n){var t="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=t,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=d.createElement("script");o.type="text/javascript",o.async=!0,o.src=t+"?sdkid="+e+"&lib="+t;var a=d.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
        ttq.load('{{ $ttPixel }}');
        ttq.page();
    }(window, document, 'ttq');
</script>
@endif

<!-- Snapchat Pixel -->
@if($scPixel)
<script type='text/javascript'>
    (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function(){a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};
    a.queue=[];var r=t.createElement(n);r.async=!0;r.src="https://sc-static.net/scevent.min.js";
    var s=t.getElementsByTagName(n)[0];s.parentNode.insertBefore(r,s)})(window,document,"script");
    snaptr('init', '{{ $scPixel }}');
    snaptr('track', 'PAGE_VIEW');
</script>
@endif

<!-- Pinterest Tag -->
@if($pnTag)
<script>
    !function(e){if(!window.pintrk){window.pintrk=function(){window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var n=window.pintrk;n.queue=[],n.version="3.0";var t=document.createElement("script");t.async=!0,t.src=e;var r=document.getElementsByTagName("script")[0];r.parentNode.insertBefore(t,r)}}("https://s.pinimg.com/ct/core.js");
    pintrk('load', '{{ $pnTag }}');
    pintrk('page');
</script>
@endif

<!-- Global Event Dispatcher -->
<script>
    window.trackAdEvent = function(eventName, data) {
        console.log('[Tracking] Dispatching:', eventName, data);

        // 1. Meta (Facebook)
        if (typeof fbq === 'function') {
            fbq('track', eventName, data);
        }

        // 2. Google (GA4/Ads)
        if (typeof gtag === 'function') {
            gtag('event', eventName.toLowerCase().replace(/ /g, '_'), data);
        }

        // 3. TikTok
        if (typeof ttq === 'object') {
            const ttMapping = {
                'AddToCart': 'AddToCart',
                'ViewContent': 'ViewContent',
                'InitiateCheckout': 'InitiateCheckout',
                'Purchase': 'CompletePayment'
            };
            ttq.track(ttMapping[eventName] || eventName, data);
        }

        // 4. Snapchat
        if (typeof snaptr === 'function') {
             const scMapping = {
                'AddToCart': 'ADD_CART',
                'ViewContent': 'VIEW_CONTENT',
                'InitiateCheckout': 'START_CHECKOUT',
                'Purchase': 'PURCHASE'
            };
            snaptr('track', scMapping[eventName] || eventName, data);
        }

        // 5. Pinterest
        if (typeof pintrk === 'function') {
            pintrk('track', eventName, data);
        }
    };

    // Auto-detect cart additions if any global logic exists
    document.addEventListener('DOMContentLoaded', function() {
        // We will trigger events manually from PDP and Checkout pages for maximum precision
    });
</script>
