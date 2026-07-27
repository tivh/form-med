<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Cpf implements Rule
{
    public function passes($attribute, $value): bool
    {
        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) !== 11) {
            return false;
        }

        // Reject sequences like 000.000.000-00, 111.111.111-11, etc.
        if (preg_match('/^(\d)\1{10}$/', $digits)) {
            return false;
        }

        // Validate first check digit
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $digits[$i] * (10 - $i);
        }
        $remainder = ($sum * 10) % 11;
        if ($remainder === 10) {
            $remainder = 0;
        }
        if ($remainder !== (int) $digits[9]) {
            return false;
        }

        // Validate second check digit
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $digits[$i] * (11 - $i);
        }
        $remainder = ($sum * 10) % 11;
        if ($remainder === 10) {
            $remainder = 0;
        }
        if ($remainder !== (int) $digits[10]) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return 'O :attribute informado não é válido.';
    }
}
