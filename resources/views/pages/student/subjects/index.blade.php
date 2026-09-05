@php
    use App\Models\Option;
    $minPoints = Option::where('key', 'min_points')->value('value') ?? 60;
@endphp
@extends('layouts.app')

@section('style')
    <style>
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
                    <div class="col-sm-6">
                        <div class="font-weight-bold h3">
                            Akademik qarzdorliklar
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Bosh sahifa</a></li>
                            <li class="breadcrumb-item active">Qarzdor fanlar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <div class="card card-outline card-danger">
                            <div class="card-header font-weight-bold">
                                <div class="card-title font-weight-bold">Fanlar ro‘yxati</div>
                                <div class="card-tools">
                                    <form action="{{ route('applications.store') }}" method="POST"
                                          onsubmit="return confirm('Fanlar ro‘yxati ma’lumotlari yangilansinmi?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            Arizalarni yangilash
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-center table-custom">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%">ID</th>
                                        <th class="text-left">Fan nomi va guruhi</th>
                                        <th style="width: 10%">Ariza raqami</th>
                                        <th style="width: 10%">Semestr</th>
                                        <th style="width: 10%">Kredit</th>
                                        <th style="width: 10%">O‘tish ball</th>
                                        <th style="width: 10%">Urinish</th>
                                        <th style="width: 10%">Holati</th>
                                        <th style="width: 10%"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($subjects as $subject)
                                        <tr>
                                            <td>#{{ $subject->id }}</td>
                                            <td class="text-left">
                                                <div class="font-weight-bold">
                                                    {{ $subject->failed_subject->subject_name }}
                                                </div>
                                                <div class="small">
                                                    {{ $subject->group->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <code>
                                                    {{ $subject->application->application_number }}
                                                </code>
                                            </td>
                                            <td>
                                                <div class="badge badge-primary">
                                                    {{ $subject->semester->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <code>
                                                    {{ number_format($subject->failed_subject->credit, 2) }}
                                                </code>
                                            </td>
                                            <td class="small">
                                                {{ $minPoints }} ball
                                            </td>
                                            <td class="small">
                                                {{ $subject->attempt }}
                                            </td>
                                            <td class="small">
                                                @if($subject->status == '0')
                                                    Boshlanmagan
                                                @elseif($subject->status == '1')
                                                    Davom etmoqda
                                                @elseif($subject->status == '2')
                                                    Yakunlangan
                                                @endif
                                            </td>
                                            <td class="text-nowrap" id="action-cell-{{ $subject->id }}">
                                                @if(auth()->user()->specialty->department->access == '1')
                                                    @if($subject->finished == '0')
                                                        @if($subject->status == '0')
                                                            {{-- Tekshirish tugmasi — status 0 (boshlanmagan) --}}
                                                            <button type="button"
                                                                    class="btn btn-outline-primary btn-sm check-questions-btn"
                                                                    data-exam-id="{{ $subject->id }}"
                                                                    data-check-url="{{ route('tests.check', $subject->id) }}"
                                                                    data-start-url="{{ route('tests.show', $subject->id) }}">
                                                                <i class="fas fa-search mr-1" style="font-size:10px"></i>
                                                                Savollarni tekshirish
                                                            </button>
                                                        @elseif($subject->status == '1')
                                                            {{-- Davom ettirish — status 1 (jarayonda) --}}
                                                            <a href="{{ route('tests.show', $subject->id) }}"
                                                               class="btn btn-warning btn-sm text-white font-weight-bold shadow-sm">
                                                                <i class="fas fa-spinner fa-spin mr-1"></i>
                                                                Davom ettirish
                                                            </a>
                                                        @endif
                                                    @endif
                                                @else
                                                    <div class="badge badge-light text-info border shadow-sm p-2">
                                                        <i class="fas fa-lock mr-1"></i> Taqiqlangan
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">
                                                Ma'lumotlar topilmadi.
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // ── Savollarni tekshirish tugmasi ──
            $(document).on('click', '.check-questions-btn', function () {
                var btn      = $(this);
                var examId   = btn.data('exam-id');
                var checkUrl = btn.data('check-url');
                var startUrl = btn.data('start-url');
                var cell     = $('#action-cell-' + examId);

                btn.prop('disabled', true)
                   .html('<i class="fas fa-spinner fa-spin mr-1"></i> Tekshirilmoqda...');

                $.ajax({
                    url: checkUrl,
                    method: 'GET',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function (res) {
                        if (res.ok) {
                            Swal.fire({
                                title: 'Savollar tayyor!',
                                html: '<span class="text-success"><i class="fas fa-check-circle"></i> ' + res.reason + '</span>'
                                    + '<hr><small class="text-muted">Test boshlangandan keyin uni toxtatib bolmaydi.</small>',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonColor: '#28a745',
                                cancelButtonColor:  '#6c757d',
                                confirmButtonText:  '<i class="fas fa-play mr-1"></i> Ha, boshlaymiz!',
                                cancelButtonText:   'Qaytish',
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    window.location.href = startUrl;
                                } else {
                                    btn.prop('disabled', false)
                                       .html('<i class="fas fa-search mr-1" style="font-size:10px"></i> Savollarni tekshirish');
                                }
                            });
                        } else {
                            cell.html(
                                '<div class="d-flex flex-column align-items-start">'
                                + '<span class="badge badge-danger mb-1"><i class="fas fa-times-circle mr-1"></i> Test boshlash mumkin emas</span>'
                                + '<small class="text-danger" style="font-size:11px">' + res.reason + '</small>'
                                + '<button type="button" class="btn btn-outline-secondary btn-sm mt-1 check-questions-btn" '
                                +   'data-exam-id="' + examId + '" '
                                +   'data-check-url="' + checkUrl + '" '
                                +   'data-start-url="' + startUrl + '">'
                                +   '<i class="fas fa-redo mr-1"></i> Qayta tekshirish</button>'
                                + '</div>'
                            );
                        }
                    },
                    error: function () {
                        btn.prop('disabled', false)
                           .html('<i class="fas fa-search mr-1" style="font-size:10px"></i> Savollarni tekshirish');
                        toastr.error('Server bilan aloqa yoq. Qayta urinib koring.');
                    }
                });
            });
        });
    </script>
@endsection
