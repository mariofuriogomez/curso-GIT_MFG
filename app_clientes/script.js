const apiUrl = "http://localhost/clientes.php"; // Cambia a la URL correcta de tu backend

// Obtener referencias a elementos HTML
const clienteForm = document.getElementById("clienteForm");
const clienteIdInput = document.getElementById("clienteId");
const nombreInput = document.getElementById("nombre");
const emailInput = document.getElementById("email");
const telefonoInput = document.getElementById("telefono");
const clientesContainer = document.getElementById("clientesContainer");

// Cargar clientes al iniciar
document.addEventListener("DOMContentLoaded", cargarClientes);

// Manejar el envío del formulario
clienteForm.addEventListener("submit", (e) => {
  e.preventDefault();
  const id = clienteIdInput.value;
  const cliente = {
    nombre: nombreInput.value,
    email: emailInput.value,
    telefono: telefonoInput.value,
  };

  if (id) {
    actualizarCliente(id, cliente);
  } else {
    crearCliente(cliente);
  }
});

// Cargar clientes desde el backend
function cargarClientes() {
  fetch(apiUrl)
    .then((response) => response.json())
    .then((clientes) => mostrarClientes(clientes))
    .catch((error) => console.error("Error al cargar clientes:", error));
}

// Mostrar clientes como tarjetas
function mostrarClientes(clientes) {
  clientesContainer.innerHTML = "";
  clientes.forEach((cliente) => {
    const card = document.createElement("div");
    card.className = "cliente-card";
    card.innerHTML = `
      <h3>${cliente.nombre}</h3>
      <p><strong>Email:</strong> ${cliente.email}</p>
      <p><strong>Teléfono:</strong> ${cliente.telefono}</p>
      <button onclick="editarCliente(${cliente.id})">Editar</button>
      <button class="delete" onclick="eliminarCliente(${cliente.id})">Eliminar</button>
    `;
    clientesContainer.appendChild(card);
  });
}

// Crear cliente
function crearCliente(cliente) {
  fetch(apiUrl, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(cliente),
  })
    .then((response) => response.json())
    .then(() => {
      clienteForm.reset();
      cargarClientes();
    })
    .catch((error) => console.error("Error al crear cliente:", error));
}

// Actualizar cliente
function actualizarCliente(id, cliente) {
  fetch(`${apiUrl}?id=${id}`, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(cliente),
  })
    .then((response) => response.json())
    .then(() => {
      clienteForm.reset();
      cargarClientes();
    })
    .catch((error) => console.error("Error al actualizar cliente:", error));
}

// Eliminar cliente
function eliminarCliente(id) {
  fetch(`${apiUrl}?id=${id}`, { method: "DELETE" })
    .then((response) => response.json())
    .then(() => cargarClientes())
    .catch((error) => console.error("Error al eliminar cliente:", error));
}

// Cargar cliente en el formulario para edición
function editarCliente(id) {
  fetch(`${apiUrl}?id=${id}`)
    .then((response) => response.json())
    .then((cliente) => {
      clienteIdInput.value = cliente.id;
      nombreInput.value = cliente.nombre;
      emailInput.value = cliente.email;
      telefonoInput.value = cliente.telefono;
    })
    .catch((error) => console.error("Error al cargar cliente:", error));
}
