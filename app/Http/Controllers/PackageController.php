<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Model\Package;
use App\Model\Lista;
use Validator, DB;


class PackageController extends Controller
{  

    public function index()
    { 
      $total = DB::table('packages')
      ->join('lists', 'packages.list_id', '=', 'lists.id')
      ->join('users', 'lists.user_id', '=', 'users.id')
      ->select('lists.id as id', DB::raw('count(*) as total'), 'user_id', 'users.name', 'lists.status')
      ->groupBy('list_id')
      ->get();

        return response()->json([
          'code' => 200,
          "data" => $total
        ]);  
    }

    
    public function individual($id, $lista){
      $packages = Package::select("packages.*")
      ->where('id', $id)
      ->where('list_id', $lista)
      ->get();

      return response()->json([
        'code' => 200,
        "data" => $packages
      ]); 
    }
    

    public function entregados($id)
    { 
      $packages = Package::select("packages.*")
      ->where('list_id', $id)
      ->where('status', 1)
      ->get();

      return response()->json([
        'code' => 200,
        "data" => $packages
      ]); 
    }

    public function porentregar($id)
    { 
      $packages = Package::select("packages.*")
      ->where('list_id', $id)
      ->where('status', 2)
      ->get();

      return response()->json([
        'code' => 200,
        "data" => $packages
      ]);  
    }

    public function devueltos($id)
    { 
      $packages = Package::select("packages.*")
      ->where('list_id', $id)
      ->where('status', 3)
      ->get();

      return response()->json([
        'code' => 200,
        "data" => $packages
      ]); 
    }

     public function show($id)
    {   
      $packages = Package::select("packages.*")
      ->where('list_id', $id)
      ->get();

      return response()->json([
        'code' => 200,
        "data" => $packages
      ]);  
    }

    public function listado_asignado($id)
    {   
      $packages = Package::select("packages.*")
      ->join('lists', 'packages.list_id', '=', 'lists.id')
      ->where('lists.user_id', $id)
      ->get();

      return response()->json([
        'code' => 200,
        "data" => $packages
      ]);  
    }
     
    public function total_estatus($id)
    {   
      $packages = DB::table('packages')
                     ->select(DB::raw('count(*) as total, status'))
                     ->where('list_id', $id)
                     ->groupBy('status')
                     ->get();

      return response()->json([
        'code' => 200,
        "data" => $packages
      ]);  
    }
    //Borrar mediante el escaneo de codigo
    public function delete(Request $request)
    {
      $id = $request->code;
      
      $packages = Package::select("packages.*")
      ->where('code', $id)
      ->get();

        if ($packages == [] ) {
          return redirect()->back()->with(["mensaje" => "Paquete no encontrado",]);

        } else {
          DB::table('packages')->where('code', $id)->delete();
          return redirect()->back()->with(["mensaje" => "Paquete entregado exitosamente",]);
        }  
    }

    //Destruir con el boton
    public function destroy($id)
    {
      $package = Package::findOrFail($id);
      $package->delete();

      return redirect()->back()->with(["mensaje" => "Paquete entregado exitosamente",]);
    }

    //Funcion para obtener el tipo de paquete mediante el codigo
    public function obtener_tipo($code)
    {  
        //Datos del paquete
        $A = substr($code, 0, 4);
        $B = substr($code, 4, 4);
        $C = substr($code, 8, 4);
        $vol = ($A * $B * $C) / 1000000000;
        $orden = array($A, $B, $C);
        rsort($orden);

        // Probamos con las medidas de cada modelo pasando del A al B y asi en adelante
        if ( ($orden[0] <= 260) && ($orden[1] <=200) && ($orden[2] <= 100) && ($vol <= 0.0052) ) {

            $type = 'A';

          }else 
          if ( ($orden[0] <= 400) && ($orden[1] <=390) && ($orden[2] <= 50) && ($vol <= 0.0078) ){

            $type = 'B';

          }else 
          if ( ($orden[0] <= 400) && ($orden[1] <=390) && ($orden[2] <= 125) && ($vol <= 0.0195) ){

            $type = 'C';

          }else 
          if ( ($orden[0] <= 400) && ($orden[1] <=390) && ($orden[2] <= 250) && ($vol <= 0.039) ){

            $type = 'D';

          }else 
          if ( ($orden[0] > 840) && ($orden[1] > 550) || ($vol > 0.04) ){

            $type = 'GGV';

        }else {

          $type = 'GV';

        }

        return $type; 

    }

