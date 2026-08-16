@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@php
    $siteLogo = setting('logo') ?: setting('favicon');
    $siteName = setting('site_name', 'Konut.Update');
@endphp
@if ($siteLogo)
<img src="{{ url(Storage::url($siteLogo)) }}" alt="{{ $siteName }}" class="logo" style="display: block; width: auto; height: auto; max-width: 200px; max-height: 75px; margin: 15px auto 10px auto;">
@elseif (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
