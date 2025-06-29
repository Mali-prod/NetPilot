@extends('template.layout')

@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
          @if ($resource != null)
            <h4 class="card-title"><strong>{{$device['name']}}</strong>'s resources </h4>
            <div class="row">
                <div class="col-md-6">
                    <address>
                    <p class="fw-bold">
                        Board name
                    </p>
                    <p class="mb-2">
                        {{$resource['platform']}} {{$resource['board-name']}}
                    </p>
                    <p class="fw-bold">
                        CPU
                    </p>
                    <p>
                        {{$resource['cpu']}} ({{$resource['cpu-count']}} cores, {{$resource['cpu-frequency']}} MHz)
                    </p>
                    <p class="fw-bold">
                        Version:
                    </p>
                    <p>
                        {{$resource['version']}} <br>
                        Built on {{$resource['build-time']}}
                    </p>
                    </address>
                </div>
                <div class="col-md-6">
                    <address>
                    <p class="fw-bold">
                        Uptime
                    </p>
                    <p class="mb-2">
                        {{$resource['uptime']}}
                    </p>
                    <p class="fw-bold">
                        HDD (used/total)
                    </p>
                    <p class="mb-2">
                        {{$resource['free-hdd-space']}} / {{$resource['total-hdd-space']}}
                    </p>
                    <p class="fw-bold">
                        Memory (used/total)
                    </p>
                    <p class="mb-2">
                        {{$resource['free-memory']}} / {{$resource['total-memory']}}
                    </p>
                    </address>
                </div>
            </div>
            @else
            <div class="alert alert-danger">
                <h5>❌ Connection Failed</h5>
                <p><strong>Error:</strong> {{$conn_error}}</p>
                
                <hr>
                <h6>🔧 Troubleshooting Steps:</h6>
                <ol>
                    <li><strong>Check RouterOS API:</strong> Make sure API is enabled in System > API</li>
                    <li><strong>Verify Credentials:</strong> Check username and password are correct</li>
                    <li><strong>Network Connectivity:</strong> Ensure router is reachable from this network</li>
                    <li><strong>Firewall Rules:</strong> Check if firewall is blocking API access</li>
                    <li><strong>SSL Certificate:</strong> If using HTTPS, verify SSL certificate is valid</li>
                </ol>
                
                <div class="mt-3">
                    <a href="{{ route('device.diagnostic') }}?endpoint={{$device['endpoint']}}&username={{$device['username']}}&method={{$device['method']}}" class="btn btn-primary">
                        🔍 Run Connection Diagnostic
                    </a>
                    <a href="{{ route('Devices.edit', $device['id']) }}" class="btn btn-secondary">
                        ✏️ Edit Device Settings
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection