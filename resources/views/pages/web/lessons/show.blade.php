@extends('layouts.web')
@section('style')
    <style>
        /* Paginatsiya raqamlari dizayni */
        .page-item.active .page-link {
            background-color: #6f42c1; /* Sizning asosiy rangingiz */
            border-color: #6f42c1;
            color: white;
        }

        .page-link {
            color: #6f42c1;
            border-radius: 4px !important;
            margin: 0 2px; /* Raqamlar orasini biroz ochish */
            border: 1px solid #dee2e6;
            font-size: 0.9rem;
        }

        .page-link:hover {
            color: #5a32a3;
            background-color: #e9ecef;
        }

        /* "Previous" va "Next" tugmalari uchun */
        .page-item:first-child .page-link,
        .page-item:last-child .page-link {
            border-radius: 4px !important;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 text-sm">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">{{ $subject->name }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Imtihon sozlamalari</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if(!$subject->test)
                    <div class="row justify-content-center">
                        <div class="col-md-7">
                            <div class="card card-outline card-danger shadow-sm">
                                <div class="card-header">
                                    <div class="card-title font-weight-bold text-sm">
                                        Imtihon parametrlari
                                    </div>
                                </div>
                                <form action="{{ route('tests.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                    <input type="hidden" name="durations" value="50">
                                    <input type="hidden" name="questions" value="50">
                                    <input type="hidden" name="attempts" value="2">
                                    <input type="hidden" name="points" value="100">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 border-right">
                                                <p class="text-bold text-xs text-muted">Imtihon muddatlari</p>
                                                <div class="form-group">
                                                    <label class="text-xs">Boshlanish sanasi va vaqti</label>
                                                    <input type="datetime-local" name="started_at"
                                                           class="form-control form-control-sm" required>
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-xs">Tugallanish sanasi va vaqti</label>
                                                    <input type="datetime-local" name="finished_at"
                                                           class="form-control form-control-sm" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <p class="text-bold text-xs text-muted">Standart ko'rsatkichlar</p>
                                                <div class="form-group">
                                                    <label class="text-xs">Vaqt (daqiqa)</label>
                                                    <input type="number" class="form-control form-control-sm" value="50"
                                                           disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-xs">Urinishlar soni</label>
                                                    <input type="number" class="form-control form-control-sm" value="2"
                                                           disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label class="text-xs">O'tish bali</label>
                                                    <input type="number" class="form-control form-control-sm" value="60"
                                                           disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white text-right border-0">
                                        <button type="submit" class="btn btn-danger btn-sm px-4">
                                            <i class="fas fa-check-circle mr-1"></i> Imtihonni faollashtirish
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-outline card-success shadow-sm" style="border-radius: 8px;">
                                <div class="card-header border-0 pt-3">
                                    <h3 class="card-title font-weight-bold text-muted">
                                        <i class="fas fa-info-circle mr-1 text-success"></i> Test parametrlari
                                    </h3>
                                    <div class="card-tools">
                <span class="badge badge-success shadow-sm px-3 py-1" style="font-size: 11px;">
                    <i class="fas fa-check-circle mr-1"></i> FAOL
                </span>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div class="row m-0 text-center py-3 bg-white">
                                        <div class="col-4 border-right">
                                            <div class="text-muted small">Boshlanish vaqti</div>
                                            <div class="h6 mb-0 font-weight-bold text-dark">
                                                {{ $subject->test->started_at->format('d.m.Y') }}
                                                <span class="text-success ml-1">
                                                    {{ $subject->test->started_at->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-4 border-right">
                                            <div class="text-muted small">Tugallanish vaqti</div>
                                            <div class="h6 mb-0 font-weight-bold text-dark">
                                                {{ $subject->test->finished_at->format('d.m.Y') }}
                                                <span class="text-danger ml-1">
                                                    {{ $subject->test->finished_at->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-muted small">Qayta urinish</div>
                                            <div class="font-weight-bold text-primary">
                                                @if($subject->test->retest == 'y')
                                                    Ha
                                                @else
                                                    Yoq
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row m-0 text-center py-3 bg-white">
                                        <div class="col-4 border-right">
                                            <div class="text-muted small">Davomiyligi</div>
                                            <div class="font-weight-bold text-primary">{{ $subject->test->durations }}
                                                daqiqa
                                            </div>
                                        </div>
                                        <div class="col-4 border-right">
                                            <div class="text-muted small">Urinishlar</div>
                                            <div class="font-weight-bold text-primary">{{ $subject->test->attempts }}
                                                ta
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-muted small">Maksimal ball</div>
                                            <div class="font-weight-bold text-primary">
                                                {{ $subject->test->points }} ball
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-4 shadow-sm border-0" style="border-radius: 8px;">
                                <div class="card-header bg-white py-3">
                                    <h3 class="card-title font-weight-bold text-dark text-sm">
                                        <i class="fas fa-database mr-2 text-primary"></i> Savollar bazasi (Savollar)
                                    </h3>
                                    <div class="card-tools">
                                        <small class="text-muted mr-2">Jami: {{ count($subject->test->questions_list) }}
                                            ta savol</small>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        @forelse($questions as $file)
                                            <div
                                                class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 lesson-item">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success-light mr-3 p-2 rounded text-center"
                                                         style="width: 40px;">
                                                        <i class="fas fa-question text-success"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-weight-bold text-dark mb-0">
                                                            {{ $file->question }}
                                                        </div>
                                                        @foreach($file->answers as $answer)
                                                            <div class="text-muted small">
                                                                @if($answer->correct == '1')
                                                                    <i class="far fa-check-circle"></i>
                                                                @else
                                                                    <i class="far fa-circle"></i>
                                                                @endif
                                                                {{ $answer->text }}
                                                            </div>
                                                        @endforeach

                                                    </div>
                                                </div>
                                                <div class="btn-group">
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-danger shadow-none ml-1"
                                                            title="O'chirish">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-5">
                                                <img src="{{ asset('dist/img/no-data.png') }}" alt=""
                                                     style="width: 60px; opacity: 0.2;" class="mb-3 d-block mx-auto">
                                                <i class="fas fa-file-import fa-2x text-light mb-2"></i>
                                                <p class="text-muted small">
                                                    Hali test savollari yuklanmagan.<br>
                                                    O'ng tomondagi panel orqali Aiken formatdagi fayl qo'shing.
                                                </p>
                                            </div>
                                        @endforelse
                                    </div>
                                    <div class="card-footer bg-white clearfix">
                                        <div class="float-right">
                                            {{ $questions->links() }}
                                        </div>
                                        @if($questions->total())
                                            <div class="text-muted small mt-2">
                                                Jami: <b>{{ $questions->total() }}</b> ta savoldan
                                                <b>{{ $questions->firstItem() }}</b>-<b>{{ $questions->lastItem() }}</b>
                                                ko'rsatilmoqda
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="card card-primary card-outline shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold text-sm">Test yuklash</h3>
                                </div>
                                <form action="{{ route('questions.update', $subject->id) }}" method="POST"
                                      enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf
                                    <input type="hidden" name="test_id" value="{{ $subject->test->id }}">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="text-xs">Sarlavha</label>
                                            <input type="text" class="form-control form-control-sm bg-light"
                                                   value="{{ $subject->test->name }}" readonly disabled>
                                        </div>
                                        <div class="form-group">
                                            <label class="text-xs">Faylni tanlang (.txt)</label>
                                            <div class="custom-file custom-file-sm">
                                                <input type="file" name="questions_file" class="custom-file-input"
                                                       id="qFile" required>
                                                <label class="custom-file-label text-xs" for="qFile">
                                                    Aiken format savollar...
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-0">
                                        <button type="submit" class="btn btn-primary btn-sm btn-block shadow-sm">
                                            <i class="fas fa-upload mr-1"></i> Bazaga qo'shish
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <style>
        .custom-file-label::after {
            content: "Tallash";
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
@endsection
