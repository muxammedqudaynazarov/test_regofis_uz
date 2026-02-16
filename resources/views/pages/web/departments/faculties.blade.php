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
                        <h1 class="font-weight-bold">Fakultetlar ro‘yxati</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Fakultetlar</li>
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
                                <th style="width: 5%">#</th>
                                <th style="text-align: left;">Fakultet nomi</th>
                                <th style="width: 35%">Kafedralar</th>
                                <th style="width: 7%;">O‘quv rejalar</th>
                                <th style="width: 7%;">Talabalar</th>
                                <th style="width: 5%;">Ruxsat</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($faculties as $faculty)
                                <tr>
                                    <td style="vertical-align: middle">#{{ $faculty->id }}</td>
                                    <td style="text-align: left; vertical-align: middle"
                                        class="font-weight-bold">
                                        {{ $faculty->name }}
                                    </td>

                                    <td style="vertical-align: middle">
                                        @foreach($faculty->children as $child)
                                            <div class="badge badge-info">
                                                {{ $child->name }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td style="vertical-align: middle">
                                        {{ $faculty->curricula->count() }}
                                    </td>
                                    <td style="vertical-align: middle">
                                        0
                                    </td>
                                    <td style="vertical-align: middle">
                                        <input type="checkbox">
                                    </td>
                                    <td style="vertical-align: middle">
                                        <a href="#" class="btn btn-outline-success btn-sm">
                                            <i class="fa fa-cloud-download-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Fakultetlar topilmadi.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div class="card-footer bg-white clearfix">
                            <div class="float-right">
                                {{ $faculties->links() }}
                            </div>
                            @if($faculties->total())
                                <div class="text-muted small mt-2">
                                    Jami: <b>{{ $faculties->total() }}</b> ta savoldan
                                    <b>{{ $faculties->firstItem() }}</b>-<b>{{ $faculties->lastItem() }}</b>
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
