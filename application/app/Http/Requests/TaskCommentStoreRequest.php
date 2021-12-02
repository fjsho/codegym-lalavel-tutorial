<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskCommentStoreRequest extends FormRequest
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
        if (isset($all['comment'])) {
            $all['comment'] = preg_replace("/\r\n/", "\n", $all['comment']);
        }
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
            'comment' => 'required|string|max:1000',
        ];
    }
}
