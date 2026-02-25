@extends('layouts.web')

@section('style')
    <style>
        .table-custom th, .table-custom td {
            vertical-align: middle !important;
            font-size: 14px;
        }

        details summary {
            cursor: pointer;
            font-weight: bold;
            color: #007bff;
            outline: none;
        }

        details summary:hover {
            text-decoration: underline;
        }

        .json-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: pre-wrap; /* JSON chiroyli turishi uchun */
            margin-top: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="font-weight-bold">Tizim harakatlari (Loglar)</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Loglar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header bg-white mb-2">
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="small font-weight-bold mb-0">Foydalanuvchi F.I.Sh.</label>
                                    <input type="text" name="search" class="form-control form-control-sm"
                                           placeholder="F.I.Sh..." value="{{ request('search') }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="small font-weight-bold mb-0">Sana (dan)</label>
                                    <input type="date" name="date_from" class="form-control form-control-sm"
                                           value="{{ request('date_from') }}">
                                </div>

                                <div class="col-md-2">
                                    <label class="small font-weight-bold mb-0">Sana (gacha)</label>
                                    <input type="date" name="date_to" class="form-control form-control-sm"
                                           value="{{ request('date_to') }}">
                                </div>

                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-filter"></i> Saralash
                                    </button>
                                    @if(request()->anyFilled(['search', 'date_from', 'date_to']))
                                        <a href="{{ url()->current() }}" class="btn btn-sm btn-default text-danger">
                                            <i class="fas fa-times"></i> Tozalash
                                        </a>
                                    @endif
                                </div>

                                <div class="col-md-2 text-right">
                                    <button type="button" class="btn btn-sm btn-danger font-weight-bold w-100"
                                            onclick="if(confirm('Jurnalni tozalash?')) document.getElementById('clear-form').submit();">
                                        <i class="fas fa-trash-alt"></i> Jurnalni tozalash
                                    </button>
                                </div>
                            </div>
                        </form>

                        <form id="clear-form" action="{{ route('logs.destroy', 'clear') }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                    <div class="card card-outline card-primary shadow-sm m-0">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-custom text-center">
                                <thead class="bg-light">
                                <tr>
                                    <th style="width: 5%">ID</th>
                                    <th style="width: 7%">Sana</th>
                                    <th style="width: 20%">Foydalanuvchi</th>
                                    <th style="width: 15%">Harakat turi</th>
                                    <th style="width: 10%">Qaysi bo‘limda</th>
                                    <th style="width: 35%">Ma’lumotlar & o‘zgarishlar</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="text-center">{{ $log->id }}</td>
                                        <td>
                                            <div class="font-weight-bold">{{ $log->created_at->format('Y-m-d') }}</div>
                                            <div class="text-muted small">{{ $log->created_at->format('H:i:s') }}</div>
                                        </td>
                                        <td>
                                            @if($log->causer)
                                                <span
                                                    class="badge badge-info">{{ json_decode($log->causer->name)->full_name ?? 'Noma\'lum' }}</span>
                                            @else
                                                <span class="badge badge-secondary">Tizim avtomatik</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $color = 'secondary';
                                                if(str_contains(strtolower($log->description), 'created')) $color = 'success';
                                                if(str_contains(strtolower($log->description), 'updated')) $color = 'primary';
                                                if(str_contains(strtolower($log->description), 'deleted')) $color = 'danger';
                                            @endphp
                                            <span
                                                class="badge badge-{{ $color }}">{{ ucfirst($log->description) }}</span>
                                        </td>
                                        <td>
                                            @if($log->subject_type)
                                                <b>{{ class_basename($log->subject_type) }}</b> <br>
                                                <span class="text-muted small">ID: {{ $log->subject_id }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-left">
                                            @if(isset($log->properties['ip']))
                                                <div class="mb-1">
                                                    <small class="text-muted">
                                                        <i class="fas fa-globe"></i> {{ $log->properties['ip'] }} |
                                                        <i class="fas fa-desktop"></i> {{ \Illuminate\Support\Str::limit($log->properties['user_agent'] ?? '', 30) }}
                                                    </small>
                                                </div>
                                            @endif

                                            @if(isset($log->properties['old']) || isset($log->properties['attributes']))
                                                <details>
                                                    <summary><i class="fas fa-eye"></i> O‘zgarishlarni ko‘rish</summary>
                                                    <div class="row mt-2">
                                                        @if(isset($log->properties['old']))
                                                            <div class="col-md-6">
                                                                <strong class="text-danger small">Eski holati:</strong>
                                                                <div
                                                                    class="json-box text-danger">{{ json_encode($log->properties['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
                                                            </div>
                                                        @endif

                                                        @if(isset($log->properties['attributes']))
                                                            <div class="col-md-6">
                                                                <strong class="text-success small">Yangi
                                                                    holati:</strong>
                                                                <div
                                                                    class="json-box text-success">{{ json_encode($log->properties['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </details>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            Hozircha tizim loglari mavjud emas.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer bg-white clearfix">
                            <div class="float-right">
                                {{ $logs->links() }}
                            </div>
                            <div class="text-muted small mt-2">
                                Jami: <b>{{ $logs->total() }}</b> ta harakat,
                                sahifada <b>{{ $logs->firstItem() }}</b> dan <b>{{ $logs->lastItem() }}</b> gacha.
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
@endsection
