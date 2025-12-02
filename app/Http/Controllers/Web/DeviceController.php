<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

/**
 * Web Cihaz Controller
 */
class DeviceController extends Controller
{
    /**
     * Cihaz listesi ve hassasiyet ayarları
     * GET /cihazlar
     */
    public function index(Request $request)
    {
        $query = Device::with('user:id,name');

        // Filtreler
        if ($request->has('device_name')) {
            $query->where('device_name', 'like', '%' . $request->device_name . '%');
        }

        if ($request->has('fps_setting')) {
            $query->where('fps_setting', $request->fps_setting);
        }

        if ($request->has('gyro_enabled')) {
            $query->where('gyro_enabled', $request->boolean('gyro_enabled'));
        }

        $devices = $query->latest()->paginate(20);

        // Filtre için cihaz isimleri
        $deviceNames = Device::select('device_name')
            ->distinct()
            ->orderBy('device_name')
            ->pluck('device_name');

        return view('devices.index', compact('devices', 'deviceNames'));
    }

    /**
     * Cihaz detay sayfası
     * GET /cihazlar/{id}
     */
    public function show($id)
    {
        $device = Device::with('user')->findOrFail($id);

        return view('devices.show', compact('device'));
    }
}
