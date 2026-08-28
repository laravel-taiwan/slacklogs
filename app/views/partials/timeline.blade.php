<ul>
    @foreach($timeline as $year => $months)
        <li class="timeline-year">
            <a href="#">{{ $year }}</a>
            <ul>
                @foreach($months as $month => $days)
                    <li class="timeline-month">
                        <a href="#">{{ \Carbon\CarbonImmutable::create(2012, (int) $month, 1)->format('F') }}</a>
                        <ul>
                            @foreach($days as $day => $date)
                                @if (\Illuminate\Support\Str::contains(url()->full(), $date->format('Y-m-d')) or $date->isToday())
                                    <li class="timeline-day current">
                                @else
                                    <li class="timeline-day">
                                @endif
                                    <a href="{{ url("/$chan/" . $date->format('Y-m-d')) }}">
                                        {{ $date->format('d l') }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>
