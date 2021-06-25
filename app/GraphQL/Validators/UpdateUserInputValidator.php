<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateUserInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'phone' => ['sometimes', Rule::unique('users', 'phone')->ignore($this->arg('id'), 'id')],
      'email' => ['sometimes', Rule::unique('users', 'email')->ignore($this->arg('id'), 'id')],
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'phone.unique' => __('lang.NotAvailablePhone'),
      'email.unique' => __('lang.NotAvailableEmail'),
    ];
  }

}