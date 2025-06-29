@extends('template.layout')

@section('main-content')

<div class="col-md-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Add new RouterOS device</h4>
            <p class="card-description">
                Here you can add a new RouterOS device for you to control
            </p>
            
            <div class="alert alert-info">
                <strong>💡 Tip:</strong> Having trouble connecting? 
                <a href="{{ route('device.diagnostic') }}" class="alert-link">Use our diagnostic tool</a> to test your connection first.
            </div>
            
            <form method="POST" action="{{route('Devices.store')}}">
            @csrf
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Device name</label>
                <div class="col-sm-12">
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}" placeholder="My RouterOS Device">
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Username</label>
                <div class="col-sm-12">
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{old('username')}}" placeholder="admin">
                    @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-12">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" value="{{old('password')}}" placeholder="••••••">
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Endpoint</label>
                <div class="col-sm-12">
                    <input type="text" name="endpoint" class="form-control @error('endpoint') is-invalid @enderror" value="{{old('endpoint')}}" placeholder="192.168.88.1">
                    <small class="form-text text-muted">Enter the IP address of your RouterOS device (e.g., 192.168.88.1)</small>
                    @error('endpoint')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Endpoint communication method</label>
                <div class="col-sm-12">
                    <select class="form-select" name="method">
                        <option value="http">HTTP (Not secure but simpler access)</option>
                        <option value="https">HTTPS (Secure, needs SSL configured on the device)</option>
                    </select>
                    <small class="form-text text-muted">Use HTTP for testing, HTTPS for production (requires SSL certificate on router)</small>
                    @error('method')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 col-form-label">Timeout (optional)</label>
                <div class="col-sm-12">
                    <input type="text" name="timeout" class="form-control @error('timeout') is-invalid @enderror" value="{{old('timeout')}}" placeholder="3">
                    <small class="form-text text-muted">Connection timeout in seconds (default: 3)</small>
                    @error('timeout')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-fw">Submit</button>
            </form>
            
            <hr>
            <div class="mt-3">
                <h6>🔧 Prerequisites for RouterOS Connection:</h6>
                <ul class="text-muted">
                    <li>RouterOS API must be enabled (System > API)</li>
                    <li>Correct username and password</li>
                    <li>Router must be reachable from this network</li>
                    <li>Firewall must allow API access (port 80 for HTTP, 443 for HTTPS)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection