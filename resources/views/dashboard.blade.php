@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Weather Dashboard') }}</div>
                    <!-- Fetch Weather Data Button -->

                    <div class="card-body">
                        @if ($weatherData->isEmpty())
                            <button id="fetch-weather-btn" class="btn btn-sm btn-primary mb-3">Fetch Weather Data</button>
                        @endif

                        <table class="table" id="weather-table">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Temperature (°C)</th>
                                    <th>Temperature (°F)</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const weatherTable = $('#weather-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('weather.data.table') }}',
                columns: [{
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'temperature_celsius',
                        name: 'temperature_celsius'
                    },
                    {
                        data: 'temperature_fahrenheit',
                        name: 'temperature_fahrenheit'
                    },
                    {
                        data: 'retrieved_at',
                        name: 'retrieved_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Fetch Weather Data Button Click
            $('#fetch-weather-btn').on('click', function() {
                let city = prompt("Enter the city name:");
                if (city) {
                    $.ajax({
                        url: '{{ route('fetch.weather') }}',
                        type: 'POST',
                        data: {
                            city: city,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert(response.message);
                            weatherTable.ajax.reload(); // Reload DataTable to show new data
                        },
                        error: function(response) {
                            alert(response.responseJSON.message);
                        }
                    });
                }
            });

            // Handle action button click within DataTable
            $('body').on('click', '.fetch-weather', function() {
                let city = $(this).data('city');

                $.ajax({
                    url: '{{ route('fetch.weather') }}',
                    type: 'POST',
                    data: {
                        city: city,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.message);
                        weatherTable.ajax.reload();
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endpush
