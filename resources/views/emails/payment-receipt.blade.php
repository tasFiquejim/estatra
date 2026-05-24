<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
</head>
<body>
    <h1>Payment Receipt</h1>
    <p>Dear {{ $payment->lease->tenant->first_name }},</p>
    <p>Thank you for your payment of {{ number_format($payment->amount_paid, 2) }}.</p>
    <p>Receipt Number: {{ $payment->receipt_number }}</p>
    <p>Date: {{ $payment->payment_date->format('M d, Y') }}</p>
</body>
</html>
