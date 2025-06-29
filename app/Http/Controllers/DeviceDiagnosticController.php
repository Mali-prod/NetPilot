<?php

namespace App\Http\Controllers;

use App\Models\Device;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceDiagnosticController extends Controller
{
    public function testConnection(Request $request): View
    {
        $endpoint = $request->input('endpoint');
        $username = $request->input('username');
        $password = $request->input('password');
        $method = $request->input('method', 'http');
        $timeout = $request->input('timeout', 5);

        $results = [
            'endpoint' => $endpoint,
            'username' => $username,
            'method' => $method,
            'timeout' => $timeout,
            'tests' => []
        ];

        if (!$endpoint || !$username || !$password) {
            return view('devices.diagnostic', ['results' => $results]);
        }

        $client = new Client([
            'timeout' => $timeout,
            'connect_timeout' => $timeout,
            'verify' => false // Disable SSL verification for testing
        ]);

        // Test 1: Basic connectivity
        $results['tests']['connectivity'] = $this->testBasicConnectivity($client, $method, $endpoint);

        // Test 2: RouterOS API endpoint
        $results['tests']['api_endpoint'] = $this->testApiEndpoint($client, $method, $endpoint);

        // Test 3: Authentication
        $results['tests']['authentication'] = $this->testAuthentication($client, $method, $endpoint, $username, $password);

        // Test 4: System resource endpoint
        $results['tests']['system_resource'] = $this->testSystemResource($client, $method, $endpoint, $username, $password);

        return view('devices.diagnostic', ['results' => $results]);
    }

    private function testBasicConnectivity($client, $method, $endpoint)
    {
        try {
            $response = $client->get($method . "://" . $endpoint, [
                'timeout' => 3
            ]);
            
            return [
                'status' => 'success',
                'message' => 'Connection successful',
                'status_code' => $response->getStatusCode(),
                'headers' => $response->getHeaders()
            ];
        } catch (ConnectException $e) {
            return [
                'status' => 'error',
                'message' => 'Connection failed: ' . $e->getMessage(),
                'details' => 'Check if the router is reachable and the IP address is correct'
            ];
        } catch (RequestException $e) {
            return [
                'status' => 'warning',
                'message' => 'Connection established but request failed: ' . $e->getMessage(),
                'status_code' => $e->getResponse() ? $e->getResponse()->getStatusCode() : 'N/A'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Unexpected error: ' . $e->getMessage()
            ];
        }
    }

    private function testApiEndpoint($client, $method, $endpoint)
    {
        try {
            $response = $client->get($method . "://" . $endpoint . "/rest", [
                'timeout' => 3
            ]);
            
            return [
                'status' => 'success',
                'message' => 'RouterOS API endpoint accessible',
                'status_code' => $response->getStatusCode()
            ];
        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 'N/A';
            
            if ($statusCode == 401) {
                return [
                    'status' => 'success',
                    'message' => 'RouterOS API endpoint accessible (authentication required)',
                    'status_code' => $statusCode
                ];
            }
            
            return [
                'status' => 'error',
                'message' => 'RouterOS API endpoint not accessible: ' . $e->getMessage(),
                'details' => 'Make sure RouterOS API is enabled in System > API'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error testing API endpoint: ' . $e->getMessage()
            ];
        }
    }

    private function testAuthentication($client, $method, $endpoint, $username, $password)
    {
        try {
            $response = $client->get($method . "://" . $endpoint . "/rest/system/resource", [
                'auth' => [$username, $password],
                'timeout' => 5
            ]);
            
            return [
                'status' => 'success',
                'message' => 'Authentication successful',
                'status_code' => $response->getStatusCode()
            ];
        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 'N/A';
            
            if ($statusCode == 401) {
                return [
                    'status' => 'error',
                    'message' => 'Authentication failed: Invalid username or password',
                    'details' => 'Check your RouterOS credentials'
                ];
            }
            
            return [
                'status' => 'error',
                'message' => 'Authentication test failed: ' . $e->getMessage(),
                'status_code' => $statusCode
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error during authentication test: ' . $e->getMessage()
            ];
        }
    }

    private function testSystemResource($client, $method, $endpoint, $username, $password)
    {
        try {
            $response = $client->get($method . "://" . $endpoint . "/rest/system/resource", [
                'auth' => [$username, $password],
                'headers' => ['Content-Type' => 'application/json'],
                'timeout' => 5
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            return [
                'status' => 'success',
                'message' => 'System resource endpoint working',
                'status_code' => $response->getStatusCode(),
                'data' => $data
            ];
        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 'N/A';
            
            return [
                'status' => 'error',
                'message' => 'System resource endpoint failed: ' . $e->getMessage(),
                'status_code' => $statusCode
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error testing system resource: ' . $e->getMessage()
            ];
        }
    }
} 