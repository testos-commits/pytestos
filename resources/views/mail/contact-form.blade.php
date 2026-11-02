<div>
    <p><strong>From:</strong> {{ $name }}</p>
    <p><strong>Email:</strong> {{ $email }}</p>
    @if (!is_null($phone))
        <p> <strong>Phone:</strong> {{ $phone }}</p>
    @endif

    <p><strong>Message:</strong></p>
    <p>{{ $content }}</p>
</div>
