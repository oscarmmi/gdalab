<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Models\Regions;
use App\Models\Communes;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        $this->insertLog([
            'input' => json_encode($credentials, true)            
        ]);
        if (! $token = auth()->attempt($credentials)) {
            $response = ['success' => false, 'error' => 'Unauthorized'];
            $this->insertLog([
                'input' => json_encode($response, true),
                'output' => json_decode($response, true)
            ], 1);
            return response()->json($response, 401);
        }
        $finalReponse = $this->respondWithToken($token);
        //return strlen(json_encode($finalReponse, true));
        $this->insertLog([
            'input' => json_encode($credentials['email'], true),
            'output' => json_encode($finalReponse, true)   
        ], 1);
        return $finalReponse;
    }

    public function register(RegisterRequest $request)
    {   
        $this->insertLog([
            'input' => json_encode($request->data, true)            
        ]);
        $aResponse = $this->validateRegionCommune($request);
        if(count($aResponse['errors'])){
            return $aResponse['errors'];
        }
        return $this->create([
            'dni' => $request->data['dni'],
            'id_reg' => $aResponse['data']['id_reg'],
            'id_com' => $aResponse['data']['id_com'],
            'email' => $request->data['email'],
            'name' => $request->data['name'],
            'last_name' => $request->data['last_name'],
            'address' => $request->data['address'],
            'date_reg' => date('Y-m-d H:i:s'),
            'status' => 'A',
            'password' => Hash::make($request->data['password'])
        ]);
    }

    public function find(Request $request){

    }

    public function validateRegionCommune($request){ 
        $aErrors = [];   
        $id_reg = NULL;
        $id_com = NULL;    
        $region = Regions::where('description', $request->data['region'])
        ->where('status', 'A')
        ->get();        
        if(!count($region)){
            $aErrors = [
                "success" => false,
                "message" => "Validation errors",
                "data" => [
                    "data.region" => "- El departamento ingresado no existe en el sistema"
                ]
            ];
        }else{
            $id_reg = $region[0]->id_reg;
        }
        $commune = Communes::where('description', $request->data['commune'])
        ->where('status', 'A')
        ->get();
        if(!count($commune)){
            $aErrors = [
                "success" => false,
                "message" => "Validation errors",
                "data" => [
                    "data.commune" => "- El municipio ingresado no existe en el sistema"
                ]
            ];
        }        
        $communeRegion = Communes::join('regions', 'regions.id_reg', 'communes.id_reg')
        ->where('communes.description', $request->data['commune'])
        ->where('regions.description', $request->data['region'])
        ->where('communes.status', 'A')
        ->where('regions.status', 'A')
        ->get();
        if(!count($communeRegion)){
            $aErrors = [
                "success" => false,
                "message" => "Validation errors",
                "data" => [
                    "data.commune" => "- El municipio ingresado no esta relacionado con la region ingresada"
                ]
            ];
        }else{
            $id_com = $communeRegion[0]->id_com;
        }
        return [
            'errors' => $aErrors,
            'data' => [
                'id_reg' => $id_reg,
                'id_com' => $id_com
            ]
        ];
    }

    protected function create(array $data)
    {        
        $flagCreation = User::create([
            'dni' => $data['dni'],
            'id_reg' => $data['id_reg'],
            'id_com' => $data['id_com'],
            'email' => $data['email'],
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'address' => $data['address'],
            'date_reg' => date('Y-m-d H:i:s'),
            'status' => 'A',
            'password' => $data['password'],
        ]);
        $response = [
            'success' => false,
            'message' => "Error al insertar el usuario"
        ];
        if($flagCreation){
            $response = [
                'success' => true,
                'message' => "El usuario fue creado exitosamente con ID # ".$flagCreation->id
            ];
        }
        unset($data['password']);
        $this->insertLog([
            'input' => json_encode($data, true),
            'output' => json_encode($response, true),
        ], 1);
        return $response;
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => '- El usuario cerro su sesión correctamente']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
