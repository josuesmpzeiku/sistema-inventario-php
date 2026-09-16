/**
 * Director de Orquesta Global
 * Ubicación: public/js/dashboard.js
 */

document.addEventListener('DOMContentLoaded', () => {
    const BASE_URL =
        (window.location.hostname === 'localhost' ||
         window.location.hostname === '127.0.0.1')
            ? '/optica'
            : '';

    window.history.pushState(null, null, window.location.href);
    window.addEventListener('popstate', () => {
        window.history.pushState(null, null, window.location.href);
    });

    const btnToggleMenu = document.getElementById('btn-toggle-menu');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

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

    const menuItems = document.querySelectorAll('.menu-item');
    const tituloModulo = document.getElementById('titulo-modulo-activo');
    const contenedorModulo = document.getElementById('modulo-activo-contenedor');

    /**
     * Carga un script una sola vez por página.
     * No se elimina ni se vuelve a solicitar al cambiar de módulo.
     */
    const cargarScriptUnaVez = (id, src, inicializador) => {
        const scriptExistente = document.getElementById(id);

        if (scriptExistente) {
            if (typeof inicializador === 'function') {
                inicializador();
            }
            return;
        }

        const script = document.createElement('script');
        script.id = id;
        script.src = src;
        script.onload = () => {
            if (typeof inicializador === 'function') {
                inicializador();
            }
        };
        script.onerror = () => {
            console.error(`No se pudo cargar el script: ${src}`);
        };
        document.body.appendChild(script);
    };

    const cargarModulo = async (ruta, scriptId, scriptNombre, inicializador) => {
        try {
            const respuesta = await fetch(`${BASE_URL}${ruta}`);

            if (!respuesta.ok) {
                throw new Error(`Error HTTP ${respuesta.status}`);
            }

            if (contenedorModulo) {
                contenedorModulo.innerHTML = await respuesta.text();
            }

            /*
             * No se utiliza Date.now() para estos scripts.
             * Date.now() solo evita caché; no configura la zona horaria.
             * El script se carga una sola vez para evitar redeclaration de const.
             */
            cargarScriptUnaVez(
                scriptId,
                `${BASE_URL}/js/${scriptNombre}`,
                inicializador
            );
        } catch (error) {
            console.error(`Fallo al cargar ${ruta}:`, error);
        }
    };

    menuItems.forEach((item) => {
        item.style.cursor = 'pointer';

        item.addEventListener('click', async function () {
            menuItems.forEach((menuItem) => {
                menuItem.classList.remove('active');
            });

            this.classList.add('active');

            const moduloSeleccionado = this.getAttribute('data-module');

            if (tituloModulo) {
                tituloModulo.textContent = moduloSeleccionado;
            }

            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }

            if (moduloSeleccionado === 'Admisión') {
                await cargarModulo(
                    '/modulos/admision',
                    'script-modulo-admision',
                    'admision.js',
                    () => {
                        if (typeof inicializarModuloAdmision === 'function') {
                            inicializarModuloAdmision();
                        }
                    }
                );
                return;
            }

            if (moduloSeleccionado === 'Consultorio') {
                await cargarModulo(
                    '/modulos/consultorio',
                    'script-modulo-consultorio',
                    'consultorio.js',
                    () => {
                        if (typeof inicializarModuloConsultorio === 'function') {
                            inicializarModuloConsultorio();
                        }
                    }
                );
                return;
            }

            if (moduloSeleccionado === 'Ventas') {
                await cargarModulo(
                    '/modulos/ventas',
                    'script-modulo-ventas',
                    'ventas.js',
                    () => {
                        if (typeof inicializarModuloVentas === 'function') {
                            inicializarModuloVentas();
                        }
                    }
                );
                return;
            }

            if (contenedorModulo) {
                contenedorModulo.innerHTML = `
                    <h3>Módulo de ${moduloSeleccionado}</h3>
                    <p>Espacio en preparación.</p>
                `;
            }
        });
    });

    const btnLogout = document.getElementById('btn-logout');

    if (btnLogout) {
        btnLogout.onclick = async () => {
            if (!confirm('¿Desea salir del sistema de forma segura?')) {
                return;
            }

            try {
                const respuesta = await fetch(`${BASE_URL}/api/logout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                if (!respuesta.ok) {
                    throw new Error(`Error HTTP ${respuesta.status}`);
                }

                const resultado = await respuesta.json();

                if (resultado.success) {
                    window.location.href = `${BASE_URL}/login`;
                }
            } catch (error) {
                console.error('Fallo crítico en cierre de sesión:', error);
            }
        };
    }
});
