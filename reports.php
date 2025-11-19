<?php
require 'config.php';
require_login();
// allow staff & admin
$start = $_GET['start'] ?? date('Y-m-d');
$end = $_GET['end'] ?? date('Y-m-d');
// normalize
$start_dt = date('Y-m-d 00:00:00', strtotime($start));
$end_dt = date('Y-m-d 23:59:59', strtotime($end));
$stmt = $pdo->prepare("SELECT t.*, c.name as customer_name, s.name as service_name FROM transactions t LEFT JOIN customers c ON t.customer_id=c.id LEFT JOIN services s ON t.service_id=s.id WHERE t.created_at BETWEEN ? AND ? ORDER BY t.created_at DESC");
$stmt->execute([$start_dt, $end_dt]);
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html lang="id">
<head> ... </head>
<body>
<?php include 'navbar_partial.php'; ?>
<div class="container my-4">
<h3>Laporan Transaksi</h3>
<form class="row g-2 mb-3">
<div class="col-md-3"><input type="date" name="start" value="<?=htmlspecialchars($start)?>" class="form-control"></div>
<div class="col-md-3"><input type="date" name="end" value="<?=htmlspecialchars($end)?>" class="form-control"></div>
<div class="col-md-2"><button class="btn btn-primary">Filter</button></div>
<div class="col-md-4 text-end">
<a class="btn btn-outline-success" href="export.php?format=csv&start=<?=urlencode($start)?>&end=<?=urlencode($end)?>">Export CSV</a>
<a class="btn btn-outline-secondary" href="export.php?format=xls&start=<?=urlencode($start)?>&end=<?=urlencode($end)?>">Export Excel</a>
</div>
</form>
<table class="table table-striped">
<thead><tr><th>No</th><th>Tanggal</th><th>Pelanggan</th><th>Layanan</th><th>Qty</th><th>Subtotal</th></tr></thead>
<tbody>
<?php $i=1; foreach($rows as $r): ?>
<tr>
<td><?=$i++?></td>
<td><?=htmlspecialchars($r['created_at'])?></td>
<td><?=htmlspecialchars($r['customer_name'] ?? 'Umum')?></td>
<td><?=htmlspecialchars($r['service_name'])?></td>
<td><?=htmlspecialchars($r['qty'])?></td>
<td><?=number_format($r['subtotal'],0,',','.')?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</body>
</html>