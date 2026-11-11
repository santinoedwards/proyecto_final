<?php
// Mantiene la sesión del administrador para proteger postulaciones y archivos adjuntos.
session_set_cookie_params([
  'httponly' => true,
  'samesite' => 'Lax',
  'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
]);
session_start();

require_once __DIR__ . '/dao/DAO.php';

$action = $_REQUEST['action'] ?? '';

function responderJson($datos, $estado = 200) {
  http_response_code($estado);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($datos, JSON_UNESCAPED_UNICODE);
  exit;
}

function requiereAdministrador() {
  if (empty($_SESSION['administrador_id'])) {
    responderJson(['error' => 'Debes iniciar sesión para ver las postulaciones.'], 401);
  }
}

try {
  switch ($action) {
    case 'iniciar_sesion':
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responderJson(['error' => 'Método no permitido.'], 405);
      }

      $email = trim($_POST['email'] ?? '');
      $password = $_POST['password'] ?? '';

      if ($email === '' || $password === '') {
        responderJson(['error' => 'Ingresa el correo electrónico y la contraseña.'], 400);
      }

      $administrador = obtenerAdministradorPorEmail($email);
      if (!$administrador || !password_verify($password, $administrador['password_hash'])) {
        responderJson(['error' => 'El correo o la contraseña no son correctos.'], 401);
      }

      session_regenerate_id(true);
      $_SESSION['administrador_id'] = (int) $administrador['id'];
      $_SESSION['administrador_email'] = $administrador['email'];
      responderJson(['exito' => true]);
      break;

    case 'cerrar_sesion':
      $_SESSION = [];
      if (ini_get('session.use_cookies')) {
        $parametros = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $parametros['path'], $parametros['domain'], $parametros['secure'], $parametros['httponly']);
      }
      session_destroy();
      responderJson(['exito' => true]);
      break;

    case 'agregar_postulacion':
      $nombre = trim($_REQUEST['nombre'] ?? '');
      $email = trim($_REQUEST['email'] ?? '');

      if (!$nombre || !$email) {
        responderJson(['error' => 'Faltan datos requeridos (nombre, email).'], 400);
      }

      $telefono = trim($_REQUEST['telefono'] ?? '');
      $puesto = trim($_REQUEST['puesto_postulado'] ?? '');
      $carta = trim($_REQUEST['carta_presentacion'] ?? '');
      $ruta_cv = null;

      // Manejo del archivo subido (campo "cv").
      if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $uploadsDir = __DIR__ . '/uploads';
        $tmp = $_FILES['cv']['tmp_name'];
        $originalName = basename($_FILES['cv']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, ['pdf', 'doc', 'docx'], true)) {
          responderJson(['error' => 'El CV debe estar en formato PDF, DOC o DOCX.'], 400);
        }

        $targetName = uniqid('cv_', true) . '.' . $ext;
        $targetPath = $uploadsDir . '/' . $targetName;

        if (move_uploaded_file($tmp, $targetPath)) {
          $ruta_cv = 'backend/uploads/' . $targetName;
        }
      }

      responderJson(agregarPostulacion($nombre, $email, $telefono, $puesto, $carta, $ruta_cv));
      break;

    case 'postulaciones':
      requiereAdministrador();
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(obtenerPostulaciones(), JSON_UNESCAPED_UNICODE);
      break;

    // Marca una postulación como aceptada (solo administrador).
    case 'aceptar_postulacion':
      requiereAdministrador();
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responderJson(['error' => 'Método no permitido.'], 405);
      }

      $id = (int) ($_POST['id'] ?? 0);
      if ($id <= 0) {
        responderJson(['error' => 'Postulación no válida.'], 400);
      }

      aceptarPostulacion($id);
      responderJson(['exito' => true]);
      break;

    // Elimina la postulación y su CV adjunto (solo administrador).
    case 'eliminar_postulacion':
      requiereAdministrador();
      if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responderJson(['error' => 'Método no permitido.'], 405);
      }

      $id = (int) ($_POST['id'] ?? 0);
      if ($id <= 0) {
        responderJson(['error' => 'Postulación no válida.'], 400);
      }

      $postulacion = obtenerPostulacionPorId($id);
      if (!$postulacion) {
        responderJson(['error' => 'No se encontró la postulación.'], 404);
      }

      eliminarPostulacion($id);

      // Borra el archivo del CV; basename evita salir de la carpeta uploads.
      if (!empty($postulacion['ruta_cv'])) {
        $archivo = __DIR__ . '/uploads/' . basename($postulacion['ruta_cv']);
        if (is_file($archivo)) {
          unlink($archivo);
        }
      }
      responderJson(['exito' => true]);
      break;

    case 'descargar_cv':
      requiereAdministrador();
      $nombreArchivo = basename($_GET['archivo'] ?? '');
      $directorio = realpath(__DIR__ . '/uploads');
      $rutaArchivo = $directorio ? realpath($directorio . DIRECTORY_SEPARATOR . $nombreArchivo) : false;

      if (!$rutaArchivo || strpos($rutaArchivo, $directorio . DIRECTORY_SEPARATOR) !== 0 || !is_file($rutaArchivo)) {
        responderJson(['error' => 'No se encontró el archivo solicitado.'], 404);
      }

      $extension = strtolower(pathinfo($rutaArchivo, PATHINFO_EXTENSION));
      $tiposMime = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
      ];
      header('Content-Type: ' . ($tiposMime[$extension] ?? 'application/octet-stream'));
      header('Content-Length: ' . filesize($rutaArchivo));
      header('Content-Disposition: ' . ($extension === 'pdf' ? 'inline' : 'attachment') . '; filename="' . $nombreArchivo . '"');
      readfile($rutaArchivo);
      break;

    default:
      responderJson(['error' => 'Ruta no válida.'], 404);
  }
} catch (Throwable $e) {
  responderJson(['error' => 'Ocurrió un error al procesar la solicitud.'], 500);
}

?>