<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $busca = trim($request->string('busca')->toString());
        $status = $request->string('status')->toString();

        $clientes = Cliente::query()
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($subQuery) use ($busca) {
                    $subQuery->where('nome', 'like', "%{$busca}%")
                        ->orWhere('email', 'like', "%{$busca}%")
                        ->orWhere('cpf', 'like', "%{$busca}%");
                });
            })
            ->when(in_array($status, ['ativo', 'bloqueado'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'nome_completo' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100', 'unique:clientes,email'],
            'cpf' => ['nullable', 'string', 'max:14', 'unique:clientes,cpf'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:100'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'estado' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'max:15'],
            'status' => ['required', 'in:ativo,bloqueado'],
        ], [
            'email.unique' => 'Este email já está cadastrado.',
            'cpf.unique' => 'CPF já cadastrado.',
        ]);

        Cliente::create($dados);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente criado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load([
            'vendas' => function ($query) {
                $query->latest()->limit(10);
            },
        ]);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $cliente->load([
            'vendas' => function ($query) {
                $query->latest()->limit(5);
            },
        ]);

        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'nome_completo' => ['nullable', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100', Rule::unique('clientes', 'email')->ignore($cliente->id)],
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('clientes', 'cpf')->ignore($cliente->id)],
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

        $cliente->update($dados);

        return redirect()->route('clientes.edit', $cliente)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }

    public function toggleStatus(Cliente $cliente)
    {
        $cliente->status = $cliente->status === 'ativo' ? 'bloqueado' : 'ativo';
        $cliente->save();

        return redirect()->route('clientes.index')
            ->with('success', 'Status do cliente alterado.');
    }
}
