<div class="card">
  <div class="card-body p-0">
    <table class="table">
      <thead>
      <tr>
        <th>Разрешение</th>
        <th>Роль</th>
        <th>Вид</th>
        <th>Параметры</th>
        <th class="text-right">Действия</th>
      </tr>
      </thead>
      <tbody>
        @foreach($available_permissions as $policyName => $chains)
          <tr><td class="bg-secondary disabled" colspan="5">{{ __("permissions.$policyName._description") }}</td></tr>
          @foreach($chains as $chainName => $chain)
            @if(is_array($chain))
              <tr><td data-chain="{{ $chainName }}" colspan="5">{{ __("permissions.$policyName.$chainName") }}</td></tr>
              @foreach($chain as $ring)
                @include('h-rbac::permission._row', ['policy' => $policyName, 'permissionName' => $ring, 'withSpacer' => true])
              @endforeach
            @else
              @include('h-rbac::permission._row', ['policy' => $policyName, 'permissionName' => $chain, 'withSpacer' => false])
            @endif
          @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
</div>
