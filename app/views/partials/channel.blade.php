<ul>
    <li class="title">
        <a href="#">All Channels</a>
        <ul>
            @foreach($channels as $index => $channel)
                @if ($channel->name == $chan)
                    <li class="sub-title current">
                @else
                    <li class="sub-title">
                @endif
                    <a href="{{ URL::to($channel->name) }}">
                        {{ $channel->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
</ul>
