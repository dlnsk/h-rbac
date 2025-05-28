@extends(config('h-rbac.permissionsUI.baseLayout'))

@section('header')
  @lang('h-rbac::permissions.permissions_for', ['user' => $user->name])
@endsection

@section('content')
  @include('h-rbac::permission._available')
@endsection
