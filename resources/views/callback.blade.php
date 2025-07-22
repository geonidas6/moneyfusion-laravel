@extends(config('moneyfusion.views.layout'))

@section('content')
    <div class="container mx-auto p-4">
        @if ($status === 'paid')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">@lang('moneyfusion.callback_status_success_title')</strong>
                <p class="mt-2">@lang('moneyfusion.callback_status_success_message')</p>
                <p>@lang('moneyfusion.transaction_number'): <strong>{{ $transaction['numeroTransaction'] ?? 'N/A' }}</strong></p>
                <p>Montant: <strong>{{ $transaction['Montant'] ?? 'N/A' }}</strong></p>
                <p>Moyen de paiement: <strong>{{ $transaction['moyen'] ?? 'N/A' }}</strong></p>
            </div>
        @elseif ($status === 'pending')
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Paiement en attente!</strong>
                <p class="mt-2">Votre paiement est en cours de traitement. Veuillez patienter.</p>
            </div>
        @elseif ($status === 'failure' || $status === 'no paid' || $status === 'cancelled')
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Paiement échoué!</strong>
                <p class="mt-2">Il y a eu un problème avec votre paiement: {{ $message ?? 'Échec inconnu.' }}</p>
            </div>
        @else
            <div class="bg-gray-100 border border-gray-400 text-gray-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Statut Inconnu</strong>
                <p class="mt-2">Le statut de votre paiement est inconnu. Veuillez vérifier vos transactions ou contacter le support.</p>
            </div>
        @endif

        <a href="/" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Retour à l'accueil
        </a>
    </div>
@endsection