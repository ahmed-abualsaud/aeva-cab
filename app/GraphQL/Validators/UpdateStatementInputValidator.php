<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateStatementInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'type' => ['sometimes', Rule::unique('statements', 'type')->ignore($this->arg('id'), 'id')],
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'type.unique' => __('lang.NotAvailableType'),
    ];
  }

}