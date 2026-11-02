<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Attribute;

class isAttribute implements ValidationRule
{
    private $attributes;
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function __construct()
    {
        $this->attributes = Attribute::all();
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {   

        $dbAttribute = $this->attributes->firstWhere('slug', $attribute);

        if (!$dbAttribute) {
            $fail("Attribute $attribute does not exist");
            return;
        }

        foreach ($value as $item) {
            if (!array_key_exists($item, $dbAttribute->options)) {
                $fail("Option $item does not exist");
            }
        }
    }
}
