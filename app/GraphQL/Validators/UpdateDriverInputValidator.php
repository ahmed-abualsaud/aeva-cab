<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateDriverInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'phone' => ['sometimes', Rule::unique('drivers', 'phone')->ignore($this->arg('id'), 'id')],
      'email' => ['sometimes', Rule::unique('drivers', 'email')->ignore($this->arg('id'), 'id')],
      'national_id' => ['sometimes', Rule::unique('drivers', 'national_id')->ignore($this->arg('id'), 'id')],
      'secondary_phone' => ['sometimes', Rule::unique('drivers', 'secondary_phone')->ignore($this->arg('id'), 'id')],
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
      'national_id.unique' => __('lang.not_available_national_id'),
      'secondary_phone.unique' => __('lang.not_available_secondary_phone'),
    ];
  }

}