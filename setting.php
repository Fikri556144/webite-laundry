<?php
require 'config.php';
require_role('admin');
$ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (!empty($_FILES['logo']['tmp_name'])) {
$f = $_FILES['logo'];
$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
$allowed = ['png','jpg','jpeg','svg'];
if (in_array($ext, $allowed)) {
$target = __DIR__ . '/uploads/logo.png';
if (!is_dir(__DIR__ . '/uploads')) mkdir(__DIR__ . '/uploads', 0755, true);
move_uploaded_file($f['tmp_name'], $target);
$ok = 'Logo diupload.';
} else {
$ok = 'Format tidak diizinkan.';
}
}
}
?>
<!doctype html>
<html lang="id">
<head> ... </head>
<body>
<?php include 'navbar_partial.php'; ?>
<div class="container my-4">
<h3>Settings</h3>
<?php if($ok): ?><div class="alert alert-info"><?=htmlspecialchars($ok)?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
<div class="mb-2"><label>Upload Logo (PNG/JPG/SVG)</label><input type="file" name="logo" class="form-control"></div>
<button class="btn btn-primary">Upload</button>
</form>
</div>
</body>
</html>