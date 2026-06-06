<?php

namespace App\Http\Controllers;

use App\Models\Signature;
use Illuminate\Http\Request;

class SignatureController extends Controller
{
    public function index()
    {
        $signature = Signature::query()->first();

        return view('signature.index', [
            'signature' => $signature,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ass1' => ['required', 'string', 'max:1000'],
            'ass2' => ['required', 'string', 'max:1000'],
        ]);

        Signature::query()->updateOrCreate(['id' => 1], $validated);

        return redirect()
            ->route('signature.index')
            ->with('success', 'Assinaturas salvas com sucesso.');
    }
}
