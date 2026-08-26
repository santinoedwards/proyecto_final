<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/dao/DAO.php';

$action = $_REQUEST['action'] ?? '';

try {
  switch ($action) {
    case 'agregar_postulacion':
      $nombre = trim($_REQUEST['nombre'] ?? '');
      $email = trim($_REQUEST['email'] ?? '');

      if (!$nombre || !$email) {
        echo json_encode(['error' => 'Faltan datos requeridos (nombre, email)']);
        break;
      }

      $telefono = trim($_REQUEST['telefono'] ?? null);
      $puesto = trim($_REQUEST['puesto_postulado'] ?? null);
      $carta = trim($_REQUEST['carta_presentacion'] ?? null);
      $ruta_cv = null;

      // Manejo de archivo subido (campo 'cv')
      if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $uploadsDir = __DIR__ . '/uploads';

        $tmp = $_FILES['cv']['tmp_name'];
        $originalName = basename($_FILES['cv']['name']);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $targetName = uniqid('cv_') . ($ext ? '.' . $ext : '');
        $targetPath = $uploadsDir . '/' . $targetName;

        if (move_uploaded_file($tmp, $targetPath)) {
          // Guardamos ruta relativa accesible desde la web
          $ruta_cv = 'backend/uploads/' . $targetName;
        }
      }

      $result = agregarPostulacion($nombre, $email, $telefono, $puesto, $carta, $ruta_cv);
      echo json_encode($result);
      break;

    case 'postulaciones':
      echo json_encode(obtenerPostulaciones());
      break;

    default:
      echo json_encode(['error' => 'Ruta no válida']);
      break;
  }
} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
}

?>
