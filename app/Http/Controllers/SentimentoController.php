<?php
namespace App\Http\Controllers;

use App\Models\Sentimento;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Mockery\Exception;

class SentimentoController extends Controller
{
    private function analyzeSentiment($msg)
    {


        // Configurações do Guzzle
        $client = new Client([
            'base_uri' => 'https://api-inference.huggingface.co',
        ]);

        try {
            $response = $client->post('/models/distilbert-base-uncased-finetuned-sst-2-english', [
                'headers' => [
                    'Authorization' => 'Bearer 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',
                ],
                'json' => [
                    'inputs' => $msg,
                ],
            ]);

            // Retorna a resposta decodificada
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        } catch (GuzzleException $e) {

        }
    }

    public function analisar(Request $request)
    {
        $request->validate([
            'age' => 'integer|required|max:100|min:5',

            'stream_date' => 'string|required|max:255',
            'feedback' => 'required|string|max:255',
        ]);

        //traduzir mensagem para inglês

        $translated_msg = TranslateController::traduzir($request['feedback']);

        //Analisar mensagem traduzida
        $resultado = $this->analyzeSentiment($translated_msg);

        // Pegar o rótulo com maior confiança
        $maiorConfidencia = collect($resultado[0])->sortByDesc('score')->first();

        Sentimento::create(['age' => $request['age'],
                            'stream_date' => $request['stream_date'],
                            'analised_message' => $request['feedback'],
                            'sentiment' => $maiorConfidencia['label'],
                            'goal' => $maiorConfidencia['score'],
                            'value' => $maiorConfidencia['label'] === "NEGATIVE" ? 1 : 2 ,
            ]);

        return response()->json([
            'label' => $maiorConfidencia['label'],
            'score' => $maiorConfidencia['score'],
        ]);
    }

    public function estatistica(){

        try{
            $sentimento = Sentimento::all();

        }catch (\Exception $x){

        }

        return response()->json([
            'sentimento' => $sentimento,
        ]);
    }

}
