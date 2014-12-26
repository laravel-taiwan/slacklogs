@if ($log->subtype == 'channel_join')
    {{ $log->getUser() }} join channal..
@elseif ($log->subtype == 'channel_leave')
    {{ $log->getUser() }} leave channal..
@else
<span class="log-entry-username">{{ $log->getUser() }}</span> : {{{ $log->text }}}
@endif