@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">📊 System Analytics</h2>
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Documents by Category</div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Documents by Security Clearance</div>
                    <div class="card-body">
                        <canvas id="clearanceChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Uploads per Month (Last 6 Months)</div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">User Roles Distribution</div>
                    <div class="card-body">
                        <canvas id="roleChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Category Chart
        const categoryLabels = {!! json_encode($categoryStats->keys()) !!};
        const categoryData = {!! json_encode($categoryStats->values()) !!};
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Documents',
                    data: categoryData,
                    backgroundColor: '#1d799d'
                }]
            }
        });

        // Clearance Chart
        const clearanceLabels = {!! json_encode($clearanceStats->keys()) !!};
        const clearanceData = {!! json_encode($clearanceStats->values()) !!};
        new Chart(document.getElementById('clearanceChart'), {
            type: 'pie',
            data: {
                labels: clearanceLabels,
                datasets: [{
                    data: clearanceData,
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d']
                }]
            }
        });

        // Monthly Uploads
        const monthLabels = {!! json_encode($monthlyUploads->keys()) !!};
        const monthData = {!! json_encode($monthlyUploads->values()) !!};
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Uploads',
                    data: monthData,
                    borderColor: '#1d799d',
                    fill: false
                }]
            }
        });

        // Role Chart
        const roleLabels = {!! json_encode($roleStats->keys()) !!};
        const roleData = {!! json_encode($roleStats->values()) !!};
        new Chart(document.getElementById('roleChart'), {
            type: 'doughnut',
            data: {
                labels: roleLabels,
                datasets: [{
                    data: roleData,
                    backgroundColor: ['#007bff', '#ffc107', '#28a745', '#dc3545']
                }]
            }
        });
    </script>
@endsection
