@extends('layouts.web')
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
                        <h1 class="font-weight-bold">Tillar ro‘yxati</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Statistikalar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-center table-custom">
                            <thead>
                            <tr>
                                <th style="width: 3%">
                                    <input type="checkbox">
                                </th>
                                <th style="width: 5%">#</th>
                                <th style="text-align: left">Talaba F.I.Sh.</th>
                                <th style="width: 10%">Ariza raqami</th>
                                <th>Fan nomi va guruhi</th>
                                <th style="width: 10%">Ta’lim tili</th>
                                <th style="width: 10%">Semestr / Kredit</th>
                                <th style="width: 10%">To‘plagan ball</th>
                                <th style="width: 10%">Holati</th>
                                <th class="text-nowrap" style="width: 10%">
                                    <a href="{{ route('departments.download') }}"
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-cloud-upload-alt"></i>
                                    </a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($exams as $exam)
                                <tr>
                                    <td>
                                        <input type="checkbox">
                                    </td>
                                    <td>#1</td>
                                    <td style="text-align: left">
                                        <div class="font-weight-bold">
                                            {{ json_decode($exam->student->name)->full_name }}
                                        </div>
                                        <div class="small">
                                            {{ $exam->student->specialty->code }} -
                                            {{ $exam->student->specialty->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $exam->application->application_number }}</code>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">
                                            {{ $exam->failed_subject->subject_name }}
                                        </div>
                                        <div class="small">
                                            {{ $exam->group->name }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ $exam->student->language->name }}
                                    </td>
                                    <td>
                                        {{ $exam->semester->name }} /
                                        {{ number_format($exam->failed_subject->credit, 2) }}
                                    </td>
                                    <td>
                                        {{ number_format($exam->results->first()->point ?? 0, 2) }}
                                    </td>
                                    <td>
                                        @if($exam->status == '0')
                                            <div class="badge badge-primary">
                                                Yangi
                                            </div>
                                        @elseif($exam->status == '1')
                                            <div class="badge badge-warning">
                                                Jarayonda
                                            </div>
                                        @elseif($exam->status == '2')
                                            <div class="badge badge-success">
                                                Yakunlangan
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php($min_point = \App\Models\Option::where('key', 'min_points')->value('value'))
                                        @if($exam->status == '2')
                                            @if($exam->results->first()->point >= $min_point)
                                                <a href="{{ route('departments.download') }}"
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fa fa-cloud-upload-alt"></i>
                                                    Ko‘chirish
                                                </a>
                                            @else
                                                <a href="{{ route('departments.download') }}"
                                                   class="btn btn-danger btn-sm">
                                                    <i class="fa fa-archive"></i>
                                                    Arxivga olish
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty

                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

