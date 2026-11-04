<?php
require __DIR__ . '/config.php';
mc_setup();


$out = shell_exec("mc --json ls store/$MINIO_BUCKET 2>/dev/null");
$files = [];
foreach (explode("\n", trim((string)$out)) as $line) {
    if (!$line) continue;
    $j = json_decode($line, true);
    if (!empty($j['key'])) $files[] = $j['key'];
}
?>
<!doctype html>
<html><head><title>SnapVault</title>
<style>body{font-family:sans-serif;max-width:720px;margin:40px auto}
a{display:block;padding:6px}</style></head>
<body>
<h1>SnapVault</h1>
<p>Simple file sharing. Upload something and share the link.</p>
<form action="upload.php" method="post" enctype="multipart/form-data">
  <input type="file" name="file"><button>Upload</button>
</form>
<h3>Files</h3>
<?php foreach ($files as $f): ?>
  <a href="view.php?f=<?= urlencode($f) ?>"><?= htmlspecialchars($f) ?></a>
<?php endforeach; ?>
</body></html>
