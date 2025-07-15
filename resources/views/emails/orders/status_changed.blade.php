@component('mail::message')

<img src="{{ asset('logo.png') }}" alt="Vault 64 Logo" style="width:120px; margin-bottom: 16px;">

# Order Status Update

Hello **{{ $order->user->name }}**,  
We wanted to let you know that the status of your order **#{{ $order->order_number }}** has changed.

- **Previous status:** {{ ucfirst($oldStatus) }}
- **New status:** {{ ucfirst($newStatus) }}

@component('mail::button', ['url' => url(route('orders.show', $order->id))])
View Order
@endcomponent

If you have any questions, feel free to reply to this email or contact our support team.

Thanks for shopping with **Vault 64**!

@endcomponent 