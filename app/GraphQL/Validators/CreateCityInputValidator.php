<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class CreateCityInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'name' => [
        'required', 
        Rule::unique('cities', 'name')
          ->where('type', $this->arg('type'))
      ],
      'name_ar' => [
        'required', 
        Rule::unique('cities', 'name_ar')
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
      'name.unique' => __('lang.not_available_name'),
      'name_ar.unique' => __('lang.not_available_arabic_name'),
    ];
  }

}