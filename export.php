<?php
require 'config.php';
require_login();
$format = $_GET['format'] ?? 'csv';
$start = $_GET['start'] ?? date('Y-m-d');
$end = $_GET['end'] ?? date('Y-m-d');
$start_dt = date('Y-m-d 00:00:00', strtotime($start));
$end_dt = date('Y-m-d 23:59:59', strtotime($end));
$stmt = $pdo->prepare("SELECT t.*, c.name as customer_name, s.name as service_name FROM transactions t LEFT JOIN customers c ON t.customer_id=c.id LEFT JOIN services s ON t.service_id=s.id WHERE t.created_at BETWEEN ? AND ? ORDER BY t.created_at DESC");
$stmt->execute([$start_dt, $end_dt]);
$rows = $stmt->fetchAll();


if ($format === 'xls') {
// simple Excel: send HTML table with content-type for Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachm)