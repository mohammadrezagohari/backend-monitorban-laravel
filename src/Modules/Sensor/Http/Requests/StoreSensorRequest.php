<?php

namespace Modules\Sensor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSensorRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_id' => 'nullable|integer|exists:companies,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'server_room_id' => 'required|exists:server_rooms,id',
            'sensor_type_id' => 'required|exists:sensor_types,id',
            'unit_id' => 'nullable|exists:units,id',
            'type' => 'nullable|string|max:255',
            'title_fa' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'alert_type' => 'nullable|string|max:255',
            'physical_address' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
            'alert_interval' => 'nullable|integer|min:0',
            'alert_count' => 'nullable|integer|min:0',
            'min_daily_record' => 'nullable|integer|min:0',
            'recordable_changes' => 'nullable|string|max:255',
            'has_critical_history' => 'boolean',
            'has_warning_history' => 'boolean',
            'crisis_committee' => 'boolean',
            'icon' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'threshold' => 'nullable|array',
            'threshold.unit_id' => 'nullable|exists:units,id',
            'threshold.normal_min' => 'nullable|numeric',
            'threshold.normal_max' => 'nullable|numeric',
            'threshold.warning_min' => 'nullable|numeric',
            'threshold.warning_max' => 'nullable|numeric',
            'threshold.critical_min' => 'nullable|numeric',
            'threshold.critical_max' => 'nullable|numeric',
        ];
    }

}
