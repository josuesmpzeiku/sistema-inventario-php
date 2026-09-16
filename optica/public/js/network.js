/**
 * Monitor de Conectividad y Contenedor de Peticiones Seguras
 * Ubicación: public/js/network.js
 */

// Valida el estado de la red y controla la alerta visual en la interfaz
function comprobarConexionInternet() {
    const alertaRed = document.getElementById('network-alert');
    
    if (!navigator.onLine) {
        if (alertaRed) {
            alertaRed.style.display = 'block';
        }
        return false;
    } else {
        if (alertaRed) {
            alertaRed.style.display = 'none';
        }
        return true;
    }
}

// Escuchadores de eventos nativos del navegador para cambios de red repentinos
window.addEventListener('online', comprobarConexionInternet);
window.addEventListener('offline', comprobarConexionInternet);

/**
 * Envoltura global y segura para todas las peticiones FETCH del sistema
 * @param {string} url - Ruta del recurso o controlador API
 * @param {object} opciones - Configuración de la petición (Method, Headers, Body, etc.)
 */
async function enviarPeticionAsincrona(url, opciones = {}) {
    // Intercepción proactiva antes de que la petición salga del navegador
    if (!comprobarConexionInternet()) {
        alert("Operación cancelada: No se detectó conexión a internet activa. Resguarde sus cambios e intente nuevamente.");
        throw new Error("Petición abortada por ausencia de red.");
    }

    try {
        const respuesta = await fetch(url, opciones);
        
        // Control automático de sesiones expiradas o denegadas
        if (respuesta.status === 401) {
            alert("Su sesión ha expirado o no cuenta con los permisos necesarios.");
            window.location.reload();
        }
        
        return respuesta;
    } catch (error) {
        console.error("Fallo crítico en el canal de red asíncrono:", error);
        throw error;
    }
}
