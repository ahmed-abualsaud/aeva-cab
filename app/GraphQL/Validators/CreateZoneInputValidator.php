<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class CreateZoneInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'name' => [
        'required', 
        Rule::unique('zones', 'name')
          ->where('city_id', $this->arg('city_id'))
          ->where('type', $this->arg('type'))
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
    ];
  }

}