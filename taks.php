<?php
require 'config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_task'])) {
        $title = $_POST['title'] ?? '';
        $cust = $_POST['customer_id'] ?: null;
        $stmt = $pdo->prepare("INSERT INTO tasks (title, customer_id, status, created_at) VALUES (?, ?, 'pending', NOW())");
        $stmt->execute([$title, $cust]);
    } elseif (isset($_POST['update_status'])) {
        $id = intval($_POST['task_id']);
        $status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }
}

$tasks = $pdo->query("SELECT t.*, c.name as customer_name FROM tasks t LEFT JOIN customers c ON t.customer_id=c.id ORDER BY t.created_at DESC")->fetchAll();
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Task - Laundry</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar_partial.php' ?? ''; ?>
<div class="container my-4">
  <h3>Task Laundry</h3>
  <form method="post" class="row g-2 mb-3">
    <div class="col-md-6"><input name="title" class="form-control" placeholder="Judul task" required></div>
    <div class="col-md-4">
      <select name="customer_id" class="form-select">
        <option value="">Pilih pelanggan (opsional)</option>
        <?php foreach($customers as $c): ?>
          <option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2"><button name="add_task" class="btn btn-primary w-100">Tambah</button></div>
  </form>

  <div class="row">
    <?php $cols = ['pending'=>'Pending','in_progress'=>'In Progress','done'=>'Done']; ?>
    <?php foreach($cols as $key=>$label): ?>
      <div class="col-md-4">
        <h5><?=$label?></h5>
        <div class="list-group">
        <?php foreach($tasks as $t): if ($t['status'] === $key): ?>
          <div class="list-group-item mb-2">
            <strong><?=htmlspecialchars($t['title'])?></strong>
            <div class="small text-muted"><?=htmlspecialchars($t['customer_name'] ?? '-')?></div>
            <form method="post" class="mt-2 d-flex gap-2">
              <input type="hidden" name="task_id" value="<?=$t['id']?>">
              <select name="status" class="form-select form-select-sm w-auto">
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="done">Done</option>
              </select>
              <button name="update_status" class="btn btn-sm btn-outline-primary">Ubah</button>
            </form>
          </div>
        <?php endif; endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
