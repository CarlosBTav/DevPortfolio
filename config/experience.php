<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Experiencia profesional (página «Sobre mí»)
    |--------------------------------------------------------------------------
    |
    | Las fechas ('Y-m', end null = actualidad) no se muestran en la página:
    | solo sirven para ordenar cronológicamente cada fila. Una empresa con
    | varias etapas lleva varios periodos y se ordena por el primero.
    |
    | La versión anterior en timeline vertical con fechas está en la
    | etiqueta git `about-experiencia-lineal`.
    |
    | 'logo' es una ruta dentro de public/ (SVG o WebP sin fondo). Si falta o
    | no carga, la tarjeta muestra las iniciales de la empresa. Goldenmac ya
    | no existe como marca: se usa el de K-tuin Educación, que la sustituyó.
    |
    */
    'tracks' => [
        'software' => [
            'label' => 'Programación',
            'icon' => '💻',
            'autoscroll' => true, // carrusel automático en bucle en móvil
        ],
        'electronics' => [
            'label' => 'Electrónica',
            'icon' => '🔌',
            'icon_component' => 'icons.transistor', // sustituye al emoji si existe
        ],
    ],

    'jobs' => [
        [
            'company' => 'Canagrosa',
            'logo' => 'images/companies/canagrosa.svg',
            'role' => 'Desarrollador Full Stack Web y Móvil',
            'description' => 'ERP y CRM, en web y móvil, para un laboratorio y proveedor de servicios técnicos de alta especialización en el sector aeroespacial.',
            'track' => 'software',
            'periods' => [['2026-01', null]],
        ],
        [
            'company' => 'Freelancer',
            'logo' => 'images/companies/freelancer.svg',
            'logo_fill' => true,
            'role' => 'Desarrollador de Apps Web y Móvil',
            'description' => 'Apps web de gestión de empresa y portfolios (ERP, CRM, CMS) y desarrollo de apps móviles.',
            'track' => 'software',
            'periods' => [['2025-01', null]],
        ],
        [
            'company' => 'Al Rescate Asistencia Informática',
            'logo' => 'images/companies/al-rescate.webp',
            'role' => 'Programador Web Fullstack',
            'description' => 'Aplicaciones web ERP y CRM fullstack: desarrollo, despliegue y mantenimiento.',
            'track' => 'software',
            'periods' => [['2023-01', '2024-12']],
        ],
        [
            'company' => 'Goldenmac EDU',
            'logo' => 'images/companies/k-tuin-edu.webp',
            'group' => 'Grupo K-tuin',
            'role' => 'Técnico Informático en Ecosistema iOS',
            'description' => 'Gestión remota de dispositivos Apple para uso académico y servicio técnico.',
            'track' => 'software',
            'periods' => [['2019-01', '2020-12'], ['2022-01', '2022-12']],
        ],
        [
            'company' => 'NEED TECH',
            'logo' => 'images/companies/need-tech.webp',
            'role' => 'Técnico Electrónico',
            'description' => 'Análisis y diseño de circuitos, montaje automático SMD, soldadura por ola y testeo de PCBs. Resolución de problemas a nivel de componente.',
            'track' => 'electronics',
            'periods' => [['2021-01', '2022-12']],
        ],
        [
            'company' => 'ALTER TECHNOLOGY TÜV NORD',
            'logo' => 'images/companies/alter-technology.svg',
            'logo_fill' => true, // el logo ya es un recuadro de color: sin marco blanco
            'role' => 'Testing Electrónico',
            'description' => 'Testeo riguroso y pruebas de estrés de componentes electrónicos para uso aeroespacial.',
            'track' => 'electronics',
            'periods' => [['2018-01', '2018-12']],
        ],
    ],
];
