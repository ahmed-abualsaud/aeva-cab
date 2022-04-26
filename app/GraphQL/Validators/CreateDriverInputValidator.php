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