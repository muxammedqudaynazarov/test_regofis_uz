@extends('layouts.web')
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
                        <table class="table table-hover text-center">
                            <thead>
                            <tr>
                                <th style="width: 7%">#</th>
                                <th style="text-align: left">Nomi</th>
                                <th class="text-right" style="width: 7%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td style="vertical-align: middle">#1</td>
                                <td style="vertical-align: middle; text-align: left" class="font-weight-bold">
                                    Kafedra resurslari hisoboti (.XLSX)
                                </td>
                                <td style="vertical-align: middle">
                                    <a href="{{ route('departments.download') }}"
                                       class="btn btn-outline-success btn-sm">
                                        <i class="fa fa-cloud-download-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle">#2</td>
                                <td style="vertical-align: middle; text-align: left" class="font-weight-bold">
                                    Bo‘sh (o‘qituvchi biriktirilmagan) fanlar hisoboti (.XLSX)
                                </td>
                                <td style="vertical-align: middle">
                                    <a href="#"
                                       class="btn btn-outline-success btn-sm disabled">
                                        <i class="fa fa-cloud-download-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle">#3</td>
                                <td style="vertical-align: middle; text-align: left" class="font-weight-bold">
                                    O‘qituvchi faolligi (.XLSX)
                                </td>
                                <td style="vertical-align: middle">
                                    <a href="#"
                                       class="btn btn-outline-success btn-sm disabled">
                                        <i class="fa fa-cloud-download-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

