@extends('layouts.web')
@section('style')
    <style>
        .perm-item {
            transition: background 0.15s;
            border-radius: 6px;
            padding: 4px 8px;
        }

        .perm-item:hover {
            background: #f8f9fa;
        }

        .perm-item.via-role label {
            color: #6c757d;
        }

        .perm-item.via-role input {
            accent-color: #6c757d;
        }

        .perm-item.direct-perm label {
            color: #28a745;
            font-weight: 600;
        }

        .perm-item.direct-perm input {
            accent-color: #28a745;
        }

        .perm-search {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
            padding-bottom: 6px;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    @php $nameObj = json_decode($user->name); @endphp
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">
                            {{ $nameObj?->short_name ?? "ID:{$user->id}" }}
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
                @can('users.update')
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-outline card-primary shadow-sm mb-3">
                                    <div class="card-header pb-2">
                                        <h6 class="card-title font-weight-bold mb-0">
                                            <i class="fas fa-user mr-1"></i> Foydalanuvchi ma’lumotlari
                                        </h6>
                                    </div>
                                    <div class="card-body text-center py-3">
                                        @if($user->picture)
                                            <img src="{{ $user->picture }}" alt="Rasm"
                                                 class="img-circle mb-2 shadow"
                                                 style="width:72px;height:72px;object-fit:cover;">
                                        @else
                                            <div class="bg-secondary rounded-circle mx-auto mb-2 d-flex
                                                align-items-center justify-content-center"
                                                 style="width:72px;height:72px;">
                                                <i class="fas fa-user fa-2x text-white"></i>
                                            </div>
                                        @endif
                                        <div class="font-weight-bold">
                                            {{ $nameObj?->full_name ?? "ID:{$user->id}" }}
                                        </div>
                                        <div class="text-muted"><code>HEMIS: {{ $user->hemis_id }}</code></div>
                                        <div class="badge badge-primary px-3">
                                            Joriy rol: {{ $user->current_role ?? '—' }}
                                        </div>
                                    </div>
                                    <div class="card-footer text-left py-2">
                                        @foreach($user->workplaces as $wp)
                                            <div class="small text-muted mb-1">
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $wp->department->name ?? '—' }}
                                                @if($wp->head_type === 'department')
                                                    <span class="badge badge-info badge-xs">mudir</span>
                                                @endif
                                                @if($wp->is_main == '1')
                                                    <span class="badge badge-success badge-xs">asosiy</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card card-outline card-success shadow-sm mb-3">
                                    <div class="card-header pb-2">
                                        <div class="card-title mb-0">
                                            <div class="font-weight-bold">
                                                <i class="fas fa-user-tag mr-1"></i> Rollar
                                            </div>
                                            <div class="text-muted small">
                                                Hozirgi rollari: {{ implode(', ', $user->hemis_roles_array) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        @foreach($allRoles as $role)
                                            <div class="icheck-primary mb-2">
                                                <input type="checkbox"
                                                       name="roles[]"
                                                       id="role_{{ $role->id }}"
                                                       value="{{ $role->name }}"
                                                       @can('users.update') @else disabled @endcan
                                                    {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                                <label for="role_{{ $role->id }}">
                                                    <span class="font-weight-bold">{{ $role->name }}</span>
                                                    @if($role->desc)
                                                        <br><span class="text-muted small">{{ $role->desc }}</span>
                                                    @endif
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-outline card-warning shadow-sm mb-3">
                                    <div class="card-header pb-2">
                                        <div class="card-title mb-0">
                                            <div class="font-weight-bold">
                                                <i class="fas fa-key mr-1"></i> Qo‘shimcha ruxsatlar biriktirish
                                            </div>
                                            <div class="d-flex flex-wrap mt-1 small" style="gap:6px;">
                                                <div class="badge badge-success px-2">
                                                    <i class="fas fa-check mr-1"></i> qo‘lda berilgan
                                                </div>
                                                <div class="badge badge-secondary px-2">
                                                    <i class="fas fa-shield-alt mr-1"></i> o‘zgartirib bo‘lmaydi
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        <div class="perm-search mb-2">
                                            <input type="text"
                                                   id="perm-search"
                                                   class="form-control form-control-sm"
                                                   placeholder="Ruxsat nomini qidiring...">
                                        </div>
                                        <div style="max-height:380px; overflow-y:auto;" id="perms-list">
                                            @foreach($allPerms as $perm)
                                                @php
                                                    $isDirect = in_array($perm->name, $directPermNames);
                                                    $isViaRole = in_array($perm->name, $rolePermNames);
                                                @endphp
                                                <div
                                                    class="perm-item {{ $isDirect ? 'direct-perm' : ($isViaRole ? 'via-role' : '') }} mb-1 perm-row"
                                                    data-name="{{ $perm->name }}">
                                                    @if($isViaRole && !$isDirect)
                                                        <input type="checkbox"
                                                               id="perm_{{ $perm->id }}"
                                                               checked disabled
                                                               title="Bu ruxsat '{{ implode(', ', array_filter($userRoles, fn($r) => \Spatie\Permission\Models\Role::findByName($r)->hasPermissionTo($perm->name))) }}' roli orqali berilgan">
                                                        <label for="perm_{{ $perm->id }}" class="mb-0 small"
                                                               title="Rol orqali: o'zgartirish uchun rolni tahrirlang">
                                                            <i class="fas fa-shield-alt text-secondary mr-1"
                                                               style="font-size:10px; vertical-align: top"></i>
                                                            <span
                                                                style="position: relative; top: -3px">{{ $perm->name }}</span>
                                                        </label>
                                                    @else
                                                        {{-- To'g'ridan-to'g'ri yoki umuman yo'q --}}
                                                        <input type="checkbox"
                                                               name="permissions[]"
                                                               id="perm_{{ $perm->id }}"
                                                               value="{{ $perm->name }}"
                                                               @can('users.update') @else disabled @endcan
                                                            {{ $isDirect ? 'checked' : '' }}>
                                                        <label for="perm_{{ $perm->id }}" class="mb-0 small">
                                                            @if($isDirect)
                                                                <i class="fas fa-check-circle text-success mr-1"
                                                                   style="font-size:10px"></i>
                                                            @endif
                                                            <span
                                                                style="position: relative; top: -3px">{{ $perm->name }}</span>
                                                        </label>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                    <i class="fas fa-save mr-2"></i> O‘zgarishlarni saqlab qolish
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-default ml-2">
                                    <i class="fas fa-arrow-left mr-1"></i> Bekor qilish va ortga qaytish
                                </a>
                            </div>
                        </div>
                    </form>
                @endcan
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('perm-search').addEventListener('input', function () {
            var val = this.value.toLowerCase();
            document.querySelectorAll('.perm-row').forEach(function (row) {
                row.style.display = row.dataset.name.includes(val) ? '' : 'none';
            });
        });
    </script>
@endsection
