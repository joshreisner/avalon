@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.users_create') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('UserController@index')=>Lang::get('avalon::messages.users'),
		Lang::get('avalon::messages.users_create'),
		)) }}

	{{ Former::horizontal_open()->action(URL::action('UserController@store')) }}
	
	{{ Former::text('firstname')
		->label(Lang::get('avalon::messages.users_firstname'))
		->class('required')
		}}
	
	{{ Former::text('lastname')
		->label(Lang::get('avalon::messages.users_lastname'))
		->class('required')
		}}
	
	{{ Former::text('email')
		->label(Lang::get('avalon::messages.users_email'))
		->class('email required')
		}}
	
	{{ Former::select('role')
		->options($roles, 3)
		->label(Lang::get('avalon::messages.users_role'))
		}}
	
	{{ Former::actions()
		->primary_submit(Lang::get('avalon::messages.site_save'))
		->link(Lang::get('avalon::messages.site_cancel'), URL::action('UserController@index'))
		}}
	
	{{ Former::close() }}
		
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.users_form_help') }}</p>
@endsection