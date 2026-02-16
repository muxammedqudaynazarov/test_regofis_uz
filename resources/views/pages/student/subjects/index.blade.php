@extends('layouts.app')

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
                            <div class="card-header font-weight-bold">
                                <div class="card-title font-weight-bold">Fanlar ro‘yxati</div>
                                <div class="card-tools">
                                    <form action="{{ route('applications.store') }}" method="POST"
                                          onsubmit="return confirm('Fanlar ro‘yxati ma’lumotlari yangilansinmi?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-sync"></i> Yangilash
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-center">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%">ID</th>
                                        <th class="text-left">Fan nomi</th>
                                        <th>Semestr</th>
                                        <th>Kredit</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($subjects as $subject)
                                        <tr>
                                            <td>#{{ $loop->iteration }}</td>
                                            <td class="text-left">
                                                <div>
                                                    {{ $subject->subject->name }}
                                                </div>
                                                <div class="small">
                                                    {{ $subject->group->name }}
                                                </div>
                                            </td>
                                            <td>{{ $subject->semester->name }}</td>
                                            <td>{{ number_format($subject->credit, 2) }}</td>
                                            <td>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
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
