@extends('layouts.app')

@section('title', 'Bakım — ' . brand('name'))

@push('meta')
    <meta name="robots" content="noindex">
@endpush

@section('content')

<section class="min-h-[70vh] bg-gradient-to-br from-amber-50 via-white to-yellow-50 flex items-center">
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">

        <div class="text-7xl md:text-9xl mb-4">🛠️</div>

        <h1 class="text-2xl md:text-4xl font-extrabold text-gray-900 mb-3">Sitemiz bakımda</h1>
        <p class="text-gray-600 text-lg max-w-xl mx-auto mb-8">
            AlmanyaUni şu anda bakımda. Birkaç dakika içinde tekrar erişilebilir olacak.
        </p>

        <p class="text-sm text-gray-500">
            Acil bir durumda:
            <a href="mailto:technsug@gmail.com" class="text-primary-600 hover:underline">technsug@gmail.com</a>
        </p>
    </div>
</section>

@endsection
