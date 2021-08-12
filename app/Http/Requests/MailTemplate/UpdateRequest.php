<?php

namespace App\Http\Requests\MailTemplate;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'subject'       => ['required', 'string', 'max:300'],
            'html_template' => ['required', 'string'],
            'text_template' => ['nullable', 'string'],
            'reply_to'      => ['nullable', 'email'],
            'to'            => ['nullable', 'email'],
        ];
    }
}
