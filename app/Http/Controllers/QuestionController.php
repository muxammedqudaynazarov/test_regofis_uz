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
     * Aiken formatini tahlil qilish va xatolarni ajratish
     */
    private function parseAikenWithErrors($lines)
    {
        $validQuestions = [];
        $errors = [];

        $buffer = []; // Hozirgi o'qilayotgan savol blogi
        $blockStartLine = 1; // Xatolik qaysi qatorda ekanini bilish uchun

        // Fayl oxiriga yetganda oxirgi blokni ham ishlash uchun sun'iy bo'sh qator qo'shamiz
        $lines[] = "";

        foreach ($lines as $index => $line) {
            $line = trim($line);
            $currentLineNum = $index + 1;

            // Agar qator bo'sh bo'lsa, demak bitta savol tugadi
            if ($line === '') {
                if (!empty($buffer)) {
                    // Yig'ilgan blokni tekshiramiz
                    $result = $this->processBlock($buffer);

                    if ($result['status'] === 'success') {
                        $validQuestions[] = $result['data'];
                    } else {
                        // Xatolikni yozib olamiz (Qator raqami bilan)
                        $errors[] = "Qator {$blockStartLine}-{$index}: " . $result['message'];
                    }

                    // Bufferni tozalaymiz
                    $buffer = [];
                }
                // Keyingi blokning boshlanish qatorini belgilaymiz
                $blockStartLine = $currentLineNum + 1;
            } else {
                // Bo'sh bo'lmagan qatorlarni bufferga yig'amiz
                $buffer[] = $line;
            }
        }

        return ['valid' => $validQuestions, 'errors' => $errors];
    }

    /**
     * Bitta savol blokini tekshirish va ajratish (Helper function)
     */
    private function processBlock($lines)
    {
        // 1. Eng kamida 3 ta qator bo'lishi kerak (Savol + 2 variant + Javob)
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
        $questionText = [];

        // Orqadan oldinga qarab variantlarni qidiramiz
        // Chunki savol matni bir necha qator bo'lishi mumkin
        $parsingOptions = true;

        for ($i = count($lines) - 1; $i >= 0; $i--) {
            $line = $lines[$i];

            // Variant formatini tekshirish: "A. Javob" yoki "A) Javob"
            if ($parsingOptions && preg_match('/^([A-Z])[\.\)]\s+(.+)/', $line, $optMatch)) {
                $key = strtoupper($optMatch[1]);
                $value = trim($optMatch[2]);
                $answers[$key] = $value;
            } else {
                // Agar variant formati tugasa, demak bu savol matni
                $parsingOptions = false;
                // Savol matnini massiv boshiga qo'shamiz (teskari o'qigandik)
                array_unshift($questionText, $line);
            }
        }

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

        // Muvaffaqiyatli natija
        return [
            'status' => 'success',
            'data' => [
                'question' => implode(" ", $questionText), // Savol matnini birlashtiramiz
                'answers' => $answers,
                'correct' => $correctOption
            ]
        ];
    }

    public function update(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
            'questions_file' => 'required|file|mimes:txt',
        ]);

        try {
            $file = $request->file('questions_file');

            // FILE_SKIP_EMPTY_LINES ni olib tashladik, chunki bizga bloklarni ajratish uchun bo'sh joy kerak
            $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES);

            // Yangi parserni chaqiramiz
            $parseResult = $this->parseAikenWithErrors($lines);

            $validQuestions = $parseResult['valid'];
            $errors = $parseResult['errors'];

            if (empty($validQuestions) && !empty($errors)) {
                // Hamma savollar xato bo'lsa
                return redirect()->back()->with('error', 'Fayldagi barcha savollarda xatolik topildi. Iltimos formatni tekshiring.<br>' . implode('<br>', array_slice($errors, 0, 5)));
            }

            if (empty($validQuestions)) {
                return redirect()->back()->with('error', 'Fayl bo\'sh yoki format noto\'g\'ri.');
            }

            // DB tranzaksiyasi (Faqat to'g'ri savollar uchun)
            DB::beginTransaction();
            foreach ($validQuestions as $data) {
                $question = Question::create([
                    'question' => $data['question'],
                    'test_id' => $request->test_id,
                    'type' => 'text', // Yoki 'one_answer'
                    'status' => '1'
                ]);

                // Variantlar teskari tartibda yig'ilgan bo'lishi mumkin, ularni to'g'rilash (A, B, C...)
                ksort($data['answers']);

                foreach ($data['answers'] as $key => $text) {
                    Answer::create([
                        'question_id' => $question->id,
                        'text' => $text,
                        'correct' => ($key === $data['correct']) ? '1' : '0',
                        'status' => '1',
                    ]);
                }
            }
            DB::commit();

            // Xabarni shakllantirish
            $successMsg = count($validQuestions) . ' ta savol muvaffaqiyatli yuklandi.';

            if (count($errors) > 0) {
                // Agar xatoliklar bo'lsa, ularni ham ko'rsatamiz
                $errorMsg = "<br><br><b>Quyidagi " . count($errors) . " ta blokda xatolik tufayli o'tkazib yuborildi:</b><br>" . implode('<br>', $errors);
                // AdminLTE/Bootstrap alert-warning ishlatish tavsiya etiladi
                return redirect()->back()->with('warning', $successMsg . $errorMsg);
            }

            return redirect()->back()->with('success', $successMsg);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Tizim xatoligi: ' . $e->getMessage());
        }
    }
}
