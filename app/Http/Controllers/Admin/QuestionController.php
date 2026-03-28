<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Test $test): View
    {
        $test->load(['questions.options']);
        return view('admin.tests.questions.index', compact('test'));
    }

    public function store(Request $request, Test $test): RedirectResponse
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'type' => 'required|in:multiple_choice,likert,open',
            'points' => 'required|integer|min:1|max:100',
            'is_required' => 'boolean',
            'options' => 'required_unless:type,open|array|min:2',
            'options.*.text' => 'required|string|max:500',
            'options.*.value' => 'required|numeric',
            'options.*.is_correct' => 'boolean',
        ]);

        $nextOrder = $test->questions()->max('order') + 1;

        $question = Question::create([
            'test_id' => $test->id,
            'text' => $validated['text'],
            'type' => $validated['type'],
            'points' => $validated['points'],
            'order' => $nextOrder,
            'is_required' => $request->boolean('is_required', true),
        ]);

        if ($validated['type'] !== 'open' && !empty($validated['options'])) {
            foreach ($validated['options'] as $i => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'text' => $opt['text'],
                    'value' => $opt['value'],
                    'is_correct' => isset($opt['is_correct']) && $opt['is_correct'],
                    'order' => $i + 1,
                ]);
            }
        }

        return redirect()->route('admin.tests.questions.index', $test)
            ->with('success', 'Pregunta agregada correctamente.');
    }

    public function update(Request $request, Test $test, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
            'is_required' => 'boolean',
            'options' => 'nullable|array|min:2',
            'options.*.id' => 'nullable|exists:question_options,id',
            'options.*.text' => 'required|string|max:500',
            'options.*.value' => 'required|numeric',
            'options.*.is_correct' => 'boolean',
        ]);

        $question->update([
            'text' => $validated['text'],
            'points' => $validated['points'],
            'is_required' => $request->boolean('is_required', true),
        ]);

        if (!empty($validated['options'])) {
            $question->options()->delete();
            foreach ($validated['options'] as $i => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'text' => $opt['text'],
                    'value' => $opt['value'],
                    'is_correct' => isset($opt['is_correct']) && $opt['is_correct'],
                    'order' => $i + 1,
                ]);
            }
        }

        return redirect()->route('admin.tests.questions.index', $test)
            ->with('success', 'Pregunta actualizada.');
    }

    public function destroy(Test $test, Question $question): RedirectResponse
    {
        $question->delete();
        // Reordena las preguntas restantes
        $test->questions()->orderBy('order')->each(function ($q, $i) {
            $q->update(['order' => $i + 1]);
        });

        return redirect()->route('admin.tests.questions.index', $test)
            ->with('success', 'Pregunta eliminada.');
    }
}
