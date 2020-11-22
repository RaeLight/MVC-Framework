@extends('layouts.master')
@section('title', $title)
@section('content')
<div>
	Content<br><br>
	
	Time: @time<br><br>
	
	@{{ time() }}<br><br>
	
	@each('user.card', $users, $user, 'user.empty')
	
	<br>
</div>
@endsection