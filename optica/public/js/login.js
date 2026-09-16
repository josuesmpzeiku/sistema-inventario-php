/**
 * Controlador de Interfaz Asíncrona para Inicio de Sesión
 * Ubicación: public/js/login.js
 */

document.getElementById('btn-entrar').addEventListener('click', async () => {
    // 1. Recolección de los datos ingresados por el usuario
    const userValue = document.getElementById('userusuario').value.trim();
    const passValue = document.getElementById('passusuario').value;

    // 2. Validación básica previa en el Frontend
    if (!userValue || !passValue) {
        alert("Por favor, escriba su usuario y contraseña.");
        return;
    }

    // 3. Preparación del objeto JSON plano (Sin depender de etiquetas <form>)
    const payload = {
        userusuario: userValue,
        passusuario: passValue
    };

    try {
        // 4. Envío seguro utilizando nuestro envoltorio asíncrono con detector de red
        const respuesta = await enviarPeticionAsincrona('/api/auth', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        // 5. Procesamiento de la respuesta del servidor
        const resultado = await respuesta.json();

        if (resultado.success) {
            // Acceso concedido: El enrutador de PHP nos permitirá entrar al dashboard
            window.location.href = '/dashboard';
        } else {
            // Error controlado devuelto por el controlador PHP
            alert(resultado.error || "No se pudo iniciar sesión.");
        }

    } catch (error) {
        // Los fallos de ausencia de internet o de red ya son capturados por network.js
        console.error("Detalle del error en el flujo de autenticación:", error);
    }
});
