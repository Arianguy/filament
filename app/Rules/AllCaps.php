<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AllCaps implements Rule
{
    public function passes($attribute, $value)
    {
        return strtoupper($value) === $value;
    }

    public function message()
    {
        return 'The :attribute must be in all capital letters.';
    }
}
