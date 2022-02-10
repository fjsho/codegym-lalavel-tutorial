<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PictureValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $size_mb = $value->getSize() / 1024 / 1024;
        
        return $size_mb < 10.05 && in_array($value->getMimeType(),['image/jpg', 'image/jpeg', 'image/png', 'image/gif']);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Picture should be JPG, PNG, or GIF images no larger than 10MB.');
    }
}
