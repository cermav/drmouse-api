<?php /** @noinspection ALL */

namespace app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PriceChart;
use App\Types\UserRole;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PriceChartController extends Controller {
    /**
     * @throws AuthenticationException
     */
    public function index(): \Illuminate\Http\JsonResponse {
        $this->authorizeDoctor();
        $priceChart = PriceChart::where('doctor_id', Auth::user()->id)->get();

        return response()->json($priceChart);
    }

    /**
     * @throws AuthenticationException
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse {
        $doctor_id = $this->authorizeDoctor();

        $object = json_decode($request->getContent(), false);

        foreach ($object as $item) {
            $chart = PriceChart::create([
                'doctor_id' => $doctor_id,
                'description' => $item->description,
                'price' => $item->price,
                'currency' => $item->currency,
                'display' => $item->display
            ]);

            $chart->save();
        }

        return response()->json([], Response::HTTP_OK);
    }

    public function update(Request $request): \Illuminate\Http\JsonResponse {
        $doctor_id = $this->authorizeDoctor();

        $object = json_decode($request->getContent(), false);

        foreach ($object as $item) {
            $chartItem = PriceChart::find($item->id);

            $chartItem->update([
                'doctor_id' => $doctor_id,
                'description' => $item->description,
                'price' => $item->price,
                'currency' => $item->currency,
                'display' => $item->display
            ]);
        }

        return response()->json([], Response::HTTP_OK);
    }

    public function delete(Request $request) {}

    /**
     * @throws AuthenticationException
     */
    protected function authorizeDoctor() {
        $loggedUser = Auth::User();

        if ($loggedUser->role_id !== UserRole::DOCTOR && $loggedUser->role_id
            !== UserRole::ADMINISTRATOR) {
            throw new AuthenticationException();
        }

        return $loggedUser->id;
    }
}
