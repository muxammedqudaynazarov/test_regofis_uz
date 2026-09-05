@extends('layouts.web')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    @php $name = json_decode($user->name); @endphp
                    <h1 class="font-weight-bold">
                        {{ $name?->short_name ?? "ID:{$user->id}" }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right text-sm">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Foydalanuvchilar</a></li>
                        <li class="breadcrumb-item active">Tahrirlash</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content text-sm">
        <div class="container-fluid">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">

                    {{-- Foydalanuvchi ma'lumotlari --}}
                    <div class="col-md-4">
                        <div class="card card-outline card-primary shadow-sm mb-3">
                            <div class="card-header">
                                <h6 class="card-title font-weight-bold mb-0">
                                    <i class="fas fa-user mr-2"></i>Ma'lumotlar
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                @if($user->picture)
                                    <img src="{{ $user->picture }}" alt="Rasm"
                                         class="img-circle mb-3"
                                         style="width:80px;height:80px;object-fit:cover;">
                                @else
                                    <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                         style="width:80px;height:80px;">
                                        <i class="fas fa-user fa-2x text-white"></i>
                                    </div>
                                @endif
                                <div class="font-weight-bold">{{ $name?->full_name ?? "ID:{$user->id}" }}</div>
                                <div class="text-muted small mb-2">HEMIS: {{ $user->hemis_id }}</div>
                                <span class="badge badge-primary">Joriy: {{ $user->current_role ?? '—' }}</span>
                            </div>
                            <div class="card-footer text-center">
                                @foreach($user->workplaces as $wp)
                                    <div class="small text-muted">
                                        <i class="fas fa-building mr-1"></i>
                                        {{ $wp->department->name ?? '—' }}
                                        @if($wp->is_main == '1')
                                            <span class="badge badge-success badge-xs">Asosiy</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Rollar --}}
                    <div class="col-md-4">
                        <div class="card card-outline card-success shadow-sm mb-3">
                            <div class="card-header">
                                <h6 class="card-title font-weight-bold mb-0">
                                    <i class="fas fa-user-tag mr-2"></i>Rollar
                                </h6>
                                <small class="text-muted">HEMIS rollari: {{ implode(', ', $user->hemis_roles ?? []) }}</small>
                            </div>
                            <div class="card-body">
                                @foreach($allRoles as $role)
                                    <div class="icheck-primary mb-2">
                                        <input type="checkbox"
                                               name="roles[]"
                                               id="role_{{ $role->id }}"
                                               value="{{ $role->name }}"
                                               {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                        <label for="role_{{ $role->id }}">
                                            <span class="font-weight-bold">{{ $role->name }}</span>
                                            @if($role->desc)
                                                <span class="text-muted"> — {{ $role->desc }}</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- To'g'ridan-to'g'ri permissionlar --}}
                    <div class="col-md-4">
                        <div class="card card-outline card-warning shadow-sm mb-3">
                            <div class="card-header">
                                <h6 class="card-title font-weight-bold mb-0">
                                    <i class="fas fa-key mr-2"></i>Qo'shimcha ruxsatlar
                                </h6>
                                <small class="text-muted">Rol bermaydigan alohida ruxsatlar</small>
                            </div>
                            <div class="card-body" style="max-height:400px; overflow-y:auto;">
                                @foreach($allPerms as $perm)
                                    <div class="icheck-warning mb-1">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               id="perm_{{ $perm->id }}"
                                               value="{{ $perm->name }}"
                                               {{ in_array($perm->name, $userPerms) ? 'checked' : '' }}>
                                        <label for="perm_{{ $perm->id }}" class="small">
                                            {{ $perm->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="fas fa-save mr-2"></i> Saqlash
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-default ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Orqaga
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
