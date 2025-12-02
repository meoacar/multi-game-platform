<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Device\StoreUpdateDeviceRequest;
use App\Models\Device;
use Illuminate\Http\Request;

/**
 * Device Controller
 * Cihaz ve hassasiyet ayarları yönetimi
 */
class DeviceController extends Controller
{
    /**
     * Tüm cihazları listele (filtreleme ile)
     * GET /api/v1/devices
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

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    /**
     * Kullanıcının kendi cihaz bilgisini getir
     * GET /api/v1/me/device
     */
    public function show(Request $request)
    {
        $device = $request->user()->device;

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Cihaz bilgisi bulunamadı',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $device,
        ]);
    }

    /**
     * Kullanıcının cihaz bilgisini oluştur/güncelle
     * PUT /api/v1/me/device
     */
    public function update(StoreUpdateDeviceRequest $request)
    {
        $validated = $request->validated();

        $device = $request->user()->device;

        if ($device) {
            // Policy kontrolü - güncelleme
            $this->authorize('update', $device);
            $device->update($validated);
            $message = 'Cihaz bilgisi güncellendi';
        } else {
            // Policy kontrolü - oluşturma
            $this->authorize('create', \App\Models\Device::class);
            $device = $request->user()->device()->create($validated);
            $message = 'Cihaz bilgisi oluşturuldu';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $device->fresh(),
        ]);
    }
}
