/** Director de Orquesta Global */
document.addEventListener('DOMContentLoaded', () => {
    const BASE_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica/public' : '';
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const btnToggleMenu = document.getElementById('btn-toggle-menu');
    const menuItems = document.querySelectorAll('.menu-item');
    const tituloModulo = document.getElementById('titulo-modulo-activo');
    const contenedorModulo = document.getElementById('modulo-activo-contenedor');

    if (btnToggleMenu && sidebar) {
        btnToggleMenu.addEventListener('click', event => {
            event.stopPropagation();
            sidebar.classList.toggle('open');
        });
        if (mainContent) mainContent.addEventListener('click', () => sidebar.classList.remove('open'));
    }

    const cargarScriptUnaVez = (id, archivo, inicializar) => {
        const existente = document.getElementById(id);
        if (existente) {
            if (existente.dataset.loaded === 'true') inicializar();
            else existente.addEventListener('load', inicializar, {once: true});
            return;
        }
        const script = document.createElement('script');
        script.id = id;
        script.src = `${BASE_URL}/js/${archivo}`;
        script.onload = () => {
            script.dataset.loaded = 'true';
            inicializar();
        };
        script.onerror = () => console.error(`No se pudo cargar ${script.src}`);
        document.body.appendChild(script);
    };

    const cargarModulo = async (ruta, scriptId, archivo, inicializar) => {
        try {
            const respuesta = await fetch(`${BASE_URL}${ruta}`);
            if (!respuesta.ok) throw new Error(`HTTP ${respuesta.status}`);
            if (contenedorModulo) contenedorModulo.innerHTML = await respuesta.text();
            cargarScriptUnaVez(scriptId, archivo, inicializar);
        } catch (error) {
            console.error(`Fallo al cargar ${ruta}:`, error);
        }
    };

    menuItems.forEach(item => {
        item.style.cursor = 'pointer';
        item.addEventListener('click', async function () {
            menuItems.forEach(elemento => elemento.classList.remove('active'));
            this.classList.add('active');
            const modulo = this.dataset.module;
            if (tituloModulo) tituloModulo.textContent = modulo;
            if (sidebar) sidebar.classList.remove('open');

            if (modulo === 'Admisión') {
                await cargarModulo('/modulos/admision', 'script-modulo-admision', 'admision.js', () => {
                    if (typeof inicializarModuloAdmision === 'function') inicializarModuloAdmision();
                });
            } else if (modulo === 'Consultorio') {
                await cargarModulo('/modulos/consultorio', 'script-modulo-consultorio', 'consultorio.js', () => {
                    if (typeof inicializarModuloConsultorio === 'function') inicializarModuloConsultorio();
                });
            } else if (modulo === 'Ventas') {
                await cargarModulo('/modulos/ventas', 'script-modulo-ventas', 'ventas.js', () => {
                    if (typeof inicializarModuloVentas === 'function') inicializarModuloVentas();
                });
            } else if (contenedorModulo) {
                contenedorModulo.innerHTML = `<h3>Módulo de ${modulo}</h3><p>Espacio en preparación.</p>`;
            }
        });
    });

    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) btnLogout.onclick = async () => {
        if (!confirm('¿Desea salir del sistema de forma segura?')) return;
        try {
            const respuesta = await enviarPeticionAsincrona(`${BASE_URL}/api/logout`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'}
            });
            const resultado = await respuesta.json();
            if (resultado.success) window.location.href = `${BASE_URL}/login`;
            else alert(resultado.error || 'No se pudo cerrar la sesión.');
        } catch (error) {
            console.error('Fallo en cierre de sesión:', error);
            alert('No se pudo conectar con el servidor para cerrar la sesión.');
        }
    };
});
