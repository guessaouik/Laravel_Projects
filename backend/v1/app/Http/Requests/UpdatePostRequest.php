<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $method = $this->method();
        if ($method == 'PUT') {
            return [
                'title' => ['sometimes', 'required'], // title is optional
                'content' => ['required']
            ];
        }
        else if ($method == 'PATCH') {
            return [
                'title' => ['sometimes', 'required'], // title is optional
                'content' => ['sometimes', 'required']
            ];
        }
    }
}
