@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Asosiy sahifa</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item active">Asosiy sahifa</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    @php($name = json_decode($user->name))
                    <div class="col-md-4">
                        <div class="card card-widget widget-user">
                            <div class="widget-user-header bg-info">
                                <h3 class="widget-user-username font-weight-bold">
                                    {{ mb_substr($name->second_name, 0, 1, 'UTF-8') . strtolower(mb_substr($name->second_name, 1, null, 'UTF-8')) }}
                                    {{ mb_substr($name->first_name, 0, 1, 'UTF-8') . strtolower(mb_substr($name->first_name, 1, null, 'UTF-8')) }}
                                    {{ mb_substr($name->third_name, 0, 1, 'UTF-8') . strtolower(mb_substr($name->third_name, 1, null, 'UTF-8')) }}
                                </h3>

                                <h5 class="widget-user-desc small">
                                    {{ auth('student')->user()->course->specialty->code }} -
                                    {{ auth('student')->user()->course->specialty->name }}
                                </h5>
                            </div>
                            <div class="widget-user-image">
                                <img class="img-circle elevation-2" src="{{ auth('student')->user()->picture }}"
                                     alt="{{ $name->short_name }}">
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">
                                                {{ auth('student')->user()->course->name }}
                                            </h5>
                                            <span class="description-text small">GURUHI</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">
                                                {{ auth('student')->user()->level->name }}
                                            </h5>
                                            <span class="description-text small">KURSI</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="description-block">
                                            <h5 class="description-header">35</h5>
                                            <span class="description-text">PRODUCTS</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>0</h3>
                                        <p>Umumiy arizalar</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-ios-albums-outline"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>0</h3>
                                        <p>Qarzdor fanlar</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-ios-paper-outline"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>0</h3>
                                        <p>Faol testlar</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-ios-checkmark-outline"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>0</h3>
                                        <p>Jarayonda ...</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-ios-timer-outline"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
@endsection
