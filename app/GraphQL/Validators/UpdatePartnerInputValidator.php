<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdatePartnerInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'phone1' => ['sometimes', Rule::unique('partners', 'phone1')->ignore($this->arg('id'), 'id')],
      'email' => ['sometimes', Rule::unique('partners', 'email')->ignore($this->arg('id'), 'id')],
    ];
  } 

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'phone1.unique' => __('lang.not_available_phone'),
      'email.unique' => __('lang.not_available_email'),
    ];
  }

}