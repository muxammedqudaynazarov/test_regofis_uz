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
                                <th style="width: 5%">#</th>
                                <th style="text-align: left">Nomi</th>
                                <th class="text-right" style="width: 7%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($ci = 1)
                            @foreach($stats as $stat)
                                <tr>
                                    <td style="vertical-align: middle">#{{ $ci++ }}</td>
                                    <td style="vertical-align: middle; text-align: left" class="font-weight-bold">
                                        {{ $stat['name'] }}
                                    </td>
                                    <td style="vertical-align: middle">
                                        <a href="{{ route($stat['route']) }}"
                                           class="btn btn-outline-success btn-sm @if($stat['disabled']) disabled @endif">
                                            <i class="fa fa-cloud-download-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

