@extends('layouts.construct')

@section('page_subtitle')
#{{ $chan }}
@stop

@section('search-input')
<input name="search" value="{{ isset($search) ? $search : null }}" class="header-search"
       action="{{ URL::to($chan . '/search/') }}" contenteditable placeholder="Search PHP Taiwan Slack logs @yield('page_subtitle')" autofocus autocomplete="off"
       spellcheck="false">
@stop

@section('content')
    <ul class="logs" data-first-log="{{ isset($firstLog) && $firstLog ? "#log-$firstLog->_id" : '' }}">
        @include('partials.logs')
    </ul>
@stop

@section('channel')
    <aside class="sidebar">
        @include('partials.channel')
    </aside>
@stop

@section('timeline')
    <aside class="timeline">
        @include('partials.timeline')
    </aside>
@stop
