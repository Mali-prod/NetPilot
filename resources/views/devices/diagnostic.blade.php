@extends('template.layout')

@section('main-content')
<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">MikroTik Device Connection Diagnostic</h4>
            <p class="card-description">
                Test your MikroTik device connection to identify connectivity issues
            </p>
            
            <form method="GET" action="{{ route('device.diagnostic') }}">
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Endpoint</label>
                    <div class="col-sm-12">
                        <input type="text" name="endpoint" class="form-control" value="{{ $results['endpoint'] ?? '' }}" placeholder="192.168.88.1" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Username</label>
                    <div class="col-sm-12">
                        <input type="text" name="username" class="form-control" value="{{ $results['username'] ?? '' }}" placeholder="admin" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-12">
                        <input type="password" name="password" class="form-control" placeholder="••••••" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Method</label>
                    <div class="col-sm-12">
                        <select class="form-select" name="method">
                            <option value="http" {{ ($results['method'] ?? '') == 'http' ? 'selected' : '' }}>HTTP</option>
                            <option value="https" {{ ($results['method'] ?? '') == 'https' ? 'selected' : '' }}>HTTPS</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 col-form-label">Timeout (seconds)</label>
                    <div class="col-sm-12">
                        <input type="number" name="timeout" class="form-control" value="{{ $results['timeout'] ?? 5 }}" min="1" max="30">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-fw">Run Diagnostic Tests</button>
            </form>

            @if(isset($results['tests']) && !empty($results['tests']))
                <hr>
                <h5 class="mt-4">Diagnostic Results</h5>
                
                @foreach($results['tests'] as $testName => $testResult)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                @switch($testName)
                                    @case('connectivity')
                                        🔗 Basic Connectivity Test
                                        @break
                                    @case('api_endpoint')
                                        🌐 RouterOS API Endpoint Test
                                        @break
                                    @case('authentication')
                                        🔐 Authentication Test
                                        @break
                                    @case('system_resource')
                                        📊 System Resource Test
                                        @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $testName)) }}
                                @endswitch
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($testResult['status'] == 'success')
                                <div class="alert alert-success">
                                    <strong>✅ {{ $testResult['message'] }}</strong>
                                    @if(isset($testResult['status_code']))
                                        <br><small>Status Code: {{ $testResult['status_code'] }}</small>
                                    @endif
                                </div>
                            @elseif($testResult['status'] == 'warning')
                                <div class="alert alert-warning">
                                    <strong>⚠️ {{ $testResult['message'] }}</strong>
                                    @if(isset($testResult['status_code']))
                                        <br><small>Status Code: {{ $testResult['status_code'] }}</small>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <strong>❌ {{ $testResult['message'] }}</strong>
                                    @if(isset($testResult['details']))
                                        <br><small><strong>Solution:</strong> {{ $testResult['details'] }}</small>
                                    @endif
                                    @if(isset($testResult['status_code']))
                                        <br><small>Status Code: {{ $testResult['status_code'] }}</small>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                @if(isset($results['tests']['system_resource']['data']))
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">📋 System Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Platform:</strong> {{ $results['tests']['system_resource']['data']['platform'] ?? 'N/A' }}</p>
                                    <p><strong>Board Name:</strong> {{ $results['tests']['system_resource']['data']['board-name'] ?? 'N/A' }}</p>
                                    <p><strong>Version:</strong> {{ $results['tests']['system_resource']['data']['version'] ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>CPU:</strong> {{ $results['tests']['system_resource']['data']['cpu'] ?? 'N/A' }}</p>
                                    <p><strong>Uptime:</strong> {{ $results['tests']['system_resource']['data']['uptime'] ?? 'N/A' }}</p>
                                    <p><strong>Memory:</strong> {{ $results['tests']['system_resource']['data']['free-memory'] ?? 'N/A' }} / {{ $results['tests']['system_resource']['data']['total-memory'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">🔧 Troubleshooting Guide</h6>
                    </div>
                    <div class="card-body">
                        <h6>Common Issues and Solutions:</h6>
                        <ul>
                            <li><strong>Connection Failed:</strong> Check if the router IP address is correct and reachable from your network</li>
                            <li><strong>API Endpoint Not Accessible:</strong> Enable RouterOS API in System > API settings</li>
                            <li><strong>Authentication Failed:</strong> Verify username and password are correct</li>
                            <li><strong>HTTPS Issues:</strong> Make sure SSL certificate is properly configured on the router</li>
                            <li><strong>Firewall Blocking:</strong> Check if firewall rules are blocking API access</li>
                        </ul>
                        
                        <h6>RouterOS API Setup:</h6>
                        <ol>
                            <li>Log into your MikroTik router via WinBox or WebFig</li>
                            <li>Go to System > API</li>
                            <li>Enable "API" and "API-SSL" if using HTTPS</li>
                            <li>Set appropriate access permissions</li>
                            <li>Test the connection using this diagnostic tool</li>
                        </ol>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 