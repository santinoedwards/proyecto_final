// Procesa el inicio de sesión y redirige al panel cuando las credenciales son válidas.
const formularioLogin = document.getElementById('login-form');
const mensajeLogin = document.getElementById('login-message');

formularioLogin.addEventListener('submit', async (evento) => {
    evento.preventDefault();
    const boton = formularioLogin.querySelector('button[type="submit"]');
    boton.disabled = true;
    mensajeLogin.textContent = 'Verificando datos...';
    mensajeLogin.classList.remove('mensaje-exito');

    try {
        const respuesta = await fetch('../backend/index.php?action=iniciar_sesion', {
            method: 'POST',
            body: new FormData(formularioLogin)
        });
        const datos = await respuesta.json();

        if (!respuesta.ok || !datos.exito) {
            throw new Error(datos.error || 'No se pudo iniciar sesión.');
        }

        window.location.href = 'curriculums.html';
    } catch (error) {
        mensajeLogin.textContent = error.message || 'Error de conexión con el servidor.';
    } finally {
        boton.disabled = false;
    }
});
