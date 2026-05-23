<p>Hello,</p>
<p>Thank you for your payment of <strong>${{ number_format($payment->amount_paid, 2) }}</strong>.</p>
<p>Receipt Number: {{ $payment->receipt_number }}</p>
<p>Payment Date: {{ $payment->payment_date->format('Y-m-d') }}</p>