    //Funcion para obtener el peso de paquete mediante el codigo
    public function obtener_peso($code)
    {  
        //Datos del paquete
        $decimal = substr($code, -2);
        $unidad = substr($code, -4, 2);

        $peso = $unidad.'.'.$decimal;
        return $peso; 

    }

    //Esto funciona al cargar los litados solamente
    public function cargar()
    {       
      // Cargar listas 
      $data = file_get_contents(__DIR__.'\PackageController copy.json');
      $array = json_decode($data, true);

        foreach ($array as $obj) {
        
          $listas = Lista::select("lists.*")
          ->where('id', $obj['list_id'])
          ->get();
  
          if ($listas == '[]') {
            $listas = new Lista();
            $listas->id = $obj["list_id"];
            $listas->saveOrFail();
  
          }
  
          $type = $this->obtener_tipo($obj["code"]);
          $weigth = $this->obtener_peso($obj["code"]);
          
          $package = new Package();
          $package->code = $obj["code"];
          $package->route = $obj["route"];
          $package->type = $type;
          $package->kg = $weigth;
          $package->list_id = $obj["list_id"];
          $package->saveOrFail();
  
        }    
  
        
        return response()->json([
          'ok' => true, 'code'=> 200,
          'message' => "Rutas cargadas con exito."
        ]);
  
      
      
    }
    
    //Esto funciona al cargar los litados solamente
    public function cargarurl(String $url)
    {       
      // Cargar listas 
      $data = file_get_contents($url);
      $array = json_decode($data, true);

      if ($url == '[]') {
        foreach ($array as $obj) {
        
          $listas = Lista::select("lists.*")
          ->where('id', $obj['list_id'])
          ->get();
  
          if ($listas == '[]') {
            $listas = new Lista();
            $listas->id = $obj["list_id"];
            $listas->saveOrFail();
  
          }
  
          $type = $this->obtener_tipo($obj["code"]);
          $weigth = $this->obtener_peso($obj["code"]);
          
          $package = new Package();
          $package->code = $obj["code"];
          $package->route = $obj["route"];
          $package->type = $type;
          $package->kg = $weigth;
          $package->list_id = $obj["list_id"];
          $package->saveOrFail();
  
        }    
  
        
        return response()->json([
          'ok' => true, 'code'=> 200,
          'message' => "Rutas cargadas con exito."
        ]);
      } else {
        print("Error");
        return response()->json([
          'ok' => true, 'code'=> 404,
          'message' => "Error al cargar la ruta."
        ]);
      }

    }
    
    //Con esto se clasifican los paquetes y va de la mano de cargar_al_camion
    public function datos_clasificar($list)
    {
       //acumuladores para cada tipo 
      $acuma = $acumb = $acumc = $acumd = $acume = $mayor = $total = $let = 0;

      //consulta de los elementos dentro de la lista
      $packages = Package::select("packages.*")
      ->where('list_id', $list)
      ->where('location', 0)
      ->orderBy('created_at', 'desc')
      ->get();
            
      //recorro la lista para saber cual es el tipo de paquete con mayor cifra
      foreach ($packages as $pack) {
        if ($pack->type == 'A') {
          $acuma++;
          if ($acuma > $mayor) {
            $mayor++;
            $let= 'A';
          }
        }

        if ($pack->type == 'B') {
          $acumb++;
          if ($acumb > $mayor) {
            $mayor++;
            $let= 'B';            
          }
        }

        if ($pack->type == 'C') {
          $acumc++;
          if ($acumc > $mayor) {
            $mayor++;
            $let= 'C';                        
          }
        }

        if ($pack->type == 'D') {
          $acumd++;
          if ($acumd > $mayor) {
            $mayor++;
            $let= 'D';                        
          }
        }

        $total++;
      }

      $datos = ['A' => $acuma, 'B' =>$acumb, 'C' =>$acumc, 'D' =>$acumd,'mayor' =>$let, 'mayorc' =>$mayor, 'total' =>$total];
      
      return $datos;
    }

