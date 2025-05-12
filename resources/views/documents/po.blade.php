<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order</title>
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
        .two-columns {
            display: flex;
            justify-content: space-between;
        }
        .column {
            width: 48%;
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
            width: 30%;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
        }
        .box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>CAGAYAN STATE UNIVERSITY</h2>
        <h3>OFFICE OF THE VICE PRESIDENT FOR ADMINISTRATION AND FINANCE</h3>
        <h3>PROCUREMENT MANAGEMENT OFFICE</h3>
        <h1>PURCHASE ORDER</h1>
        <p>PO No.: {{ $po->po_number }}</p>
        <p>Date: {{ $po->po_date->format('F d, Y') }}</p>
    </div>
    
    <div class="two-columns">
        <div class="column box">
            <p><strong>Supplier:</strong> {{ $po->supplierQuotation->supplier->name }}</p>
            <p><strong>Address:</strong> {{ $po->supplierQuotation->supplier->address }}</p>
            <p><strong>Contact Person:</strong> {{ $po->supplierQuotation->supplier->contact_person }}</p>
            <p><strong>Contact Number:</strong> {{ $po->supplierQuotation->supplier->contact_number }}</p>
            <p><strong>TIN:</strong> {{ $po->supplierQuotation->supplier->tin }}</p>
        </div>
        <div class="column box">
            <p><strong>PR No.:</strong> {{ $po->abstractOfQuotation->requestForQuotation->purchaseRequest->pr_number }}</p>
            <p><strong>RFQ No.:</strong> {{ $po->abstractOfQuotation->requestForQuotation->rfq_number }}</p>
            <p><strong>AOQ No.:</strong> {{ $po->abstractOfQuotation->aoq_number }}</p>
            <p><strong>Mode of Procurement:</strong> Small Value Procurement</p>
            <p><strong>Delivery Term:</strong> {{ $po->delivery_days }} calendar days</p>
        </div>
    </div>
    
    <p>Gentlemen:</p>
    <p>Please furnish this office the following articles subject to the terms and conditions contained herein:</p>
    
    <div class="two-columns">
        <div class="column box">
            <p><strong>Place of Delivery:</strong> {{ $po->delivery_location }}</p>
        </div>
        <div class="column box">
            <p><strong>Delivery Term:</strong> {{ $po->delivery_days }} calendar days</p>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Item No.</th>
                <th>Item Description</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Unit Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->supplierQuotation->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->item_description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total">
                <td colspan="5">TOTAL AMOUNT</td>
                <td>{{ number_format($po->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="box">
        <p><strong>Amount in Words:</strong> {{ ucfirst(strtolower(\App\Helpers\NumberToWords::convert($po->total_amount))) }} Pesos Only</p>
    </div>
    
    <div class="info-section">
        <p><strong>Terms and Conditions:</strong></p>
        <ol>
            <li>All prices stated herein are inclusive of all applicable taxes.</li>
            <li>Payment shall be made only upon complete delivery and acceptance of the items.</li>
            <li>Partial delivery is allowed/not allowed.</li>
            <li>The University reserves the right to reject any or all items that do not conform to the specifications.</li>
            <li>Delivery beyond the specified delivery period shall be subject to penalties.</li>
        </ol>
    </div>
    
    <div class="signatures">
        <div class="signature-box">
            <p>Prepared by:</p>
            <br><br><br>
            <p>____________________________</p>
            <p>{{ $po->creator->name }}</p>
            <p>{{ $po->creator->position ?? 'Procurement Officer' }}</p>
        </div>
        <div class="signature-box">
            <p>Approved by:</p>
            <br><br><br>
            <p>____________________________</p>
            <p>University President</p>
        </div>
        <div class="signature-box">
            <p>Conforme:</p>
            <br><br><br>
            <p>____________________________</p>
            <p>{{ $po->supplierQuotation->supplier->name }}</p>
            <p>Authorized Signature</p>
        </div>
    </div>
    
    <div class="footer">
        <p>This document is system-generated and considered official when properly signed.</p>
    </div>
</body>
</html> 