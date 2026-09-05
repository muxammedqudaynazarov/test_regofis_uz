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
                    <form action="{{ route('users.index') }}" method="GET" class="d-flex" style="gap:8px;">
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="F.I.Sh. yoki HEMIS ID bo'yicha qidirish..."
                               value="{{ request('search') }}" style="max-width:350px;">
                        <button class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                        @if(request('search'))
                            <a href="{{ route('users.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-times text-danger"></i>
                            </a>
                        @endif
                    </form>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover text-center mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width:5%">#</th>
                                <th class="text-left">F.I.Sh.</th>
                                <th>HEMIS ID</th>
                                <th>Joriy rol</th>
                                <th>Barcha rollar</th>
                                <th>Kafedra</th>
                                <th style="width:80px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @php
                                    $name = json_decode($user->name);
                                    $fullName = $name?->full_name ?? "ID:{$user->id}";
                                    $shortName = $name?->short_name ?? "ID:{$user->id}";
                                @endphp
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td class="text-left">
                                        <div class="font-weight-bold">{{ $fullName }}</div>
                                        @if($user->picture)
                                            <small class="text-muted">
                                                <i class="fas fa-image"></i> Rasm mavjud
                                            </small>
                                        @endif
                                    </td>
                                    <td><code>{{ $user->hemis_id }}</code></td>
                                    <td>
                                        @if($user->current_role)
                                            <span class="badge badge-primary">{{ $user->current_role }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-{{ $role->name === $user->current_role ? 'success' : 'secondary' }}">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($user->workplaces->take(1) as $wp)
                                            <small>{{ $wp->department->name ?? '—' }}</small>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('users.show', $user->id) }}"
                                           class="btn btn-outline-primary btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
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
