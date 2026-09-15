<?php

namespace App\Services\Cms;

use App\Models\Indicator;
use App\Models\IndicatorValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RecordIndicatorValue
{
    public function handle(Indicator $indicator, array $input, ?int $userId = null): IndicatorValue
    {
        $data = Validator::make($input, [
            'value' => ['required', 'numeric', 'min:0', 'max:999999999999999', 'decimal:0,4'],
            'source' => ['required', 'string', 'max:2000'], 'period_label' => ['nullable', 'string', 'max:255'],
            'scope' => ['nullable', 'string', 'max:2000'], 'followup_months' => ['nullable', 'integer', 'min:0', 'max:1200'],
            'method' => ['nullable', 'string', 'max:4000'], 'limitations' => ['nullable', 'string', 'max:4000'], 'change_note' => ['nullable', 'string', 'max:2000'],
        ])->validate();
        if ($indicator->unit === 'percent' && $data['value'] > 100) {
            throw \Illuminate\Validation\ValidationException::withMessages(['value' => 'Un pourcentage doit être compris entre 0 et 100.']);
        }

        return DB::transaction(function () use ($indicator, $data, $userId) {
            Indicator::whereKey($indicator->id)->lockForUpdate()->firstOrFail();

            return $indicator->values()->create($data + ['recorded_by' => $userId]);
        });
    }
}
