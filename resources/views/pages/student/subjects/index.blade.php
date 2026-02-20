@php use App\Models\Option; @endphp
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
                                            <td>
                                                60 ball
                                            </td>
                                            <td>
                                                @if($subject->status == '0')
                                                    Boshlanmagan
                                                @elseif($subject->status == '1')
                                                    Davom etmoqda
                                                @elseif($subject->status == '2')
                                                    Yakunlangan
                                                @endif
                                            </td>
                                            <td class="text-nowrap">
                                                @if(auth()->user()->specialty->department->access == '1')
                                                    @if($subject->resource->questions->count())
                                                        @if($subject->finished == '0')
                                                            @if($subject->status == '0')
                                                                <a href="javascript:void(0)"
                                                                   data-url="{{ route('tests.show', $subject->id) }}"
                                                                   class="btn btn-primary btn-sm start-test-btn">
                                                                    <i class="fas fa-play mr-1"
                                                                       style="font-size: 10px"></i> Testni boshlash
                                                                </a>
                                                            @elseif($subject->status == '1')
                                                                <a href="{{ route('tests.show', $subject->id) }}"
                                                                   class="btn btn-warning btn-sm text-white font-weight-bold shadow-sm">
                                                                    <i class="fas fa-spinner fa-spin mr-1"></i>
                                                                    Davom ettirish
                                                                </a>
                                                            @endif
                                                        @else
                                                            <div class="badge badge-success">
                                                                {{ $subject->results->first()->point }} ball
                                                            </div>
                                                        @endif
                                                    @else
                                                        <a class="btn btn-danger btn-sm disabled shadow-sm">
                                                            <i class="fas fa-times-circle mr-1"></i> Resurs yo'q
                                                        </a>
                                                    @endif
                                                @else
                                                    <div class="badge badge-light text-info border">
                                                        <i class="fas fa-lock mr-1"></i> Taqiqlangan
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.start-test-btn', function (e) {
                e.preventDefault();
                var targetUrl = $(this).data('url');
                Swal.fire({
                    title: 'Testni boshlashga tayyormisiz?',
                    text: "Ushbu fandan test boshlangandan keyin uni to‘xtatib bo‘lmaydi. Tayyormisiz?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#007bff',
                    confirmButtonText: 'Ha, boshlaymiz!',
                    cancelButtonText: 'Yo‘q, qaytish',
                    heightAuto: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = targetUrl;
                    }
                });
            });
        });
    </script>
@endsection
