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
                                        <th style="width: 10%">Urinish</th>
                                        <th style="width: 10%">To‘plagan ball</th>
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
                                                60 ball
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
                                            <td class="small">
                                                {{ $subject->attempt }}
                                            </td>
                                            <td class="text-nowrap">
                                                <div class="badge badge-success">
                                                    {{ $subject->results->first()->point }} ball
                                                </div>
                                            </td>
                                            <td class="text-nowrap">
                                                <a href="{{ route('tests.edit', $subject->id) }}"
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="far fa-eye"></i>
                                                    Testni ko‘rish
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
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

