const verFormulario = document.getElementById('formulario-lr')
const botonAparecerFormulario = document.getElementById('aparecer-formulario')

function visivilidadFormulario(){
 
    verFormulario.style.display = 'none' 
    botonAparecerFormulario.addEventListener('click', adFormulario)
    verFormulario.style.display = 'block' 
}

function adFormulario(){
    if (verFormulario.style.display === 'none'){
        verFormulario.style.display = 'block'
    } else{
         verFormulario.style.display = 'none'
    }   
}

window.addEventListener('load', visivilidadFormulario)