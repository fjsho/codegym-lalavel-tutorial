<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    /**
     * @overRide
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $all =  $this->all();
        if (isset($all['task_detail'])) {
            $all['task_detail'] = preg_replace("/\r\n/", "\n", $all['task_detail']);
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
            'task_kind_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'task_detail' => 'string|max:1000|nullable',
            'task_status_id' => 'required|integer',
            'assigner_id' => 'nullable|integer',
            'task_category_id' => 'nullable|integer',
            'task_resolution_id' => 'nullable|integer',
            'due_date' => 'nullable|date',
        ];
    }
}
