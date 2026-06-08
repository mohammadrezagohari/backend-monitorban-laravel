<?php

namespace Modules\Sensor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Modules\Sensor\Http\Requests\StoreSensorRequest;
class UpdateSensorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return collect((new StoreSensorRequest())->rules())
            ->map(fn ($rule) => is_string($rule) ? str_replace('required', 'sometimes|required', $rule) : $rule)
            ->all();
    }
}
