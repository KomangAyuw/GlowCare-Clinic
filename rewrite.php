<?php
$file = 'c:/xampp/htdocs/GlowCare-Clinic/treatment.php';
$content = file_get_contents($file);

// Add PHP header
if (strpos($content, '<?php') === false) {
    $header = "<?php\nsession_start();\n\$conn = require_once 'backend/koneksi.php';\n\$treatments = [];\n\$q = mysqli_query(\$conn, \"SELECT * FROM treatment WHERE status='Aktif' ORDER BY urutan ASC\");\nwhile(\$r = mysqli_fetch_assoc(\$q)) {\n    \$treatments[] = \$r;\n}\n?>\n";
    $content = $header . $content;
}

// Replace grid
$grid_start = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter lg:gap-lg" id="treatments-grid">';
$grid_end = '<!-- Call to Action -->';

$start_pos = strpos($content, $grid_start);
$end_pos = strpos($content, '</section>', $start_pos);

if ($start_pos !== false && $end_pos !== false) {
    $grid_html = $grid_start . "\n" . '<?php foreach($treatments as $tr): 
    $cat_class = strtolower(explode(\' \', $tr[\'kategori\'])[0]); 
    if ($cat_class == \'hair\' || $cat_class == \'body\') $cat_class = \'body\'; 
    else if ($cat_class == \'anti-aging\') $cat_class = \'anti-aging\';
    else if ($cat_class == \'acne\') $cat_class = \'acne\';
    else if ($cat_class == \'brightening\') $cat_class = \'brightening\';
    else $cat_class = \'other\';
    $durasi = htmlspecialchars($tr[\'durasi\']);
    $nama = htmlspecialchars($tr[\'nama\']);
    $img = htmlspecialchars($tr[\'gambar_url\']);
    $kat = htmlspecialchars($tr[\'kategori\']);
    $desc = htmlspecialchars($tr[\'deskripsi_panjang\']);
?>
<div data-category="<?= $cat_class ?>" class="treatment-card bg-surface-container-lowest rounded-2xl border border-outline-variant/40 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group">
<div class="relative h-[240px] w-full overflow-hidden">
<img alt="<?= $nama ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="<?= $img ?>">
<span class="absolute top-4 left-4 bg-surface/90 backdrop-blur-sm text-primary font-label-sm text-label-sm px-3 py-1 rounded-full border border-primary/20"><?= $kat ?></span>
</div>
<div class="p-md flex flex-col flex-grow">
<div class="flex justify-between items-start mb-2">
<h3 class="font-headline-md text-headline-md text-on-background leading-tight"><?= $nama ?></h3>
<div class="flex items-center text-on-surface-variant/70">
<span class="material-symbols-outlined text-sm mr-1" style="font-size: 16px;">schedule</span>
<span class="font-label-sm text-label-sm"><?= $durasi ?></span>
</div>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-lg flex-grow"><?= $desc ?></p>
<a href="jadwal.php?treatment=<?= urlencode($nama) ?>" class="w-full py-3 bg-surface-container-high text-primary font-label-md text-label-md rounded-lg border border-primary/20 hover:bg-primary hover:text-on-primary transition-all duration-300 uppercase tracking-wider text-center block">Booking Sekarang</a>
</div>
</div>
<?php endforeach; ?>
</div>
';
    
    $content = substr($content, 0, $start_pos) . $grid_html . substr($content, $end_pos);
}

file_put_contents($file, $content);
echo "treatment.php updated successfully.";
?>
