<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
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
        $this->middleware('auth:api', ['except' => ['login', 'register', 'logout', 'find', 'delete', 'refresh']]);
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
            'input' => json_encode($credentials, true),
            'action' => 'login'                
        ]);
        if (! $token = auth()->attempt($credentials)) {
            $response = ['success' => false, 'error' => 'Unauthorized'];
            $this->insertLog([
                'input' => json_encode($response, true),
                'action' => 'login',
                'output' => json_decode($response, true)
            ], 1);
            return response()->json($response, 401);
        }
        $finalReponse = $this->respondWithToken($token);
        $this->insertLog([
            'input' => json_encode($credentials['email'], true),
            'action' => 'login',
            'output' => json_encode($finalReponse, true)   
        ], 1);
        return $finalReponse;
    }

    public function validateUniqueEmailandDni($dni, $email){
        $aErrors = [];
        $uniqueUser = User::where('dni', $dni)->where('status', '!=', 'trash')->get();
        if(count($uniqueUser)){
            $aErrors[] = ['data.dni.unique' => '- El DNI ingresado ya se encuentra registrado'];
        }
        $uniqueEmail = User::where('email', $email)->where('status', '!=', 'trash')->get();
        if(count($uniqueEmail)){
            $aErrors[] = ['data.email.unique' => '- El email ingresado ya se encuentra registrado'];
        }
        return $aErrors;
    }

    public function register(RegisterRequest $request)
    {   
        $this->insertLog([
            'input' => json_encode($request->data, true),
            'action' => 'register'        
        ]);        
        $aResponse = $this->validateRegionCommune($request);
        if(count($aResponse['errors'])){
            return $aResponse['errors'];
        }
        $aErrors = $this->validateUniqueEmailandDni($request->data['dni'], $request->data['email']);
        if(count($aErrors)){
            return [
                "success" => false,
                "message" => "Validation errors",
                "data" => $aErrors
            ];
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

    public function delete(Request $request){
        $input = "dni: ".(isset($request->dni) ? $request->dni : ""). " email: ".(isset($request->email) ? $request->email : "");
        $this->insertLog([
            'input' => $input,
            'action' => 'delete'
        ]);
        $usuario = null;
        if(isset($request->dni)){
            $usuario = User::where('dni', $request->dni)->where('status', 'A')->first();
        }else if(isset($request->email)){
            $usuario = User::where('email', $request->email)->where('status', 'A')->first();
        }
        if($usuario){
            $usuario->status = 'trash';
            $usuario->save();
            $aResponse = [
                'success' => true,
                'message' => "El usuario fue eliminado exitosamente"
            ];
        }else{
            $aResponse = [
                'success' => false,
                'message' => "Registro no existe"
            ];
        }
        $this->insertLog([
            'input' => $input,
            'action' => 'delete',
            'output' => json_encode($aResponse, true),
        ], 1);
        return $aResponse;
    }

    public function find(Request $request){
        $input = "dni: ".(isset($request->dni) ? $request->dni : ""). " email: ".(isset($request->email) ? $request->email : "");
        $this->insertLog([
            'input' => $input,
            'action' => 'find'
        ]);
        $aResponse = [
            'success' => false,
            'message' => "No se ingresaron filtros validos para realizar la busqueda"
        ];
        if(isset($request->dni) || isset($request->email)){
            $usuario = [];
            if(isset($request->dni)){
                $usuario = User::where('customers.status', 'A')
                    ->where('customers.dni', $request->dni)
                    ->join('regions', 'regions.id_reg', 'customers.id_reg')
                    ->join('communes', 'communes.id_com', 'customers.id_com')
                    ->select(DB::raw('
                        customers.name, 
                        customers.last_name, 
                        customers.address, 
                        regions.description AS region, 
                        communes.description AS municipio
                    '))
                    ->get();
            }else if(isset($request->email)){
                $usuario = User::where('customers.status', 'A')
                    ->where('customers.email', $request->email)
                    ->join('regions', 'regions.id_reg', 'customers.id_reg')
                    ->join('communes', 'communes.id_com', 'customers.id_com')
                    ->select(DB::raw('
                        customers.name, 
                        customers.last_name, 
                        customers.address, 
                        regions.description AS region, 
                        communes.description AS municipio
                    '))
                    ->get();
            }
            if(count($usuario)){
                $aResponse = [
                    'success' => true,
                    'message' => "Se encontro un usuario relacionado con los datos ingresados",
                    'data' => $usuario
                ];
            }else{
                $aResponse = [
                    'success' => true,
                    'message' => "No se encontraron usuarios relacionados con los datos ingresados"
                ];
            }
        }
        $this->insertLog([
            'input' => $input,
            'action' => 'find',
            'output' => json_encode($aResponse, true)
        ], 1);
        return $aResponse;
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
            'action' => 'register',
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

        return response()->json(['message' => '- El usuario cerro su sesi??n correctamente']);
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
