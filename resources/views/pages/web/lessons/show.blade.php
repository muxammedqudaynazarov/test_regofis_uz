@extends('layouts.web')

@section('style')
    <style>
        /* Paginatsiya raqamlari dizayni */
        .page-item.active .page-link {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }

        .page-link {
            color: #6f42c1;
            border-radius: 4px !important;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            font-size: 0.9rem;
        }

        .page-link:hover {
            color: #5a32a3;
            background-color: #e9ecef;
        }

        .page-item:first-child .page-link,
        .page-item:last-child .page-link {
            border-radius: 4px !important;
        }

        .custom-checkbox .custom-control-label::before {
            width: 1rem;
            height: 1rem;
        }

        .custom-checkbox .custom-control-label::after {
            width: 1rem;
            height: 1rem;
        }

        .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .custom-file-label::after {
            content: "Faylni tanlash";
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
                            <div class="card-body py-3">
                                @foreach($languages as $language)
                                    @php($quest_count = $subject->questions->where('language_id', $language->id)->count())
                                    <span
                                        class="badge @if($quest_count) badge-success @else badge-primary @endif mr-1 p-2"
                                        style="font-weight: normal; font-size: 13px;">
                                        {{ $language->name }} tilida:
                                        <b>{{ $quest_count }}</b> ta savol
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0" style="border-radius: 8px;">
                            <div class="card-header card-primary card-outline">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <div class="custom-control custom-checkbox header-checkbox mr-2">
                                            <input type="checkbox" class="custom-control-input" id="selectAll">
                                            <label class="custom-control-label" for="selectAll">
                                                Savollar bazasi
                                            </label>
                                        </div>
                                    </div>
                                    <div class="px-2">
                                        <button type="button" id="bulkDeleteBtn"
                                                class="btn btn-danger btn-sm shadow-sm p-0 px-1 m-0"
                                                style="display: none;" onclick="submitBulkDelete()">
                                            <i class="fas fa-trash-alt mr-1"></i> Tanlanganlarni o‘chirish
                                        </button>
                                        <a href="#" class="btn btn-info btn-sm shadow-sm p-0 px-1 m-0">
                                            <i class="fas fa-download mr-1"></i> Yuklash
                                        </a>
                                    </div>
                                    <div>
                                        <small class="text-muted mr-2">
                                            Jami: {{ $subject->questions->count() ?? 0 }} ta savol
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    @forelse($questions as $file)
                                        <div
                                            class="list-group-item d-flex align-items-start py-3 px-4 lesson-item">
                                            <div class="mr-3 pt-1">
                                                @if($file->user_id == auth()->id())
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox"
                                                               class="custom-control-input item-checkbox"
                                                               id="check_{{ $file->id }}"
                                                               value="{{ $file->id }}">
                                                        <label class="custom-control-label"
                                                               for="check_{{ $file->id }}"></label>
                                                    </div>
                                                @else
                                                    <div style="width: 24px;"></div>
                                                @endif
                                            </div>
                                            <div class="bg-light mr-3 p-2 rounded text-center"
                                                 style="width: 40px; height: 40px; min-width: 40px;">
                                                <i class="fas fa-question text-secondary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="text-sm font-weight-bold text-dark mb-1">
                                                    {!! $file->question_text !!}
                                                </div>
                                                @foreach($file->answers as $answer)
                                                    @php($isCorrect = $answer->correct == '1')
                                                    <div class="text-muted small">
                                                        {{ $answer->answer }}
                                                    </div>
                                                @endforeach
                                            </div>
                                            @can('subjects.resource.delete')
                                                @if($file->user_id == auth()->id())
                                                    <div class="ml-2">
                                                        <form action="{{ route('questions.destroy', $file->id) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('Haqiqatan ham ushbu savolni o‘chirmoqchimisiz?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger shadow-none"
                                                                    title="O'chirish">
                                                                <i class="far fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endcan
                                        </div>
                                    @empty
                                        <div class="text-center py-5">
                                            <img src="{{ asset('dist/img/no-data.png') }}" alt=""
                                                 style="width: 60px; opacity: 0.2;"
                                                 class="mb-3 d-block mx-auto">
                                            <i class="fas fa-file-import fa-2x text-light mb-2"></i>
                                            <p class="text-muted small">
                                                Hali test savollari yuklanmagan.<br>
                                                O'ng tomondagi panel orqali Aiken formatdagi fayl qo'shing.
                                            </p>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Paginatsiya --}}
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

                    @can('subjects.resource.create')
                        <div class="col-md-4">
                            <div class="card card-primary card-outline shadow-sm">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card-title font-weight-bold text-sm">Test yuklash</div>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <a href="{{ url('for_example.txt') }}"
                                               class="btn btn-xs btn-outline-info" download>
                                                <i class="fas fa-download mr-1"></i> Namuna
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
                                            <select name="language_id" class="form-control form-control-sm"
                                                    required>
                                                <option value="" disabled selected></option>
                                                @foreach($languages as $language)
                                                    <option
                                                        value="{{ $language->id }}">{{ $language->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label class="text-xs">Faylni tanlang (.txt)</label>
                                            <div class="custom-file custom-file-sm">
                                                <input type="file" name="questions_file"
                                                       class="custom-file-input" id="qFile" accept=".txt"
                                                       required>
                                                <label class="custom-file-label text-xs m-0" for="qFile">
                                                    Aiken format savollar...
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-0">
                                        <button type="submit"
                                                class="btn btn-primary btn-sm btn-block shadow-sm">
                                            <i class="fas fa-upload mr-1"></i> Bazaga qo'shish
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
        </section>

        {{-- YASHIRIN FORMA (OMMAVIY O'CHIRISH UCHUN) --}}
        <form id="bulkDeleteForm" action="{{ route('questions.destroyMany') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
            <input type="hidden" name="ids[]" id="bulkDeleteInput">
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('selectAll');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            // 1. "Hammasini belgilash" bosilganda
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    const isChecked = this.checked;
                    itemCheckboxes.forEach(cb => {
                        cb.checked = isChecked;
                    });
                    toggleBulkDeleteButton();
                });
            }

            // 2. Har bir checkbox bosilganda
            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    // Agar bittasi o'chsa, "Select All" ham o'chadi
                    if (!this.checked && selectAllCheckbox) {
                        selectAllCheckbox.checked = false;
                    }
                    // Agar hammasi belgilansa, "Select All" yonadi
                    if (selectAllCheckbox) {
                        const allChecked = Array.from(itemCheckboxes).every(c => c.checked);
                        if (allChecked && itemCheckboxes.length > 0) {
                            selectAllCheckbox.checked = true;
                        }
                    }
                    toggleBulkDeleteButton();
                });
            });

            // 3. Tugmani ko'rsatish/yashirish funksiyasi
            function toggleBulkDeleteButton() {
                const anyChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
                if (anyChecked) {
                    bulkDeleteBtn.style.display = 'inline-block';
                } else {
                    bulkDeleteBtn.style.display = 'none';
                }
            }
        });

        // 4. Formani yuborish funksiyasi
        function submitBulkDelete() {
            if (!confirm("Haqiqatan ham belgilangan savollarni o'chirmoqchimisiz?")) {
                return;
            }

            const selectedIds = [];
            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                selectedIds.push(cb.value);
            });

            if (selectedIds.length === 0) return;

            // Yashirin formaga ID larni qo'shish
            const form = document.getElementById('bulkDeleteForm');

            // Eski inputlarni tozalash (agar bo'lsa)
            form.innerHTML = '@csrf @method("DELETE")';

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            form.submit();
        }
    </script>
@endsection
