<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Effectuer un retrait MoneyFusion</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Effectuer un retrait</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Succès !</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Erreur !</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('moneyfusion.payout.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="montant" class="block text-gray-700 text-sm font-bold mb-2">Montant :</label>
                <input type="number" name="montant" id="montant" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ex: 5000" required>
            </div>
            <div>
                <label for="numero" class="block text-gray-700 text-sm font-bold mb-2">Numéro de téléphone :</label>
                <input type="text" name="numero" id="numero" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ex: 01010101" required>
            </div>
            <div>
                <label for="moyen" class="block text-gray-700 text-sm font-bold mb-2">Moyen de paiement :</label>
                <select name="moyen" id="moyen" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="orange">Orange</option>
                    <option value="mtn">MTN</option>
                    <option value="moov">Moov</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                Retirer
            </button>
        </form>
    </div>
</body>
</html>