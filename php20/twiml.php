<?php
header('Content-Type: text/xml; charset=utf-8');

$streamUrl = 'wss://subastas.grupopcr.com.pa/stream';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<Response>
    <Connect>
        <Stream url="<?= htmlspecialchars($streamUrl, ENT_QUOTES, 'UTF-8'); ?>" />
    </Connect>
</Response>
