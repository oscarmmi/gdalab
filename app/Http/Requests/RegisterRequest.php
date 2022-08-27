<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
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
            'data.dni' => 'required| max:255 |min: 3|unique:customers,dni,'.$this->input('dni'),
            'data.commune' => 'required|max:255',
            'data.region' => 'required|max:255',
            'data.name' => 'required|max:255',
            'data.last_name' => 'required|max:255',
            'data.address' => 'required|max:255',
            //'data.status' => 'required|max:5',
            'data.password' => 'required|max:255|min: 6',
            'data.email' => 'required|email|unique:customers,email,'.$this->input('email'),
        ];
    }

    public function messages(){
        return [
            'data.dni.required' =>  '- Se debe ingresar un DNI', 
            'data.dni.max' =>  '- El DNI debe tener como maximo 255 caracteres', 
            'data.dni.min' =>  '- El DNI debe tener como minimo 3 caracteres', 
            'data.dni.unique' =>  '- El DNI debe ser unico y el actual ya se encuentra registrado' ,
            'data.commune.required' =>  '- Se debe ingresar el nombre de un municipio', 
            'data.commune.max' =>  '- El municipio  debe tener como maximo 255 caracteres', 
            'data.region.required' =>  '- Se debe ingresar el nombre de un departamento', 
            'data.region.max' =>  '- El departamento  debe tener como maximo 255 caracteres', 
            'data.name.required' =>  '- Se debe ingresar un nombre', 
            'data.name.max' =>  '- El nombre debe tener como maximo 255 caracteres', 
            'data.address.required' =>  '- Se debe ingresar una direccion', 
            'data.address.max' =>  '- La direccion debe tener como maximo 255 caracteres', 
            //'data.status.required' =>  '- Se debe ingresar un status', 
            //'data.status.max' =>  '- La direccion debe tener como maximo 255 caracteres', 
            'data.password.required' =>  '- Se debe ingresar un password', 
            'data.password.max' =>  '- El password debe tener como maximo 255 caracteres', 
            'data.password.min' =>  '- El password debe tener como maximo 6 caracteres', 
            'data.email.required' =>  '- Se debe ingresar un email', 
            'data.email.email' =>  '- El email ingresado no tiene un formato valido', 
            'data.email.max' =>  '- El email debe tener como maximo 255 caracteres', 
            'data.email.unique' =>  '- El email debe ser unico y el actual ya se encuentra registrado' 
        ];
    }

    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
