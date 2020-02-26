<?php

namespace App\GraphQL\Directives;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Schema\Directives\ValidationDirective;

class UpdateDriverValidationDirective extends ValidationDirective
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'phone' => ['sometimes', Rule::unique('drivers', 'phone')->ignore($this->args['id'], 'id')],
      'email' => ['sometimes', Rule::unique('drivers', 'email')->ignore($this->args['id'], 'id')],
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