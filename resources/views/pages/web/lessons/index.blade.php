@extends('layouts.web')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Biriktirilgan fanlar</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Asosiy sahifa</li>
                            <li class="breadcrumb-item active">Qarzdor fanlar</li>
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
                                Fanlar ro‘yxati
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
                    <div class="card-body">
                        @foreach($subjects as $subject)
                            <div class="row row-cols-1 row-cols-md-3 g-3">
                                <div class="col">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="card-title font-weight-bold">
                                                {{ $subject->name }}
                                            </div>
                                            <div class="card-text">
                                                <div class="m-0 p-0">Nazoratlar: 0</div>
                                                <div class="m-0 p-0">Savollar: 0</div>
                                            </div>
                                        </div>
                                        <div class="card-footer" style="text-align: right">
                                            <button class="btn btn-success btn-sm">
                                                Nazorat yaratish
                                            </button>
                                            <button class="btn btn-primary btn-sm">
                                                Savol qo‘shish
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{--<div class="card-body">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture">
                                </div>

                                <h3 class="profile-username text-center">Nina Mcintire</h3>

                                <p class="text-muted text-center">Software Engineer</p>

                                <ul class="list-group list-group-unbordered mb-3">
                                    <li class="list-group-item">
                                        <b>Followers</b> <a class="float-right">1,322</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Following</b> <a class="float-right">543</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Friends</b> <a class="float-right">13,287</a>
                                    </li>
                                </ul>

                                <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                            </div>
                            <!-- /.card-body -->
                        </div>


                        <table class="table table-hover text-center">
                            <thead>
                            <tr>
                                <th style="width: 7%">#</th>
                                <th style="text-align: start">Nomi va guruhi</th>
                                <th>Semestr</th>
                                <th>Kredit</th>
                                <th>Savollar</th>
                                <th style="width: 14%">Nazorat</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>--}}
                </div>
            </div>
        </section>
@endsection
