@extends('layouts.web')
@section('style')
    <style>
        .table-custom th, .table-custom td {
            vertical-align: middle !important;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">Yakuniy nazoratlar</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Yakuniy nazoratlar</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        @php($min_point = \App\Models\Option::where('key', 'min_points')->value('value'))
        <section class="content text-sm">
            <div class="container-fluid">
                <div class="card card-outline card-primary shadow-sm">
                    @if($status == 'all')
                        <div class="card-header bg-light">
                            <form action="{{ route('final-results.index') }}" method="GET">
                                <div class="row align-items-center">
                                    <div class="col-md-8 mb-2 mb-md-0">
                                        <input type="text" name="search" class="form-control"
                                               placeholder="Talaba F.I.Sh. bo‘yicha qidirish..."
                                               value="{{ request('search') }}">
                                    </div>

                                    <div class="col-md-2 mb-2 mb-md-0">
                                        <select name="exam_status" class="form-control">
                                            <option value=""></option>
                                            <option value="0" {{ request('exam_status') === '0' ? 'selected' : '' }}>
                                                Yangi
                                            </option>
                                            <option value="1" {{ request('exam_status') === '1' ? 'selected' : '' }}>
                                                Jarayonda
                                            </option>
                                            <option value="2" {{ request('exam_status') === '2' ? 'selected' : '' }}>
                                                Yakunlangan
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-2 mb-2 mb-md-0">
                                        <div class="d-flex justify-content-between" style="gap: 5px;">
                                            @if(request()->filled('search') || request()->filled('exam_status'))
                                                <button type="submit" class="btn btn-primary shadow-sm flex-fill">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                <a href="{{ route('final-results.index') }}"
                                                   class="btn btn-default shadow-sm flex-fill"
                                                   title="Filtrni tozalash">
                                                    <i class="fas fa-times text-danger"></i>
                                                </a>
                                            @else
                                                <button type="submit" class="btn btn-primary shadow-sm w-100">
                                                    <i class="fas fa-search mr-1"></i> Izlash
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-center table-custom">
                            <thead>
                            <tr>
                                {{--
                                                                <th style="width: 3%">
                                                                    <input type="checkbox">
                                                                </th>
                                --}}
                                <th style="width: 5%">#</th>
                                <th style="text-align: left">Talaba F.I.Sh.</th>
                                <th style="width: 7%">Ariza raqami</th>
                                <th>Fan nomi va guruhi</th>
                                <th style="width: 7%">Ta’lim tili</th>
                                <th style="width: 10%">Semestr / Kredit</th>
                                <th style="width: 7%">To‘plagan ball</th>
                                <th style="width: 7%">Urinish</th>
                                <th style="width: 7%">Holati</th>
                                @if($status=='uploaded' || $status=='archived')
                                    <th style="width: 7%">Amalni bajardi</th>
                                @endif
                                @if($status == 'all')
                                    <th class="text-nowrap" style="width: 7%">
                                        @can('exam.upload.all')
                                            <form action="{{ route('final-results.store') }}" method="POST"
                                                  class="d-inline-block">
                                                @csrf
                                                <button class="btn btn-primary btn-sm font-weight-bold"
                                                        type="submit"
                                                        title="O‘tish balidan yuqori bo‘lgan hamma talabalarning natijalarini ko‘chirish"
                                                        onclick="return confirm('O‘tish balidan yuqori bo‘lgan hamma talabalarning natijalarini ko‘chirishni tasdiqlaysizmi?')">
                                                    <i class="fa fa-cloud-upload-alt"></i>
                                                </button>
                                            </form>
                                        @endcan
                                        {{--@can('exam.upload.all')
                                            <a href="#" title="Hamma test natijalarini qayta ko‘rib chiqish"
                                               class="btn btn-outline-warning btn-sm">
                                                <i class="fa fa-reply"></i>
                                            </a>
                                        @endcan--}}
                                        {{--@can('exam.download')
                                            <a href="{{ route('final-results.download') }}"
                                               title="Imtihonlarning umumiy ro‘yxatini .XLSx formatida yuklab olish"
                                               class="btn btn-outline-danger btn-sm">
                                                <i class="fa fa-cloud-download-alt"></i>
                                            </a>
                                        @endcan--}}
                                    </th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($exams as $exam)
                                <tr>
                                    {{--<td>
                                        @if(($exam->results->first()->point ?? 0) >= $min_point)
                                            <input type="checkbox">
                                        @else
                                            <input type="checkbox" disabled>
                                        @endif
                                    </td>--}}
                                    <td>#{{ $exam->id }}</td>
                                    <td style="text-align: left">
                                        <div class="font-weight-bold">
                                            {{ json_decode($exam->student->name)->full_name }}
                                        </div>
                                        <div class="small">
                                            {{ $exam->student->specialty->code }} -
                                            {{ $exam->student->specialty->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $exam->application->application_number }}</code>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">
                                            {{ $exam->failed_subject->subject_name }}
                                        </div>
                                        <div class="small">
                                            {{ $exam->group->name }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ $exam->student->language->name }}
                                    </td>
                                    <td>
                                        {{ $exam->semester->name }} /
                                        {{ number_format($exam->failed_subject->credit, 2) }}
                                    </td>
                                    <td>
                                        {{ number_format($exam->results->first()->point ?? 0, 2) }}
                                    </td>
                                    <td>
                                        {{ $exam->attempt }}
                                    </td>
                                    <td>
                                        @if($exam->status == '0')
                                            <div class="badge badge-primary">
                                                Yangi
                                            </div>
                                        @elseif($exam->status == '1')
                                            <div class="badge badge-warning">
                                                Jarayonda
                                            </div>
                                        @elseif($exam->status == '2')
                                            <div class="badge badge-success">
                                                Yakunlangan
                                            </div>
                                        @endif
                                    </td>
                                    @if($status=='uploaded' || $status=='archived')
                                        <td class="text-nowrap">
                                            <div>
                                                @if($exam->admin)
                                                    {{ json_decode($exam->admin->name)->short_name ?? 'Noma’lum' }}
                                                @else
                                                    <span class="text-muted text-xs">Tizim (avtomat)</span>
                                                @endif
                                            </div>
                                            <code>
                                                {{ $exam->updated_at->format('d.m.Y H:i:s') }}
                                            </code>
                                        </td>
                                    @endif
                                    @if($status == 'all')
                                        <td class="text-nowrap">
                                            @if($exam->status == '2')
                                                @if($exam->results->first()->point >= $min_point)
                                                    @can('exam.upload')
                                                        <a href="{{ route('final-results.show', $exam->id) }}"
                                                           class="btn btn-outline-primary btn-sm"
                                                           onclick="return confirm('{{ addslashes(json_decode($exam->student->name)->full_name ?? '') }}ning {{ addslashes($exam->failed_subject->subject_name ?? '') }} fanidan bahosini serverga ko‘chirishni tasdiqlaysizmi?')">
                                                            <i class="fa fa-cloud-upload-alt"></i>
                                                            Ko‘chirish
                                                        </a>
                                                    @endcan
                                                @else
                                                    @can('exam.archive')
                                                        @if($exam->attempt == 1)
                                                            <form
                                                                action="{{ route('final-results.update', $exam->id) }}"
                                                                method="POST">
                                                                @method('PUT')
                                                                @csrf
                                                                <button class="btn btn-danger btn-sm font-weight-bold"
                                                                        type="submit"
                                                                        onclick="return confirm('{{ addslashes(json_decode($exam->student->name)->full_name ?? '') }}ning {{ addslashes($exam->failed_subject->subject_name ?? '') }} fanidan hozirgi natijasini arxivga olishni tasdiqlaysizmi?')">
                                                                    <i class="fa fa-archive"></i>
                                                                    Arxivlash
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endcan
                                                @endif
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        Ma’lumot topilmadi.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div class="card-footer bg-white clearfix">
                            <div class="float-right">
                                {{ $exams->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

