@extends(config('h-rbac.permissionsUI.baseLayout'))

@section('header')
  @lang('h-rbac::permissions.permissions_for', ['user' => $user->name])
@endsection

@section('content')
  <div class="alert alert-secondary" role="alert">
    @lang('h-rbac::permissions.user_roles'): {{ implode(', ', $user_roles) }}
  </div>

  @include('h-rbac::permission._available')
@endsection
