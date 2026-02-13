@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Qarzdor fanlar</h1>
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
                                Fanlar royxati
                            </div>
                            <div class="p-1">
                                <form action="{{ route('applications.store') }}" method="POST" class="d-flex">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        Yangilash
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover text-center">
                            <thead>
                            <tr>
                                <th style="width: 7%">#</th>
                                <th style="text-align: start">Nomi va guruhi</th>
                                <th>Semestr</th>
                                <th>Kredit</th>
                                <th style="width: 14%">Nazorat</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($subjects as $subject)
                                <tr>
                                    <td class="align-middle">#{{ $subject->id }}</td>
                                    <td class="align-middle" style="text-align: start">
                                        <div class="font-weight-bold">
                                            {{ $subject->subject->name }}
                                        </div>
                                        <div class="small">
                                            {{ $subject->group->name }}
                                        </div>
                                    </td>
                                    <td class="align-middle">{{ $subject->semester->name }}</td>
                                    <td class="align-middle">{{ number_format($subject->credit, 2) }}</td>
                                    <td class="align-middle">
                                        @if(count($subject->subject->tests))
                                            @foreach($subject->subject->tests as $test)
                                                @if($test->type == 'on')
                                                    @if($test->status == '1')
                                                        <a href="#" class="btn btn-primary d-block btn-sm m-1">
                                                            Oraliq (0 / 50)
                                                        </a>
                                                    @endif
                                                @endif
                                                @if($test->type == 'on')
                                                    @if($test->status == '1')
                                                        <a href="#" class="btn btn-danger d-block btn-sm m-1">
                                                            Yakuniy (0 / 50)
                                                        </a>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @else
                                            <a href="#" class="btn btn-info d-block btn-sm m-1 disabled">
                                                Nazorat yaratilmagan
                                            </a>
                                        @endif
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
