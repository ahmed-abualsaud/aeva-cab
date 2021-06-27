<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class CreateTerminalInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'partner_id' => ['required'],
      'terminal_id' => ['required', Rule::unique('terminals', 'terminal_id')],
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'terminal_id.unique' => __('lang.terminal_exist'),
    ];
  }

}