<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRole('admin', 'api');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'string', 'email', 'max:255'],
            'role_id'                => ['required', 'exists:roles,id'],
            'warehouse_id'           => ['nullable', 'exists:warehouses,id'],
            'default_dashboard_uri'  => ['nullable', 'string', 'max:255'],
        ];
    }
}
