<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUSina - Bus Reservation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h3 class="mb-0">Bus List</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>Bus Number</th>
                        <th>Seating Capacity</th>
                        <th>Driver Name</th>
                        <th>Bus Type</th>
                    </tr>
                </thead>
               
                <tbody>
                    @foreach ($amlv_bus as $bus)
                        <tr>
                            <td>{{ $bus->bus_number }}</td>
                            <td>{{ $bus->seating_capacity }}</td>
                            <td>{{ $bus->driver_name }}</td>
                            <td>{{ $bus->typeLabel() }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
</body>
</html>