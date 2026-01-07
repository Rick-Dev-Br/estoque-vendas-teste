<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::latest()->get();
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'email' => 'required|email|unique:clientes,email',
        ], [
            'email.unique' => 'Este email já está cadastrado.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Cliente::create($request->all());
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        $cliente->load(['vendas' => function($q){
            $q->latest()->limit(5);
        }]);

        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'nome_completo' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100', 'unique:clientes,email,' . $cliente->id],
            'cpf' => ['nullable', 'string', 'max:14'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'max:15'],
            'status' => ['required', 'in:ativo,bloqueado'],
        ]);

        // Validar CPF único se fornecido
        if (!empty($data['cpf'])) {
            $cpfExiste = Cliente::where('cpf', $data['cpf'])
                ->where('id', '!=', $cliente->id)
                ->exists();
            if ($cpfExiste) {
                return back()->withErrors(['cpf' => 'CPF já cadastrado.'])->withInput();
            }
        }

        $cliente->update($data);

        return redirect()->route('clientes.edit', $cliente)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }

    public function toggleStatus(Cliente $cliente)
    {
        $cliente->status = $cliente->status == 'ativo' ? 'bloqueado' : 'ativo';
        $cliente->save();

        return redirect()->route('clientes.index')
            ->with('success', 'Status do cliente alterado');
    }
}