    //Con esto se clasifican los paquetes y va de la mano de cargar_al_camion    
    public function cargar_al_camion($list)
    { //valida si ya el camion a sido cargado 

      $datos = $this->datos_clasificar($list);

      $col = $lado = 1;
      $count = 1;      
      $cantidad = $datos['total']; 
      $acuma = $datos['A'];
      $acumb = $datos['B'];
      $acumc = $datos['C'];
      $acumd = $datos['D'];
      $max = $datos['mayor'];
      $maxc = $datos['mayorc'];
      

      while ($cantidad != 0) {
      //Revisa si esta vacio el B 
        if ($acumb == 0) {
          //Revisa si A es el maximo 
          if ($max == 'A') {
            if ($acumd == 0) {
              $proceso = $proceso = $this->CC($list, $col, 'A', 'A', 'C', 'C', 'GV', 'GGV',);        
              $col++;
            }else {
              $proceso = $this->CC($list, $col, 'A', 'A', 'C', 'D', 'GV', 'GGV',);        
              $col++;
            }
          }

          //Revisa si C es el maximo 
          if ($max == 'C') {
            if ($acumd == 0) {
              $proceso = $this->CB($list, $col, 'A', 'A', 'C', 'C', 'GV', 'GGV',);        
              $col++;
            }else {
              $proceso = $this->CB($list, $col, 'A', 'A', 'C', 'D', 'GV', 'GGV',);        
              $col++;
            }
            
          }

          //Revisa si D es el maximo 
          else{            
            $proceso = $this->CB($list, $col, 'A', 'A', 'C', 'D', 'GV', 'GGV',);        
            $col++;
          }
        }else

      //Revisa si esta vacio el C 
        if ($acumc == 0) {
            //Revisa si A es el maximo 
            if ($max == 'A') {
              if ($acumb == 0) {
                $proceso = $this->CA($list, $col, 'A', 'A', 'C', 'D', 'GV', 'GGV',);        
                $col++;
              }else {
                $proceso = $this->CC($list, $col, 'A', 'B', 'B', 'D', 'GV', 'GGV',);        
                $col++;
              }
            }
  
            //Revisa si B es el maximo 
            if ($max == 'B') {
                $proceso = $this->CA($list, $col, 'A', 'B', 'B', 'D', 'GV', 'GGV',);        
                $col++;
            }
  
            //Revisa si D es el maximo 
            else{  

                $proceso = $this->CA($list, $col, 'A', 'B', 'B', 'D', 'GV', 'GGV',);        
                $col++;
            }  

        }else 
      //Revisa si esta vacio el D
        if ($acumd == 0) {
            //Revisa si A es el maximo 
            if ($max == 'A') {
              if ($acumb == 0) {
                $proceso = $this->CA($list, $col, 'A', 'A', 'C', 'C', 'GV', 'GGV',);        
                $col++;
              }else {
                $proceso = $this->CC($list, $col, 'A', 'B', 'C', 'C', 'GV', 'GGV',);        
                $col++;
              }
            }else
  
            //Revisa si B es el maximo 
            if ($max == 'B') {
              if ($acumc == 0) {
                $proceso = $this->CA($list, $col, 'A', 'B', 'B', 'D', 'GV', 'GGV',);        
                $col++;
              }else {
                $proceso = $this->CB($list, $col, 'A', 'B', 'C', 'C', 'GV', 'GGV',);        
                $col++;
              }
              
            }

            //Revisa si C es el maximo 
            if ($max == 'C') {
                $proceso = $this->CB($list, $col, 'A', 'B', 'C', 'C', 'GV', 'GGV',);        
                $col++;
            }

        }

        

      else{
        //Si Todos tienen entra aqui 
        //Revisa si A es el maximo 
          if ($max == 'A') {

              $proceso = $this->CC($list, $col, 'a', 'b', 'c', 'd', 'gv', 'ggv');
              $col++; 
                     
          }else   
        //Revisa si B es el maximo 
            if ($max == 'B') {
              $proceso = $this->CA($list, $col, 'a', 'b', 'c', 'd', 'gv', 'ggv');
              $col++;            
            }else

        //Revisa si C es el maximo 
            if ($max == 'C') {
              $proceso = $this->CB($list, $col, 'a', 'b', 'c', 'd', 'gv', 'ggv');
              $col++;            
        //Revisa si D es el maximo
          }else{
        //Si el maximo es D valoramos el segundo mayor 
            $datos=$proceso = $this->menora($list);
            $max = $datos['mayor'];

            if ($max == 'C') {

                $proceso = $this->CB($list, $col, 'a', 'b', 'c', 'd', 'gv', 'ggv');
                $col++;  

            }else if($max == 'B'){

              $proceso = $this->CA($list, $col, 'a', 'b', 'c', 'd', 'gv', 'ggv');
              $col++; 

            }else{ //Max A 
              $proceso = $this->CC($list, $col, 'a', 'b', 'c', 'd', 'gv', 'ggv');
              $col++; 
            }  
                     
        }
      }  
        $datos = $this->datos_clasificar($list);
        $cantidad = $datos['total']; 
        $acuma = $datos['A'];
        $acumb = $datos['B'];
        $acumc = $datos['C'];
        $acumd = $datos['D'];
        $max = $datos['mayor'];
        $maxc = $datos['mayorc'];
        
      }//Cierrre del while

      DB::table('lists')
                ->where('id', $list)
                ->where('status', 1)
                ->update(['status' => 2]);

    }

