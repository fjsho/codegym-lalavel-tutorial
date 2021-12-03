<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskPictureStoreRequest extends FormRequest
{
    /**
     * @overRide
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $all = $this->all();

        return $all;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|file|max:10240|mimes:jpg,png,gif',
        ];
    }
}
