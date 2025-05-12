<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Requests Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-subtitle {
            font-size: 14px;
            margin-bottom: 10px;
        }
        .report-meta {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="report-title">Cagayan State University</div>
        <div class="report-subtitle">{{ $title }}</div>
        <div class="report-meta">Generated on: {{ $generated_at }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>PR Number</th>
                <th>Title</th>
                <th>Requestor</th>
                <th>Department</th>
                <th>Status</th>
                <th>Total Amount</th>
                <th>Date Submitted</th>
            </tr>
        </thead>
        <tbody>
            @if(count($items) > 0)
                @foreach($items as $request)
                <tr>
                    <td>{{ $request->pr_number }}</td>
                    <td>{{ $request->title }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ $request->department->name }}</td>
                    <td>{{ ucfirst($request->status) }}</td>
                    <td>₱ {{ number_format($request->items->sum('estimated_cost'), 2) }}</td>
                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" style="text-align: center;">No purchase requests found</td>
                </tr>
            @endif
        </tbody>
    </table>
    
    @if(count($items) > 0)
    <div>
        <strong>Total Requests:</strong> {{ count($items) }}<br>
        <strong>Total Amount:</strong> ₱ {{ number_format($items->sum(function($request) { return $request->items->sum('estimated_cost'); }), 2) }}
    </div>
    @endif
    
    <div class="footer">
        This is a system-generated report.
    </div>
</body>
</html> 