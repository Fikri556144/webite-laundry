<?php
require 'config.php';
require_login();

// composer autoload for dompdf
require_once __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_tx'])) {
        $customer_id = $_POST['customer_id'] ?: null;
        $service_id = $_POST['service_id'];
        $qty = floatval($_POST['qty']);
        $note = $_POST['note'] ?? '';

        // get service price
        $stmt = $pdo->prepare("SELECT price, name, type FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        $s = $stmt->fetch();
        $subtotal = $s['price'] * $qty;

        $stmt = $pdo->prepare("INSERT INTO transactions (customer_id, service_id, qty, subtotal, note, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$customer_id, $service_id, $qty, $subtotal, $note]);
        $tx_id = $pdo->lastInsertId();

        // generate PDF and send to browser
        $stmt = $pdo->prepare("SELECT t.*, c.name as customer_name, s.name as service_name, s.type as service_type FROM transactions t LEFT JOIN customers c ON t.customer_id=c.id LEFT JOIN services s ON t.service_id=s.id WHERE t.id = ?");
        $stmt->execute([$tx_id]);
        $tdata = $stmt->fetch();

        $html = '<h2>Nota Laundry</h2>';
        $html .= '<p>No: '.$tdata['id'].'</p>';
        $html .= '<p>Pelanggan: '.htmlspecialchars($tdata['customer_name'] ?? 'Umum').'</p>';
        $html .= '<table style="width:100%;border-collapse:collapse;" border="1">
                    <thead><tr><th>Layanan</th><th>Jenis</th><th>Qty</th><th>Subtotal</th></tr></thead>
                    <tbody>
                      <tr>
                        <td>'.htmlspecialchars($tdata['service_name']).'</td>
                        <td>'.htmlspecialchars($tdata['service_type']).'</td>
                        <td>'.$tdata['qty'].'</td>
                        <td>'.number_format($tdata['subtotal'],0,',','.').'</td>
                      </tr>
                    </tbody>
                  </table>';
        $html .= '<p>Total: <strong>'.number_format($tdata['subtotal'],0,',','.').'</strong></p>';
        $html .= '<p>Tanggal: '.$tdata['created_at'].'</p>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','portrait');
        $dompdf->render();
        $dompdf->stream("nota_".$tdata['id'].".pdf", ["Attachment" => 0]); // 0 untuk buka di browser, 1 untuk download
        exit;
    }
}

$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC")->fetchAll();
$services = $pdo->query("SELECT * FROM services ORDER BY id ASC")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Nota - Laundry</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar_partial.php' ?? ''; ?>
<div class="container my-4">
  <h3>Buat Transaksi & Cetak Nota</h3>
  <form method="post" class="row g-2">
    <div class="col-md-4">
      <select name="customer_id" class="form-select">
        <option value="">-- Pelanggan (Umum) --</option>
        <?php foreach($customers as $c): ?>
          <option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <select name="service_id" class="form-select" required>
        <?php foreach($services as $s): ?>
          <option value="<?=$s['id']?>"><?=htmlspecialchars($s['name'])?> (<?=htmlspecialchars($s['type'])?>) - <?=number_format($s['price'],0,',','.')?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2"><input name="qty" type="number" step="0.1" value="1" class="form-control" required></div>
    <div class="col-md-2"><button name="create_tx" class="btn btn-success w-100">Cetak Nota (PDF)</button></div>
    <div class="col-12 mt-2"><input class="form-control" name="note" placeholder="Catatan (opsional)"></div>
  </form>
</div>
</body>
</html>
