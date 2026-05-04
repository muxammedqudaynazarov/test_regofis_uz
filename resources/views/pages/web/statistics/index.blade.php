@extends('layouts.web')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">
                            Statistikalar
                        </h1>
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
                <div class="card card-outline card-primary shadow-sm rounded-lg">
                    <div class="card-body p-3">
                        <ul class="list-group list-group-flush">
                            @php($ci = 1)
                            @foreach($stats as $index => $stat)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3"
                                    style="border-radius: 8px; margin-bottom: 5px;">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-primary mr-3 p-2"
                                              style="font-size: 13px;">#{{ $ci++ }}</span>
                                        <div
                                            class="font-weight-bold @if($stat['disabled']) text-muted @else text-dark @endif"
                                            style="font-size: 15px;">
                                            {{ $stat['name'] }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center text-nowrap">
                                        @if($stat['optional']['status'])
                                            <form action="{{ route($stat['route']) }}" method="POST"
                                                  class="d-flex align-items-center m-0">
                                                @csrf
                                                <select name="optional_id" id="optional_id_{{ $index }}"
                                                        class="form-control form-control-sm mr-3 shadow-none"
                                                        style="min-width: 180px; border-radius: 6px; border-color: #ced4da;">
                                                    @foreach($stat['optional']['values'] as $value)
                                                        <option value="{{ $value['id'] }}"
                                                                @if($value['status'] == '1') selected @endif>
                                                            {{ $value['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit"
                                                        class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                                                    <i class="fa fa-cloud-download-alt mr-1"></i> Yuklab olish
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ $stat['disabled'] ? '#' : route($stat['route']) }}"
                                               class="btn btn-{{ $stat['disabled'] ? 'secondary' : 'success' }} btn-sm rounded-pill px-3 shadow-sm @if($stat['disabled']) disabled @endif">
                                                <i class="fa fa-cloud-download-alt mr-1"></i> Yuklab olish
                                            </a>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
