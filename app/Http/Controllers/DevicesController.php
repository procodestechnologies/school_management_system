<?php

namespace App\Http\Controllers;

use App\Models\Devices;
use Athwari\LaravelZktecoAdms\Models\ZktecoDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevicesController extends Controller
{
    public function index()
    {
        $institutions = Auth::user()->institution()->get();

        $devices = $institutions->flatMap(function ($institution) {
            return $institution->devices()->with('zktecoDevice', 'institution')->get();
        });

        if ($devices->isEmpty()) {
            abort(404, 'No devices found for your institution.');
        }

        $deviceCount = $devices->count();
        return view('layouts.devices.index', compact('devices', 'deviceCount'));
    }
    public function create()
    {
        return view('layouts.devices.create');
    }
    public function edit(Devices $device)
    {
        $zktecoDevice = ZktecoDevice::findOrFail($device->zkteco_device_id);
        return view('layouts.devices.edit', compact('device', 'zktecoDevice'));
    }
    public function update(Request $request, Devices $device)
    {
        // dd($device->zktecoDevice->id);
        $zktecoDeviceData = $request->only('name', 'ip_address', 'serial_number');
        $zktecoDevice = ZktecoDevice::findOrFail($device->zktecoDevice->id);
        $deviceData = [
            'zkteco_device_id' => $device->zktecoDevice->id,
            'serial_number' => $request->input('serial_number'),
        ];
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'serial_number' => 'required|string|max:255',
        ]);
        $zktecoDevice->update($zktecoDeviceData);
        $device->update($deviceData);

        return redirect()->route('devices.index')->with('success', 'Device updated successfully.');
    }
    public function destroy(Request $request, Devices $device)
    {
        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }
}
