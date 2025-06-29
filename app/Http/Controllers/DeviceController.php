<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeviceRequest;
use App\Models\Device;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(): View
    {
        $devices = Auth::user()->devices;
        if ($devices->isEmpty())
            $devices = null;

        $client = new Client();

        if ($devices != null) {
            foreach($devices as $device) {
                try {
                    $response = $client->request('get', $device['method']."://".$device['endpoint']."/rest/system/resource", [
                        'auth' => [$device['username'], $device['password']],
                        'headers' => ['Content-Type' => 'application/json'],
                        'timeout' => $device['timeout'] ?? 3,
                        'connect_timeout' => $device['timeout'] ?? 3,
                        'verify' => false // Disable SSL verification for testing
                    ]);
        
                    $device['online'] = $response->getStatusCode();
                } catch (ConnectException $e) {
                    $device['online'] = null;
                    $device['error'] = 'Connection failed: ' . $e->getMessage();
                } catch (RequestException $e) {
                    $device['online'] = null;
                    $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 'N/A';
                    $device['error'] = 'Request failed (Status: ' . $statusCode . '): ' . $e->getMessage();
                } catch (\Exception $e) {
                    $device['online'] = null;
                    $device['error'] = 'Unexpected error: ' . $e->getMessage();
                }
            }
        }

        return view('dashboard.index',['devices'=> $devices]);
    }
    
    public function indexDevice($deviceId): View
    {
        $device = Device::findOrFail($deviceId);
        
        $client = new Client();

        try {
            $response = $client->request('get', $device['method']."://".$device['endpoint']."/rest/system/resource", [
                'auth' => [$device['username'], $device['password']],
                'headers' => ['Content-Type' => 'application/json'],
                'timeout' => $device['timeout'] ?? 3,
                'connect_timeout' => $device['timeout'] ?? 3,
                'verify' => false // Disable SSL verification for testing
            ]);

            $data = json_decode($response->getBody(), true);

            return view('devices.index',['resource'=> $data, 'device'=> $device, 'deviceParam' => $device['id']]);
        } catch (ConnectException $e) {
            return view('devices.index', [
                'resource'=> null, 
                'device'=> $device, 
                'conn_error' => 'Connection failed: ' . $e->getMessage(), 
                'deviceParam' => $device['id']
            ]);
        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 'N/A';
            $errorMessage = 'Request failed (Status: ' . $statusCode . '): ' . $e->getMessage();
            
            if ($statusCode == 401) {
                $errorMessage = 'Authentication failed: Invalid username or password';
            } elseif ($statusCode == 404) {
                $errorMessage = 'RouterOS API endpoint not found. Make sure API is enabled in System > API';
            }
            
            return view('devices.index', [
                'resource'=> null, 
                'device'=> $device, 
                'conn_error' => $errorMessage, 
                'deviceParam' => $device['id']
            ]);
        } catch (\Exception $e) {
            return view('devices.index', [
                'resource'=> null, 
                'device'=> $device, 
                'conn_error' => 'Unexpected error: ' . $e->getMessage(), 
                'deviceParam' => $device['id']
            ]);
        }
    }

    public function create(): View
    {
        return view('devices.create');
    }

    public function store(DeviceRequest $request)
    {
        // Validate the incoming request data
        $formData = $request->validated();
        
        if ($formData['timeout'] == null)
            $formData['timeout'] = 3;

        // Create a new Device instance and save it to the database
        Device::create([
            'name' => $formData['name'],
            'user_id' => Auth::user()->id,
            'username' => $formData['username'],
            'password' => $formData['password'],
            'endpoint' => $formData['endpoint'],
            'method' => $formData['method'],
            'timeout' => $formData['timeout'],
        ]);

        return redirect()->route('Dashboard.index')->with('success-msg', "A Device was added with success");
    }

    public function edit($id): View
    {
        $device = Device::findOrFail($id);
        return view('devices.edit', ['device' => $device]);
    }

    public function update(DeviceRequest $request,$id)
    {
        $formData = $request->validated();

        if ($formData['timeout'] == null)
            $formData['timeout'] = 3;

        $device = Device::findOrFail($id);
        $device->update($formData);

        return redirect()->route('Dashboard.index')->with('success-msg', "A Device was updated with success");
    }

    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();

        return redirect()->route('Dashboard.index')->with('success-msg', "A Device was deleted with success");
    }
}
