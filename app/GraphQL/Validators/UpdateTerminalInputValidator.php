<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateTerminalInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'partner_id' => ['required'],
      'terminal_id' => ['required', Rule::unique('terminals', 'terminal_id')
        ->ignore($this->arg('id'), 'id')],
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'terminal_id.unique' => 'This terminal already exists',
    ];
  }

}