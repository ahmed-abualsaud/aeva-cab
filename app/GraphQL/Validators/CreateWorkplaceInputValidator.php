<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class CreateWorkplaceInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'name' => [
        'required', 
        Rule::unique('workplaces', 'name')
          ->where('zone_id', $this->arg('zone_id'))
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
    ];
  }

}