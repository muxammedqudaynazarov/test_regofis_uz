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
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" style="text-transform: uppercase"
                                       placeholder="Xodim bo‘yicha qidirish (masalan: 346221100092 / 1568 / F.I.Sh.)..."
                                       value="@if($search){{ $search }}@endif" autofocus="">
                                <span class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Qidirish
                                    </button>
                                </span>
                            </div>
                        </form>
                    </div>

                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover text-center mb-0">
                            <thead class="bg-light">
                            <tr>
                                <th style="width:5%" colspan="2">ID</th>
                                <th class="text-left">Xodimning F.I.Sh.</th>
                                <th style="width:120px">HEMIS ID</th>
                                <th style="width:130px">Joriy rol</th>
                                <th style="width:200px">Barcha rollar</th>
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
                                    <td class="text-muted align-middle">#{{ $user->id }}</td>
                                    <td class="align-middle" style="width: 1%;">
                                        <img src="{{ $user->picture }}"
                                             style="width: 40px; height: 40px; object-fit: cover" alt=""
                                             class="img-circle">
                                    </td>
                                    <td class="text-left font-weight-bold align-middle">
                                        <div>
                                            {{ $fullName }}
                                        </div>
                                        <div class="small text-muted">
                                            @foreach($user->workplaces->take(1) as $wp)
                                                {{ $wp->department->name ?? '—' }}
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <code class="text-xs">{{ $user->hemis_id }}</code>
                                    </td>
                                    <td class="align-middle">
                                        @if($user->current_role)
                                            <span class="badge badge-primary px-2">
                                                {{ $user->current_role }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @foreach($user->roles as $role)
                                            <span
                                                class="badge badge-{{ $role->name === $user->current_role ? 'success' : 'secondary' }} mr-1">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="align-middle">
                                        @can('users.view')
                                            <a href="{{ route('users.show', $user->id) }}"
                                               class="btn btn-outline-primary btn-sm" title="Tahrirlash">
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
