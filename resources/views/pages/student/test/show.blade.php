@extends('layouts.app')
@section('style')
    <style>
        .test-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border-top: 4px solid #007bff;
        }

        .test-card-header {
            padding: 25px;
            display: flex;
            align-items: flex-start;
            background: #fcfcfd;
        }

        .q-number {
            background: #007bff;
            color: #fff;
            min-width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }

        .q-text {
            font-size: 1.15rem;
            color: #2d3436;
            line-height: 1.6;
            font-weight: 500;
        }

        /* Javob Variantlari */
        .test-card-body {
            padding: 0 25px 25px 25px;
        }

        .options-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .option-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border: 2px solid #edf2f7;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            margin-bottom: 0;
        }

        .option-item:hover {
            background: #f8faff;
            border-color: #cbd5e0;
        }

        .option-item input[type="radio"] {
            display: none;
        }

        .option-indicator {
            width: 22px;
            height: 22px;
            border: 2px solid #cbd5e0;
            border-radius: 50%;
            margin-right: 15px;
            position: relative;
            flex-shrink: 0;
        }

        .option-item input[type="radio"]:checked + .option-indicator {
            border-color: #007bff;
            background: #007bff;
        }

        .option-item input[type="radio"]:checked + .option-indicator::after {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            background: #fff;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .option-item input[type="radio"]:checked ~ .option-text {
            color: #007bff;
            font-weight: 600;
        }

        .option-item input[type="radio"]:checked {
            background: #f0f7ff; /* Radio checked bo'lgandagi fon */
        }

        .option-item:has(input:checked) {
            border-color: #007bff;
            background: #f0f7ff;
        }

        /* Savollar Xaritasi */
        .quiz-map {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(35px, 1fr));
            gap: 6px;
        }

        .map-item {
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none !important;
            transition: 0.2s;
        }

        .map-item:hover {
            background: #e2e8f0;
        }

        .map-item.answered {
            background: #007bff;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        }

        /* Timer dizayni */
        #timerDisplay {
            color: #2d3436;
            font-variant-numeric: tabular-nums;
        }

        .text-blink {
            animation: blinker 1s linear infinite;
            color: #e74c3c !important;
        }

        @keyframes blinker {
            50% {
                opacity: 0.3;
            }
        }

        body {
            scroll-behavior: smooth;
        }
    </style>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

@endsection

