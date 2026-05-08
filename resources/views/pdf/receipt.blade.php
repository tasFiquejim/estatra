<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Receipt #{{ $payment->receipt_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #ffffff;
            padding: 40px;
        }

        /* ── Header ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 24px;
            border-bottom: 2px solid #2563eb;
            margin-bottom: 28px;
        }
        .brand-name {
            font-size: 24px;
            font-weight: 700;
            color: #2563eb;
            letter-spacing: -0.5px;
        }
        .brand-tagline {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }
        .receipt-badge {
            text-align: right;
        }
        .receipt-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .receipt-number {
            font-size: 13px;
            color: #2563eb;
            font-weight: 600;
            margin-top: 4px;
        }
        .receipt-date {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── Status banner ── */
        .status-banner {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 28px;
        }
        .status-paid {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .status-partial {
            background: #fefce8;
            border: 1px solid #fef08a;
        }
        .status-unpaid {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        .dot-paid { background: #22c55e; }
        .dot-partial { background: #eab308; }
        .dot-unpaid { background: #ef4444; }
        .status-text {
            font-size: 13px;
            font-weight: 600;
        }
        .paid-text { color: #16a34a; }
        .partial-text { color: #a16207; }
        .unpaid-text { color: #dc2626; }

        /* ── Info Grid ── */
        .info-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 28px;
        }
        .info-box {
            flex: 1;
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .info-box-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 100px;
            font-size: 11px;
            color: #64748b;
            flex-shrink: 0;
        }
        .info-value {
            font-size: 11px;
            font-weight: 600;
            color: #1e293b;
        }

        /* ── Amount Table ── */
        .amount-section {
            margin-bottom: 28px;
        }
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #94a3b8;
            margin-bottom: 10px;
        }
        table.amount-table {
            width: 100%;
            border-collapse: collapse;
        }
        .amount-table thead tr {
            background: #2563eb;
            color: #ffffff;
        }
        .amount-table thead th {
            padding: 10px 14px;
            font-size: 11px;
            text-align: left;
            font-weight: 600;
        }
        .amount-table thead th:last-child {
            text-align: right;
        }
        .amount-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        .amount-table tbody tr:last-child {
            border-bottom: none;
        }
        .amount-table tbody td {
            padding: 10px 14px;
            font-size: 12px;
            color: #334155;
        }
        .amount-table tbody td:last-child {
            text-align: right;
            font-weight: 600;
        }
        .amount-table tfoot tr {
            background: #f1f5f9;
        }
        .amount-table tfoot td {
            padding: 12px 14px;
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }
        .amount-table tfoot td:last-child {
            text-align: right;
            color: #2563eb;
            font-size: 15px;
        }

        /* ── Notes ── */
        .notes-box {
            padding: 12px 16px;
            background: #fafafa;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 28px;
        }
        .notes-box p {
            font-size: 11px;
            color: #64748b;
            line-height: 1.6;
        }

        /* ── Footer ── */
        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-note {
            font-size: 10px;
            color: #94a3b8;
        }
        .footer-brand {
            font-size: 11px;
            font-weight: 700;
            color: #2563eb;
        }
        .watermark {
            position: fixed;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72px;
            font-weight: 900;
            color: rgba(37,99,235,0.04);
            text-transform: uppercase;
            letter-spacing: 8px;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body>

    <div class="watermark">Estatra</div>

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="brand-name">Estatra</div>
            <div class="brand-tagline">Property & Tenant Management</div>
        </div>
        <div class="receipt-badge">
            <div class="receipt-title">Receipt</div>
            <div class="receipt-number">#{{ $payment->receipt_number }}</div>
            <div class="receipt-date">Issued: {{ $payment->payment_date->format('F j, Y') }}</div>
        </div>
    </div>

    {{-- Status Banner --}}
    @php
        $statusValue = $payment->status->value;
        $bannerClass = match($statusValue) {
            'paid'    => 'status-paid',
            'partial' => 'status-partial',
            default   => 'status-unpaid',
        };
        $dotClass = match($statusValue) {
            'paid'    => 'dot-paid',
            'partial' => 'dot-partial',
            default   => 'dot-unpaid',
        };
        $textClass = match($statusValue) {
            'paid'    => 'paid-text',
            'partial' => 'partial-text',
            default   => 'unpaid-text',
        };
    @endphp
    <div class="status-banner {{ $bannerClass }}">
        <span class="status-dot {{ $dotClass }}"></span>
        <span class="status-text {{ $textClass }}">
            Payment Status: {{ ucfirst($statusValue) }}
        </span>
    </div>

    {{-- Property & Tenant Info --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-box-title">Property Details</div>
            <div class="info-row">
                <span class="info-label">Property</span>
                <span class="info-value">{{ $payment->lease->unit->property->property_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Address</span>
                <span class="info-value">{{ $payment->lease->unit->property->address }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Unit</span>
                <span class="info-value">{{ $payment->lease->unit->unit_name }}</span>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title">Tenant Details</div>
            <div class="info-row">
                <span class="info-label">Name</span>
                <span class="info-value">{{ $payment->lease->tenant->first_name }} {{ $payment->lease->tenant->last_name }}</span>
            </div>
            @if($payment->lease->tenant->email)
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $payment->lease->tenant->email }}</span>
            </div>
            @endif
            @if($payment->lease->tenant->phone)
            <div class="info-row">
                <span class="info-label">Phone</span>
                <span class="info-value">{{ $payment->lease->tenant->phone }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Payment Breakdown --}}
    <div class="amount-section">
        <div class="section-title">Payment Breakdown</div>
        <table class="amount-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Rent Period</th>
                    <th>Method</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Monthly Rent — Unit {{ $payment->lease->unit->unit_name }}</td>
                    <td>{{ $payment->rent_period->format('F Y') }}</td>
                    <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method->value)) }}</td>
                    <td>৳{{ number_format($payment->lease->rent_amount, 2) }}</td>
                </tr>
                @if($payment->lease->service_charge)
                <tr>
                    <td>Service Charge</td>
                    <td>{{ $payment->rent_period->format('F Y') }}</td>
                    <td>—</td>
                    <td>৳{{ number_format($payment->lease->service_charge, 2) }}</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Total Amount Paid</td>
                    <td>৳{{ number_format($payment->amount_paid, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Notes --}}
    @if($payment->notes)
    <div class="notes-box">
        <div class="section-title" style="margin-bottom:6px;">Notes</div>
        <p>{{ $payment->notes }}</p>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-note">
            This is a computer-generated receipt and does not require a signature.<br>
            Generated on {{ now()->format('F j, Y \a\t h:i A') }}
        </div>
        <div class="footer-brand">Estatra</div>
    </div>

</body>
</html>
