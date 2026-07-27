<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Cnpj implements Rule
{
    public function passes($attribute, $value): bool
    {
        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) !== 14) {
            return false;
        }

        // Reject all-same-digit sequences
        if (preg_match('/^(\d)\1{13}$/', $digits)) {
            return false;
        }

        // Validate first check digit
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $digits[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $check = $remainder < 2 ? 0 : 11 - $remainder;
        if ($check !== (int) $digits[12]) {
            return false;
        }

        // Validate second check digit
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $digits[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $check = $remainder < 2 ? 0 : 11 - $remainder;
        if ($check !== (int) $digits[13]) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return 'O :attribute informado não é válido.';
    }
}
