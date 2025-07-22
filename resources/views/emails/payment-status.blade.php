@component('mail::message')
    # @if($statusType === 'success') @lang('moneyfusion.email_heading_success') @else @lang('moneyfusion.email_heading_failure') @endif

    @if($statusType === 'success')
        @lang('moneyfusion.email_body_success', ['name' => $transaction->nom_client, 'amount' => number_format($transaction->total_price, 2)])
    @else
        @lang('moneyfusion.email_body_failure', ['name' => $transaction->nom_client, 'amount' => number_format($transaction->total_price, 2)])
    @endif

    **@lang('moneyfusion.invoice_id'):** {{ $transaction->token_pay }}
    **@lang('moneyfusion.amount'):** {{ number_format($transaction->total_price, 2) }}
    **@lang('moneyfusion.invoice_status'):** {{ ucfirst($transaction->status) }}
    **@lang('moneyfusion.invoice_date'):** {{ $transaction->created_at->format('d/m/Y H:i') }}

    @if($statusType === 'success')
        @component('mail::button', ['url' => route('moneyfusion.invoices.show', $transaction)])
            @lang('moneyfusion.email_view_transaction')
        @endcomponent
    @endif

    @lang('moneyfusion.email_regards'),<br>
    {{ config('moneyfusion.billing.company_name') }}
@endcomponent