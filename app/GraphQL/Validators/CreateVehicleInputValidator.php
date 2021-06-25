<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class CreateVehicleInputValidator extends Validator
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'license_plate' => ['required', Rule::unique('vehicles', 'license_plate')]
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'license_plate.unique' => 'You can not add the same license plate to multiple vehicles',
    ];
  }

}