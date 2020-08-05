const updateInPageMenu = (event) => {
  // Deselecciona tabs
  window.document.body.querySelectorAll('.link-dynamic-menu').forEach((link) => link.classList.remove('active-item'))
  if (event) {
    // Selecciona tab activa
    event.target.classList.add('active-item')
  } else {
    const hash = window.document.location.href.split("#").pop();
    window.document.body.querySelectorAll('.link-dynamic-menu').forEach((link) => {
      if (link.href.includes(hash)) {
        link.classList.add('active-item')
      }
    })
  }
}


const updateTab = () => {
  // Oculto secciones
  window.document.body.querySelectorAll('.tab-section').forEach((section) => section.classList.add('is-hidden'))
  // Muestro sección activa
  if (event) {
    const hash = event.target.href.split("#").pop();
    const activeElement = document.getElementById(hash);
    activeElement.classList.remove('is-hidden');
  } else {
    const hash = window.document.location.href.split("#").pop();
    const activeElement = document.getElementById(hash);
    activeElement.classList.remove('is-hidden');
  }
}


// PRODUCTS AND SERVICES PAGES TAB UPDATE
const updateWindow = (event) => {
  updateInPageMenu(event)
  updateTab()
  $('#new-btn').click();
}

