<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DistanceFeedRequest extends FormRequest
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
           'admincomment' => 'required_if:flagged,true'
        ];
    }

    public function messages()
    {
        return [
            'admincomment.reuired' => 'please add comments',
        ];
    }

    protected function failedValidation(Validator $validator) {
        $response = [
           "Exceptions" => 'true',
           "Status" => 222,
           "ResultType" => 0,
           "Message" => 'validation error',
           "Data" => $validator->errors(),
       ];
        throw new HttpResponseException(response()->json($response));
    }
}