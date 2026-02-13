@extends('layouts.web')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Fanlarni biriktirish</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Asosiy sahifa</li>
                            <li class="breadcrumb-item active">Fanlarni biriktirish</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header font-weight-bold">
                        <div class="d-flex">
                            <div class="p-1 flex-grow-1">
                                Fanlar royxati
                            </div>
                            {{--<div class="p-1">
                                <form action="{{ route('applications.store') }}" method="POST" class="d-flex">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        Yangilash
                                    </button>
                                </form>
                            </div>--}}
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover text-center small">
                            <thead>
                            <tr>
                                <th style="width: 7%">#</th>
                                <th style="text-align: start;">Fan nomi</th>
                                <th>Fan guruhi</th>
                                <th>O‘qituvchilar</th>
                                <th style="width: 7%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($lessons as $lesson)
                                <tr>
                                    <td style="vertical-align: middle">
                                        #{{ $lesson->subject_id }}
                                    </td>
                                    <td style="text-align: start; vertical-align: middle">
                                        <div class="font-weight-bold">
                                            {{ $lesson->name }}
                                        </div>
                                    </td>
                                    <td style="vertical-align: middle">
                                        @foreach($lesson->groups as $group)
                                            <div class="badge badge-success">
                                                {{ $group->group->name }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td style="vertical-align: middle">

                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-success btn-sm">Biriktirish</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
@endsection
