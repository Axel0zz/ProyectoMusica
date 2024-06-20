<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="sweetalert2.min.css">
    <style>
        #productos {
            position: absolute;
            left: 20px;
            top: 150px;
            width: 900px;
            height: 600px;
            background-color: grey;
        }

        #carrito {
            position: absolute;
            left: 1200px;
            top: 250px;
            width: 100px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="principal.php">Principal</a>
        <a href="perfil.html">
            <?php
            session_start();
            include 'database.php';

            // Obtener la foto de perfil del usuario
            if (isset($_SESSION['correo'])) {
                $correo = $_SESSION['correo'];
                $sql = "SELECT profile_pic FROM usuarios WHERE correo = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $correo);
                $stmt->execute();
                $stmt->bind_result($profile_pic);
                $stmt->fetch();
                $stmt->close();

                // Mostrar la imagen de perfil si está definida
                if ($profile_pic) {
                    echo '<img src="uploads/' . $profile_pic . '" class="rounded-circle" width="40" height="40" alt="Foto de perfil">';
                } else {
                    echo 'Foto de perfil no disponible';
                }
            } else {
                echo 'No hay sesión iniciada';
            }
            ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="perfil.html">Perfil</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<br>

<table>
    <tr>
        <th>
            <h3>Agregar Producto:<button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">AGREGAR</button></h3>
        </th>
    </tr>
</table>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar Producto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="registrarP" action="agregar_producto.php" method="post" enctype="multipart/form-data">
                    <label for="productoPic">Foto del Producto:</label>
                    <input type="file" name="productoPic" accept="image/*"><br>
                    <label for="producto">Producto:</label>
                    <input type="text" id="producto" name="producto" required><br>
                    <label for="categoria">Categoria:</label>
                    <input type="text" id="categoria" name="categoria" required><br>
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" name="precio" required><br>
                    <button type="submit" class="btn btn-info">Agregar Producto</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="productos">
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>IMAGEN</th>
            <th>PRODUCTO</th>
            <th>CATEGORIA</th>
            <th>PRECIO</th>
            <th>AGREGAR</th>
        </tr>
        <?php
        include 'database.php';

        // Consultar los productos de la base de datos
        $sql = "SELECT id, imagen, nombre, categoria, precio FROM productos";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td><img src='" . $row['imagen'] . "' width='50'></td>";
                echo "<td>" . $row['nombre'] . "</td>";
                echo "<td>" . $row['categoria'] . "</td>";
                echo "<td>" . $row['precio'] . "</td>";
                echo "<td><button class='btn btn-primary agregarCarrito' data-id='" . $row['id'] . "'>Agregar</button></td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
</div>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal1" id="carrito">
  Ver Carrito
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Productos en el carrito</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="carritoTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>IMAGEN</th>
                            <th>PRODUCTO</th>
                            <th>CATEGORIA</th>
                            <th>PRECIO</th>
                            <th>CANTIDAD</th>
                            <th>ELIMINAR</th>
                        </tr>
                    </thead>
                    <tbody id="carritoBody">
                        <!-- Aquí se agregarán dinámicamente los productos del carrito -->
                    </tbody>
                </table>
                <div id="total">
                    <b>TOTAL:</b> <span id="totalCarrito"></span>
                </div>
                <div id="exito">
                    <!-- Aquí se mostrará el mensaje de éxito -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="sweetalert2.min.js"></script>
<script src="bootstrap.min.js"></script>
<script>
    // Función para obtener el correo almacenado en localStorage
    function obtenerCorreo() {
        return localStorage.getItem('correo');
    }

    // Función para obtener productos del carrito desde la sesión
    function obtenerProductosCarrito() {
        return JSON.parse(sessionStorage.getItem('carrito')) || [];
    }

    // Función para guardar productos del carrito en la sesión
    function guardarProductosCarrito(carrito) {
        sessionStorage.setItem('carrito', JSON.stringify(carrito));
    }

    // Función para obtener la suma total del carrito
    function calcularTotalCarrito(carrito) {
        return carrito.reduce((total, producto) => total + (producto.precio * producto.cantidad), 0);
    }

    // Función para actualizar la tabla de productos en el carrito
    function actualizarCarritoHTML(carrito) {
        const tbody = document.getElementById('carritoBody');
        const totalCarrito = document.getElementById('totalCarrito');
        let html = '';
        let total = 0;

        carrito.forEach(producto => {
            html += `
                <tr>
                    <td>${producto.id}</td>
                    <td><img src="${producto.imagen}" width="50"></td>
                    <td>${producto.nombre}</td>
                    <td>${producto.categoria}</td>
                    <td>${producto.precio}</td>
                    <td>${producto.cantidad}</td>
                    <td><button class="btn btn-danger eliminarProducto" data-id="${producto.id}">Eliminar</button></td>
                </tr>
            `;
            total += producto.precio * producto.cantidad;
        });

        tbody.innerHTML = html;
        totalCarrito.textContent = total.toFixed(2);
    }

    // Función para manejar el evento de agregar producto al carrito
    function agregarAlCarrito(producto) {
        let carrito = obtenerProductosCarrito();

        // Verificar si el producto ya está en el carrito
        const index = carrito.findIndex(item => item.id === producto.id);

        if (index !== -1) {
            // Si el producto ya existe, incrementar la cantidad
            carrito[index].cantidad++;
        } else {
            // Si el producto no existe, agregarlo al carrito
            carrito.push({
                id: producto.id,
                nombre: producto.nombre,
                categoria: producto.categoria,
                precio: producto.precio,
                cantidad: 1,
                imagen: producto.imagen
            });
        }

        // Guardar el carrito actualizado en la sesión
        guardarProductosCarrito(carrito);

        // Actualizar la tabla del carrito en el modal
        actualizarCarritoHTML(carrito);

        // Mostrar mensaje de éxito
        Swal.fire({
            icon: 'success',
            title: 'Producto agregado al carrito',
            showConfirmButton: false,
            timer: 1500
        });
    }

    // Función para eliminar un producto del carrito
    function eliminarDelCarrito(id) {
        let carrito = obtenerProductosCarrito();

        // Filtrar el producto a eliminar
        carrito = carrito.filter(producto => producto.id !== id);

        // Guardar el carrito actualizado en la sesión
        guardarProductosCarrito(carrito);

        // Actualizar la tabla del carrito en el modal
        actualizarCarritoHTML(carrito);
    }

    // Evento al cargar el documento
    document.addEventListener('DOMContentLoaded', function() {
        // Evento para agregar producto al carrito
        document.querySelectorAll('.agregarCarrito').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const producto = {
                    id: id,
                    nombre: this.parentNode.parentNode.cells[2].textContent,
                    categoria: this.parentNode.parentNode.cells[3].textContent,
                    precio: parseFloat(this.parentNode.parentNode.cells[4].textContent),
                    imagen: this.parentNode.parentNode.cells[1].querySelector('img').getAttribute('src')
                };
                agregarAlCarrito(producto);
            });
        });

        // Evento para eliminar producto del carrito
        document.querySelectorAll('.eliminarProducto').forEach(button => {
            button.addEventListener('click', function() {
                const id = parseInt(this.getAttribute('data-id'));
                eliminarDelCarrito(id);
            });
        });

        // Cargar productos del carrito al abrir el modal
        $('#exampleModal1').on('shown.bs.modal', function() {
            const carrito = obtenerProductosCarrito();
            actualizarCarritoHTML(carrito);
        });
    });
</script>
</body>
</html>
