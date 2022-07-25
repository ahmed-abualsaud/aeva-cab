<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class CreateDriverInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'phone' => ['required', Rule::unique('drivers', 'phone')],
      'email' => ['sometimes', 'email', Rule::unique('drivers', 'email')],
      'national_id' => ['sometimes', Rule::unique('drivers', 'national_id')],
      'secondary_phone' => ['sometimes', Rule::unique('drivers', 'secondary_phone')],
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