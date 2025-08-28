<?php
namespace App\Http\Controllers;

use App\Models\Sentimento;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Mockery\Exception;

class SentimentoController extends Controller
{
    public function analyzeSentiment($msg)
    {
        $client = new Client([
            'base_uri' => env("HUGGING_FACE_BASE_URI"),
        ]);

        try {
            $response = $client->post(env("HUGGING_FACE_DISTILBERT_MODEL_URI"), [
                'headers' => [
                    'Authorization' => 'Bearer '.env("BREAR_HUGGING_FACE_TOKEN"),
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'inputs' => $msg,
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }


    public function analisar(Request $request): JsonResponse
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

    public function estatistica(): JsonResponse
    {

        try{
            $sentimento = Sentimento::all();

        }catch (\Exception $x){

        }

        return response()->json([
            'sentimento' => $sentimento,
        ]);
    }

}
