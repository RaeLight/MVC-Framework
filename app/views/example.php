@extends('layouts.master')
@section('title', $title)
@section('content')
<div>
	Content<br>
	@time<br>

@for($i = 0;$i <= 5;$i++)
	@continue($i == 0)
	<b>{{ $i }}</b>. Turn<br>
	@if($i == 1)
	<h3>ONE</h3>
	@elseif($i == 3)
	<h3>THRI</h3>
	@else
	<h3>:{{ $i }}:</h3>
	@endif
	
	@break($i == 4)
@endfor
	@php $tarr = []; @endphp

	@isset($title)
	$title is isset and value: {{ $title }}<br>
	@endisset
	
	@empty($tarr)
	$tarr is empty
	@endempty
	
	<br> var arr = @json($arr);<br><br>

@forelse($arr as $key => $val)
	<b>{{ $key }}</b> => {{ $val }}<br>
@empty
	<b>Empty</b>
@endforelse
	
</div>
@endsection