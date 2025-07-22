@extends(config('moneyfusion.views.layout'))

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Détails de la Transaction #{{ $transaction->token_pay }}</h1>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <p class="block text-gray-700 text-sm font-bold mb-2">ID de Transaction:</p>
                <p>{{ $transaction->token_pay }}</p>
            </div>
            <div class="mb-4">
                <p class="block text-gray-700 text-sm font-bold mb-2">Statut:</p>
                <p>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($transaction->status == 'paid') bg-green-100 text-green-800
                        @elseif($transaction->status == 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </p>
            </div>
            <div class="mb-4">
                <p class="block text-gray-700 text-sm font-bold mb-2">Montant Total:</p>
                <p>{{ number_format($transaction->total_price, 2) }}</p>
            </div>
            <div class="mb-4">
                <p class="block text-gray-700 text-sm font-bold mb-2">Client:</p>
                <p>{{ $transaction->nom_client }} ({{ $transaction->numero_send }})</p>
            </div>
            <div class="mb-4">
                <p class="block text-gray-700 text-sm font-bold mb-2">Date de la Transaction:</p>
                <p>{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
            </div>

            @if ($transaction->articles)
                <div class="mb-4">
                    <p class="block text-gray-700 text-sm font-bold mb-2">Articles:</p>
                    <ul class="list-disc ml-5">
                        @foreach ($transaction->articles as $article)
                            <li>{{ json_encode($article) }}</li> {{-- Afficher de manière plus lisible si le format est connu --}}
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($transaction->status === 'paid')
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('moneyfusion.invoices.download', $transaction) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Télécharger Facture PDF
                    </a>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('moneyfusion.invoices.index') }}" class="inline-block text-blue-500 hover:text-blue-800">Retour à la liste des transactions</a>
            </div>
        </div>
    </div>
@endsection