<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateZoneInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'name' => [
        'sometimes', 
        Rule::unique('zones', 'name')
          ->ignore($this->arg('id'), 'id')
          ->where('city_id', $this->args['city_id'])
          ->where('type', $this->args['type'])
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