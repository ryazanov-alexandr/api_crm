<?php

namespace App\Modules\Admin\Task\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'text' => 'string|nullable',
            'title' => 'string|required',
            'description' => 'string|nullable',
            'priority_id' => 'required',
            'lead_id' => 'required',
            'responsible_id' => 'required',
            'due_date' => 'date|required',
            'time_to_complete' => 'string|required',
            'is_complete' => 'required',
        ];
    }
}
