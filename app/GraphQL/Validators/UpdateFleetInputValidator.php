<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateFleetInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'phone' => ['sometimes', Rule::unique('fleets', 'phone')->ignore($this->arg('id'), 'id')],
      'email' => ['sometimes', Rule::unique('fleets', 'email')->ignore($this->arg('id'), 'id')],
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'phone.unique' => __('lang.not_available_phone'),
      'email.unique' => __('lang.not_available_email'),
    ];
  }

}