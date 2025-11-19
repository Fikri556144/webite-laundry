<?php
// assume config sudah di-include
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
<div class="container">
<a class="navbar-brand d-flex align-items-center" href="index.php">
<?php if (file_exists($SITE_LOGO)): ?>
<img src="<?=$SITE_LOGO?>" alt="logo" style="height:36px;margin-right:8px;" class="animate-logo">
<?php endif; ?>
Laundry+
</a>
<div class="collapse navbar-collapse">
<ul class="navbar-nav ms-auto">
<li class="nav-item"><a class="nav-link" href="presensi.php">Presensi</a></li>
<li class="nav-item"><a class="nav-link" href="pelayanan.php">Pelayanan</a></li>
<li class="nav-item"><a class="nav-link" href="taks.php">Task</a></li>
<li class="nav-item"><a class="nav-link" href="nota.php">Nota</a></li>
<li class="nav-item"><a class="nav-link" href="reports.php">Laporan</a></li>
<?php if (has_role('admin')): ?><li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li><?php endif; ?>
<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</nav>