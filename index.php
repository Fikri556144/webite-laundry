<?php
require 'config.php';
if (!is_logged_in()) header('Location: login.php');
$name = $_SESSION['user_name'];
$role = $_SESSION['user_role'];
?>
<!doctype html>
<html lang="id">
<head> ... </head>
<body>
<?php include 'navbar_partial.php'; ?>
<div class="container">
<header class="hero text-center">
<?php if (file_exists($SITE_LOGO)): ?>
<img src="<?=$SITE_LOGO?>" alt="logo" style="max-height:80px;" class="mb-3 animate-logo">
<?php endif; ?>
<h1>Halo, <?=htmlspecialchars($name)?> <small class="text-muted">(<?=$role?>)</small></h1>
</header>
...
<?php if (has_role('admin')): ?><a href="settings.php" class="btn btn-sm btn-outline-secondary">Settings</a><?php endif; ?>
</div>
</body>
</html>