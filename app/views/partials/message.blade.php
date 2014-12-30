@if ($log->subtype == 'channel_join')
    {{ $log->getUser() }} join channal..
@elseif ($log->subtype == 'channel_leave')
    {{ $log->getUser() }} leave channal..
@elseif ($log->subtype and ! $log->user)
    <i>{{ Helpers::parseText($log->text) }}</i>
@else
<span class="log-entry-username">{{ $log->getUser() }}</span> : {{ Helpers::parseText($log->text) }}
@endif
