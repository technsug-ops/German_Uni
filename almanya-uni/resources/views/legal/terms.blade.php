@extends('layouts.app')

@section('title', __('Terms of Use') . ' — ' . brand('name'))

<x-seo
    :title="__('Terms of Use') . ' — ' . brand('name')"
    :description="__('Terms of use, liability limits and content policy of the AlmanyaUni platform.')" />

@section('content')

<section class="bg-gradient-to-br from-primary-700 to-primary-900 text-white">
    <div class="max-w-3xl mx-auto px-4 py-12 md:py-16">
        <p class="text-sm uppercase tracking-wide text-primary-200 mb-3">{{ __('Legal Notice') }}</p>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-3">{{ __('Terms of Use') }}</h1>
        <p class="text-primary-100">{{ __('Last updated') }}: {{ $updated_at }}</p>
    </div>
</section>

<article class="max-w-3xl mx-auto px-4 py-12 prose prose-lg max-w-none
                prose-headings:font-extrabold prose-headings:text-gray-900
                prose-h2:text-2xl prose-h2:mt-10 prose-h2:mb-4
                prose-p:text-gray-700 prose-p:leading-relaxed
                prose-li:text-gray-700
                prose-a:text-primary-700 prose-a:no-underline hover:prose-a:underline
                prose-strong:text-gray-900">

    <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-r-lg mb-8 not-prose">
        <p class="text-sm text-gray-700 leading-relaxed">
            <strong class="text-amber-900">{{ __('Summary') }}:</strong>
            {!! __('AlmanyaUni is an information platform, <strong>not an official application channel</strong>. We try to keep our content current, but for final application decisions you should always verify with <strong>the university\'s official pages + DAAD + the relevant German embassy or consulate</strong>.') !!}
        </p>
    </div>

    <h2>1. {{ __('Definition of the Platform') }}</h2>
    <p>
        {!! __('AlmanyaUni is a <strong>public-benefit</strong> digital platform offering information, comparison and community services for international students who want to pursue higher education in Germany.') !!}
    </p>
    <p>{{ __('Services offered') }}:</p>
    <ul>
        <li>{{ __('University and program database (search, filter, compare)') }}</li>
        <li>{{ __('Blog posts and guides') }}</li>
        <li>{{ __('Community forum (peer-to-peer Q&A)') }}</li>
        <li>{{ __('Tools (cost-of-living calculator, grade converter, university recommendation)') }}</li>
        <li>{{ __('FAQ archive') }}</li>
        <li>{{ __('Optional email newsletter') }}</li>
    </ul>

    <h2>2. {{ __('Acceptance') }}</h2>
    <p>
        {{ __('By using the Platform you accept these terms. If you do not agree to the terms, please do not use the Platform. If you are under 18, parental consent is required.') }}
    </p>

    <h2>3. {{ __('Accuracy of Content — Important Notice') }}</h2>
    <p>
        {!! __('All content on AlmanyaUni is compiled from <strong>official sources</strong> (Wikidata, DAAD, Bundesagentur für Arbeit, HRK, university websites). Nevertheless:') !!}
    </p>
    <ul>
        <li>{!! __('<strong>No warranty:</strong> information may be out of date; advertised fees/deadlines may change') !!}</li>
        <li>{!! __('<strong>Final source:</strong> always check the university\'s official pages + DAAD before applying') !!}</li>
        <li>{!! __('<strong>Visa information:</strong> verify the latest official status with the German embassy or consulate competent for you') !!}</li>
        <li>{!! __('<strong>Calculators:</strong> tools such as the cost-of-living estimator are indicative and do not replace personal budgeting') !!}</li>
    </ul>

    <h2>4. {{ __('User Account') }}</h2>
    <ul>
        <li>{{ __('You must sign up with a real email address') }}</li>
        <li>{{ __('Keep your password safe, do not share it with anyone') }}</li>
        <li>{{ __('You are responsible for all activity carried out under your account') }}</li>
        <li>{{ __('You can delete your account at any time:') }} <a href="mailto:{{ $contact_email }}?subject=Account%20deletion">{{ $contact_email }}</a></li>
        <li>{{ __('We reserve the right to temporarily suspend an account in case of suspicious activity') }}</li>
    </ul>

    <h2>5. {{ __('Community Rules (Forum)') }}</h2>
    <p>{!! __('The following behaviour is <strong>prohibited</strong> on the forum:') !!}</p>
    <ul>
        <li>{{ __('Insults, harassment, discrimination (race, religion, gender, nationality, etc.)') }}</li>
        <li>{{ __('Advertising, spam, multiple accounts') }}</li>
        <li>{{ __('Spreading misleading or false information (especially about visas and application procedures)') }}</li>
        <li>{{ __('Copyright infringement (university logos, photos, paywalled content)') }}</li>
        <li>{{ __('Requesting personal contact details (phone, address)') }}</li>
        <li>{{ __('Paid consultancy advertising (outside Platform approval)') }}</li>
        <li>{{ __('Phishing, fraud, promotion of fake visa or Sperrkonto intermediaries') }}</li>
        <li>{{ __('Illegal content') }}</li>
    </ul>
    <p>
        {{ __('In case of a rule violation the message is deleted and the user is warned; repeated violations lead to temporary or permanent bans.') }}
        {{ __('To report:') }} <a href="mailto:{{ $contact_email }}?subject=Forum%20report">{{ $contact_email }}</a>
    </p>

    <h2>6. {{ __('Content Rights (Copyright)') }}</h2>
    <h3>6.1 {{ __('Platform content') }}</h3>
    <p>
        {!! __('Blog posts, guides, descriptions and visuals <strong>authored by AlmanyaUni</strong> are published under the <strong>Creative Commons BY-NC 4.0</strong> licence. This means:') !!}
    </p>
    <ul>
        <li>{!! __('You <strong>may share</strong> them with attribution (a link back)') !!}</li>
        <li>{!! __('You <strong>may use</strong> them for educational purposes') !!}</li>
        <li>{!! __('<strong>Commercial use is prohibited</strong> (contact us for permission)') !!}</li>
    </ul>

    <h3>6.2 {{ __('Third-party data') }}</h3>
    <p>
        {!! __('Third-party data such as university names, program descriptions, DAAD data and Wikidata content are subject to their own original licences. Wikidata = CC-0, DAAD = open data, university pages = the respective university\'s copyright policy.') !!}
    </p>

    <h3>6.3 {{ __('User content') }}</h3>
    <p>
        {!! __('You <strong>retain the copyright</strong> in the messages you share on the forum. However you grant the Platform:') !!}
    </p>
    <ul>
        <li>{{ __('The right to publish, display and archive your message (perpetually, worldwide)') }}</li>
        <li>{{ __('The right to use it for anonymised statistical purposes') }}</li>
        <li>{{ __('The right to attribute it as a source if a third party quotes it') }}</li>
    </ul>
    <p>{{ __('You can delete your message at any time.') }}</p>

    <h2>7. {{ __('Fees and Advertising Policy') }}</h2>
    <ul>
        <li>{!! __('The Platform <strong>is free to use</strong> and we commit to keep it free') !!}</li>
        <li>{!! __('Revenue sources: advertising (AdSense), affiliate links (Expatrio, Fintiba, etc.), <strong>optional</strong> premium membership, consultancy services') !!}</li>
        <li>{!! __('Affiliate links <strong>do not add any extra cost</strong> to the user; AlmanyaUni receives a commission') !!}</li>
        <li>{{ __('Advertising slots are clearly marked as "Advertisement"') }}</li>
    </ul>

    <h2>8. {{ __('Limitation of Liability') }}</h2>
    <p>{!! __('AlmanyaUni is <strong>not responsible</strong> for:') !!}</p>
    <ul>
        <li>{{ __('The content of third-party sites linked to from the Platform') }}</li>
        <li>{{ __('University application outcomes (acceptance/rejection)') }}</li>
        <li>{{ __('Visa application outcomes') }}</li>
        <li>{{ __('Discussions between users on the forum') }}</li>
        <li>{{ __('Decisions taken based on misinterpreted or outdated content') }}</li>
        <li>{{ __('Temporary outages of the Platform (maintenance, server issues)') }}</li>
        <li>{{ __('Services provided by affiliate partners (Expatrio, etc.)') }}</li>
    </ul>
    <p>
        {!! __('<strong>In any case</strong> AlmanyaUni\'s liability is limited to the amount, if any, paid by the user for the Platform in the previous 12 months.') !!}
    </p>

    <h2>9. {{ __('Changes to the Service') }}</h2>
    <p>
        {{ __('AlmanyaUni reserves the right to evolve the Platform, add or remove content, and change pricing. Significant changes are announced to newsletter subscribers.') }}
    </p>

    <h2>10. {{ __('Account Closure') }}</h2>
    <p>{{ __('Your account may be suspended or closed in the following cases:') }}</p>
    <ul>
        <li>{{ __('Violation of community rules') }}</li>
        <li>{{ __('Spam or fraud attempts') }}</li>
        <li>{{ __('Sharing illegal content') }}</li>
        <li>{{ __('Multiple accounts held by the same person (abuse)') }}</li>
    </ul>
    <p>
        {{ __('You may also request deletion of your account at any time by') }} <a href="mailto:{{ $contact_email }}?subject=Account%20deletion">{{ __('email') }}</a>
        {{ __('(within the scope of your data-protection deletion rights).') }}
    </p>

    <h2>11. {{ __('Governing Law and Dispute Resolution') }}</h2>
    @if (app()->getLocale() === 'tr')
        <p>
            {!! __('These terms are governed by <strong>Turkish law</strong>. In case of a dispute the parties shall first attempt mutual negotiation, then mediation. If no resolution is reached, the courts of Istanbul, Republic of Türkiye shall have jurisdiction.') !!}
        </p>
    @else
        <p>
            {{ __('These terms are governed by the law of the operator\'s jurisdiction. In case of a dispute the parties shall first attempt mutual negotiation, then mediation. If no resolution is reached, the competent courts of that jurisdiction shall have authority.') }}
        </p>
    @endif
    <p>
        {{ __('For users located in the EU the rights granted under GDPR remain reserved.') }}
    </p>

    <h2>12. {{ __('Contact') }}</h2>
    <p>
        {{ __('For questions, feedback or legal notices:') }}
        <a href="mailto:{{ $contact_email }}" class="text-primary-700 font-semibold">{{ $contact_email }}</a>
    </p>

    <p class="text-sm text-gray-500 mt-12 pt-6 border-t border-gray-200">
        {{ __('These terms were published on :date.', ['date' => $updated_at]) }}
        {{ __('Related:') }} <a href="{{ route('legal.privacy') }}">{{ __('Privacy Policy') }}</a> ·
        <a href="{{ route('legal.cookies') }}">{{ __('Cookie Policy') }}</a>
    </p>

</article>

@endsection