@section('content')
    <div class="content-wrapper bg-white">
        <section class="content-header pb-2 pt-4">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-8">
                        <h4 class="font-weight-bold text-dark mb-1">{{ $lesson->failed_subject->subject_name ?? 'N/A'}}</h4>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <form id="examForm" action="{{ route('results.update', $lesson->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="exam_id" id="exam_id" value="{{ $lesson->id }}">

                    <div class="row">
                        <div class="col-md-9">
                            @foreach($attempts as $index => $attempt)
                                <div class="test-card mb-5" id="q_{{ $attempt->id }}">
                                    <div class="test-card-header">
                                        <span class="q-number">{{ $attempt->pos }}</span>
                                        <div class="q-text">
                                            {!! $attempt->question->question_text !!}
                                        </div>
                                    </div>
                                    <div class="test-card-body">
                                        <div class="options-container">
                                            @foreach($attempt->positions as $position)
                                                <label class="option-item" for="opt_{{ $position->id }}">
                                                    <input type="radio"
                                                           name="attempt[{{ $attempt->question_id }}]"
                                                           id="opt_{{ $position->id }}"
                                                           value="{{ $position->answer_id }}"
                                                           data-attempt-id="{{ $attempt->id }}"
                                                           data-question-id="{{ $attempt->question_id }}"
                                                           data-index="{{ $attempt->pos }}"
                                                           onchange="saveAnswer(this)"
                                                           @if($attempt->answer_id == $position->answer_id) checked @endif>
                                                    <span class="option-indicator"></span>
                                                    <span class="option-text">{{ $position->answer->answer }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-md-3">
                            <div class="sticky-top" style="top: 80px; z-index: 1000;">
                                <div class="card shadow-sm border-0 mb-3 rounded-lg">
                                    <div class="card-body p-3 text-center">
                                        <p class="text-muted mb-1 text-uppercase small font-weight-bold">Qolgan vaqt</p>
                                        <h2 class="font-weight-bold mb-0" id="timerDisplay">00:00:00</h2>
                                    </div>
                                </div>

                                <div class="card shadow-sm border-0 mb-3 rounded-lg">
                                    <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                        <h6 class="font-weight-bold mb-0 text-sm text-center text-uppercase">
                                            Savollar xaritasi
                                        </h6>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="quiz-map">
                                            @foreach($attempts as $att)
                                                <a href="#q_{{ $att->id }}"
                                                   id="map_btn_{{ $att->pos }}"
                                                   class="map-item {{ $att->answer_id ? 'answered' : '' }}">
                                                    {{ $att->pos }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <button type="button"
                                        class="btn btn-success btn-block btn-lg shadow-sm font-weight-bold rounded-lg py-3"
                                        onclick="finishExam()">
                                    <i class="fas fa-paper-plane mr-2"></i> Testni yakunlash
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function saveAnswer(element) {
            // Controller kutayotgan barcha ma'lumotlarni yig'amiz
            const examId = document.getElementById('exam_id').value;
            const attemptId = element.getAttribute('data-attempt-id');
            const questionId = element.getAttribute('data-question-id'); // Bu yangi qo'shildi
            const answerId = element.value;
            const index = element.getAttribute('data-index');

            const mapBtn = document.getElementById('map_btn_' + index);
            if (mapBtn) mapBtn.classList.add('answered');

            // Controllerdagi upload_answer funksiyasiga moslab yuboramiz
            fetch('/student/exams/answer/upload', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    exam_id: examId,       // Controller find($request->exam_id) uchun
                    attempt_id: attemptId, // Controller validation uchun
                    question_id: questionId, // Controller validation uchun
                    answer_id: answerId    // Controller validation uchun
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error') {
                        // Agar vaqt tugagan bo'lsa yoki boshqa xato bo'lsa ogohlantirish
                        Swal.fire('Xato!', data.message, 'error');
                    }
                })
                .catch(err => console.error('Network error:', err));
        }

        let endTime = new Date("{{ $lesson->finished_at }}").getTime();
        let serverTime = {{ time() * 1000 }};
        let timeDiff = serverTime - new Date().getTime();

        let timerInterval = setInterval(function () {
            let now = new Date().getTime() + timeDiff;
            let distance = endTime - now;
            if (distance < 0) {
                clearInterval(timerInterval);
                document.getElementById("timerDisplay").innerHTML = "00:00:00";
                submitExam(true);
                return;
            }
            let hours = Math.floor(distance / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById("timerDisplay").innerHTML =
                (hours < 10 ? "0" + hours : hours) + ":" +
                (minutes < 10 ? "0" + minutes : minutes) + ":" +
                (seconds < 10 ? "0" + seconds : seconds);

            if (distance < 60000) document.getElementById("timerDisplay").classList.add("text-blink");
        }, 1000);

        function finishExam() {
            Swal.fire({
                title: 'Test yakunlansinmi?',
                text: "Javoblaringiz qayta tekshirib oling.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ha, yakunlash',
                cancelButtonText: 'Yo‘q',
                confirmButtonColor: '#28a745'
            }).then(res => {
                if (res.isConfirmed) submitExam(false);
            });
        }

        function submitExam(isAuto) {
            window.onbeforeunload = null;
            document.getElementById('examForm').submit();
        }

        window.onbeforeunload = function () {
            return "Chiqib ketilsa test to'xtatiladi!";
        };
    </script>
@endsection
