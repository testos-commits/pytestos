@extends('web.layout')

@section('content')
    <div class="pb-8">
        <div class="bg-white rounded-md px-4 py-4 mb-4">
            <h1 class="text-2xl font-bold mb-4 text-py-blue">Subscripiton status</h1>
            <p class="text-gray-500">You currently have a <span class='text-py-orange font-bold'>{{ $accountType }}</span>
            </p>
        </div>

        @foreach ($subscriptions as $subscription)
            <div class="bg-white rounded-md px-4 py-4 mb-4">
                <div class="flex flex-row justify-between items-center border-b pb-2 mb-4">
                    <div>
                        <h1 class="text-base font-bold text-py-blue">{{ $subscription->name }}</h1>
                        <p class=" text-sm text-py-orange">{{ $subscription->price_text }}</p>
                    </div>
                    @if (!$subscription->is_free)
                        @if ($subscription->is_valid)
                            <form action="{{ route('subscription.cancel', ['subscriptionId' => $subscription->id]) }}"
                                method="post">
                                @csrf
                                <button type="submit"
                                    class="bg-transparent border text-sm border-py-orange text-py-orange px-4 py-1 rounded-md ">Cancel</button>
                            </form>
                        @else
                            <a href="{{route('subscription.terms', ['subscriptionId' => $subscription->id])}}" class="!bg-py-orange text-white text-sm px-4 py-1 rounded-md ">Subscribe</a>
                        @endif
                    @endif

                </div>

                @foreach ($subscription->features as $feature)
                    <p class="text-gray-500 text-sm flex flex-row items-start pb-3"> @svg('heroicon-o-check-circle', ['class' => 'h-5 min-w-5 mr-1 text-py-orange'])
                        {{ $feature['feature'] }}</p>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
