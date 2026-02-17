@extends('layouts.web')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">Tizim sozlamalari</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Sozlamalar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content text-sm">
            <div class="container-fluid">
                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">Test va tizim konfiguratsiyasi</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th>Sozlama nomi</th>
                                <th>Kalit (key)</th>
                                <th class="text-center">Joriy qiymat</th>
                                <th style="width: 10%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($options as $option)
                                <tr>
                                    <td style="vertical-align: middle">#{{ $loop->iteration }}</td>
                                    <td style="vertical-align: middle" class="font-weight-bold">{{ $option->name }}</td>
                                    <td style="vertical-align: middle" class="text-muted small">
                                        <code>{{ $option->key }}</code>
                                    </td>
                                    <td style="vertical-align: middle" class="text-center">
                                        @if($option->key == 'retest')
                                            @if($option->value == '1')
                                                <span class="badge badge-success">Ha (yoqilgan)</span>
                                            @else
                                                <span class="badge badge-danger">Yo‘q (o‘chirilgan)</span>
                                            @endif
                                        @else
                                            <span class="badge badge-info">{{ $option->value }}</span>
                                        @endif
                                    </td>
                                    <td style="vertical-align: middle" class="text-right">
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                                data-toggle="modal"
                                                data-target="#editOption{{ $option->id }}">
                                            <i class="fas fa-edit"></i> o‘zgartirish
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Sozlamalar mavjud emas.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        @foreach($options as $option)
            <div class="modal fade" id="editOption{{ $option->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('options.update', $option->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header bg-light">
                                <div class="modal-title font-weight-bold">Sozlamani o‘zgartirish</div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body small">
                                <div class="form-group">
                                    <label>Nomi:</label>
                                    <input type="text" class="form-control" value="{{ $option->name }}" disabled>
                                    <small class="text-muted">Tizim nomi o'zgartirilmaydi</small>
                                </div>
                                <div class="form-group">
                                    <label>Qiymatni kiriting:</label>
                                    @if($option->key == 'retest')
                                        <select name="value" class="form-control">
                                            <option value="1" {{ $option->value == 1 ? 'selected' : '' }}>
                                                Ha (ruxsat berish)
                                            </option>
                                            <option value="0" {{ $option->value == 0 ? 'selected' : '' }}>
                                                Yo‘q (taqiqlash)
                                            </option>
                                        </select>
                                    @elseif(in_array($option->key, ['questions', 'durations', 'min_points', 'max_points', 'attempts']))
                                        <input type="number" name="value" class="form-control" required
                                               min="0" value="{{ $option->value }}">
                                    @else
                                        <input type="text" name="value" class="form-control" required
                                               value="{{ $option->value }}">
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Saqlash
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endsection
