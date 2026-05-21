<?php
require_once __DIR__ . '/../config/koneksi.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? 0;

$stmt = $koneksi->prepare("SELECT id, name FROM kelurahan WHERE district_id = ? ORDER BY name ASC");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
