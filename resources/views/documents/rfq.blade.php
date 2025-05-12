<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Request for Quotation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: auto;
        }
        h1, h2, h3 {
            margin: 5px 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>CAGAYAN STATE UNIVERSITY</h2>
        <h3>OFFICE OF THE VICE PRESIDENT FOR ADMINISTRATION AND FINANCE</h3>
        <h3>PROCUREMENT MANAGEMENT OFFICE</h3>
        <h1>REQUEST FOR QUOTATION</h1>
        <p>RFQ No.: {{ $rfq->rfq_number }}</p>
        <p>Date: {{ $rfq->created_at->format('F d, Y') }}</p>
    </div>
    
    <div class="info-section">
        <p><strong>To:</strong> Selected Suppliers</p>
        <p><strong>From:</strong> Procurement Management Office, CSU</p>
        <p><strong>Subject:</strong> Request for Quotation for the Procurement of Goods/Services</p>
    </div>
    
    <p>1. The Cagayan State University (CSU) invites interested suppliers to submit their quotation for the items listed below:</p>
    
    <table>
        <thead>
            <tr>
                <th>Item No.</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rfq->purchaseRequest->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit }}</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="info-section">
        <p><strong>2. Quotation Instructions:</strong></p>
        <ul>
            <li>All prices quoted must be inclusive of all applicable taxes and charges.</li>
            <li>Quotation validity shall be for a period of {{ $rfq->validity_period ?? '30' }} calendar days from the deadline of submission.</li>
            <li>Deadline of submission: {{ $rfq->deadline->format('F d, Y') }}</li>
            <li>Delivery period shall be {{ $rfq->delivery_period ?? '30' }} calendar days from receipt of Purchase Order.</li>
        </ul>
    </div>
    
    <p><strong>3. Contact Information:</strong><br>
    For inquiries, please contact the Procurement Management Office at:<br>
    Email: procurement@csu.edu.ph<br>
    Tel No.: (078) 123-4567</p>
    
    <div class="signatures">
        <div class="signature-box">
            <p>Prepared by:</p>
            <br><br><br>
            <p>____________________________</p>
            <p>{{ $rfq->creator->name }}</p>
            <p>{{ $rfq->creator->position ?? 'Procurement Officer' }}</p>
        </div>
        <div class="signature-box">
            <p>Approved by:</p>
            <br><br><br>
            <p>____________________________</p>
            <p>BAC Chairperson</p>
        </div>
    </div>
    
    <div class="footer">
        <p>This document is system-generated and considered official when properly signed.</p>
    </div>
</body>
</html> 