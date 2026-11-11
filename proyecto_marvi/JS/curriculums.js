const cuerpoTabla = document.getElementById('tabla-cuerpo-cvs');
const mensajeTabla = document.getElementById('tabla-message');
const botonCerrarSesion = document.getElementById('logout-button');
const rutaBackend = '../backend/index.php';

// Agrega contenido como texto para evitar interpretar datos de postulantes como HTML.
function agregarCelda(fila, valor, clase = '') {
    const celda = document.createElement('td');
    celda.textContent = valor || '';
    if (clase) {
        celda.className = clase;
    }
    fila.appendChild(celda);
    return celda;
}

function mostrarFilaVacia(mensaje) {
    cuerpoTabla.replaceChildren();
    const fila = document.createElement('tr');
    const celda = document.createElement('td');
    celda.colSpan = 8;
    celda.className = 'tabla-vacia';
    celda.textContent = mensaje;
    fila.appendChild(celda);
    cuerpoTabla.appendChild(fila);
}

function mostrarMensaje(texto) {
    mensajeTabla.textContent = texto;
}

function crearBoton(texto, clase, onClick) {
    const boton = document.createElement('button');
    boton.type = 'button';
    boton.className = `btn-accion ${clase}`;
    boton.textContent = texto;
    boton.addEventListener('click', onClick);
    return boton;
}

// Arma el enlace de Gmail (web) con el mensaje listo para la persona aceptada.
// Si usas otro correo, cambia esta URL base (ej. Outlook: https://outlook.live.com/mail/0/deeplink/compose).
function crearEnlaceCorreo(postulacion) {
    const puesto = postulacion.puesto_postulado || 'el puesto al que te postulaste';
    const asunto = `Invitación a entrevista - ${puesto} | Marvi`;
    const cuerpo =
        `Estimado/a ${postulacion.nombre || ''}:\n\n` +
        `Es un gusto saludarte. Hemos revisado con atención tu postulación para ${puesto} ` +
        `y nos complace invitarte a una entrevista con nuestro equipo.\n\n` +
        `Te esperamos el día: [ESCRIBE AQUÍ EL DÍA]\n` +
        `A la hora: [ESCRIBE AQUÍ LA HORA]\n\n` +
        `Si tienes algún inconveniente con la fecha o el horario, respóndenos a este correo ` +
        `y buscaremos juntos una alternativa.\n\n` +
        `Agradecemos tu interés en formar parte de Marvi y esperamos conocerte pronto.\n\n` +
        `Cordialmente,\nEquipo de Marvi`;

    return (
        'https://mail.google.com/mail/?view=cm&fs=1' +
        `&to=${encodeURIComponent(postulacion.email)}` +
        `&su=${encodeURIComponent(asunto)}` +
        `&body=${encodeURIComponent(cuerpo)}`
    );
}

// Abre la redacción del correo en una pestaña nueva.
// "ventana" es una pestaña ya abierta con el clic (para que el navegador no la bloquee).
function abrirCorreo(postulacion, ventana = null) {
    const enlace = crearEnlaceCorreo(postulacion);
    if (ventana && !ventana.closed) {
        ventana.location.href = enlace;
    } else {
        window.open(enlace, '_blank', 'noopener');
    }
}

