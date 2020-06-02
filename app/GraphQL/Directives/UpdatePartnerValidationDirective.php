<?php

namespace App\GraphQL\Directives;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Schema\Directives\ValidationDirective;

class UpdatePartnerValidationDirective extends ValidationDirective
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'phone1' => ['sometimes', Rule::unique('partners', 'phone1')->ignore($this->args['id'], 'id')],
      'email' => ['sometimes', Rule::unique('partners', 'email')->ignore($this->args['id'], 'id')],
    ];
  } 

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'phone1.unique' => 'The chosen phone is not available',
      'email.unique' => 'The chosen email is not available',
    ];
  }

}