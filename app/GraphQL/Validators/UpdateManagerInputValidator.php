<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateManagerInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'name' => ['required'],
      'partner_id' => ['required'],
      'phone' => [
        'required', 
        Rule::unique('managers', 'phone')
          ->ignore($this->arg('id'), 'id')
          ->where('partner_id', $this->arg('partner_id'))
      ],
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'phone.unique' => __('lang.not_available_phone'),
    ];
  }

}