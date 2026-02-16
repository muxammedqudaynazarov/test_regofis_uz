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
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">Fanlarga o‘qituvchi biriktirish</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Biriktirish</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th style="text-align: left">Fan nomi</th>
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
                                    <td style="vertical-align: middle">#{{ $lesson->id }}</td>
                                    <td style="text-align: left; vertical-align: middle">
                                        <div class="font-weight-bold">
                                            {{ $lesson->subject->name }}
                                        </div>
                                        <div class="small">
                                            {{ $lesson->code }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle">
                                        {{ $lesson->department->name }}
                                    </td>
                                    <td style="vertical-align: middle">
                                        {{ $lesson->curriculum->name }}
                                    </td>
                                    <td style="vertical-align: middle">
                                        <div class="badge badge-primary">
                                            {{ $lesson->curriculum->edu_year->name }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle">
                                        <div class="badge badge-success">
                                            {{ $lesson->semester->name }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle">
                                        @forelse($lesson->teachers as $teacher)
                                            <span class="badge bg-purple">
                                                 {{ json_decode($teacher->name)->short_name ?? $teacher->short_name }}
                                             </span>
                                        @empty
                                            <span class="text-muted small">
                                                 O‘qituvchi biriktirilmagan
                                             </span>
                                        @endforelse
                                    </td>
                                    <td style="vertical-align: middle" class="text-right text-nowrap">
                                        <button type="button" style="font-size: 11px"
                                                class="btn btn-default btn-sm" data-toggle="modal"
                                                data-target="#assignModal{{ $lesson->id }}">
                                            <i class="fas fa-user-plus mr-1"></i>
                                            O‘qituvchi qo‘shish
                                        </button>
                                        <a href="#" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="assignModal{{ $lesson->id }}" tabindex="-1" role="dialog"
                                     aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('subjects-register.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="subject_id" value="{{ $lesson->id }}">
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title font-weight-bold">{{ $lesson->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>O'qituvchilarni tanlang</label>
                                                        <div class="select2-purple">
                                                            <select name="user_ids[]"
                                                                    class="form-control select2-custom"
                                                                    multiple="multiple"
                                                                    data-placeholder="O‘qituchini tanlang"
                                                                    data-dropdown-css-class="select2-purple"
                                                                    style="width: 100%;">
                                                                @foreach($users as $user)
                                                                    <option value="{{ $user->id }}"
                                                                        {{ $lesson->teachers->contains($user->id) ? 'selected' : '' }}>
                                                                        {{ json_decode($user->name)->full_name ?? $user->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer justify-content-between bg-light">
                                                    <button type="button" class="btn btn-default btn-sm"
                                                            data-dismiss="modal">Yopish
                                                    </button>
                                                    <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                                                        Saqlash
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Fanlar topilmadi.</td>
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

@push('scripts')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.modal').on('shown.bs.modal', function (e) {
                $(this).find('.select2-custom').select2({
                    theme: 'bootstrap4',
                    placeholder: $(this).find('.select2-custom').data('placeholder'),
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(this)
                });
            });

            $('.modal').on('hidden.bs.modal', function () {
                if ($(this).find('.select2-custom').data('select2')) {
                    $(this).find('.select2-custom').select2('destroy');
                }
            });
        });
    </script>
@endpush
