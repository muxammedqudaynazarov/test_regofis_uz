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
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="font-weight-bold">O‘quv rejalar ro‘yxati</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">O‘quv rejalar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filtrlash</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="department_id" id="department_id" class="form-control select2"
                                                style="width: 100%">
                                            <option value=""></option>
                                            @foreach($departments as $dept)
                                                <option
                                                    value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select name="specialty_id" id="specialty_id" class="form-control select2"
                                                style="width: 100%">
                                            <option value=""></option>
                                            @foreach($specialties as $spec)
                                                <option
                                                    value="{{ $spec->id }}" {{ request('specialty_id') == $spec->id ? 'selected' : '' }}>
                                                    {{ $spec->code }} - {{ $spec->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select name="edu_year_id" class="form-control select2" style="width: 100%">
                                            <option value=""></option>
                                            @foreach($eduYears as $year)
                                                <option
                                                    value="{{ $year->id }}" {{ request('edu_year_id') == $year->id ? 'selected' : '' }}>
                                                    {{ $year->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-1 d-flex align-items-end">
                                    <div class="form-group w-100 mx-1">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    @if(request()->hasAny(['department_id', 'specialty_id', 'edu_year_id']))
                                        <div class="form-group w-100">
                                            <a href="{{ url()->current() }}" class="btn btn-danger btn-block">
                                                <i class="fas fa-times mr-1"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- JADVAL (O'zgarishsiz) --}}
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-center">
                            <thead>
                            <tr>
                                <th style="width: 7%">#</th>
                                <th style="text-align: left; width: 35%">O‘quv reja</th>
                                <th>Fakultet</th>
                                <th>Mutaxassislik / Yo‘nalish</th>
                                <th>O‘quv yili</th>
                                <th class="text-right"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($curr as $item)
                                <tr>
                                    <td style="vertical-align: middle">#{{ $item->id }}</td>
                                    <td style="vertical-align: middle; text-align: left" class="font-weight-bold">
                                        {{ $item->name }}
                                    </td>
                                    <td style="vertical-align: middle">{{ $item->department->name }}</td>
                                    <td style="vertical-align: middle">
                                        <div class="badge badge-info">
                                            {{ $item->specialty->code }} - {{ $item->specialty->name }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle">
                                        <div class="badge badge-success">
                                            {{ $item->edu_year->name }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle">
                                        <form action="{{ route('curriculum.destroy', $item->id) }}" method="POST"
                                              onsubmit="return confirm('Haqiqatan ham ushbu o‘quv rejani o‘chirmoqchimisiz?');"
                                              style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="O‘chirish">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">O‘quv rejalar topilmadi.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div class="card-footer bg-white clearfix">
                            <div class="float-right">
                                {{ $curr->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            // Select2 Init
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "Tanlang",
                allowClear: true
            });

            // --- AJAX: Fakultet tanlanganda Mutaxassisliklarni yuklash ---
            $('#department_id').on('change', function () {
                var departmentId = $(this).val();
                var $specialtySelect = $('#specialty_id');

                // Selectni tozalaymiz
                $specialtySelect.empty().append('<option value="">-- Yuklanmoqda... --</option>');

                if (departmentId) {
                    $.ajax({
                        url: "{{ route('api.get-specialties') }}",
                        type: "GET",
                        data: {department_id: departmentId},
                        success: function (data) {
                            $specialtySelect.empty().append('<option value="">-- Barchasi --</option>');

                            $.each(data, function (key, value) {
                                $specialtySelect.append('<option value="' + value.id + '">' + value.code + ' - ' + value.name + '</option>');
                            });

                            // Select2 ni yangilash shart
                            $specialtySelect.trigger('change');
                        },
                        error: function () {
                            $specialtySelect.empty().append('<option value="">Xatolik yuz berdi</option>');
                        }
                    });
                } else {
                    // Agar fakultet tanlanmagan bo'lsa (Barchasi qilinsa), mutaxassisliklarni ham "Barchasi"ga qaytaramiz (yoki tozalaymiz)
                    $specialtySelect.empty().append('<option value="">-- Avval fakultetni tanlang --</option>');
                    // Yoki hamma mutaxassisliklarni yuklash uchun yana ajax yuborish mumkin
                    // Hozircha oddiylik uchun tozalab qo'yamiz
                    $specialtySelect.trigger('change');
                }
            });
        });
    </script>
@endsection
