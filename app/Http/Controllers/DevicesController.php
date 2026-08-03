<?php

namespace App\Http\Controllers;

use App\Models\Devices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DevicesController extends Controller
{
    public function index()
    {
        $institutions = Auth::user()->institution()->get();

        $devices = $institutions->flatMap(function ($institution) {
            return $institution->devices()->with('zktecoDevice', 'institution')->get();
        });

        $deviceCount = $devices->count();
        return view('layouts.devices.index', compact('devices', 'deviceCount'));
    }
    public function create()
    {
        return view('layouts.devices.create');
    }
    public function edit(Devices $device)
    {
        // The matching ZktecoDevice may not exist yet - the physical unit
        // could still be offline and never have connected to the ADMS
        // server, so this must not 404 on a missing match.
        $zktecoDevice = $device->zktecoDevice;

        return view('layouts.devices.edit', compact('device', 'zktecoDevice'));
    }
    public function update(Request $request, Devices $device)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'ip_address' => 'nullable|ip',
            'serial_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('devices', 'serial_number')->ignore($device->id),
            ],
        ]);

        // Re-resolve the ZktecoDevice for the (possibly changed) serial
        // number rather than renaming whichever device happened to be
        // linked before - editing the serial number re-points this
        // institution's device to a different physical unit, it never
        // hijacks another device's identity.
        $device->serial_number = $validated['serial_number'];
        $device->linkZktecoDevice($validated['name'] ?? null, $validated['ip_address'] ?? null);

        return redirect()->route('devices.index')->with('success', 'Device updated successfully.');
    }
    public function destroy(Request $request, Devices $device)
    {
        $device = Devices::findOrFail($device->id);
        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }
}
