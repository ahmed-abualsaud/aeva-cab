<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdatePaymentCategoryInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'partner_id' => ['required'],
      'value' => [
        'required', 
        Rule::unique('payment_categories', 'value')
          ->ignore($this->arg('id'), 'id')
          ->where('partner_id', $this->arg('partner_id'))
      ],
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'value.unique' => __('lang.payment_category_exist'),
    ];
  }

}