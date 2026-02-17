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
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2 text-sm">
                    <div class="col-sm-6">
                        <h1 class="font-weight-bold">{{ $subject->subject->name }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Asosiy</a></li>
                            <li class="breadcrumb-item active">Fan resurslari</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">

                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                @foreach($languages as $language)
                                    @php($quest_count = $subject->questions->where('language_id', $language->id)->count())
                                    <div class="badge @if($quest_count) badge-success @else badge-primary @endif"
                                         style="font-weight: normal">
                                        {{ $language->name }} tilida
                                        {{ $quest_count }} ta savol
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0" style="border-radius: 8px;">
                            <div class="card-header bg-white py-3">
                                <h3 class="card-title font-weight-bold text-dark text-sm">
                                    <i class="fas fa-database mr-2 text-primary"></i>
                                    Savollar bazasi
                                </h3>
                                <div class="card-tools">
                                    <small class="text-muted mr-2">
                                        Jami: {{ $subject->questions->count() ?? 0 }} ta savol
                                    </small>
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
                                                        {{ $file->question_text }}
                                                    </div>
                                                    @foreach($file->answers as $answer)
                                                        <div class="text-muted small">
                                                            {{ $answer->answer }}
                                                        </div>
                                                    @endforeach

                                                </div>
                                            </div>
                                            <div class="btn-group">
                                                <form action="{{ route('questions.destroy', $file->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Haqiqatan ham ushbu savolni o‘chirmoqchimisiz?');"
                                                      style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger shadow-none ml-1"
                                                            title="O'chirish">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </form>
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card-title font-weight-bold text-sm">Test yuklash</div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <a href="{{ url('for_example.txt') }}" class="btn btn-xs btn-outline-info"
                                           download><i class="fas fa-download mr-1"></i> Namuna
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('questions.update', $subject->id) }}" method="POST"
                                  enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="text-xs">Savollar tilni tanlang</label>
                                        <select name="language_id" class="form-control form-control-sm" required>
                                            <option value="" disabled selected></option>
                                            @foreach($languages as $language)
                                                <option value="{{ $language->id }}">{{ $language->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label class="text-xs">Faylni tanlang (.txt)</label>
                                        <div class="custom-file custom-file-sm">
                                            <input type="file" name="questions_file" class="custom-file-input"
                                                   id="qFile" accept=".txt" required>
                                            <label class="custom-file-label text-xs m-0" for="qFile">
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
            </div>
        </section>
    </div>

    <style>
        .custom-file-label::after {
            content: "Faylni tanlash";
        }
    </style>
@endsection
