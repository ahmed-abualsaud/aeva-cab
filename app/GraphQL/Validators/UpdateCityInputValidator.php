<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateCityInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'name' => [
        'sometimes', 
        Rule::unique('cities', 'name')
          ->ignore($this->arg('id'), 'id')
      ],
      'name_ar' => [
        'sometimes', 
        Rule::unique('cities', 'name_ar')
          ->ignore($this->arg('id'), 'id')
      ],
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'name.unique' => 'The chosen name is not available',
      'name_ar.unique' => 'The chosen arabic name is not available',
    ];
  }

}