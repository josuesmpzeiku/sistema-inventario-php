const btnFiltroA = document.getElementById('bt-filtro-1')
const btnFiltroB = document.getElementById('bt-filtro-2')
const btnFiltroC = document.getElementById('bt-filtro-3')
const btnFiltroD = document.getElementById('bt-filtro-4')
const btnFiltroE = document.getElementById('bt-filtro-5')
const btnDivA = document.getElementById('bt-div-1')
const verSeccionA = document.getElementById('filtro1')
const verSeccionB = document.getElementById('filtro2')
const verSeccionC = document.getElementById('filtro3')
const verSeccionD = document.getElementById('filtro4')
const verSeccionE = document.getElementById('filtro5')
const verDivA = document.getElementById('div1')
const verDivB = document.getElementById('div2')

function visivilidadSection(){
    verSeccionA.style.display = 'none'
    verSeccionB.style.display = 'none'
    verSeccionC.style.display = 'none'
    verSeccionD.style.display = 'none'
    verSeccionE.style.display = 'none'
    btnFiltroA.addEventListener('click', seccionA)
    btnFiltroB.addEventListener('click', seccionB) 
    btnFiltroC.addEventListener('click', seccionC) 
    btnFiltroD.addEventListener('click', seccionD) 
    btnFiltroE.addEventListener('click', seccionE)
}
function visivilidadDiv(){
    verDivA.style.display = 'none'
    verDivB.style.display = 'block'  
    btnDivA.addEventListener('click', divA)
   
}

function seccionA() { 
    verSeccionA.style.display = 'block'    
    verSeccionB.style.display = 'none'
    verSeccionC.style.display = 'none'
    verSeccionD.style.display = 'none' 
    verSeccionE.style.display = 'none' 
}
function seccionB() {
    verSeccionA.style.display = 'none'
    verSeccionB.style.display = 'block'
    verSeccionC.style.display = 'none'
    verSeccionD.style.display = 'none'
    verSeccionE.style.display = 'none'
}
function seccionC() {
    verSeccionA.style.display = 'none'
    verSeccionB.style.display = 'none'
    verSeccionC.style.display = 'block'
    verSeccionD.style.display = 'none' 
    verSeccionE.style.display = 'none'
}
function seccionD() { 
    verSeccionA.style.display = 'none'
    verSeccionB.style.display = 'none'
    verSeccionC.style.display = 'none'
    verSeccionD.style.display = 'block'
    verSeccionE.style.display = 'none'
}
function seccionE() { 
    verSeccionA.style.display = 'none'
    verSeccionB.style.display = 'none'
    verSeccionC.style.display = 'none'
    verSeccionD.style.display = 'none'
    verSeccionE.style.display = 'block'
}
function divA() { 
    if (verDivA.style.display === 'none'){
        verDivA.style.display = 'block'
        verDivB.style.display= 'none'
    } else{
         verDivA.style.display= 'none'
         verDivB.style.display= 'block'
    }  
}


function limpiar() {
    location.reload()
}


window.addEventListener('load', visivilidadSection)
window.addEventListener('load', visivilidadDiv)

