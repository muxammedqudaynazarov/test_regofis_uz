<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    /**
     * Savol matni yoki variantlar ichida matematika borligini aniqlash
     */
    private function detectQuestionType($text)
    {
        // 1. LaTeX bloklari: \( ... \) yoki \[ ... \] yoki $$ ... $$
        if (preg_match('/\\\\\(|\\\\\[|\$\$|\\\\begin\{equation\}/', $text)) {
            return 'math';
        }

        // 2. Aniq matematik buyruqlar: \frac, \sqrt, \int, \sum, \lim, \sin, \cos va h.k.
        // Bu yerda eng ko'p ishlatiladiganlari keltirilgan
        if (preg_match('/\\\(frac|sqrt|int|sum|lim|sin|cos|tan|cot|log|ln|pi|infty|theta|alpha|beta|gamma)/', $text)) {
            return 'math';
        }

        // 3. Daraja (^) yoki indeks (_) belgilari (faqat oddiy matn bo'lmasa)
        // Eslatma: Bu juda nozik, oddiy matnda ham uchrashi mumkin, shuning uchun ehtiyotkorlik bilan ishlatamiz.
        // Agar aniqroq kerak bo'lsa, bu qismni olib tashlash mumkin.
        if (preg_match('/[a-zA-Z0-9]\^[a-zA-Z0-9\{]|\_[a-zA-Z0-9\{]/', $text)) {
            return 'math';
        }

        return 'text';
    }

    /**
     * Aiken formatini tahlil qilish va xatolarni ajratish
     */
    private function parseAikenWithErrors($lines)
    {
        $validQuestions = [];
        $errors = [];

        $buffer = [];
        $blockStartLine = 1;

        // Fayl oxiriga yetganda oxirgi blokni ham ishlash uchun sun'iy bo'sh qator qo'shamiz
        $lines[] = "";

        foreach ($lines as $index => $line) {
            $line = trim($line); // Bo'shliqlarni tozalash
            $currentLineNum = $index + 1;

            // Agar qator bo'sh bo'lsa, demak bitta savol tugadi
            if ($line === '') {
                if (!empty($buffer)) {
                    // Yig'ilgan blokni tekshiramiz
                    $result = $this->processBlock($buffer);

                    if ($result['status'] === 'success') {
                        $validQuestions[] = $result['data'];
                    } else {
                        $errors[] = "Qator {$blockStartLine}-{$index}: " . $result['message'];
                    }

                    $buffer = [];
                }
                $blockStartLine = $currentLineNum + 1;
            } else {
                $buffer[] = $line;
            }
        }

        return ['valid' => $validQuestions, 'errors' => $errors];
    }

    /**
     * Bitta savol blokini tekshirish va ajratish
     */
    private function processBlock($lines)
    {
        // 1. Eng kamida 3 ta qator bo'lishi kerak
        if (count($lines) < 3) {
            return ['status' => 'error', 'message' => 'Format noto\'g\'ri yoki qatorlar yetarli emas.'];
        }

        // Oxirgi qator Javob kaliti bo'lishi shart
        $lastLine = array_pop($lines);
        if (!preg_match('/^ANSWER:\s*([A-Z])\s*$/i', $lastLine, $answerMatch)) {
            return ['status' => 'error', 'message' => "ANSWER qatori topilmadi yoki noto'g'ri: '$lastLine'"];
        }
        $correctOption = strtoupper($answerMatch[1]);

        // Qolgan qatorlardan variantlarni ajratamiz
        $answers = [];
        $questionTextArr = [];

        // Bu yerda mantiqni o'zgartiramiz: Qatorlarni teskari emas, to'g'ridan-to'g'ri o'qib,
        // qayerda "A." yoki "A)" boshlansa, o'sha yerdan pastini variant deb hisoblaymiz.

        $isOptionSection = false;

        foreach ($lines as $line) {
            // Variant formatini tekshirish: "A. Javob" yoki "A) Javob"
            if (!$isOptionSection && preg_match('/^([A-Z])[\.\)]\s+(.+)/', $line, $optMatch)) {
                $isOptionSection = true;
            }

            if ($isOptionSection) {
                // Agar variantlar zonasi boshlangan bo'lsa
                if (preg_match('/^([A-Z])[\.\)]\s+(.+)/', $line, $optMatch)) {
                    $key = strtoupper($optMatch[1]);
                    $value = trim($optMatch[2]);
                    $answers[$key] = $value;
                } else {
                    // Variantlar orasida oddiy matn kelsa (ko'p qatorli variant), uni oldingi variantga qo'shamiz
                    // Yoki bu xato format bo'lishi mumkin. Hozircha e'tiborsiz qoldiramiz yoki oxirgi variantga qo'shamiz.
                    $keys = array_keys($answers);
                    if (!empty($keys)) {
                        $lastParams = end($keys);
                        $answers[$lastParams] .= " " . $line;
                    }
                }
            } else {
                // Hali variantlar boshlanmadi, demak bu savol matni
                $questionTextArr[] = $line;
            }
        }

        $questionText = implode(" ", $questionTextArr);

        // Tekshiruvlar
        if (empty($questionText)) {
            return ['status' => 'error', 'message' => 'Savol matni topilmadi.'];
        }
        if (count($answers) < 2) {
            return ['status' => 'error', 'message' => 'Variantlar yetarli emas (kamida 2 ta).'];
        }
        if (!array_key_exists($correctOption, $answers)) {
            return ['status' => 'error', 'message' => "To'g'ri javob ($correctOption) variantlar orasida mavjud emas."];
        }

        // --- TYPE ANIQLASH QISMI ---
        // Savol matni va barcha variantlarni birlashtirib tekshiramiz
        $fullContentToCheck = $questionText . " " . implode(" ", $answers);
        $detectedType = $this->detectQuestionType($fullContentToCheck);

        return [
            'status' => 'success',
            'data' => [
                'question' => $questionText,
                'answers' => $answers,
                'correct' => $correctOption,
                'type' => $detectedType // Yangi qo'shilgan maydon
            ]
        ];
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'questions_file' => 'required|file|mimes:txt',
            'language_id' => 'required',
        ]);

        try {
            $file = $request->file('questions_file');
            $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES);
            $parseResult = $this->parseAikenWithErrors($lines);
            $validQuestions = $parseResult['valid'];
            $errors = $parseResult['errors'];
            if (empty($validQuestions) && !empty($errors)) {
                return redirect()->back()->with('error', 'Fayldagi barcha savollarda xatolik topildi.<br>' . implode('<br>', array_slice($errors, 0, 5)));
            }
            if (empty($validQuestions)) {
                return redirect()->back()->with('error', 'Fayl bo\'sh yoki format noto\'g\'ri.');
            }
            // DB tranzaksiyasi
            DB::beginTransaction();

            foreach ($validQuestions as $data) {
                $question = Question::create([
                    'question_text' => $data['question'],
                    'subject_id' => $id,
                    'language_id' => $request->language_id,
                    'type' => $data['type'],
                    'status' => '1'
                ]);
                // Variantlarni saqlash
                foreach ($data['answers'] as $key => $text) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer' => $text,
                        'correct' => ($key === $data['correct']) ? '1' : '0',
                        'type' => $data['type'],
                        'status' => '1',
                    ]);
                }
            }

            DB::commit();

            $successMsg = count($validQuestions) . ' ta savol muvaffaqiyatli yuklandi.';

            if (count($errors) > 0) {
                $errorMsg = "<br><br><b>" . count($errors) . " ta savol xatolik tufayli yuklanmadi:</b><br>" . implode('<br>', array_slice($errors, 0, 10));
                return redirect()->back()->with('warning', $successMsg . $errorMsg);
            }

            return redirect()->back()->with('success', $successMsg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Savol yuklashda xatolik: " . $e->getMessage());
            return redirect()->back()->with('error', 'Tizim xatoligi: ' . $e->getMessage());
        }
    }
}
