@extends('layouts.web')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">Qayta o‘qishni boshqarish</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Qayta o‘qish</li>
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
                                <th style="width: 10%">Arizalar</th>
                                <th style="width: 10%">Imtihonlar</th>
                                <th style="width: 10%">Natijalar</th>
                                <th style="width: 10%">
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#modal-lg">
                                        Yaratish
                                    </button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($retrains as $retrain)
                                <tr>
                                    <td style="vertical-align: middle">#{{ $retrain->id }}</td>
                                    <td style="vertical-align: middle; text-align: left" class="font-weight-bold">
                                        {{ $retrain->name }}
                                    </td>
                                    <td>{{ $retrain->applications->count() }}</td>
                                    <td>{{ $retrain->exams->count() }}</td>
                                    <td>{{ $retrain->results->count() }}</td>
                                    <td style="vertical-align: middle" class="text-nowrap">
                                        @if($retrain->status == '1')
                                            <button class="btn btn-default btn-sm">Aktiv holatda</button>
                                        @else
                                            <form action="{{ route('retrains.update', $retrain->id) }}" method="POST"
                                                  onsubmit="return confirm('Ushbu qayta o‘qishni qayta faollashtirmoqchimisiz?')">
                                                @method('PUT')
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    Aktiv qilish
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Qayta o‘qish ro‘yxati bo‘sh.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="modal fade" id="modal-lg">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('retrains.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h4 class="modal-title">Qayta o‘qish yaratish</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="labelRetrainName">Qayta o‘qish nomi</label>
                                            <input type="text" class="form-control" id="labelRetrainName" name="name">
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <button type="submit" class="btn btn-primary">
                                            Yaratish (saqlash)
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

