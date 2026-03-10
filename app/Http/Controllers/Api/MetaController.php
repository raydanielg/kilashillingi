<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class MetaController extends Controller
{
    public function currencies(): JsonResponse
    {
        $available = Setting::get('available_currencies', 'KSh,USD,TSH');
        $default = Setting::get('currency', 'KSh');

        $list = array_values(array_filter(array_map('trim', explode(',', (string) $available))));
        if (! in_array($default, $list, true)) {
            array_unshift($list, $default);
        }

        return response()->json([
            'default' => $default,
            'available' => array_values(array_unique($list)),
        ]);
    }
}
