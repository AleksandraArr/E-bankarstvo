<!DOCTYPE html>
<html>
<head>
    <title>Transaction Report - Laravel PDF</title>
    <link rel="stylesheet" href="{{ public_path('css/bootstrap.min.css') }}">
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $date }}</p>
    <p>Below are the details of the selected transaction:</p>
  
    <table class="table table-bordered">
        <tr>
            <th>Field</th>
            <th>Value</th>
        </tr>
        <tr>
            <td>ID</td>
            <td>{{ $id }}</td>
        </tr>
        <tr>
            <td>Sender</td>
            <td>{{ $sender }}</td>
        </tr>
        @if(isset($receiver))
        <tr>
            <td>Receiver</td>
            <td>{{ $receiver }}</td>
        </tr>
        @else
        <tr>
            <td>Receiver Account Number</td>
            <td>{{ $receiver_account_number }}</td>
        </tr>
        @endif
        <tr>
            <td>Category</td>
            <td>{{ $category }}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td>{{ $date }}</td>
        </tr>
        <tr>
            <td>Amount</td>
            <td>{{ $amount }}</td>
        </tr>
        <tr>
            <td>Description</td>
            <td>{{ $description }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>{{ $status }}</td>
        </tr>
        <tr>
            <td>Scope</td>
            <td>{{ $scope }}</td>
        </tr>
    </table>
  
    <p class="text-center">This PDF was generated on {{ $date }}.</p>
</body>
</html>
