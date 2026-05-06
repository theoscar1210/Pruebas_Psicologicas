<?php

namespace App\Exports\Concerns;

trait SanitizesForExcel
{
    // Previene Formula Injection (CSV Injection) en celdas de Excel.
    // Cualquier valor que empiece con =, +, -, @, tabulador o retorno de carro
    // puede ser interpretado como fórmula por Excel/LibreOffice.
    private function sanitize(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}
