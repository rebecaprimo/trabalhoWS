<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class StoreClienteRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "nomeCliente" => "required",
            "emailCliente" => "required",
            "telefoneCliente" => "required",
            "cpfCliente" => "required",
            "senha" => "required",
        ];



    }

     /**Get the validation messages.**/
     public function messages(): array{
        return ['required'  => 'há um campo que não foi preenchido.'];
    }

          public function failedValidation(Validator $validator) {
          throw new ValidationException(
              $validator,
              response()->json([
                  'status' => 422,
                  'message' => $validator->errors()
              ], 422)
          );
      }
}

