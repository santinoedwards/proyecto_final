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

  $sql = "SELECT id, nombre, email, telefono, puesto_postulado, carta_presentacion, ruta_cv, fecha_postulacion FROM postulaciones ORDER BY fecha_postulacion DESC";
  $resultado = $conn->query($sql);
  $filas = [];

  if ($resultado) {
    while ($r = $resultado->fetch_assoc()) {
      $filas[] = $r;
    }
  }

  return $filas;
}

?>
