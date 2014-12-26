{{-- Infinite-scroll up link --}}
@if (isset($moreup))
    <a href="/{{ $chan }}/infinite/up/{{ $moreup }}" class="infinite-more-link-up"></a>
@endif

{{-------------------------------- Log entry --------------------------------}}

@foreach ($logs as $log)

    {{-- Date and day --}}
    @if (!isset($lastLog) || $lastLog->getCarbon()->day != $log->getCarbon()->day)
        <li class='logs-day'>{{ $log->getDay() }}</li>
    @endif
    <?php $lastLog = $log ?>

    {{-- The log entry --}}
    <a href="/{{ $chan }}/{{ $log->getUrl() }}" class="logs-nav{{ isset($search) ? ' search-entry' : '' }}">

        {{-- Entry username, hour and message --}}
        <li class="log-entry new-log log-entry-{{ $log->subtype }}" data-url="{{ $log->getUrl() }}" id="log-{{ $log->_id }}">
            <span class="log-entry-time">
                {{ $log->getHour() }}
            </span>
            @include('partials.message')
        </li>
    </a>
@endforeach

{{-- Infinite-scroll down link --}}
@if (isset($moredown))
    <a href="/{{ $chan }}/infinite/down/{{ $moredown }}" class="infinite-more-link-down"></a>
@endif
