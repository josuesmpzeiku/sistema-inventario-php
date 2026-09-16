/**
 * Controlador de inicio de sesión.
 */

const BASE_URL = (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') ? '/optica/public' : '';

const btnEntrar = document.getElementById('btn-entrar');
if (btnEntrar) {
    btnEntrar.addEventListener('click', async () => {
        const userValue = document.getElementById('userusuario').value.trim();
        const passValue = document.getElementById('passusuario').value;
        if (!userValue || !passValue) {
            alert('Por favor, escriba su usuario y contraseña.');
            return;
        }
        try {
            const respuesta = await enviarPeticionAsincrona(`${BASE_URL}/api/auth`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({userusuario: userValue, passusuario: passValue})
            });
            const resultado = await respuesta.json();
            if (resultado.success) {
                window.location.href = `${BASE_URL}/dashboard`;
            } else {
                alert(resultado.error || 'No se pudo iniciar sesión.');
            }
        } catch (error) {
            console.error('Detalle del error en el flujo de autenticación:', error);
        }
    });
}
