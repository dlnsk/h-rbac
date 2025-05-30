<div class="card">
  <div class="card-body p-0">
    <table class="table">
      <thead>
      <tr>
        <th>@lang('h-rbac::permissions.permission')</th>
        <th>@lang('h-rbac::permissions.role')</th>
        <th>@lang('h-rbac::permissions.kind')</th>
        <th>@lang('h-rbac::permissions.params')</th>
        <th class="text-right">@lang('h-rbac::permissions.actions')</th>
      </tr>
      </thead>
      <tbody>
        @foreach($available_permissions as $policyName => $chains)
          <tr><td class="text-white bg-secondary disabled" colspan="5">{{ __("h-rbac::permissions.$policyName._description") }}</td></tr>
          @foreach($chains as $chainName => $chain)
            @if(is_array($chain))
              <tr><td data-chain="{{ $chainName }}" colspan="5">{{ __("h-rbac::permissions.$policyName.$chainName") }}</td></tr>
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
