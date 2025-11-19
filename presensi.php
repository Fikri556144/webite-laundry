<?php
require 'config.php';
require_login();

$employee_id = $_SESSION['user_id']; // assume employee and user tied (simplify)
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $now = date('Y-m-d H:i:s');
    if ($action === 'checkin') {
        $stmt = $pdo->prepare("INSERT INTO attendance (employee_id, checkin_time) VALUES (?, ?)");
        $stmt->execute([$employee_id, $now]);
        $msg = "Check-in berhasil pada $now";
    } elseif ($action === 'checkout') {
        // update last open attendance without checkout
        $stmt = $pdo->prepare("UPDATE attendance SET checkout_time = ? WHERE employee_id = ? AND checkout_time IS NULL ORDER BY id DESC LIMIT 1");
        $stmt->execute([$now, $employee_id]);
        $msg = "Check-out berhasil pada $now";
    }
}

// fetch recent attendance
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE employee_id = ? ORDER BY id DESC LIMIT 10");
$stmt->execute([$employee_id]);
$att = $stmt->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Presensi - Laundry</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar_partial.php' ?? ''; ?>
<div class="container my-4">
  <h3>Presensi Karyawan</h3>
  <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
  <form method="post" class="mb-3">
    <button name="action" value="checkin" class="btn btn-success me-2">Check-in</button>
    <button name="action" value="checkout" class="btn btn-danger">Check-out</button>
  </form>

  <h5>Riwayat Terakhir</h5>
  <table class="table table-striped">
    <thead><tr><th>#</th><th>Check-in</th><th>Check-out</th></tr></thead>
    <tbody>
      <?php foreach($att as $a): ?>
      <tr>
        <td><?=htmlspecialchars($a['id'])?></td>
        <td><?=htmlspecialchars($a['checkin_time'])?></td>
        <td><?=htmlspecialchars($a['checkout_time'] ?? '-')?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>
