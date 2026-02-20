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
                        <h1 class="font-weight-bold">Kafedralar ro‘yxati</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Kafedralar</li>
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
                                <th style="width: 7%">#</th>
                                <th style="text-align: left;">Kafedra nomi</th>
                                <th style="width: 20%">Fakultet</th>
                                <th style="width: 7%">O‘qituvchilar</th>
                                <th style="width: 7%">Fanlar</th>
                                <th style="width: 3%;">
                                    <a href="{{ route('departments.download') }}"
                                       class="btn btn-outline-success btn-sm">
                                        <i class="fa fa-cloud-download-alt"></i>
                                    </a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($faculties as $faculty)
                                @if(count($faculty->children))
                                    @foreach($faculty->children as $child)
                                        <tr>
                                            <td style="vertical-align: middle">#{{ $child->id }}</td>
                                            <td style="text-align: left; vertical-align: middle"
                                                class="font-weight-bold">
                                                {{ $child->name }}
                                            </td>
                                            <td style="vertical-align: middle">
                                                <div class="badge badge-info">
                                                    {{ $faculty->name }}
                                                </div>
                                            </td>
                                            <td style="vertical-align: middle">
                                                {{ $child->teachers_count }}
                                            </td>
                                            <td style="vertical-align: middle">
                                                {{ $child->subjects->count() }}
                                            </td>
                                            <td style="vertical-align: middle">
                                                {{--<a href="#" class="btn btn-outline-success btn-sm">
                                                    <i class="fa fa-cloud-download-alt"></i>
                                                </a>--}}
                                            </td>
                                        </tr>

                                    @endforeach
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Kafedralar topilmadi.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
