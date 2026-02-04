<?php

namespace App\Http\Controllers;

use App\Models\Venda;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\VendaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendas = Venda::with('cliente')->latest()->get();
        return view('vendas.index', compact('vendas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::ativo()->get();
        $produtos = Produto::where('estoque', '>', 0)->get();

        return view('vendas.create', compact('clientes', 'produtos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'forma_pagamento' => 'nullable|string|max:30',
            'data_compra' => 'nullable|date',
            'usar_endereco_cliente' => 'required|boolean',
            'endereco_entrega' => 'required_if:usar_endereco_cliente,0|string|max:255',
            'numero' => 'required_if:usar_endereco_cliente,0|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'required_if:usar_endereco_cliente,0|string|max:100',
            'cidade' => 'required_if:usar_endereco_cliente,0|string|max:100',
            'estado' => 'required_if:usar_endereco_cliente,0|string|size:2',
            'cep' => 'required_if:usar_endereco_cliente,0|string|max:15',
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
        ]);

        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $cliente = Cliente::findOrFail($request->cliente_id);
            $usarEnderecoCliente = $request->boolean('usar_endereco_cliente');
            $enderecoData = $usarEnderecoCliente
                ?[
                    'endereco_entrega' => $cliente->endereco,
                    'numero' => $cliente->numero,
                    'complemento' => $cliente->complemento,
                    'bairro' => $cliente->bairro,
                    'cidade' => $cliente->cidade,
                    'estado' => $cliente->estado,
                    'cep' => $cliente->cep,
                ]
                : [
                    'endereco_entrega' => $request->endereco_entrega,
                    'numero' => $request->numero,
                    'complemento' => $request->bairro->complemento,
                    'bairro' => $request->bairro,
                    'cidade' => $request->cidade,
                    'estado' => $request->estado,
                    'cep' => $request->cep,
                ];

            $total = 0;
            $itensData = [];

            foreach ($request->itens as $item) {
                $produto = Produto::find($item['produto_id']);

                if (!$produto->podeVender($item['quantidade'])) {
                    throw new \Exception(
                        "Produto {$produto->nome} não pode ser vendido." .
                        "Verifique status e estoque."
                    );
                }

                $subtotal = $produto->preco * $item['quantidade'];
                $total += $subtotal;

                $itensData[] = [
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $produto->preco,
                    'subtotal' => $subtotal
                ];
            }

            $venda = Venda::create([
                'cliente_id' => $request->cliente_id,
                'total' => $total,
                'status' => 'pendente',
                'forma_pagamento' => $request->forma_pagamento,
                'data_compra' => $request->data_compra,
                'usar_endereco_cliente' => $usarEnderecoCliente,
                'endereco_entrega' => $enderecoData['endereco_entrega'],
                'numero' => $enderecoData['numero'],
                'complemento' => $enderecoData['complemento'],
                'bairro' => $enderecoData['bairro'],
                'cidade' => $enderecoData['cidade'],
                'estado' => $enderecoData['estado'],
                'cep' => $enderecoData['cep'],
            ]);

            foreach ($itensData as $itemData) {
                VendaItem::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $itemData['produto_id'],
                    'quantidade' => $itemData['quantidade'],
                    'preco_unitario' => $itemData['preco_unitario']
                ]);
            }

            DB::commit();

            return redirect()->route('vendas.index')
                ->with('success', 'Venda criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venda $venda)
    {
        $venda->load(['cliente', 'itens.produto']);

        return view('vendas.show', compact('venda'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venda $venda)
    {
        if ($venda->status !== 'pendente') {
            abort(403, 'Só é possível editar vendas pendentes.');
        }

        $clientes = Cliente::orderBy('nome')->get();
        $produtos = Produto::orderBy('nome')->get();

        $venda->load(['itens.produto', 'cliente']);

        return view('vendas.edit', compact('venda', 'clientes', 'produtos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venda $venda)
    {
        if ($venda->status !== 'pendente') {
            abort(403, 'Só é possível editar vendas pendentes.');
        }

        $request->validate([
            'cliente_id' => ['required','exists:clientes,id'],
            'forma_pagamento' => ['nullable','string','max:30'],
            'data_compra' => ['nullable','date'],
            'usar_endereco_cliente' => ['required','boolean'],
            'endereco_entrega' => ['required_if:usar_endereco_cliente,0','string','max:255'],
            'numero' => ['required_if:usar_endereco_cliente,0','string','max:20'],
            'complemento' => ['nullable','string','max:100'],
            'bairro' => ['required_if:usar_endereco_cliente,0','string','max:100'],
            'cidade' => ['required_if:usar_endereco_cliente,0','string','max:100'],
            'estado' => ['required_if:usar_endereco_cliente,0','string','size:2'],
            'cep' => ['required_if:usar_endereco_cliente,0','string','max:15'],
            'itens' => 'required|array|min:1',
            'itens.*.produto_id' => 'required|exists:produtos,id',
            'itens.*.quantidade' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $cliente = Cliente::findOrFail($request->cliente_id);
            $usarEnderecoCliente = $request->boolean('usar_endereco_cliente');
            $enderecoData = $usarEnderecoCliente
                ? [
                    'endereco_entrega' => $cliente->endereco,
                    'numero' => $cliente->numero,
                    'complemento' => $cliente->complemento,
                    'bairro' => $cliente->bairro,
                    'cidade' => $cliente->cidade,
                    'estado' => $cliente->estado,
                    'cep' => $cliente->cep,
                ]
                : [
                    'endereco_entrega' => $request->endereco_entrega,
                    'numero' => $request->numero,
                    'complemento' => $request->complemento,
                    'bairro' => $request->bairro,
                    'cidade' => $request->cidade,
                    'estado' => $request->estado,
                    'cep' => $request->cep,
                ];

            // Remove old items
            $venda->itens()->delete();

            $total = 0;
            $itensData = [];

            // Procss of items
            foreach ($request->itens as $item) {
                $produto = Produto::find($item['produto_id']);

                if (!$produto->podeVender($item['quantidade'])) {
                    throw new \Exception(
                        "Produto {$produto->nome} não pode ser vendido."
                    );
                }

                $subtotal = $produto->preco * $item['quantidade'];
                $total += $subtotal;

                $itensData[] = [
                    'produto_id' => $item['produto_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $produto->preco
                ];
            }

            // att data of sell
            $venda->update([
                'cliente_id' => $request->cliente_id,
                'total' => $total,
                'forma_pagamento' => $request->forma_pagamento,
                'data_compra' => $request->data_compra,
                'usar_endereco_cliente' => $usarEnderecoCliente,
                'endereco_entrega' => $enderecoData['endereco_entrega'],
                'numero' => $enderecoData['numero'],
                'complemento' => $enderecoData['complemento'],
                'bairro' => $enderecoData['bairro'],
                'cidade' => $enderecoData['cidade'],
                'estado' => $enderecoData['estado'],
                'cep' => $enderecoData['cep'],
            ]);

            // crate new items
            foreach ($itensData as $itemData) {
                VendaItem::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $itemData['produto_id'],
                    'quantidade' => $itemData['quantidade'],
                    'preco_unitario' => $itemData['preco_unitario']
                ]);
            }

            DB::commit();

            return redirect()->route('vendas.historico')
                ->with('success', 'Venda atualizada!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venda $venda)
    {
        if ($venda->status != 'pendente') {
            return redirect()->route('vendas.index')
                ->with('error', 'Apenas vendas pendentes podem ser excluídas.');
        }

        $venda->delete();

        return redirect()->route('vendas.index')
            ->with('success', 'Venda excluída com sucesso!');
    }

    /**
     * altear the status sell
     */
    public function alterarStatus(Request $request, Venda $venda)
    {
        $request->validate([
            'status' => 'required|in:pago,cancelado'
        ]);

        try {
            $venda->mudarStatus($request->status);

            return redirect()->route('vendas.index')
                ->with('success', 'Status da venda alterado para: ' . $venda->status_formatado);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * show sales history
     */
    public function historico()
    {
        $vendas = Venda::with(['cliente', 'itens.produto'])
            ->latest('created_at')
            ->get();

        return view('vendas.historico', compact('vendas'));
    }
}