    //Funciones para cargar columnas
    public function CA($list, $col, $a, $b, $c, $d, $gv, $ggv)
    {
      $cajon = 'C'.$col;
      DB::table('lists')
      ->where('id', $list)
      ->update([$cajon => 'CA']);

      $count = 1;
      
      $as = Package::select("packages.*")->where('list_id', $list)->where('type', $a)->where('location', 0)
      ->orderBy('created_at', 'desc')->limit(6)->get();
      $bs = Package::select("packages.*")->where('list_id', $list)->where('type', $b)->where('location', 0)
      ->orderBy('created_at', 'desc')->limit(20)->get();
      $cs = Package::select("packages.*")->where('list_id', $list)->where('type', $c)->where('location', 0)
      ->orderBy('created_at', 'desc')->limit(4)->get();
      $ds = Package::select("packages.*")->where('list_id', $list)->where('type', $d)->where('location', 0)
      ->orderBy('created_at', 'desc')->limit(2)->get();
      
      if ($as != ['']) {
        foreach ($as as $a ) {
          
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'A',
            'location' => $count,
            ]);
          $count++;
        }
      }

      if ($bs != ['']) {
        foreach ($bs as $a ) {
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'B',
            'location' => $count,
            ]);
          $count++;
        }
      }

      if ($cs != ['']) {        
        foreach ($cs as $a ) {
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'C',
            'location' => $count,
            ]);
          $count++;
        }
      }

      if ($ds != ['']) {        
        foreach ($ds as $a ) {
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'D',
            'location' => $count,
            ]);
          $count++;
        }
      }     

      return (($count - 1) * 100 ) / 32;

    }

    public function CB($list, $col, $a, $b, $c, $d, $gv, $ggv)
    {
      $cajon = 'C'.$col;
      DB::table('lists')
      ->where('id', $list)
      ->update([$cajon => 'CB']);

      $count = 1;

      $as = Package::select("packages.*")->where('list_id', $list)->where('type', $a)->where('location', 0)
      ->orderBy('created_at', 'desc')->limit(6)->get();
      $cs = Package::select("packages.*")->where('list_id', $list)->where('type', $c)->where('location', 0)
      ->orderBy('created_at', 'desc')->limit(12)->get();
      $ds = Package::select("packages.*")->where('list_id', $list)->where('type', $d)->where('location', 0)
      ->orderBy('created_at', 'desc')->limit(2)->get();
      
      if ($as != ['']) {        
        foreach ($as as $a ) {
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'A',
            'location' => $count,
            ]);
          $count++;            
        }
      }

      if ($cs != ['']) {        
        foreach ($cs as $a ) {
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'C',
            'location' => $count,
            ]);
          $count++;
        }
      }

      if ($ds != ['']) {        
        foreach ($ds as $a ) {
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'D',
            'location' => $count,
            ]);
          $count++;
        }
      }

      return (($count - 1) * 100 ) / 20;

    }

    public function CC($list, $col, $a, $b, $c, $d, $gv, $ggv)
    {
      $cajon = 'C'.$col;
      DB::table('lists')
      ->where('id', $list)
      ->update([$cajon => 'CC']);

      $count = 1;

      $as = Package::select("packages.*")->where('list_id', $list)->where('type', $a)->where('location', 0)->orderBy('created_at', 'desc')->limit(18)->get();
      $bs = Package::select("packages.*")->where('list_id', $list)->where('type', $b)->where('location', 0)->orderBy('created_at', 'desc')->limit(12)->get();
      $cs = Package::select("packages.*")->where('list_id', $list)->where('type', $c)->where('location', 0)->orderBy('created_at', 'desc')->limit(4)->get();
      $ds = Package::select("packages.*")->where('list_id', $list)->where('type', $d)->where('location', 0)->orderBy('created_at', 'desc')->limit(2)->get();
      
      if ($as != ['']) {
        foreach ($as as $a ) {
          
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'A',
            'location' => $count,
            ]);
          $count++;
        }
      }

      if ($bs != ['']) {
        foreach ($bs as $a ) {
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'B',
            'location' => $count,
            ]);
          $count++;
        }
      }

      if ($cs != ['']) {        
        foreach ($cs as $a ) {
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'C',
            'location' => $count,
            ]);
          $count++;
        }
      }

      if ($ds != ['']) {        
        foreach ($ds as $a ) {
          $list = Package::select("packages.*")
          ->where('id', $a['id'])
          ->update([
            'column' => $col,
            'box' => 'D',
            'location' => $count,
            ]);
          $count++;
        }
      }  
      
      return (($count - 1) * 100 ) / 36;

    }

    //Funcion para cuando sea el MAX = D (Esto es para ayudar a la clasificación)
    public function menora($list)
    {
       //acumuladores para cada tipo 
      $acuma = $acumb = $acumc = $acumd = $acume = $mayor = $total = $let = 0;

      //consulta de los elementos dentro de la lista
      $packages = Package::select("packages.*")
      ->where('list_id', $list)
      ->where('location', 0)
      ->orderBy('created_at', 'desc')
      ->get();

      //recorro la lista para saber cual es el tipo de paquete con mayor cifra
      foreach ($packages as $pack) {
        
        if ($pack->type == 'A') {
          $acumb++;
          if ($acumb > $mayor) {
            $mayor++;
            $let= 'B';            
          }
        }

        if ($pack->type == 'B') {
          $acumb++;
          if ($acumb > $mayor) {
            $mayor++;
            $let= 'B';            
          }
        }

        if ($pack->type == 'C') {
          $acumc++;
          if ($acumc > $mayor) {
            $mayor++;
            $let= 'C';                        
          }
        }

        $total++;
      }

      $datos = ['A' => $acuma, 'B' =>$acumb, 'C' =>$acumc, 'D' =>$acumd, 'mayor' =>$let, 'mayorc' =>$mayor, 'total' =>$total];
      
      return $datos;
    }

}