// Envía una acción POST al backend (aceptar o eliminar) y maneja sesión vencida.
async function enviarAccion(action, id) {
    const respuesta = await fetch(`${rutaBackend}?action=${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ id })
    });

    if (respuesta.status === 401) {
        window.location.replace('adminstrador.html');
        throw new Error('Sesión vencida.');
    }

    const datos = await respuesta.json().catch(() => ({}));
    if (!respuesta.ok) {
        throw new Error(datos.error || 'No se pudo completar la acción.');
    }
    return datos;
}

async function aceptarPostulacion(postulacion) {
    // Se abre la pestaña ahora, durante el clic, para que el navegador no la bloquee.
    const ventana = window.open('', '_blank');
    try {
        await enviarAccion('aceptar_postulacion', postulacion.id);
        mostrarMensaje(`Aceptaste a ${postulacion.nombre}. Se abrió el correo para escribirle.`);
        await cargarCurriculums();
        abrirCorreo(postulacion, ventana);
    } catch (error) {
        if (ventana) ventana.close();
        mostrarMensaje(error.message);
    }
}

async function eliminarPostulacion(postulacion) {
    if (!confirm(`¿Eliminar la postulación de ${postulacion.nombre}? Esta acción no se puede deshacer.`)) {
        return;
    }
    try {
        await enviarAccion('eliminar_postulacion', postulacion.id);
        mostrarMensaje(`Eliminaste la postulación de ${postulacion.nombre}.`);
        await cargarCurriculums();
    } catch (error) {
        mostrarMensaje(error.message);
    }
}

// Obtiene todas las postulaciones del backend; el servidor exige una sesión activa.
async function cargarCurriculums() {
    try {
        const respuesta = await fetch(`${rutaBackend}?action=postulaciones`);
        const datos = await respuesta.json();

        if (respuesta.status === 401) {
            window.location.replace('adminstrador.html');
            return;
        }
        if (!respuesta.ok || !Array.isArray(datos)) {
            throw new Error(datos.error || 'No se pudieron cargar las postulaciones.');
        }
        if (datos.length === 0) {
            mostrarFilaVacia('Todavía no se han recibido postulaciones.');
            return;
        }

        cuerpoTabla.replaceChildren();
        datos.forEach((postulacion) => {
            const fila = document.createElement('tr');
            agregarCelda(fila, postulacion.nombre || 'Sin nombre');
            agregarCelda(fila, postulacion.email || '');
            agregarCelda(fila, postulacion.telefono || '');
            agregarCelda(fila, postulacion.puesto_postulado || 'No especificado');
            agregarCelda(fila, postulacion.carta_presentacion || 'Sin carta');

            const celdaCv = document.createElement('td');
            if (postulacion.ruta_cv) {
                const nombreArchivo = postulacion.ruta_cv.split('/').pop();
                const enlace = document.createElement('a');
                enlace.className = 'btn-descargar';
                enlace.href = `${rutaBackend}?action=descargar_cv&archivo=${encodeURIComponent(nombreArchivo)}`;
                enlace.textContent = 'Ver / descargar CV';
                enlace.target = '_blank';
                enlace.rel = 'noopener noreferrer';
                celdaCv.appendChild(enlace);
            } else {
                celdaCv.textContent = 'No adjuntó archivo';
                celdaCv.className = 'sin-cv';
            }
            fila.appendChild(celdaCv);

            // Estado
            const aceptada = postulacion.estado === 'aceptada';
            const celdaEstado = document.createElement('td');
            const etiqueta = document.createElement('span');
            etiqueta.className = `estado ${aceptada ? 'estado-aceptada' : 'estado-pendiente'}`;
            etiqueta.textContent = aceptada ? 'Aceptada' : 'Pendiente';
            celdaEstado.appendChild(etiqueta);
            fila.appendChild(celdaEstado);

            // Acciones
            const celdaAcciones = document.createElement('td');
            celdaAcciones.className = 'celda-acciones';
            if (aceptada) {
                celdaAcciones.appendChild(
                    crearBoton('Escribir correo', 'btn-aceptar', () => abrirCorreo(postulacion))
                );
            } else {
                celdaAcciones.appendChild(
                    crearBoton('Aceptar', 'btn-aceptar', () => aceptarPostulacion(postulacion))
                );
            }
            celdaAcciones.appendChild(
                crearBoton('Eliminar', 'btn-eliminar', () => eliminarPostulacion(postulacion))
            );
            fila.appendChild(celdaAcciones);

            cuerpoTabla.appendChild(fila);
        });
    } catch (error) {
        mostrarFilaVacia('Error al cargar los currículums. Comprueba la conexión con el servidor.');
        mensajeTabla.textContent = error.message || 'Error de conexión.';
    }
}

// Cierra la sesión en el servidor antes de volver al formulario de acceso.
botonCerrarSesion.addEventListener('click', async () => {
    botonCerrarSesion.disabled = true;
    try {
        await fetch(`${rutaBackend}?action=cerrar_sesion`, { method: 'POST' });
    } finally {
        window.location.replace('adminstrador.html');
    }
});

cargarCurriculums();