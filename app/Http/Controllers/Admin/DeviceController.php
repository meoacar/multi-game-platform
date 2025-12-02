<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of devices
     */
    public function index(Request $request)
    {
        $query = Device::with('user.profile');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('device_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('fps_setting')) {
            $query->where('fps_setting', $request->fps_setting);
        }

        if ($request->filled('gyro_enabled')) {
            $query->where('gyro_enabled', $request->gyro_enabled === 'yes');
        }

        $devices = $query->latest()->paginate(20);

        // Get unique values for filters
        $fpsSettings = Device::distinct()->pluck('fps_setting')->filter()->sort()->values();

        return view('admin.devices.index', compact('devices', 'fpsSettings'));
    }

    /**
     * Show the form for editing the specified device
     */
    public function edit($id)
    {
        $device = Device::with('user.profile')->findOrFail($id);
        
        return view('admin.devices.edit', compact('device'));
    }

    /**
     * Update the specified device
     */
    public function update(Request $request, $id)
    {
        $device = Device::findOrFail($id);

        $validated = $request->validate([
            'device_name' => 'nullable|string|max:255',
            'graphics_settings' => 'nullable|string|max:255',
            'fps_setting' => 'nullable|string|max:255',
            'gyro_enabled' => 'boolean',
            'sensitivity_settings' => 'nullable|json',
            'notes' => 'nullable|string',
        ]);

        $device->update($validated);

        return redirect()->route('admin.devices.index')
            ->with('success', 'Cihaz kaydı başarıyla güncellendi.');
    }

    /**
     * Remove spam/troll device entry
     */
    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();

        return redirect()->route('admin.devices.index')
            ->with('success', 'Cihaz kaydı silindi.');
    }
}
