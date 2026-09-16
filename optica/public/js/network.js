/**
 * Monitor de conectividad y normalizador de rutas.
 * Compatible con /optica/public en local y con la raíz en producción.
 */

function comprobarConexionInternet() {
    const alertaRed = document.getElementById('network-alert');
    if (!navigator.onLine) {
        if (alertaRed) alertaRed.style.display = 'block';
        return false;
    }
    if (alertaRed) alertaRed.style.display = 'none';
    return true;
}

window.addEventListener('online', comprobarConexionInternet);
window.addEventListener('offline', comprobarConexionInternet);

/* Compatibilidad con módulos antiguos que todavía usan /optica/... */
(() => {
    const fetchOriginal = window.fetch.bind(window);
    const esLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';

    window.fetch = (recurso, opciones) => {
        if (esLocal && typeof recurso === 'string' && recurso.startsWith('/optica/') && !recurso.startsWith('/optica/public/')) {
            recurso = `/optica/public/${recurso.substring('/optica/'.length)}`;
        }
        return fetchOriginal(recurso, opciones);
    };
})();

async function enviarPeticionAsincrona(url, opciones = {}) {
    if (!comprobarConexionInternet()) {
        alert('Operación cancelada: No se detectó conexión de red activa.');
        throw new Error('Petición abortada por ausencia de red.');
    }

    const respuesta = await fetch(url, opciones);
    if (respuesta.status === 401) {
        alert('Su sesión ha expirado o no cuenta con permisos suficientes.');
        window.location.reload();
    }
    return respuesta;
}
