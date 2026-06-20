<?php

function estado_solicitud_label(int $stat): string
{
    return match ($stat) {
        1 => 'Pendiente',
        2 => 'Aprobado',
        3 => 'Eliminado',
        4 => 'En revisión supervisor',
        default => 'Desconocido',
    };
}

function estado_solicitud_badge(int $stat): string
{
    $class = match ($stat) {
        1 => 'badge-pendiente',
        2 => 'badge-aprobado',
        3 => 'badge-eliminado',
        4 => 'badge-supervisor',
        default => 'badge-desconocido',
    };

    return '<span class="pcr-badge ' . $class . '">' . htmlspecialchars(estado_solicitud_label($stat)) . '</span>';
}

function estado_usuario_label(int $stat): string
{
    return match ($stat) {
        1 => 'Activo',
        2 => 'Inactivo',
        3 => 'Eliminado',
        default => 'Desconocido',
    };
}

function estado_usuario_badge(int $stat): string
{
    $class = match ($stat) {
        1 => 'badge-aprobado',
        2 => 'badge-pendiente',
        3 => 'badge-eliminado',
        default => 'badge-desconocido',
    };

    return '<span class="pcr-badge ' . $class . '">' . htmlspecialchars(estado_usuario_label($stat)) . '</span>';
}

function tipo_usuario_badge(string $tipo): string
{
    $class = match (strtolower($tipo)) {
        'admin' => 'badge-admin',
        'supervisor' => 'badge-supervisor',
        'vendedor' => 'badge-vendedor',
        default => 'badge-desconocido',
    };

    return '<span class="pcr-badge ' . $class . '">' . htmlspecialchars(ucfirst($tipo)) . '</span>';
}

function tipo_persona_badge(string $tipo): string
{
    $class = match ($tipo) {
        'NATURAL' => 'badge-natural',
        'NATURAL INDEPENDIENTE' => 'badge-independiente',
        'JURIDICA' => 'badge-juridica',
        default => 'badge-desconocido',
    };

    $label = match ($tipo) {
        'NATURAL' => 'Persona natural',
        'NATURAL INDEPENDIENTE' => 'Natural independiente',
        'JURIDICA' => 'Persona jurídica',
        default => $tipo,
    };

    return '<span class="pcr-badge ' . $class . '">' . htmlspecialchars($label) . '</span>';
}
