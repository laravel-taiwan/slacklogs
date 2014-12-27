<ul>
    <li class="all">
        <a href="#">All Channels</a>
        <ul>
            @foreach($channels as $index => $channel)
                @if ($channel->name == $chan)
                    <li class="title current">
                @else
                    <li class="title">
                @endif
                    <a href="{{ URL::to($channel->name) }}">
                        {{ $channel->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
</ul>
