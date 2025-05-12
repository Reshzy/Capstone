<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Abstract of Quotation</title>
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
            width: 30%;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
        }
        .highlight {
            background-color: #ffffcc;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>CAGAYAN STATE UNIVERSITY</h2>
        <h3>OFFICE OF THE VICE PRESIDENT FOR ADMINISTRATION AND FINANCE</h3>
        <h3>PROCUREMENT MANAGEMENT OFFICE</h3>
        <h1>ABSTRACT OF QUOTATION</h1>
        <p>AOQ No.: {{ $aoq->aoq_number }}</p>
        <p>Date: {{ $aoq->created_at->format('F d, Y') }}</p>
    </div>
    
    <div class="info-section">
        <p><strong>Reference RFQ:</strong> {{ $aoq->requestForQuotation->rfq_number }}</p>
        <p><strong>Purchase Request:</strong> {{ $aoq->requestForQuotation->purchaseRequest->pr_number }}</p>
        <p><strong>PR Purpose:</strong> {{ $aoq->requestForQuotation->purchaseRequest->purpose }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th rowspan="2">Item No.</th>
                <th rowspan="2">Description</th>
                <th rowspan="2">Quantity</th>
                <th rowspan="2">Unit</th>
                @foreach($aoq->requestForQuotation->supplierQuotations as $quotation)
                <th colspan="2">{{ $quotation->supplier->name }}</th>
                @endforeach
                <th rowspan="2">LCP</th>
            </tr>
            <tr>
                @foreach($aoq->requestForQuotation->supplierQuotations as $quotation)
                <th>Unit Price</th>
                <th>Total Price</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $prItems = $aoq->requestForQuotation->purchaseRequest->items;
            @endphp
            
            @foreach($prItems as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit }}</td>
                
                @foreach($aoq->requestForQuotation->supplierQuotations as $quotation)
                    @php
                        $quotationItem = $quotation->items->where('item_description', $item->description)->first();
                        $unitPrice = $quotationItem ? $quotationItem->unit_price : 0;
                        $totalPrice = $quotationItem ? ($quotationItem->unit_price * $item->quantity) : 0;
                        
                        // Check if this is the awarded supplier and the lowest price
                        $isAwarded = $aoq->awarded_supplier_id == $quotation->supplier->id;
                    @endphp
                    
                    <td class="{{ $isAwarded ? 'highlight' : '' }}">{{ number_format($unitPrice, 2) }}</td>
                    <td class="{{ $isAwarded ? 'highlight' : '' }}">{{ number_format($totalPrice, 2) }}</td>
                @endforeach
                
                <td>
                    @php
                        $lowestPrice = PHP_INT_MAX;
                        foreach($aoq->requestForQuotation->supplierQuotations as $quotation) {
                            $quotationItem = $quotation->items->where('item_description', $item->description)->first();
                            if ($quotationItem && $quotationItem->unit_price < $lowestPrice) {
                                $lowestPrice = $quotationItem->unit_price;
                            }
                        }
                        echo $lowestPrice < PHP_INT_MAX ? number_format($lowestPrice, 2) : 'N/A';
                    @endphp
                </td>
            </tr>
            @endforeach
            
            <tr class="total">
                <td colspan="4">TOTAL AMOUNT</td>
                @foreach($aoq->requestForQuotation->supplierQuotations as $quotation)
                    @php
                        $totalAmount = 0;
                        foreach($quotation->items as $item) {
                            $prItem = $prItems->where('description', $item->item_description)->first();
                            if ($prItem) {
                                $totalAmount += $item->unit_price * $prItem->quantity;
                            }
                        }
                        $isAwarded = $aoq->awarded_supplier_id == $quotation->supplier->id;
                    @endphp
                    <td class="{{ $isAwarded ? 'highlight' : '' }}"></td>
                    <td class="{{ $isAwarded ? 'highlight' : '' }}">{{ number_format($totalAmount, 2) }}</td>
                @endforeach
                <td></td>
            </tr>
        </tbody>
    </table>
    
    <div class="info-section">
        <p><strong>Awarded Supplier:</strong> {{ $aoq->awardedSupplier ? $aoq->awardedSupplier->name : 'Not awarded yet' }}</p>
        <p><strong>Awarded Amount:</strong> PHP {{ $aoq->awarded_amount ? number_format($aoq->awarded_amount, 2) : '0.00' }}</p>
        <p><strong>Remarks:</strong> {{ $aoq->remarks ?? 'N/A' }}</p>
    </div>
    
    <div class="signatures">
        <div class="signature-box">
            <p>Prepared by:</p>
            <br><br><br>
            <p>____________________________</p>
            <p>{{ $aoq->creator->name }}</p>
            <p>{{ $aoq->creator->position ?? 'Procurement Officer' }}</p>
        </div>
        <div class="signature-box">
            <p>Verified by:</p>
            <br><br><br>
            <p>____________________________</p>
            <p>BAC Secretariat</p>
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