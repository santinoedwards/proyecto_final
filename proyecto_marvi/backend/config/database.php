<?php

function conectar() {
  $conexion = new mysqli("localhost", "root", "", "marvi");

  if ($conexion->connect_error) {
    throw new Exception("No se pudo conectar al servidor MySQL: " . $conexion->connect_error);
  }

  $conexion->set_charset("utf8mb4");

  return $conexion;
}

?>
