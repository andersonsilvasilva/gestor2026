<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\Procediment;
use App\Models\eventos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Termwind\Components\Raw;
use App\Http\Requests\AgendaRequest;

class AgendaController extends Controller
{
    public function index($id)
    {

        $id = $this->decryptId($id);
        
        $cliente=Client::find($id);
        
        $procedimentos = Procediment::all();

        $terapeutas = user::where('id',Auth::user()->id)->first();

        return view('agenda.index',compact('cliente','terapeutas','procedimentos'));    
    }

    public function agendaAdd($id)
    {
        $cliente=Client::find($id);

        $procedimentos = Procediment::all();

        $terapeutas = user::where('ativo', true)->get();
    
        return view('agenda.add', compact('cliente','terapeutas','procedimentos'));    
    }

    public function getEvents()
    {
        $eventos = DB::table('eventos')
            ->join('procediments','eventos.procediments_id','=','procediments.id' )
            ->join('users','eventos.user_id','=','users.id' )
            ->join('clients','eventos.client_id','=','clients.id' )
            ->select('eventos.*','procediments.name as procedimento','clients.cns as codigo','clients.nome as nomecliente','clients.telefone','users.name as terapeuta')
            ->where('status','1')
            ->where('users.id',Auth::user()->id)
            ->whereNull('eventos.deleted_at')
            ->get();

        return response()->json($eventos);
    }

    public function mostraDados($id)
    {
        $dados = eventos::with(['Procediment_eventos', 'user_eventos'])->findOrFail($id);

        return response()->json([
            'evento'       => $dados,
            'procedimento' => $dados->Procediment_eventos->name ?? null,
            'terapeuta'    => $dados->user_eventos->name ?? null,
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:100',
            'start'           => 'required|date',
            'end'             => 'required|date|after:start',
            'client_id'       => 'required|exists:clients,id',
            'user_id'         => 'required|exists:users,id',
            'procediments_id' => 'required|exists:procediments,id',
            'color'           => 'required|string',
            'descricao'       => 'required|string|max:255',
        ]);

        eventos::create($validated);

        return redirect()->route('clientes')->with('sucesso', 'Evento criado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $bookings = eventos::findOrFail($id);
        $bookings->update([
            'start' => Carbon::parse($request->input('start_date'))->setTimezone('UTC'),
            'end'   => Carbon::parse($request->input('end_date'))->setTimezone('UTC'),
        ]);
        return response()->json('Eventos atualizado');
    }

    public function deleteEvent($id)
    {

        $schedule = eventos::findOrFail($id);
        $schedule->delete();

        return response()->json(['message'=>'Evento apagado com sucesso']);
    }

    private function decryptId($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (DecryptException $e) {
            return redirect()->route('home');
        }
        return $id;
    }

    
}

