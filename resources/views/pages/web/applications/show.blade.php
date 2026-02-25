@extends('layouts.web')

@section('style')
    <style>
        .profile-user-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }

        .list-group-unbordered > .list-group-item {
            padding-left: 0;
            padding-right: 0;
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
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">Ariza batafsil: #{{ $app->application_number }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item"><a href="#">Arizalar</a></li>
                            <li class="breadcrumb-item active">{{ $app->application_number }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-primary card-outline shadow-sm">
                            <div class="card-body box-profile">
                                <div class="text-center mb-3">
                                    <img class="profile-user-img img-fluid img-circle border"
                                         src="{{ $app->student->picture }}">
                                </div>

                                <h3 class="profile-username text-center font-weight-bold">
                                    {{ json_decode($app->student->name)->full_name }}
                                </h3>
                                <p class="text-muted text-center mb-4">
                                    {{ $app->student->specialty->name }}
                                </p>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <b class="mr-3 text-nowrap"><i class="fas fa-university text-primary mr-1"></i> Fakultet</b>
                                        <span class="text-muted text-right">{{ $app->student->specialty->department->name }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <b class="mr-3 text-nowrap"><i class="fas fa-book-reader text-success mr-1"></i> O‘quv reja</b>
                                        <span class="text-muted text-right">{{ $app->student->curriculum->name }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <b class="mr-3 text-nowrap"><i class="fas fa-language text-info mr-1"></i> Ta’lim tili</b>
                                        <span class="text-muted text-right">{{ $app->student->language->name ?? '-' }}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <b class="mr-3 text-nowrap"><i class="far fa-clock text-warning mr-1"></i> Ariza vaqti</b>
                                        <span class="text-muted text-right">{{ $app->created_at ? $app->created_at->format('d.m.Y H:i') : '-' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card card-primary card-outline shadow-sm">
                            <div class="card-header bg-white">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-book-open text-primary mr-1"></i> Arizadagi fanlar ro‘yxati
                                </h3>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-hover table-striped mb-0 text-center table-custom">
                                    <thead class="bg-light">
                                    <tr>
                                        <th style="width: 10%">#</th>
                                        <th class="text-left">Fan nomi</th>
                                        <th style="width: 10%">Kredit / Soat</th>
                                        <th style="width: 10%">Semestr</th>
                                        <th style="width: 10%">Holati</th>
                                        <th style="width: 10%">Natija</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($app->exams as $exam)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="text-left font-weight-bold">
                                                {{ $exam->failed_subject->subject_name }}
                                            </td>
                                            <td>
                                                {{ number_format($exam->failed_subject->credit, 2) }}
                                            </td>
                                            <td>
                                                {{ $exam->semester->name ?? '-' }}
                                            </td>
                                            <td>
                                                @if($exam->status == '1')
                                                    <span class="badge badge-warning">
                                                        Kutilmoqda
                                                    </span>
                                                @elseif($exam->status == '2')
                                                    <span class="badge badge-success">
                                                        Yakunlangan
                                                    </span>
                                                @else
                                                    <span class="badge badge-primary">
                                                        Yangi
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ number_format(($exam->results->first()->point ?? 0), 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox fa-3x d-block mb-2"></i>
                                                Ushbu arizaga hech qanday fan biriktirilmagan.
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-white text-right">
                                Jami fanlar soni: <b>{{ $app->exams->count() }}</b> ta
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
