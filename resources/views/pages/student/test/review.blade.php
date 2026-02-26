@extends('layouts.app')

@section('style')
    <style>
        .test-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border-top: 4px solid #007bff;
            margin-bottom: 25px;
        }

        .test-card-header {
            padding: 25px;
            display: flex;
            align-items: flex-start;
            background: #fcfcfd;
            border-bottom: 1px solid #edf2f7;
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
            font-weight: 500;
            line-height: 1.6;
        }

        .test-card-body {
            padding: 25px;
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
            transition: all 0.2s ease;
            margin-bottom: 0;
            position: relative;
        }

        /* Tanlangan javob dizayni */
        .option-item.selected {
            border-color: #007bff;
            background: #f0f7ff;
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

        .selected .option-indicator {
            border-color: #007bff;
            background: #007bff;
        }

        .selected .option-indicator::after {
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

        .option-text {
            font-size: 1rem;
            color: #4a5568;
        }

        .selected .option-text {
            color: #007bff;
            font-weight: 600;
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
            color: #1a202c;
        }

        .map-item.answered {
            background: #007bff;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        }

        body {
            scroll-behavior: smooth;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper bg-white">
        <section class="content-header pt-4">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="font-weight-bold text-dark">###</h4>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-9">
                        @foreach($lessons as $attempt)
                            <div class="test-card shadow" id="q_{{ $attempt->id }}">
                                <div class="test-card-header">
                                    <span class="q-number">{{ $attempt->pos }}</span>
                                    <div class="q-text font-weight-bold">
                                        {!! $attempt->question->question_text !!}
                                    </div>
                                </div>
                                <div class="test-card-body">
                                    <div class="options-container">
                                        @foreach($attempt->question->answers as $answer)
                                            <div
                                                class="option-item {{ $attempt->answer_id == $answer->id ? 'selected' : '' }}">
                                                <div class="option-indicator"></div>
                                                <div class="option-text">
                                                    {{ $answer->answer }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="col-md-3">
                        <div class="sticky-top" style="top: 20px; z-index: 10;">
                            <div class="card shadow-sm border-0 rounded-lg">
                                <div class="card-header bg-white border-bottom-0 pt-3">
                                    <h6 class="font-weight-bold mb-0 text-center text-uppercase small text-muted">
                                        Savollar xaritasi
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="quiz-map">
                                        @foreach($lessons as $att)
                                            <a href="#q_{{ $att->id }}"
                                               class="map-item {{ $att->answer_id ? 'answered' : '' }}"
                                               title="Savol {{ $att->pos }}">
                                                {{ $att->pos }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
@endsection
