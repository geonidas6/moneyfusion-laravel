<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Transactions MoneyFusion</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Liste des Transactions</h1>

        @if($transactions->isEmpty())
            <p class="text-center text-gray-600">Aucune transaction trouvée.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Type</th>
                            <th class="py-2 px-4 border-b text-left">Statut</th>
                            <th class="py-2 px-4 border-b text-left">Montant</th>
                            <th class="py-2 px-4 border-b text-left">Numéro</th>
                            <th class="py-2 px-4 border-b text-left">Client</th>
                            <th class="py-2 px-4 border-b text-left">Moyen</th>
                            <th class="py-2 px-4 border-b text-left">Date</th>
                            <th class="py-2 px-4 border-b text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50" id="transaction-{{ $transaction->id }}">
                                <td class="py-2 px-4 border-b">{{ $transaction->id }}</td>
                                <td class="py-2 px-4 border-b">{{ ucfirst($transaction->type) }}</td>
                                <td class="py-2 px-4 border-b status-cell">{{ ucfirst($transaction->status) }}</td>
                                <td class="py-2 px-4 border-b">{{ number_format($transaction->montant, 0, '', ' ') }}</td>
                                <td class="py-2 px-4 border-b">{{ $transaction->numero_send }}</td>
                                <td class="py-2 px-4 border-b">{{ $transaction->nom_client }}</td>
                                <td class="py-2 px-4 border-b">{{ $transaction->moyen }}</td>
                                <td class="py-2 px-4 border-b">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-2 px-4 border-b">
                                    <button
                                        class="check-status-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs"
                                        data-id="{{ $transaction->id }}"
                                        data-type="{{ $transaction->type }}"
                                    >
                                        Vérifier Statut
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.check-status-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const transactionId = this.dataset.id;
                    const transactionType = this.dataset.type;
                    const statusCell = this.closest('tr').querySelector('.status-cell');

                    statusCell.textContent = 'Vérification...';
                    this.disabled = true;

                    fetch(`/transactions/${transactionId}/check-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ type: transactionType })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            statusCell.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                        } else if (data.error) {
                            statusCell.textContent = 'Erreur';
                            alert(data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        statusCell.textContent = 'Erreur réseau';
                        alert('Une erreur est survenue lors de la vérification du statut.');
                    })
                    .finally(() => {
                        this.disabled = false;
                    });
                });
            });
        });
    </script>
</body>
</html>
