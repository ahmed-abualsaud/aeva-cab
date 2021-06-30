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
      'name.unique' => __('lang.not_available_name'),
      'name_ar.unique' => __('lang.not_available_arabic_name'),
    ];
  }

}