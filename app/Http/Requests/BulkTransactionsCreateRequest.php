<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class BulkTransactionsCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'admin_id' => ['integer',Rule::exists('admins','id')],
            'driver_id' => ['required','array'],
            'driver_id.*' => ['required','distinct','integer',Rule::exists('drivers','id')],
            'amount' => ['required','numeric','gt:0'],
            'type' => ['required',Rule::in(['Wallet Deposit','Wallet Withdraw','Cashout'])],
            'admin_type' => ['required',Rule::in(['App\\Admin','App\\Partner','App\\Manager'])],
            'notes' => ['nullable','string','max:191'],
        ];
    }

    protected function passedValidation()
    {
        $this->merge([
            'admin_id' => @auth('admin')->id() ?? $this->admin_id ?? dashboard_error('Admin id required')
        ]);
    }

    /**
     * @param Validator $validator
     * @return mixed|void
     */
    protected function failedValidation(Validator $validator)
    {
        return dashboard_error($validator->errors()->first());
    }
}

