<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class CreateDeviceInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'name' => ['required'],
      'imei' => ['required'],
      'partner_id' => ['required'],
      'device_id' => ['required', Rule::unique('devices', 'device_id')],
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