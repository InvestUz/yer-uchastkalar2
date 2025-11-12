<?php

namespace App\Http\Controllers;

use App\Models\GlobalQoldiq;
use Illuminate\Http\Request;

class GlobalQoldiqController extends Controller
{
    /**
     * Display list of qoldiq records
     */
    public function index()
    {
        $qoldiqlar = GlobalQoldiq::orderBy('sana', 'desc')->get();

        return view('qoldiq.index', compact('qoldiqlar'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('qoldiq.form');
    }

    /**
     * Store new qoldiq record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sana' => 'required|date|unique:global_qoldiq,sana',
            'summa' => 'required|numeric|min:0',
            'tur' => 'required|in:plus,minus',
            'izoh' => 'nullable|string|max:1000'
        ]);

        GlobalQoldiq::create($validated);

        return redirect()->route('qoldiq.index')
            ->with('success', 'Қолдиқ муваффақиятли қўшилди!');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $qoldiq = GlobalQoldiq::findOrFail($id);

        return view('qoldiq.form', compact('qoldiq'));
    }

    /**
     * Update qoldiq record
     */
    public function update(Request $request, $id)
    {
        $qoldiq = GlobalQoldiq::findOrFail($id);

        $validated = $request->validate([
            'sana' => 'required|date|unique:global_qoldiq,sana,' . $id,
            'summa' => 'required|numeric|min:0',
            'tur' => 'required|in:plus,minus',
            'izoh' => 'nullable|string|max:1000'
        ]);

        $qoldiq->update($validated);

        return redirect()->route('qoldiq.index')
            ->with('success', 'Қолдиқ муваффақиятли янгиланди!');
    }

    /**
     * Delete qoldiq record
     */
    public function destroy($id)
    {
        $qoldiq = GlobalQoldiq::findOrFail($id);
        $qoldiq->delete();

        return redirect()->route('qoldiq.index')
            ->with('success', 'Қолдиқ ўчирилди!');
    }
}
