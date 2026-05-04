<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $toolId = DB::table('tools')->insertGetId([
            'category_id' => 1, // Text Tools
            'name' => 'MCQ Question Maker',
            'slug' => 'mcq-generator',
            'short_description' => 'Create professional multiple choice questions from any text or topic automatically.',
            'component_name' => 'AiMcqGenerator',
            'icon' => 'ClipboardDocumentListIcon',
            'is_active' => true,
            'is_premium' => false,
            'daily_limit_free' => 5,
            'daily_limit_pro' => -1,
            'meta_title' => 'AI MCQ Question Maker - Generate Multiple Choice Questions Online',
            'meta_description' => 'Generate high-quality MCQ questions from any text, PDF, or topic using AI. Perfect for teachers, students, and examiners.',
            'meta_keywords' => 'mcq generator, question maker, ai quiz maker, multiple choice question generator, bangla mcq maker',
            'order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $systemPrompt = "You are an expert MCQ (Multiple Choice Question) creator. 
Your task is to generate highly accurate and relevant MCQ questions based on the provided topic or content.

For each question, follow this EXACT format:
[Question Number]. [Question Text]
[Option Label]) [Option Text]
[Option Label]) [Option Text]
[Option Label]) [Option Text]
[Option Label]) [Option Text]
[Question Number]. Answer: [Correct Option Label]
Explanation: [Brief explanation]

Localization Rules:
1. Language: English
   - Question Numbers: 1, 2, 3...
   - Option Labels: A, B, C, D
   - Example: 1. What is the capital of France?
              A) Paris
              1. Answer: A

2. Language: Bengali
   - Question Numbers: ১, ২, ৩...
   - Option Labels: ক, খ, গ, ঘ
   - Example: ১. ফ্রান্সের রাজধানী কোনটি?
              ক) প্যারিস
              ১. উত্তর: ক

3. Language: Hindi
   - Question Numbers: १, २, ३...
   - Option Labels: क, ख, ग, घ
   - Example: १. फ्रांस की राजधानी क्या है?
              क) पेरिस
              १. उत्तर: क

4. Language: Arabic/Urdu
   - Question Numbers: 1, 2, 3... (or ١, ٢, ٣...)
   - Option Labels: أ, ب, ج, د
   - Example: 1. ما هي عاصمة فرنسا؟
              أ) باريس
              1. الإجابة: أ

Important Instructions:
- Generate exactly the number of questions requested in the 'Count' option.
- The entire response MUST be in the requested 'Language'.
- Ensure questions match the 'Difficulty Level'.
- If a custom prompt is provided, follow its specific instructions.";

        $modelId = DB::table('ai_models')->where('name', 'gemini-2.5-flash')->value('id') ?? 7;

        DB::table('ai_tool_configs')->insert([
            'tool_id' => $toolId,
            'provider_id' => 2, // Gemini
            'model_id' => $modelId,
            'system_prompt' => $systemPrompt,
            'max_tokens_free' => 1000,
            'max_tokens_pro' => 3000,
            'max_input_length_free' => 2000,
            'max_input_length_pro' => 10000,
            'temperature' => 0.7,
            'show_language_selector' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tool = DB::table('tools')->where('slug', 'mcq-generator')->first();
        if ($tool) {
            DB::table('ai_tool_configs')->where('tool_id', $tool->id)->delete();
            DB::table('tools')->where('id', $tool->id)->delete();
        }
    }
};
