<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVOICE #{{ $invoice->invoice_number }} - {{ $invoice->student->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.2;
            color: #000;
            background: #fff;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        
        .invoice-container {
            max-width: 8.27in;
            margin: 0 auto;
            background: white;
            border: 1px solid #000;
        }
        
        .invoice-header {
            background: #000;
            color: white;
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #000;
        }
        
        .company-logo {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        
        .company-tagline {
            font-size: 10pt;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        
        .invoice-title {
            font-size: 24pt;
            font-weight: bold;
            margin: 10px 0;
            letter-spacing: 2px;
        }
        
        .invoice-number {
            font-size: 14pt;
            font-weight: bold;
            background: #fff;
            color: #000;
            padding: 5px 10px;
            border: 1px solid #fff;
            display: inline-block;
        }
        
        .invoice-content {
            padding: 20px;
        }
        
        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .company-info, .client-info {
            border: 1px solid #000;
            padding: 12px;
            background: #f9f9f9;
        }
        
        .company-info h3, .client-info h3 {
            color: #000;
            margin-bottom: 8px;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .company-info p, .client-info p {
            margin-bottom: 3px;
            color: #000;
            font-size: 9pt;
        }
        
        .company-info strong, .client-info strong {
            font-weight: bold;
            font-size: 10pt;
        }
        
        .invoice-details {
            border: 1px solid #000;
            padding: 12px;
            margin-bottom: 15px;
            background: #f9f9f9;
        }
        
        .invoice-details h3 {
            color: #000;
            margin-bottom: 8px;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px solid #ccc;
            font-size: 9pt;
        }
        
        .detail-label {
            font-weight: bold;
            color: #000;
            text-transform: uppercase;
        }
        
        .detail-value {
            color: #000;
            font-weight: normal;
        }
        
        .amounts-section {
            border: 1px solid #000;
            padding: 12px;
            margin-bottom: 15px;
            background: #f9f9f9;
        }
        
        .amounts-section h3 {
            color: #000;
            margin-bottom: 8px;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #ccc;
            font-size: 9pt;
        }
        
        .amount-row.total {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            margin-top: 8px;
            padding: 8px 0;
            background: #f0f0f0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            border: 1px solid #000;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-paid {
            background: #000;
            color: #fff;
        }
        
        .status-pending {
            background: #fff;
            color: #000;
        }
        
        .status-draft {
            background: #ccc;
            color: #000;
        }
        
        .status-sent {
            background: #000;
            color: #fff;
        }
        
        .status-overdue {
            background: #000;
            color: #fff;
        }
        
        .status-cancelled {
            background: #666;
            color: #fff;
        }
        
        .footer {
            background: #000;
            color: white;
            padding: 10px 15px;
            text-align: center;
            border-top: 1px solid #000;
        }
        
        .footer h4 {
            font-size: 10pt;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .footer p {
            font-size: 8pt;
            margin-bottom: 2px;
        }
        
        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #000;
            color: white;
            border: 1px solid #000;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #fff;
            color: #000;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 48pt;
            color: rgba(0,0,0,0.03);
            z-index: -1;
            pointer-events: none;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .print-button {
                display: none;
            }
            
            .invoice-container {
                border: none;
                box-shadow: none;
                max-width: none;
                margin: 0;
            }
            
            .watermark {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .invoice-info {
                grid-template-columns: 1fr;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="watermark">OFFICIAL INVOICE</div>
    
    <button class="print-button" onclick="window.print()">
        🖨️ PRINT
    </button>
    
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="company-logo">EDUCATION MANAGEMENT SYSTEMS</div>
            <div class="company-tagline">Professional Learning Solutions</div>
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
        </div>
        
        <div class="invoice-content">
            <div class="invoice-info">
                <div class="company-info">
                    <h3>Issued By</h3>
                    <p><strong>EDUCATION MANAGEMENT SYSTEMS</strong></p>
                    <p>123 Education Boulevard</p>
                    <p>Learning District, LD 12345</p>
                    <p>United States</p>
                    <p><strong>Phone:</strong> (555) 123-4567</p>
                    <p><strong>Email:</strong> billing@educationmanagement.com</p>
                    <p><strong>Website:</strong> www.educationmanagement.com</p>
                    <p><strong>Tax ID:</strong> 12-3456789</p>
                </div>
                
                <div class="client-info">
                    <h3>Bill To</h3>
                    <p><strong>{{ $invoice->student->name }}</strong></p>
                    <p><strong>Student ID:</strong> {{ $invoice->student->student_id_number }}</p>
                    <p><strong>Email:</strong> {{ $invoice->student->email }}</p>
                    @if($invoice->student->phone)
                        <p><strong>Phone:</strong> {{ $invoice->student->phone }}</p>
                    @endif
                    @if($invoice->student->address)
                        <p>{{ $invoice->student->address }}</p>
                        @if($invoice->student->city && $invoice->student->state)
                            <p>{{ $invoice->student->city }}, {{ $invoice->student->state }} {{ $invoice->student->zip_code }}</p>
                        @endif
                        @if($invoice->student->country)
                            <p>{{ $invoice->student->country }}</p>
                        @endif
                    @endif
                </div>
            </div>
            
            <div class="invoice-details">
                <h3>Invoice Information</h3>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Invoice Number:</span>
                        <span class="detail-value">#{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Invoice Date:</span>
                        <span class="detail-value">{{ $invoice->created_at->format('F j, Y') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Due Date:</span>
                        <span class="detail-value">{{ $invoice->due_date ? $invoice->due_date->format('F j, Y') : 'Upon Receipt' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payment Status:</span>
                        <span class="detail-value">
                            <span class="status-badge status-{{ $invoice->status }}">
                                {{ strtoupper($invoice->status) }}
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Course Title:</span>
                        <span class="detail-value">{{ $invoice->course->title }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Course Code:</span>
                        <span class="detail-value">{{ $invoice->course->code }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Enrollment Date:</span>
                        <span class="detail-value">{{ $invoice->enrollment->enrolled_at ? $invoice->enrollment->enrolled_at->format('F j, Y') : 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payment Terms:</span>
                        <span class="detail-value">Net 30 Days</span>
                    </div>
                </div>
            </div>
            
            <div class="amounts-section">
                <h3>Payment Summary</h3>
                <div class="amount-row">
                    <span><strong>Course Fee:</strong></span>
                    <span><strong>${{ number_format($invoice->subtotal, 2) }}</strong></span>
                </div>
                @if($invoice->discount_amount > 0)
                <div class="amount-row">
                    <span>Discount Applied:</span>
                    <span>-${{ number_format($invoice->discount_amount, 2) }}</span>
                </div>
                @endif
                @if($invoice->tax_amount > 0)
                <div class="amount-row">
                    <span>Tax (8.5%):</span>
                    <span>${{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                @endif
                <div class="amount-row total">
                    <span><strong>TOTAL AMOUNT DUE:</strong></span>
                    <span><strong>${{ number_format($invoice->total_amount, 2) }}</strong></span>
                </div>
            </div>
            
            @if($invoice->notes)
            <div class="invoice-details">
                <h3>Additional Information</h3>
                <p><strong>Notes:</strong> {{ $invoice->notes }}</p>
            </div>
            @endif
            
            <div class="invoice-details">
                <h3>Payment Instructions</h3>
                <p><strong>Payment Methods Accepted:</strong></p>
                <p>• Credit Card (Visa, MasterCard, American Express)</p>
                <p>• Bank Transfer (ACH)</p>
                <p>• Check (Payable to Education Management Systems)</p>
                <p>• Online Payment Portal: www.educationmanagement.com/pay</p>
                <p><strong>Reference:</strong> Please include invoice number #{{ $invoice->invoice_number }} with your payment.</p>
            </div>
        </div>
        
        <div class="footer">
            <h4>EDUCATION MANAGEMENT SYSTEMS</h4>
            <p>123 Education Boulevard | Learning District, LD 12345</p>
            <p>Phone: (555) 123-4567 | Email: billing@educationmanagement.com</p>
            <p>Website: www.educationmanagement.com</p>
            <p><strong>Thank you for choosing our educational services!</strong></p>
            <p>This is an official invoice. Please retain for your records.</p>
        </div>
    </div>
    
    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
