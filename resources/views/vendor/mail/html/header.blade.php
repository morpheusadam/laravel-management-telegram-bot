<tr>
<td class="header">
<h1 style="text-align:center;margin-top: 30px;">
	@if (trim($slot) === 'Laravel')
	<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
	@else
	{{ $slot }}
	@endif
</h1>
</td>
</tr>
