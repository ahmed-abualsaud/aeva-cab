<?php

namespace App\GraphQL\Directives;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Schema\Directives\ValidationDirective;

class UpdateCityValidationDirective extends ValidationDirective
{
  /**
   * @return mixed[]
   */
  public function rules(): array
  {
    return [
      'name' => [
        'sometimes', 
        Rule::unique('cities', 'name')
          ->ignore($this->args['id'], 'id')
      ],
      'name_ar' => [
        'sometimes', 
        Rule::unique('cities', 'name_ar')
          ->ignore($this->args['id'], 'id')
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
      'name_ar.unique' => 'The chosen arabic name is not available',
    ];
  }

}