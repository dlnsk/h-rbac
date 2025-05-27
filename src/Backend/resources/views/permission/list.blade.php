@extends(config('h-rbac.permissionsUI.baseLayout'))

@section('header')
  Разрешения для {{ $user->name }}
@endsection

@section('content')
  @include('h-rbac::permission._available')
@endsection
