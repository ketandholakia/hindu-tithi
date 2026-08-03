@extends('layouts.app')

@section('content')
    <h1>API Keys</h1>
    <p class="lead">Create and manage API keys for the current account. The full key is shown only once after creation.</p>

    @if (session('new_api_key'))
        <div class="alert alert-success">
            <strong>New API key:</strong>
            <code>{{ session('new_api_key') }}</code>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('api.keys.store') }}" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-6">
                    <label class="form-label" for="name">Key name</label>
                    <input id="name" name="name" type="text" class="form-control" placeholder="Local dev key" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="expires_at">Expires at</label>
                    <input id="expires_at" name="expires_at" type="datetime-local" class="form-control">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Generate</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Existing Keys</h5>
            @if ($keys->isEmpty())
                <p class="mb-0">No API keys yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created</th>
                                <th>Last used</th>
                                <th>Expires</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($keys as $key)
                                <tr>
                                    <td>{{ $key->name }}</td>
                                    <td>{{ $key->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $key->last_used_at?->format('Y-m-d H:i') ?? 'Never' }}</td>
                                    <td>{{ $key->expires_at?->format('Y-m-d H:i') ?? 'No expiry' }}</td>
                                    <td>
                                        @if ($key->revoked_at)
                                            <span class="badge text-bg-secondary">Revoked</span>
                                        @elseif ($key->expires_at && $key->expires_at->isPast())
                                            <span class="badge text-bg-warning">Expired</span>
                                        @else
                                            <span class="badge text-bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if (!$key->revoked_at)
                                            <form method="POST" action="{{ route('api.keys.destroy', $key) }}" onsubmit="return confirm('Revoke this API key?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Revoke</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
