@if ($log->subtype == 'channel_join')
    {{ $log->getUser() }} join channal..
@elseif ($log->subtype == 'channel_leave')
    {{ $log->getUser() }} leave channal..
@elseif ($log->subtype and ! $log->user)
    <i>{{ \App\Support\Helpers::parseText($log->text) }}</i>
@else
<span class="log-entry-username">{{ $log->getUser() }}</span> : {{ \App\Support\Helpers::parseText($log->text) }}
@endif
