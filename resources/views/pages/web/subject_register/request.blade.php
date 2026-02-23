@extends('layouts.web')

@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
            background-color: #6f42c1 !important;
            border-color: #643ab0 !important;
            color: #fff !important;
        }

        .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff !important;
        }

        .select2-custom {
            opacity: 0;
        }

        .table-custom th, .table-custom td {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="font-weight-bold">Fanlar ro‘yxati</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Fanlar ro‘yxati</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content mb-3 text-sm">
            <div class="container-fluid">
                <form action="{{ url()->current() }}" method="GET">
                    <div class="row">
                        <div class="col-12">
                            <input type="text"
                                   name="search"
                                   class="form-control shadow-sm"
                                   placeholder="Fan nomi bo'yicha qidirish (Enter bosing)..."
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-center table-custom">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th style="text-align: left; vertical-align: middle">Fan nomi</th>
                                <th>Kafedra</th>
                                <th>O‘quv rejalar</th>
                                <th>O‘quv yili</th>
                                <th>Semestrlar</th>
                                <th>Masul o‘qituvchilar</th>
                                <th class="text-right"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($subjects as $index => $lesson)
                                <tr>
                                    <td>#{{ $lesson->id }}</td>
                                    <td style="text-align: left; vertical-align: middle">
                                        <div class="font-weight-bold">{{ $lesson->subject->name }}</div>
                                        <div class="small">{{ $lesson->code }}</div>
                                    </td>
                                    <td>{{ $lesson->department->name }}</td>
                                    <td>{{ $lesson->curriculum->name }}</td>
                                    <td>
                                        <div class="badge badge-primary">{{ $lesson->curriculum->edu_year->name }}</div>
                                    </td>
                                    <td>
                                        <div class="badge badge-success">{{ $lesson->semester->name }}</div>
                                    </td>
                                    <td>
                                        @forelse($lesson->teachers as $teacher)
                                            <span class="badge bg-purple">
                                                 {{ json_decode($teacher->name)->short_name ?? $teacher->short_name }}
                                             </span>
                                        @empty
                                            <span class="text-muted small">O‘qituvchi biriktirilmagan</span>
                                        @endforelse
                                    </td>
                                    <td class="text-nowrap">
                                        @can('lessons.delete')
                                            <form action="{{ route('subjects-register.destroy', $lesson->id) }}"
                                                  method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                @if($lesson->request_delete == '5')
                                                    <input type="hidden" name="type" value="0">
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                            title="Qayta tiklash">
                                                        <i class="fas fa-trash-restore mr-1"></i> Tiklash
                                                    </button>
                                                @elseif($lesson->request_delete == '1')
                                                    <div class="input-group input-group-sm" style="width: 160px;">
                                                        <select name="type"
                                                                class="custom-select custom-select-sm border-danger"
                                                                required>
                                                            <option value="5" class="text-danger">O‘chirish</option>
                                                            <option value="0" class="text-success">Qaytarish</option>
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button type="submit"
                                                                    class="btn btn-danger btn-sm shadow-sm"
                                                                    title="Tasdiqlash">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Fanlar topilmadi.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div class="card-footer bg-white clearfix">
                            <div class="float-right">
                                {{ $subjects->links() }}
                            </div>
                            @if($subjects->total())
                                <div class="text-muted small mt-2">
                                    Jami: <b>{{ $subjects->total() }}</b> ta savoldan
                                    <b>{{ $subjects->firstItem() }}</b>-<b>{{ $subjects->lastItem() }}</b>
                                    ko'rsatilmoqda
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

