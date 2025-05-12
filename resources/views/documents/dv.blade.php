<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Disbursement Voucher</title>
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
        .stamp {
            position: absolute;
            top: 100px;
            right: 50px;
            font-size: 24px;
            color: #ff0000;
            border: 3px solid #ff0000;
            padding: 10px;
            transform: rotate(-15deg);
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>CAGAYAN STATE UNIVERSITY</h2>
        <h3>OFFICE OF THE VICE PRESIDENT FOR ADMINISTRATION AND FINANCE</h3>
        <h3>ACCOUNTING OFFICE</h3>
        <h1>DISBURSEMENT VOUCHER</h1>
        <p>DV No.: {{ $disbursementVoucher->dv_number }}</p>
        <p>Date: {{ $disbursementVoucher->dv_date->format('F d, Y') }}</p>
    </div>
    
    @if ($disbursementVoucher->status == 'paid')
    <div class="stamp">PAID</div>
    @elseif ($disbursementVoucher->status == 'approved')
    <div class="stamp">APPROVED</div>
    @endif
    
    <div class="box">
        <p><strong>Mode of Payment:</strong> {{ ucfirst($disbursementVoucher->payment_method) }}</p>
        @if ($disbursementVoucher->payment_method == 'check')
        <p><strong>Check No.:</strong> {{ $disbursementVoucher->check_number }}</p>
        <p><strong>Check Date:</strong> {{ $disbursementVoucher->check_date->format('F d, Y') }}</p>
        @endif
    </div>
    
    <div class="box">
        <p><strong>Payee:</strong> {{ $disbursementVoucher->payee }}</p>
        <p><strong>Address:</strong> {{ $disbursementVoucher->purchaseOrder->supplierQuotation->supplier->address }}</p>
        <p><strong>PR No.:</strong> {{ $disbursementVoucher->purchaseOrder->abstractOfQuotation->requestForQuotation->purchaseRequest->pr_number }}</p>
        <p><strong>PO No.:</strong> {{ $disbursementVoucher->purchaseOrder->po_number }}</p>
    </div>
    
    <div class="info-section">
        <p><strong>Particulars:</strong> {{ $disbursementVoucher->particulars }}</p>
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
            @foreach($disbursementVoucher->purchaseOrder->supplierQuotation->items as $index => $item)
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
                <td>{{ number_format($disbursementVoucher->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="box">
        <p><strong>Amount in Words:</strong> {{ ucfirst(strtolower(\App\Helpers\NumberToWords::convert($disbursementVoucher->total_amount))) }} Pesos Only</p>
    </div>
    
    <div class="signatures">
        <div class="signature-box">
            <p>Prepared by:</p>
            <br><br><br>
            <p>____________________________</p>
            <p>{{ $disbursementVoucher->creator->name }}</p>
            <p>{{ $disbursementVoucher->creator->position ?? 'Financial Analyst' }}</p>
        </div>
        <div class="signature-box">
            <p>Approved by:</p>
            <br><br><br>
            <p>____________________________</p>
            @if ($disbursementVoucher->approver)
            <p>{{ $disbursementVoucher->approver->name }}</p>
            <p>{{ $disbursementVoucher->approver->position ?? 'Accounting Officer' }}</p>
            @else
            <p>University Accounting Officer</p>
            @endif
        </div>
        <div class="signature-box">
            <p>Received by:</p>
            <br><br><br>
            <p>____________________________</p>
            <p>{{ $disbursementVoucher->payee }}</p>
            <p>Date: _________________</p>
        </div>
    </div>
    
    <div class="footer">
        <p>This document is system-generated and considered official when properly signed.</p>
    </div>
</body>
</html> 