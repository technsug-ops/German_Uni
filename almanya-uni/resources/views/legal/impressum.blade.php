@extends('layouts.app')

@section('title', __('Imprint') . ' — ' . brand('name'))

<x-seo
    :title="__('Imprint') . ' — ' . brand('name')"
    :description="__('Legal disclosure according to § 5 TMG and § 18 MStV: operator, address, contact, register, tax info for :brand.', ['brand' => brand('name')])"
/>

<x-json-ld :data="[
    '@context' => 'https://schema.org',
    '@type' => 'AboutPage',
    'name' => __('Imprint'),
    'url' => route('legal.impressum'),
    'publisher' => [
        '@type' => 'Organization',
        'name' => $company,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $street,
            'postalCode' => '61440',
            'addressLocality' => 'Oberursel (Taunus)',
            'addressCountry' => 'DE',
        ],
        'telephone' => $phone,
        'email' => $email,
        'vatID' => $vat_id,
    ],
]" />

@section('content')

<section class="bg-gradient-to-br from-primary-700 to-primary-900 text-white">
    <div class="max-w-3xl mx-auto px-4 py-12 md:py-16">
        <p class="text-sm uppercase tracking-wide text-primary-200 mb-3">{{ __('Legal Notice') }}</p>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3">{{ __('Imprint') }}</h1>
        <p class="text-primary-100">{{ __('Last updated:') }} {{ $updated_at }}</p>
    </div>
</section>

<article class="max-w-3xl mx-auto px-4 py-12 prose prose-lg max-w-none
                prose-headings:font-extrabold prose-headings:text-gray-900
                prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                prose-p:text-gray-700 prose-p:leading-relaxed
                prose-a:text-primary-700 prose-a:no-underline hover:prose-a:underline
                prose-strong:text-gray-900">

    <div class="bg-primary-50 border-l-4 border-primary-500 p-5 rounded-r-lg mb-8 not-prose">
        <p class="text-sm text-gray-700 leading-relaxed">
            <strong class="text-primary-900">{{ __('Legal disclosure') }}:</strong>
            {{ __('Information according to § 5 TMG (Telemediengesetz) and § 18 MStV (Medienstaatsvertrag), Germany.') }}
        </p>
    </div>

    <h2>{{ __('Operator (§ 5 TMG)') }}</h2>
    <div class="not-prose bg-white border border-gray-200 rounded-xl p-5 mb-6">
        <p class="font-bold text-gray-900 mb-1">{{ $company }}</p>
        <p class="text-gray-700 leading-relaxed">
            {{ $street }}<br>
            {{ $postal_city }}<br>
            {{ $country }}
        </p>
    </div>

    <h2>{{ __('Management') }}</h2>
    <p>{{ __('Managing Director') }}: <strong>{{ $manager }}</strong></p>

    <h2>{{ __('Contact') }}</h2>
    <ul>
        <li>{{ __('Phone') }}: <a href="tel:{{ preg_replace('/[^+0-9]/', '', $phone) }}">{{ $phone }}</a></li>
        <li>{{ __('Email') }}: <a href="mailto:{{ $email }}">{{ $email }}</a></li>
        <li>{{ __('Website') }}: <a href="{{ $website }}" rel="noopener">{{ $website }}</a></li>
    </ul>

    <h2>{{ __('Commercial Register') }}</h2>
    <ul>
        <li>{{ __('Registry court') }}: {{ $register_court }}</li>
        <li>{{ __('Registration number') }}: {{ $register_number }}</li>
    </ul>

    <h2>{{ __('Tax Information') }}</h2>
    <ul>
        <li>{{ __('VAT identification number (USt-IdNr.) according to § 27 a UStG') }}: <strong>{{ $vat_id }}</strong></li>
        <li>{{ __('Tax number') }}: {{ $tax_number }}</li>
    </ul>

    <h2>{{ __('Responsible for content (§ 18 Abs. 2 MStV)') }}</h2>
    <p>{{ $responsible_content }}<br>
    {{ $street }}, {{ $postal_city }}, {{ $country }}</p>

    <h2>{{ __('EU Online Dispute Resolution') }}</h2>
    <p>{!! __('The European Commission provides a platform for online dispute resolution (OS): <a href=":url" rel="noopener noreferrer">:url</a>. We are not obligated and not willing to participate in dispute resolution proceedings before a consumer arbitration board.', ['url' => 'https://ec.europa.eu/consumers/odr']) !!}</p>

    <h2>{{ __('Liability for content') }}</h2>
    <p>{{ __('As a service provider, we are responsible for our own content on these pages according to general laws (§ 7 Abs. 1 TMG). According to §§ 8 to 10 TMG, we are not obliged to monitor transmitted or stored third-party information or to investigate circumstances that indicate illegal activity. Obligations to remove or block the use of information under general law remain unaffected.') }}</p>

    <h2>{{ __('Liability for links') }}</h2>
    <p>{{ __('Our offer contains links to external websites of third parties on whose contents we have no influence. Therefore we cannot assume any liability for these external contents. The respective provider or operator of the pages is always responsible for the contents of the linked pages.') }}</p>

    <h2>{{ __('Copyright') }}</h2>
    <p>{{ __('The contents and works created by the site operators on these pages are subject to German copyright law. Duplication, processing, distribution and any kind of exploitation outside the limits of copyright require the written consent of the respective author or creator.') }}</p>

    <p class="text-sm text-gray-500 mt-10">
        {{ __('Sources: Imprint generator templates, § 5 TMG, § 18 MStV.') }}
    </p>

</article>

@endsection
