<?php
$qrs = \App\Models\Qr::all();
foreach($qrs as $qr) {
    if (strpos($qr->url, "dawscape.fun") === false || strpos($qr->url, "localhost") !== false || strpos($qr->url, "-") !== false || strpos($qr->url, "dawscape.fun/qr/") !== false && preg_match('/-[0-9a-f]{13}$/', $qr->url)) {
        $baseUrl = "https://dawscape.fun";
        $slug = str_replace("-", "", \Illuminate\Support\Str::slug($qr->nombre));
        $qr->url = $baseUrl . "/qr/" . $slug;
        $qr->save();
        echo "Updated QR: " . $qr->nombre . " -> " . $qr->url . "\n";
    }
}
