<?php
require __DIR__ . '/config.php';
mc_setup();

shell_exec("mc mirror --overwrite store/$MINIO_BUCKET /var/www/html/uploads 2>&1");

$files = array_values(array_filter(scandir(__DIR__ . '/uploads'),
    fn($f) => !in_array($f, ['.', '..'])));
?>
<!doctype html>
<html><head><title>SnapVault</title>
<script src="app.js"></script>
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
  <a href="uploads/<?= htmlspecialchars($f) ?>"><?= htmlspecialchars($f) ?></a>
<?php endforeach; ?>
</body></html>
