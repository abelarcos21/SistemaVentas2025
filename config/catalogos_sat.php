<?php

return [

    'formas_pago' => [
        '01' => 'Efectivo',
        '03' => 'Transferencia electrónica',
        '28' => 'Tarjeta de débito',
        '29' => 'Tarjeta de crédito',
    ],

    'metodos_pago' => [
        'PUE' => 'Pago en una sola exhibición',
        'PPD' => 'Pago en parcialidades o diferido',
    ],

    'usos_cfdi' => [
        'G01' => 'Adquisición de mercancías',
        'G03' => 'Gastos en general',
        'P01' => 'Por definir',
    ],

    'regimenes_fiscales' => [
        '601' => 'General de Ley Personas Morales',
        '605' => 'Sueldos y Salarios e Ingresos Asimilados',
        '612' => 'Personas Físicas con Actividades Empresariales y Profesionales',
    ],

    'objetos_impuesto' => [
        '01' => 'No objeto de impuesto',
        '02' => 'Sí objeto de impuesto',
        '03' => 'Sí objeto de impuesto y no obligado al desglose',
    ],

];
