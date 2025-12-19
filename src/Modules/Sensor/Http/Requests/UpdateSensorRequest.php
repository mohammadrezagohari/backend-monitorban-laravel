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
        return (new StoreSensorRequest())->rules();
    }
}
