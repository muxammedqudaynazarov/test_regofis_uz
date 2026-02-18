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

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="card card-outline card-info shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filtrlash</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                    class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('subjects-register.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <select name="curriculum_id" class="form-control select2-filter"
                                                data-placeholder="O‘quv rejani qidiring..." style="width: 100%">
                                            <option value=""></option>
                                            @foreach($curriculums as $curriculum)
                                                <option
                                                    value="{{ $curriculum->id }}" {{ $filterCurriculum == $curriculum->id ? 'selected' : '' }}>
                                                    {{ $curriculum->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <select name="status" class="form-control select2-filter"
                                                data-placeholder="Holatni tanlang" style="width: 100%">
                                            <option value=""></option>
                                            <option
                                                value="attached" {{ $filterStatus == 'attached' ? 'selected' : '' }}>
                                                Biriktirilgan fanlar
                                            </option>
                                            <option
                                                value="detached" {{ $filterStatus == 'detached' ? 'selected' : '' }}>
                                                Bo‘sh fanlar
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 text-right">
                                    <div class="form-group w-100">
                                        <button type="submit" name="filter_submit" value="1" class="btn btn-primary">
                                            <i class="fas fa-search mr-1"></i> Izlash
                                        </button>
                                        <button type="submit" name="filter_clear" value="1"
                                                class="btn btn-default ml-2">
                                            <i class="fas fa-times mr-1"></i> Tozalash
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- JADVAL QISMI --}}
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-center">
                            <thead>
                            <tr>
                                <th style="vertical-align: middle">#</th>
                                <th style="text-align: left; vertical-align: middle">Fan nomi</th>
                                <th style="vertical-align: middle">Kafedra</th>
                                <th style="vertical-align: middle">O‘quv rejalar</th>
                                <th style="vertical-align: middle">O‘quv yili</th>
                                <th style="vertical-align: middle">Semestrlar</th>
                                <th style="vertical-align: middle">Masul o‘qituvchilar</th>
                                <th style="vertical-align: middle" class="text-right"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($subjects as $index => $lesson)
                                <tr>
                                    <td style="vertical-align: middle">#{{ $lesson->id }}</td>
                                    <td style="text-align: left; vertical-align: middle">
                                        <div class="font-weight-bold">{{ $lesson->subject->name }}</div>
                                        <div class="small">{{ $lesson->code }}</div>
                                    </td>
                                    <td style="vertical-align: middle">{{ $lesson->department->name }}</td>
                                    <td style="vertical-align: middle">{{ $lesson->curriculum->name }}</td>
                                    <td style="vertical-align: middle">
                                        <div class="badge badge-primary">{{ $lesson->curriculum->edu_year->name }}</div>
                                    </td>
                                    <td style="vertical-align: middle">
                                        <div class="badge badge-success">{{ $lesson->semester->name }}</div>
                                    </td>
                                    <td style="vertical-align: middle">
                                        @forelse($lesson->teachers as $teacher)
                                            <span class="badge bg-purple">
                                                 {{ json_decode($teacher->name)->short_name ?? $teacher->short_name }}
                                             </span>
                                        @empty
                                            <span class="text-muted small">O‘qituvchi biriktirilmagan</span>
                                        @endforelse
                                    </td>
                                    <td style="vertical-align: middle" class="text-right text-nowrap">
                                        <button type="button" class="btn btn-default btn-sm"
                                                data-toggle="modal"
                                                data-target="#assignModal{{ $lesson->id }}">
                                            <i class="fas fa-user-plus mr-1"></i> O‘qituvchi qo‘shish
                                        </button>
                                        @if(auth()->user()->current_role == 'department')
                                            @if ($lesson->request_delete == '1')
                                                <button type="button" class="btn btn-info btn-sm disabled">
                                                    <i class="fas fa-clock mr-1"></i> Sorov yuborildi
                                                </button>

                                            @elseif ($lesson->request_delete == '2')
                                                Bekor qilingan
                                            @elseif ($lesson->request_delete == '0')
                                                <a class="btn btn-danger btn-sm"
                                                   href="{{ route('subjects-register.edit', $lesson->id) }}"
                                                   onclick="return confirm('Ushbu harakatni tasdiqlash orqali Siz tomoningizdan tizim administratorlariga fanni o‘chirish bo‘yicha ma’lumot kiritgan deb hisoblanadi. Muhum: agar fanda resurslar bo‘lsa uni o‘chirib tashlash mumkin emas. Harakatn tasdiqlaysizmi?')">
                                                    <i class="fas fa-trash-alt mr-1"></i> So‘rov yuborish
                                                </a>
                                            @endif
                                        @endif
                                        @can('lessons.delete')
                                            <a href="#" class="btn btn-outline-danger btn-sm"><i
                                                    class="fas fa-trash"></i></a>
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

        {{-- MODALLARNI JADVAL TASHQARISIGA CHIQARAMIZ --}}
        @foreach($subjects as $lesson)
            <div class="modal fade" id="assignModal{{ $lesson->id }}" role="dialog" aria-hidden="true"
                 data-backdrop="static">
                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('subjects-register.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $lesson->id }}">
                            <div class="modal-header bg-light">
                                <h5 class="modal-title font-weight-bold">{{ $lesson->subject->name }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                                data-placeholder="O‘qituvchini tanlang"
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
                                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Yopish
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">Saqlash</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endsection

@section('scripts')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            // 1. Filter uchun Select2 (O'zgarishsiz)
            $('.select2-filter').select2({
                theme: 'bootstrap4',
                allowClear: true,
                placeholder: function () {
                    $(this).data('placeholder');
                }
            });

            // Filter qidiruv maydoniga fokus berish
            $(document).on('select2:open', () => {
                let searchField = document.querySelector('.select2-container--open .select2-search__field');
                if (searchField) {
                    searchField.focus();
                }
            });

            // 2. MODAL UCHUN SELECT2 (YANGILANGAN QISM)
            // 'shown.bs.modal' emas, 'show.bs.modal' ishlatamiz
            $('.modal').on('show.bs.modal', function (e) {
                var $select = $(this).find('.select2-custom');

                if (!$select.hasClass("select2-hidden-accessible")) {
                    $select.select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        dropdownParent: $(this),
                        allowClear: true,
                        placeholder: "O‘qituvchini tanlang"
                    });

                    // Select2 tayyor bo'lgach, elementni orqaga qaytarib 'opacity: 1' qilamiz
                    // (Lekin select2 o'zi asl selectni yashiradi, bu shunchaki ehtiyot chorasi)
                    $select.css('opacity', 1);
                }
            });
        });
    </script>
@endsection
