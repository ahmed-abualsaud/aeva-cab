<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class UpdateDeviceInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'id' => ['required'],
      'partner_id' => ['required'],
      'device_id' => ['required', Rule::unique('devices', 'device_id')
        ->ignore($this->arg('id'), 'id')],
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'device_id.unique' => __('lang.device_exist'),
    ];
  }

}