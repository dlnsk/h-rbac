@extends(config('h-rbac.permissionsUI.baseLayout'))

@section('header')
  Разрешение "{{ __("permissions.$policy_name.$permission_name") }}"
@endsection

@section('content')
  <div class="card">
    <div class="card-body p-0">
      <table class="table">
        <thead>
        <tr>
          <th>Вид</th>
          <th>Параметр</th>
          <th class="text-right">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($permissions as $permission)
          <tr>
            <td class="{{ ($permission->action == \Dlnsk\HierarchicalRBAC\Backend\Models\Permission::EXCLUDE ? 'text-danger' : '') }}">
              {{ __("permissions.$permission->action") }}
            </td>
            <td>{{ $permission->value }}</td>
            <td class="text-right">
              <form action="{{ route('permissions.destroy', ['user' => $user, 'permission' => $permission]) }}"
                    style="display: inline;"
                    method="POST">
                @method('DELETE')
                <button class="btn btn-sm btn-danger"
                        onclick="if(!confirm('Точно?')) return false;"
                        type="submit"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                  </svg>
                </button>
              </form>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      <form action="{{ route('permissions.store', ['user' => $user, 'permission' => $permission_name]) }}"
            method="POST"
            class="form-inline"
      >
        @csrf
        <input name="name" type="hidden" value="{{ $permission_name }}">

        <label class="mr-2" for="perm-kind">Вид</label>
        <select name="action" class="custom-select mr-sm-2" id="perm-kind">
          <option selected>Выберите...</option>
          <option value="exclude">Изъять</option>
          <option value="include">Предоставить</option>
        </select>

        @if(is_countable($params))
          <label class="mr-2" for="perm-value">Значение</label>
          <select name="value" class="custom-select mr-sm-2" id="perm-value">
            <option selected disabled>Выберите...</option>
            @foreach($params as $param)
              <option value="{{ $param }}">{{ $param }}</option>
            @endforeach
          </select>
        @elseif(is_string($params))
          <label class="mr-2" for="perm-value">Значение</label>
          <input name="value" type="{{ trim($params, '#_') }}" class="form-control mr-sm-2" id="perm-value">
        @endif

        <button type="submit" class="btn btn-success">Добавить</button>
      </form>
    </div>

  </div>
@endsection
