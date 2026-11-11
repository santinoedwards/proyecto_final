<?php

require_once __DIR__ . '/../config/database.php';

function agregarPostulacion($nombre, $email, $telefono = null, $puesto = null, $carta = null, $ruta_cv = null) {
  $conn = conectar();

  $stmt = $conn->prepare("INSERT INTO postulaciones (nombre, email, telefono, puesto_postulado, carta_presentacion, ruta_cv) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param('ssssss', $nombre, $email, $telefono, $puesto, $carta, $ruta_cv);

  $ok = $stmt->execute();

  if ($ok) {
    return ['exito' => true, 'id' => $stmt->insert_id];
  } else {
    return ['exito' => false, 'error' => $conn->error];
  }
}

function obtenerPostulaciones() {
  $conn = conectar();

  $sql = "SELECT id, nombre, email, telefono, puesto_postulado, carta_presentacion, ruta_cv, estado, fecha_postulacion FROM postulaciones ORDER BY fecha_postulacion DESC";
  $resultado = $conn->query($sql);
  $filas = [];

  if ($resultado) {
    while ($r = $resultado->fetch_assoc()) {
      $filas[] = $r;
    }
  }

  return $filas;
}

// Devuelve una postulación por su id (o null si no existe).
function obtenerPostulacionPorId($id) {
  $conn = conectar();
  $stmt = $conn->prepare("SELECT id, ruta_cv, estado FROM postulaciones WHERE id = ? LIMIT 1");
  $stmt->bind_param('i', $id);
  $stmt->execute();

  $resultado = $stmt->get_result();
  $postulacion = $resultado->fetch_assoc();
  $stmt->close();
  $conn->close();

  return $postulacion;
}

// Marca la postulación como aceptada.
function aceptarPostulacion($id) {
  $conn = conectar();
  $stmt = $conn->prepare("UPDATE postulaciones SET estado = 'aceptada' WHERE id = ?");
  $stmt->bind_param('i', $id);
  $ok = $stmt->execute();
  $stmt->close();
  $conn->close();

  return $ok;
}

// Elimina la postulación de la base de datos.
function eliminarPostulacion($id) {
  $conn = conectar();
  $stmt = $conn->prepare("DELETE FROM postulaciones WHERE id = ?");
  $stmt->bind_param('i', $id);
  $ok = $stmt->execute();
  $stmt->close();
  $conn->close();

  return $ok;
}

// Busca la cuenta por correo; la contraseña se verifica en el controlador con password_verify().
function obtenerAdministradorPorEmail($email) {
  $conn = conectar();
  $stmt = $conn->prepare("SELECT id, email, password_hash FROM administradores WHERE email = ? LIMIT 1");
  $stmt->bind_param('s', $email);
  $stmt->execute();

  $resultado = $stmt->get_result();
  $administrador = $resultado->fetch_assoc();
  $stmt->close();
  $conn->close();

  return $administrador;
}

?>
