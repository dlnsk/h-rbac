@extends(config('h-rbac.permissionsUI.baseLayout'))

@section('header')
Разрешения для {{ $user->fullname }}
@endsection

@section('content')
  <div class="alert alert-warning">
    <h5><i class="icon fas fa-exclamation-triangle"></i> Внимание!</h5>
    <div>{{ $api_result->getMessage() }}</div>
    <div>Невозможно прочитать список ролей пользователя.</div>
  </div>
@endsection
