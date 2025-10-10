<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use App\Classes\EacCosts;
use App\Traits\EACTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * EAC Get Curremt Rate Controller
 * {@inheritdoc}
 */
class GetCurrentRate extends Controller
{
    use EACTrait;

    /**
     * Display the current EAC rate in Json format for API.
     */
    public function index(Request $request)
    {
        $cost = new EacCosts();
        $json = '';

        try {
            $request->validate(
                [
                    'tariffCode' => [Rule::in(['01', '02', '08'])],
                    'unitsConsumed' => 'numeric|gte:0',
                    'creditUnits' => 'boolean',
                ]
            );
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        $tariffCode = request('tariffCode', '01');
        $creditUnits = request('creditUnits', 0);

        $latestDataDate = $this->getCurrentValidPeriod();

        if ($tariffCode == '01') {
            if (! $creditUnits) {
                $cost = $this->calculateEACCost01(
                    1,
                    0,
                    false,
                    date_create($latestDataDate),
                    date_create($latestDataDate)
                );
            } else {
                $cost = $this->calculateEACCost01(
                    1,
                    1,
                    false,
                    date_create($latestDataDate),
                    date_create($latestDataDate)
                );
            }
        } elseif ($tariffCode == '02') {
            $time_now = date('H') + 3;
            if ($time_now >= 9 && $time_now < 23) {
                $cost = $this->calculateEACCost02(
                    1,
                    0,
                    false,
                    date_create($latestDataDate),
                    date_create($latestDataDate)
                );
            } else {
                $cost = $this->calculateEACCost02(
                    0,
                    1,
                    false,
                    date_create($latestDataDate),
                    date_create($latestDataDate)
                );
            }
        } elseif ($tariffCode == '08') {
            if (! $creditUnits) {
                $cost = $this->calculateEACCost08(
                    1,
                    0,
                    false,
                    date_create($latestDataDate),
                    date_create($latestDataDate)
                );
            } else {
                $cost = $this->calculateEACCost08(
                    1,
                    1,
                    false,
                    date_create($latestDataDate),
                    date_create($latestDataDate)
                );
            }
        }
        $cost = $this->formatCostsAPI($cost);
        $total = $cost['total'];
        unset($cost['total']);
        $json = ['measurement' => '€/kWh',
            'total' => $total, 'breakdown' => $cost];

        return response()->json(
            $json,
            200
        );
    }
}
