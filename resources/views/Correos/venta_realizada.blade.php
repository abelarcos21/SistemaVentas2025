
<!DOCTYPE html>
<html>
<head>
    <title>Boleta de Venta</title>
</head>
<body>
    <h1>Boleta de Venta # {{ $venta->folio }}</h1>

    <p>Estimado cliente, {{ $venta->cliente->nombre }}</p>

    <p>Adjunto encontrará su boleta de venta correspondiente a la compra realizada.</p>

    <p>Gracias por su compra.</p>

    <p>Saludos cordiales,<br>
    El equipo de ventas</p>
</body>
</html>
