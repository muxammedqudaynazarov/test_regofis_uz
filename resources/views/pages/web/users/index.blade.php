@extends('layouts.web')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="font-weight-bold">Foydalanuvchilar</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right text-sm">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                        <li class="breadcrumb-item active">Foydalanuvchilar</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content text-sm">
        <div class="container-fluid">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header bg-light">
                    <form action="{{ route('users.index') }}" method="GET"
                          class="d-flex flex-wrap align-items-center" style="gap:8px;">
                        {{-- Qidiruv turi --}}
                        <select name="search_type" class="form-control form-control-sm"
                                style="width:140px;">
                            <option value="name"     {{ $searchType === 'name'     ? 'selected' : '' }}>
                                F.I.Sh. bo'yicha
                            </option>
                            <option value="id"       {{ $searchType === 'id'       ? 'selected' : '' }}>
                                ID bo'yicha
                            </option>
                            <option value="hemis_id" {{ $searchType === 'hemis_id' ? 'selected' : '' }}>
                                HEMIS ID bo'yicha
                            </option>
                        </select>

                        {{-- Qidiruv matni --}}
                        <input type="text"
                               name="search"
                               class="form-control form-control-sm"
                               placeholder="Qidirish..."
                               value="{{ $search }}"
                               style="max-width:280px;">

                        <button class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i> Qidirish
                        </button>

                        @if($search)
                            <a href="{{ route('users.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-times text-danger"></i> Tozalash
                            </a>
                        @endif
                    </form>
                </div>

                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover text-center mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width:5%">ID</th>
                                <th class="text-left">F.I.Sh.</th>
                                <th style="width:120px">HEMIS ID</th>
                                <th style="width:130px">Joriy rol</th>
                                <th style="width:200px">Barcha rollar</th>
                                <th class="text-left">Kafedra</th>
                                <th style="width:60px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @php
                                    $nameObj  = json_decode($user->name);
                                    $fullName = $nameObj?->full_name  ?? "ID:{$user->id}";
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $user->id }}</td>
                                    <td class="text-left font-weight-bold">{{ $fullName }}</td>
                                    <td><code class="text-xs">{{ $user->hemis_id }}</code></td>
                                    <td>
                                        @if($user->current_role)
                                            <span class="badge badge-primary px-2">
                                                {{ $user->current_role }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-{{ $role->name === $user->current_role ? 'success' : 'secondary' }} mr-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="text-left">
                                        @foreach($user->workplaces->take(1) as $wp)
                                            <small class="text-muted">{{ $wp->department->name ?? '—' }}</small>
                                        @endforeach
                                    </td>
                                    <td>
                                        @can('users.view')
                                        <a href="{{ route('users.show', $user->id) }}"
                                           class="btn btn-outline-primary btn-xs" title="Tahrirlash">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-users fa-2x d-block mb-2"></i>
                                        Foydalanuvchilar topilmadi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
