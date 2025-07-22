@extends(config('moneyfusion.views.layout'))

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Paiement avec MoneyFusion</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Erreur de paiement!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>

                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('moneyfusion.pay') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="totalPrice">
                    @lang('moneyfusion.payment_form_total_price')
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="totalPrice" type="number" name="totalPrice" placeholder="200" value="{{ old('totalPrice', 200) }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="article">
                    Articles (JSON)
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="article" name="article" rows="4" placeholder='[{"sac": 100}, {"chaussure": 100}]' required>{{ old('article', '[{"sac": 100}, {"chaussure": 100}]') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Exemple: `[{"sac": 100}, {"chaussure": 100}]`</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="numeroSend">
                    Numéro de Téléphone
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="numeroSend" type="text" name="numeroSend" placeholder="01010101" value="{{ old('numeroSend', '01010101') }}" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nomclient">
                    Nom du Client
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nomclient" type="text" name="nomclient" placeholder="John Doe" value="{{ old('nomclient', 'John Doe') }}" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="personal_Info">
                    Infos Personnelles (JSON, optionnel)
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="personal_Info" name="personal_Info" rows="2" placeholder='[{"userId": 1, "orderId": 123}]'>{{ old('personal_Info', '[{"userId": 1, "orderId": 123}]') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Exemple: `[{"userId": 1, "orderId": 123}]`</p>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Payer avec MoneyFusion
                </button>
            </div>
        </form>
    </div>
@endsection