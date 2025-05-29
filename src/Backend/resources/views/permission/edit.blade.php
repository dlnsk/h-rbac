@extends(config('h-rbac.permissionsUI.baseLayout'))

@section('header')
  <a href="{{ route('permissions.index', $user) }}">&lt;</a>
  @lang('h-rbac::permissions.permission') "@lang("h-rbac::permissions.$policy_name.$permission_name")"
@endsection

@section('content')
  <div class="card">
    <div class="card-body p-0">
      <table class="table">
        <thead>
        <tr>
          <th>@lang('h-rbac::permissions.kind')</th>
          <th>@lang('h-rbac::permissions.param')</th>
          <th class="text-right">@lang('h-rbac::permissions.actions')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($permissions as $permission)
          <tr>
            <td class="{{ ($permission->action == \Dlnsk\HierarchicalRBAC\Backend\Models\Permission::EXCLUDE ? 'text-danger' : '') }}">
              @lang("h-rbac::permissions.{$permission->action}d")
            </td>
            <td>{{ $permission->value }}</td>
            <td class="text-right">
              <form action="{{ route('permissions.destroy', ['user' => $user, 'permission' => $permission]) }}"
                    style="display: inline;"
                    method="POST">
                @method('DELETE')
                <button class="btn btn-sm btn-danger"
                        onclick="if(!confirm('@lang('h-rbac::permissions.sure')')) return false;"
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

        <label class="mr-2" for="perm-kind">@lang('h-rbac::permissions.kind')</label>
        <select name="action" class="custom-select mr-sm-2" id="perm-kind">
          <option selected>@lang('h-rbac::permissions.choose')</option>
          <option value="exclude">@lang('h-rbac::permissions.exclude')</option>
          <option value="include">@lang('h-rbac::permissions.include')</option>
        </select>

        @if(is_countable($params))
          <label class="mr-2" for="perm-value">@lang('h-rbac::permissions.value')</label>
          <select name="value" class="custom-select mr-sm-2" id="perm-value">
            <option selected disabled>@lang('h-rbac::permissions.choose')</option>
            @foreach($params as $key => $value)
              <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
          </select>
        @elseif(is_string($params))
          <label class="mr-2" for="perm-value">@lang('h-rbac::permissions.value')</label>
          <input name="value" type="{{ trim($params, '#_') }}" class="form-control mr-sm-2" id="perm-value">
        @endif

        <button type="submit" class="btn btn-success">@lang('h-rbac::permissions.add')</button>
      </form>
    </div>

  </div>
@endsection
