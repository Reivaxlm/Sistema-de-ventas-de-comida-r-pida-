
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Pagina 1 </title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/Estilosphp.css">
    <style>
        .contenedor-imagenes {
            display: flex;
            flex-wrap: wrap;
        }
        .imagen {
            margin: 10px;
            text-align: center;
        }
        .imagen img {
            width: 200px;
            height: 200px;
        }
        .contenedor-imagenes-seleccionadas {
            display: flex;
        }
        .imagen-seleccionada {
            margin: 10px;
            text-align: center;
        }
        .imagen-seleccionada img {
            width: 100px;
            height: 100px;
        }
    </style>
</head>
<body>
    <div class="contenedor-imagenes">
        <div class="imagen" onclick="manejarClic('image1', 400)">
            <img src="image1.jpg" alt="image1">
            <p>Super Beacon</p>
        </div>
        <div class="imagen" onclick="manejarClic('image2', 350)">
            <img src="image2.jpg" alt="image2">
            <p>Frisburguer</p>
        </div>
        <div class="imagen" onclick="manejarClic('image3', 280)">
            <img src="image3.jpg" alt="image3">
            <p>Carnivora</p>
        </div>
        <div class="imagen" onclick="manejarClic('image4', 600)">
            <img src="image4.jpg" alt="image4">
            <p>Doble carne</p>
        </div>
        <div class="imagen" onclick="manejarClic('image5', 100)">
            <img src="image5.jpg" alt="image5">
            <p>La simplecita</p>
        </div>
    </div>

    <h2>Hamburguesas Seleccionadas</h2>

    <div class="contenedor-imagenes-seleccionadas"></div>

    <p>Costo total: <span id="costo-total">0</span></p>

    <script>
        let imagenesSeleccionadas = [];
        let costoTotal = 0;

        const contenedorImagenesSeleccionadas = document.querySelector('.contenedor-imagenes-seleccionadas');
        const elementoCostoTotal = document.querySelector('#costo-total');

        function manejarClic(nombreImagen, costo) {
            if (!imagenesSeleccionadas.includes(nombreImagen)) {
                imagenesSeleccionadas.push(nombreImagen);
                costoTotal += costo;

                const elementoImagen = document.createElement('div');
                elementoImagen.classList.add('imagen-seleccionada');
                elementoImagen.innerHTML = `
                    <img src="${nombreImagen.toLowerCase().replace(/ /g, '')}.jpg" alt="${nombreImagen}">
                    <p>${nombreImagen}</p>
                `;
                contenedorImagenesSeleccionadas.appendChild(elementoImagen);

                elementoCostoTotal.textContent = costoTotal;
            }
        }
    </script>
</body>
</html>
