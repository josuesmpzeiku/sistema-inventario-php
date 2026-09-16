/**
 * Director de Orquesta Global
 * Ubicación: public/js/dashboard.js
 */

document.addEventListener('DOMContentLoaded', () => {

    /*
     * En localhost la aplicación puede estar en /optica.
     * En el hosting el subdominio apunta directamente a /public,
     * por lo tanto BASE_URL queda vacío.
     */
    const BASE_URL =
        (window.location.hostname === 'localhost' ||
         window.location.hostname === '127.0.0.1')
            ? '/optica'
            : '';

    /* Bloqueo del botón atrás del navegador. */
    window.history.pushState(null, null, window.location.href);

    window.addEventListener('popstate', () => {
        window.history.pushState(null, null, window.location.href);
    });

    /* Menú móvil. */
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
                if (sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                }
            });
        }
    }

    /* Elementos principales del dashboard. */
    const menuItems = document.querySelectorAll('.menu-item');
    const tituloModulo = document.getElementById('titulo-modulo-activo');
    const contenedorModulo = document.getElementById('modulo-activo-contenedor');

    /* Carga dinámica de los módulos. */
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

            /* Módulo de Admisión. */
            if (moduloSeleccionado === 'Admisión') {
                try {
                    const respuesta = await fetch(`${BASE_URL}/modulos/admision`);

                    if (!respuesta.ok) {
                        throw new Error(`Error HTTP ${respuesta.status}`);
                    }

                    if (contenedorModulo) {
                        contenedorModulo.innerHTML = await respuesta.text();
                    }

                    if (!document.getElementById('script-modulo-admision')) {
                        const scriptAdm = document.createElement('script');
                        scriptAdm.id = 'script-modulo-admision';
                        scriptAdm.src = `${BASE_URL}/js/admision.js`;
                        scriptAdm.onload = () => {
                            if (typeof inicializarModuloAdmision === 'function') {
                                inicializarModuloAdmision();
                            }
                        };
                        document.body.appendChild(scriptAdm);
                    } else if (typeof inicializarModuloAdmision === 'function') {
                        inicializarModuloAdmision();
                    }
                } catch (error) {
                    console.error('Fallo al cargar Admisión:', error);
                }

                return;
            }

            /* Módulo de Consultorio. */
            if (moduloSeleccionado === 'Consultorio') {
                try {
                    const respuesta = await fetch(`${BASE_URL}/modulos/consultorio`);

                    if (!respuesta.ok) {
                        throw new Error(`Error HTTP ${respuesta.status}`);
                    }

                    if (contenedorModulo) {
                        contenedorModulo.innerHTML = await respuesta.text();
                    }

                    /*
                     * No volver a cargar consultorio.js.
                     * El archivo declara const inicializarModuloConsultorio.
                     * Cargarlo otra vez provoca redeclaration en el navegador.
                     */
                    if (!document.getElementById('script-modulo-consultorio')) {
                        const scriptCons = document.createElement('script');
                        scriptCons.id = 'script-modulo-consultorio';
                        scriptCons.src = `${BASE_URL}/js/consultorio.js?v=${Date.now()}`;
                        scriptCons.onload = () => {
                            if (typeof inicializarModuloConsultorio === 'function') {
                                inicializarModuloConsultorio();
                            }
                        };
                        document.body.appendChild(scriptCons);
                    } else if (typeof inicializarModuloConsultorio === 'function') {
                        inicializarModuloConsultorio();
                    }
                } catch (error) {
                    console.error('Fallo al cargar Consultorio:', error);
                }

                return;
            }

            /* Módulo de Ventas. */
            if (moduloSeleccionado === 'Ventas') {
                try {
                    const respuesta = await fetch(`${BASE_URL}/modulos/ventas`);

                    if (!respuesta.ok) {
                        throw new Error(`Error HTTP ${respuesta.status}`);
                    }

                    if (contenedorModulo) {
                        contenedorModulo.innerHTML = await respuesta.text();
                    }

                    const scriptViejoVentas = document.getElementById('script-modulo-ventas');
                    if (scriptViejoVentas) {
                        scriptViejoVentas.remove();
                    }

                    const scriptVen = document.createElement('script');
                    scriptVen.id = 'script-modulo-ventas';
                    scriptVen.src = `${BASE_URL}/js/ventas.js?v=${Date.now()}`;
                    scriptVen.onload = () => {
                        if (typeof inicializarModuloVentas === 'function') {
                            inicializarModuloVentas();
                        }
                    };
                    document.body.appendChild(scriptVen);
                } catch (error) {
                    console.error('Fallo al cargar Ventas:', error);
                }

                return;
            }

            /* Módulos que todavía están en preparación. */
            if (contenedorModulo) {
                contenedorModulo.innerHTML = `
                    <h3>Módulo de ${moduloSeleccionado}</h3>
                    <p>Espacio en preparación.</p>
                `;
            }
        });
    });

    /* Cerrar sesión. */
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
