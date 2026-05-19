@extends('layouts.app')

@section('content')
    @include('partials.breadcrumbs', ['breadcrumbs' => [['label' => 'Technical Dashboard']]])

    <div class="container">
        <h2 class="mb-3">Technical Dashboard</h2>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">System Info</div>
                    <div class="card-body">
                        <p><strong>Laravel:</strong> {{ app()->version() }}</p>
                        <p><strong>PHP:</strong> {{ phpversion() }}</p>
                        <p><strong>Environment:</strong> {{ app()->environment() }}</p>
                        <p><strong>DB Driver:</strong> {{ config('database.default') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Database Backup</div>
                    <div class="card-body">
                        <p>Download a copy of the current SQLite database.</p>
                        <form method="POST" action="{{ route('admin.technical.backup') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Download Backup</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">System Logs (last 100 lines)</div>
                    <div class="card-body p-0">
                        @if (count($logs))
                            <pre style="max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 1rem; margin: 0; font-size: 12px;">{{ implode("\n", $logs) }}</pre>
                        @else
                            <p class="p-3 text-muted">No log file found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
