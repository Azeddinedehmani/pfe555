<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .invoice-header .logo {
            flex: 1;
        }
        .invoice-header .invoice-info {
            flex: 1;
            text-align: right;
        }
        .invoice-header .logo img {
            max-width: 200px;
            max-height: 80px;
        }
        .invoice-info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .pharmacy-info, .client-info {
            flex: 1;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th {
            padding: 10px;
            background: #4a90e2;
            color: white;
            text-align: left;
        }
        .invoice-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .invoice-table .total-row td {
            border-top: 2px solid #ddd;
            font-weight: bold;
        }
        .invoice-total {
            margin-top: 20px;
            text-align: right;
        }
        .invoice-total table {
            width: 300px;
            margin-left: auto;
        }
        .invoice-total table td {
            padding: 5px;
        }
        .invoice-total table .total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #4a90e2;
        }
        .invoice-notes {
            margin-top: 30px;
            padding: 10px;
            background: #f8f8f8;
            border-left: 4px solid #4a90e2;
        }
        .invoice-footer {
            margin-top: 30px;
            text-align: center;
            color: #888;
            font-size: 12px;
        }
        .payment-info {
            margin-top: 20px;
            padding: 10px;
            background: #f8f8f8;
        }
        .status-paid {
            color: #5cb85c;
            font-weight: bold;
        }
        .status-unpaid {
            color: #d9534f;
            font-weight: bold;
        }
        .status-partial {
            color: #f0ad4e;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- En-tête de la facture -->
        <div class="invoice-header">
            <div class="logo">
                <img src="{{ $pharmacy['logo'] }}" alt="{{ $pharmacy['name'] }}">
            </div>
            <div class="invoice-info">
                <h2>FACTURE</h2>
                <p><strong>Facture n°:</strong> {{ $invoice_number }}</p>
                <p><strong>Date:</strong> {{ $date }}</p>
                <p><strong>Date d'échéance:</strong> {{ $due_date }}</p>
            </div>
        </div>
        
        <!-- Informations de contact -->
        <div class="invoice-info-section">
            <div class="pharmacy-info">
                <h3>Informations pharmacie</h3>
                <p><strong>{{ $pharmacy['name'] }}</strong></p>
                <p>{{ $pharmacy['address'] }}</p>
                <p>Téléphone: {{ $pharmacy['phone'] }}</p>
                <p>Email: {{ $pharmacy['email'] }}</p>
                @if($pharmacy['tax_id'])
                <p>ID Fiscal: {{ $pharmacy['tax_id'] }}</p>
                @endif
            </div>
            <div class="client-info">
                <h3>Facturé à</h3>
                <p><strong>{{ $client['name'] }}</strong></p>
                @if($client['address'])
                <p>{{ $client['address'] }}</p>
                @endif
                @if($client['phone'])
                <p>Téléphone: {{ $client['phone'] }}</p>
                @endif
                @if($client['email'])
                <p>Email: {{ $client['email'] }}</p>
                @endif
            </div>
        </div>
        
        <!-- Détails de la facture -->
        <div class="invoice-details">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Remise</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item['description'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ number_format($item['price'], 2) }} DH</td>
                        <td>{{ number_format($item['discount'], 2) }} DH</td>
                        <td>{{ number_format($item['total'], 2) }} DH</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Totaux -->
        <div class="invoice-total">
            <table>
                <tr>
                    <td>Sous-total:</td>
                    <td>{{ number_format($subtotal, 2) }} DH</td>
                </tr>
                @if($discount > 0)
                <tr>
                    <td>Remise:</td>
                    <td>{{ number_format($discount, 2) }} DH</td>
                </tr>
                @endif
                @if($tax > 0)
                <tr>
                    <td>TVA:</td>
                    <td>{{ number_format($tax, 2) }} DH</td>
                </tr>
                @endif
                <tr class="total">
                    <td>Total:</td>
                    <td>{{ number_format($total, 2) }} DH</td>
                </tr>
            </table>
        </div>
        
        <!-- Informations de paiement -->
        <div class="payment-info">
            <p><strong>Mode de paiement:</strong> {{ $payment_method }}</p>
            <p>
                <strong>Statut du paiement:</strong> 
                <span class="status-{{ strtolower($sale->payment_status) }}">{{ $payment_status }}</span>
            </p>
            <p><strong>Vendeur:</strong> {{ $seller }}</p>
        </div>
        
        <!-- Notes -->
        @if($notes)
        <div class="invoice-notes">
            <h3>Notes</h3>
            <p>{{ $notes }}</p>
        </div>
        @endif
        
        <!-- Pied de page -->
        <div class="invoice-footer">
            <p>Merci pour votre confiance!</p>
            <p>{{ $pharmacy['name'] }} - {{ $pharmacy['address'] }}</p>
        </div>
    </div>
</body>
</html>