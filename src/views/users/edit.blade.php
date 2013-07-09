@extends('avalon::template')

@section('title')
	{{ Lang::get('avalon::messages.users_edit') }}
@endsection

@section('main')

	{{ Breadcrumbs::leave(array(
		URL::action('UserController@index')=>Lang::get('avalon::messages.users'),
		Lang::get('avalon::messages.users_edit'),
		)) }}

	{{ Former::horizontal_open()->action(URL::action('UserController@update', $user->id))->method('put') }}
	
	{{ Former::text('firstname')
		->label(Lang::get('avalon::messages.users_firstname'))
		->value($user->firstname)
		->class('required')
		}}
	
	{{ Former::text('lastname')
		->label(Lang::get('avalon::messages.users_lastname'))
		->value($user->lastname)
		->class('required')
		}}
	
	{{ Former::text('email')
		->label(Lang::get('avalon::messages.users_email'))
		->value($user->email)
		->class('email required')
		}}
	
	{{ Former::select('role')
		->options($roles, $user->role)
		->label(Lang::get('avalon::messages.users_role'))
		}}
	
	{{ Former::actions()
		->primary_submit(Lang::get('avalon::messages.site_save'))
		->link(Lang::get('avalon::messages.site_cancel'), URL::action('UserController@index'))
		}}
	
	{{ Former::close() }}
		
@endsection

@section('side')
	<p>{{ Lang::get('avalon::messages.users_edit_help') }}</p>
@endsection