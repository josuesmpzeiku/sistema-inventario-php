const APP_BASE = (() => {
    const host = window.location.hostname;
    return (host === 'localhost' || host === '127.0.0.1') ? '/optica/public' : '';
})();

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const btnToggleMenu = document.getElementById('btn-toggle-menu');
    const menuItems = document.querySelectorAll('.menu-item');
    const tituloModulo = document.getElementById('titulo-modulo-activo');
    const contenedorModulo = document.getElementById('modulo-activo-contenedor');

    if (btnToggleMenu && sidebar) {
        btnToggleMenu.addEventListener('click', (event) => {
            event.stopPropagation();
            sidebar.classList.toggle('open');
        });

        if (mainContent) {
            mainContent.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        }
    }

    const cargarScriptUnaVez = (id, archivo, inicializar) => {
        const existente = document.getElementById(id);
        if (existente) {
            if (typeof inicializar === 'function') inicializar();
            return;
        }

        const script = document.createElement('script');
        script.id = id;
        script.src = `${APP_BASE}/js/${archivo}`;
        script.onload = () => {
            if (typeof inicializar === 'function') inicializar();
        };
        script.onerror = () => console.error(`No se pudo cargar el script: ${script.src}`);
        document.body.appendChild(script);
    };

    const cargarModulo = async (ruta, scriptId, archivo, inicializador) => {
        try {
            const respuesta = await fetch(`${APP_BASE}${ruta}`);
            if (!respuesta.ok) throw new Error(`Error HTTP ${respuesta.status}`);

            if (contenedorModulo) {
                contenedorModulo.innerHTML = await respuesta.text();
                // Elimina cualquier bloque de diagnóstico que haya quedado en una vista.
                contenedorModulo.querySelectorAll('.contenedor-transparente').forEach((elemento) => elemento.remove());
            }

            cargarScriptUnaVez(scriptId, archivo, inicializador);
        } catch (error) {
            console.error(`Fallo al cargar ${ruta}:`, error);
        }
    };

    menuItems.forEach((item) => {
        item.style.cursor = 'pointer';

        item.addEventListener('click', async function () {
            menuItems.forEach((menuItem) => menuItem.classList.remove('active'));
            this.classList.add('active');

            const moduloSeleccionado = this.getAttribute('data-module');
            if (tituloModulo) tituloModulo.textContent = moduloSeleccionado;
            if (sidebar && sidebar.classList.contains('open')) sidebar.classList.remove('open');

            if (moduloSeleccionado === 'Admisión') {
                await cargarModulo('/modulos/admision', 'script-modulo-admision', 'admision.js', () => {
                    if (typeof inicializarModuloAdmision === 'function') inicializarModuloAdmision();
                });
                return;
            }

            if (moduloSeleccionado === 'Consultorio') {
                await cargarModulo('/modulos/consultorio', 'script-modulo-consultorio', 'consultorio.js', () => {
                    if (typeof inicializarModuloConsultorio === 'function') inicializarModuloConsultorio();
                });
                return;
            }

            if (moduloSeleccionado === 'Ventas') {
                await cargarModulo('/modulos/ventas', 'script-modulo-ventas', 'ventas.js', () => {
                    if (typeof inicializarModuloVentas === 'function') inicializarModuloVentas();
                });
                return;
            }

            if (contenedorModulo) {
                contenedorModulo.innerHTML = `<h3>Módulo de ${moduloSeleccionado}</h3><p>Espacio en preparación.</p>`;
            }
        });
    });

    const btnLogout = document.getElementById('btn-logout');
    if (btnLogout) {
        btnLogout.onclick = async () => {
            if (!confirm('¿Desea salir del sistema de forma segura?')) return;

            try {
                const respuesta = await enviarPeticionAsincrona(`${APP_BASE}/api/logout`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                const resultado = await respuesta.json();

                if (resultado.success) {
                    window.location.href = `${APP_BASE}/login`;
                } else {
                    alert(resultado.error || 'No se pudo cerrar la sesión.');
                }
            } catch (error) {
                console.error('Fallo crítico en cierre de sesión:', error);
                alert('No se pudo conectar con el servidor para cerrar la sesión.');
            }
        };
    }
});
