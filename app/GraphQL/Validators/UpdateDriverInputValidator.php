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
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'phone.unique' => 'The chosen phone is not available',
      'email.unique' => 'The chosen email is not available',
    ];
  }

}