<?php
require 'config.php';
require_login();

// add customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_customer') {
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $stmt = $pdo->prepare("INSERT INTO customers (name, phone) VALUES (?, ?)");
        $stmt->execute([$name, $phone]);
    } elseif ($_POST['action'] === 'add_service') {
        $name = $_POST['sname'] ?? '';
        $type = $_POST['stype'] ?? 'kiloan';
        $price = floatval($_POST['sprice'] ?? 0);
        $stmt = $pdo->prepare("INSERT INTO services (name, type, price) VALUES (?, ?, ?)");
        $stmt->execute([$name, $type, $price]);
    }
}

// fetch
$customers = $pdo->query("SELECT * FROM customers ORDER BY id DESC LIMIT 50")->fetchAll();
$services = $pdo->query("SELECT * FROM services ORDER BY id ASC")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pelayanan - Laundry</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar_partial.php' ?? ''; ?>
<div class="container my-4">
  <div class="row">
    <div class="col-md-6">
      <h4>Tambah Pelanggan</h4>
      <form method="post">
        <input type="hidden" name="action" value="add_customer">
        <div class="mb-2"><input class="form-control" name="name" placeholder="Nama pelanggan" required></div>
        <div class="mb-2"><input class="form-control" name="phone" placeholder="No. Telepon"></div>
        <button class="btn btn-primary">Simpan Pelanggan</button>
      </form>

      <h5 class="mt-4">Daftar Pelanggan</h5>
      <ul class="list-group">
        <?php foreach($customers as $c): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <?=htmlspecialchars($c['name'])?> <span class="text-muted small"><?=htmlspecialchars($c['phone'])?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="col-md-6">
      <h4>Tambah Layanan</h4>
      <form method="post">
        <input type="hidden" name="action" value="add_service">
        <div class="mb-2"><input class="form-control" name="sname" placeholder="Nama layanan" required></div>
        <div class="mb-2">
          <select name="stype" class="form-select">
            <option value="kiloan">Kiloan</option>
            <option value="satuan">Satuan</option>
          </select>
        </div>
        <div class="mb-2"><input class="form-control" name="sprice" placeholder="Harga (numeric)" required></div>
        <button class="btn btn-primary">Simpan Layanan</button>
      </form>

      <h5 class="mt-4">Daftar Layanan</h5>
      <table class="table table-sm">
        <thead><tr><th>Nama</th><th>Jenis</th><th>Harga</th></tr></thead>
        <tbody>
          <?php foreach($services as $s): ?>
            <tr>
              <td><?=htmlspecialchars($s['name'])?></td>
              <td><?=htmlspecialchars($s['type'])?></td>
              <td><?=number_format($s['price'],0,',','.')?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
