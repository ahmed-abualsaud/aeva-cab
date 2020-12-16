<?php

namespace App\GraphQL\Directives;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Schema\Directives\ValidationDirective;

class CreateSchoolZoneValidationDirective extends ValidationDirective
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'name' => [
        'required', 
        Rule::unique('school_zones', 'name')
          ->where('city_id', $this->args['city_id'])
      ],
    ];
  }

  /**
   * @return string[]
   */
  public function messages(): array
  {
    return [
      'name.unique' => 'The chosen name is not available',
    ];
  }

}