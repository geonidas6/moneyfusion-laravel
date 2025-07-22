<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture MoneyFusion - {{ $invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 100%;
            margin: auto;
            padding: 20px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .details div {
            width: 48%; /* Adjust for flex */
            vertical-align: top;
            display: inline-block;
        }
        .details strong {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }
        .total td {
            border: none;
            padding-top: 10px;
        }
        .text-right { text-align: right; }
        .mt-4 { margin-top: 1rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Facture</h1>
        <p>{{ $company['company_name'] }}</p>
        <p>{{ $company['company_address'] }}<br>{{ $company['company_city_zip'] }}, {{ $company['company_country'] }}</p>
        <p>Email: {{ $company['company_email'] }} | Téléphone: {{ $company['company_phone'] }}</p>
    </div>

    <div class="details">
        <div>
            <strong>Facturé à:</strong>
            <p>{{ $transaction->nom_client }}</p>
            <p>Numéro de téléphone: {{ $transaction->numero_send }}</p>
            @if($transaction->user)
                <p>ID Client: {{ $transaction->user->id }}</p>
            @endif
            @if($transaction->personal_info)
                @foreach($transaction->personal_info as $info)
                    <p>{{ json_encode($info) }}</p>
                @endforeach
            @endif
        </div>
        <div class="text-right">
            <strong>Détails de la Facture:</strong>
            <p>Numéro de Facture: {{ $invoice_number }}</p>
            <p>Date: {{ $invoice_date }}</p>
            <p>Date de Transaction: {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
            <p>Statut du Paiement: {{ ucfirst($transaction->status) }}</p>
            @if($transaction->transaction_number)
                <p>Numéro de Transaction MoneyFusion: {{ $transaction->transaction_number }}</p>
            @endif
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th class="text-right">Prix Unitaire</th>
            <th class="text-right">Quantité</th>
            <th class="text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @php
            $subtotal = 0;
        @endphp
        @foreach ($transaction->articles as $article)
            @foreach ($article as $description => $price)
                <tr>
                    <td>{{ ucfirst($description) }}</td>
                    <td class="text-right">{{ number_format($price, 2) }}</td>
                    <td class="text-right">1</td> {{-- Supposons quantité 1 si pas spécifié --}}
                    <td class="text-right">{{ number_format($price, 2) }}</td>
                </tr>
                @php
                    $subtotal += $price;
                @endphp
            @endforeach
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" class="text-right">Sous-total:</td>
            <td class="text-right">{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">Frais de Transaction:</td>
            <td class="text-right">{{ number_format($transaction->fees, 2) }}</td>
        </tr>
        <tr class="total">
            <td colspan="3" class="text-right">Montant Total Payé:</td>
            <td class="text-right">{{ number_format($transaction->total_price, 2) }}</td>
        </tr>
        </tfoot>
    </table>

    <div class="footer mt-4">
        <p>Merci pour votre achat !</p>
        <p>Généré le: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</div>
</body>
</html>