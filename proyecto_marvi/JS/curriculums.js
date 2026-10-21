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
    celda.colSpan = 6;
    celda.className = 'tabla-vacia';
    celda.textContent = mensaje;
    fila.appendChild(celda);
    cuerpoTabla.appendChild(fila);
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
