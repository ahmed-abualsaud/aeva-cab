<?php

namespace App\GraphQL\Directives;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Schema\Directives\ValidationDirective;

class UpdateUserValidationDirective extends ValidationDirective
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'phone' => ['sometimes', Rule::unique('users', 'phone')->ignore($this->args['id'], 'id')],
      'email' => ['sometimes', Rule::unique('users', 'email')->ignore($this->args['id'], 'id')],
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