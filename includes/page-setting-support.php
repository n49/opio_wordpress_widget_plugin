<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<div class="opio-flex-row">
    <div class="opio-flex-col">
        <div class="opio-support-question">
            <h3>How do I display the slider in another language?</h3>
            <p>Add a <code>lang</code> attribute to the slider shortcode. Example: <code>[opio_slider id="X" lang="fr"]</code> renders the slider in French. Omit <code>lang</code> to render English.</p>
            <p><strong>30 languages ship with hand-curated UI translations:</strong></p>
            <p>
                French — <code>fr</code>,
                Spanish — <code>es</code>,
                Portuguese — <code>pt</code>,
                German — <code>de</code>,
                Italian — <code>it</code>,
                Dutch — <code>nl</code>,
                Turkish — <code>tr</code>,
                Polish — <code>pl</code>,
                Greek — <code>el</code>,
                Swedish — <code>sv</code>,
                Ukrainian — <code>uk</code>,
                Russian — <code>ru</code>,
                Hebrew — <code>he</code>,
                Arabic — <code>ar</code>,
                Persian / Farsi — <code>fa</code>,
                Urdu — <code>ur</code>,
                Hindi — <code>hi</code>,
                Punjabi — <code>pa</code>,
                Bengali — <code>bn</code>,
                Tamil — <code>ta</code>,
                Japanese — <code>ja</code>,
                Korean — <code>ko</code>,
                Mandarin (Simplified Chinese) — <code>zh</code>,
                Cantonese (Traditional Chinese) — <code>hk</code>,
                Vietnamese — <code>vi</code>,
                Tagalog (Filipino) — <code>tl</code>,
                Indonesian — <code>id</code>,
                Malay — <code>ms</code>,
                Thai — <code>th</code>.
            </p>
            <p>Any other ISO 639-1 code (Swahili <code>sw</code>, Norwegian <code>nb</code>, Finnish <code>fi</code>, Czech <code>cs</code>, Romanian <code>ro</code>, etc.) will translate <em>review content</em> via a free machine-translation service while UI labels stay English. Invalid codes silently fall back to English everywhere.</p>
            <p>No external plugin (Polylang, WPML, etc.) required. The <code>lang</code> attribute is the single source of truth. See <code>LANGUAGES.md</code> in the plugin folder for the full developer reference.</p>
        </div>
    </div>
    <div class="opio-flex-col">
        <div class="opio-support-question">
            <h3>I found by Business ID, but the review feed says Invalid Entity ID, Why?</h3>
            <p>First of all, please make sure you have copied the correct Business ID from your OPIO business dashboard, it's typically english alphabets with about 17 characters in length, and please make sure your actual feed works by browsing it in your OPIO Dashboard</p>
        </div>
    </div>
    <div class="opio-flex-col">
        <div class="opio-support-question">
            <h3>I cannot load businesses from my OPIO Organization ID.</h3>
            <p>Please make sure you have a valid organization ID, and it's not a OPIO Business ID. From the full installation guide you can see that finding business ID and organization ID from your OPIO dashboard has couple of differences, please make sure you follow the guidelines properly</p>
        </div>
    </div>
</div>
<div class="opio-flex-row">
    <div class="opio-flex-col">
        <div class="opio-support-question">
            <h3>Is the plugin compatible with the latest version of PHP? I saw warnings about the compatibility with PHP 7 in the checker plugin.</h3>
            <p>Yes, the plugin is absolutely compatible with PHP 7 and by the way, we are using PHP 7 on the demo site.</p>
            <p>The warnings come from the code which is needed for compatible with old versions of PHP (&lt; 5.0) which is sometimes found in some users and without this code, the plugin will not work.</p>
            <p>The problem is that the plugin which you’re using to test compatibility with PHP 7 cannot understand that this code is not used under PHP 7 or greater. The compatibility plugin just search some keywords which deprecated in the latest version PHP and show warnings about it (without understanding that this is not used).</p>
        </div>
    </div>
    <div class="opio-flex-col">
        <div class="opio-support-question">
            <h3>If you still need support</h3>
            <p>You can contact us directly by email <a href="mailto:info@opioapp.com">info@opioapp.com</a> and would be great and save us a lot of time if each request to the support will contain the following data:</p>
            <ul>
                <li><b>1.</b> Clear and understandable description of the issue;</li>
                <li><b>2.</b> Direct links to your review feed;</li>
                <li><b>3.</b> Link to the page of your site where the plugin installed;</li>
                <li><b>4.</b> Better if you attach a screenshot(s) (or screencast) how you determine the issue;</li>
            </ul>
        </div>
    </div>
</div>