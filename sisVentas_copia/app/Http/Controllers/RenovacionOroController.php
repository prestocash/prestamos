<?php

namespace sisVentas\Http\Controllers;
use Illuminate\Http\Request;

use sisVentas\Http\Requests;

use sisVentas\oro;
use sisVentas\detalleoro;
use sisVentas\caja_egresos;
use sisVentas\caja_ingresos;
use sisVentas\ContratoRenovacionOro;
use Illuminate\Support\Facades\Redirect;
use sisVentas\Http\Requests\OroFormRequest;
use DB;
use Carbon\Carbon;

class RenovacionOroController extends Controller {
	
	protected $oro;
    protected $contratos_renovacionesoro;
   public function __construct()
    {

        // Verificar Autenticacion del Usuario
        //$this->middleware('auth');
        
        // Modelo Contratos
        $this->oro = new oro();
        
        // Modelo ContratosRenovaciones
        $this->contratos_renovacionesoro = new ContratoRenovacionOro();
    
    }
	public function index(Request $request) 
	{
		$now = Carbon::now ( 'America/Lima' );
		
		if ($request) {
			$oro = $this->oro->getContratosoro();
			return view ( 'detalles.renovacion_oro.index', compatc('oro', 'now'));
		}
	}
	
	public function show($id)
	{
	}
	
	
	public function create() 
	{
	}
	
	public function store(Request $request)
	{
		if ($request->dias > 0 &&  $request->dias < 35) {
			$data = [
					'fecha_renovacion' 	=> Carbon::parse($request->fecha_renovacion)->addDays(30)->format('Y-m-d'),
					'fecha_mes' 		=> Carbon::parse($request->fecha_renovacion)->addDays(60)->format('Y-m-d'),
					'fecha_final' 		=> Carbon::parse($request->fecha_renovacion)->addDays(65)->format('Y-m-d'),
			];
			
			$renovacion = $this->registrarRenovacion($request, $data);
		}elseif ($request->dias == 0){
			// Mensaje
			return redirect("contrato/renovacion/$request->contratos_id")
			->withErrors("No se realizaron correctamente los cambios solictados. Verifique e intente nuevamente.");
		}
		
		return redirect("contrato/oro");
	}
	
	public function registrarRenovacion($request, $data = array())
	{
		
		$contratos_renovaciones = new ContratoRenovacionoro();
		$renovacion = $contratos_renovaciones->create([
				'contratos_codigo'	=>	$request->contratos_codigo,
				'fecha_renovacion'	=>	$data['fecha_renovacion'],
				'fecha_mes'			=>	$data['fecha_mes'],
				'fecha_final'		=>	$data['fecha_final'],
				'dias'				=>	$request->dias,
				'total_interes'		=>	$request->total_interes,
				'total_mora'		=>	$request->total_mora,
				'total_pagado'		=>	$request->total_pagado,



		
		]);
 $caja_ingreso= new caja_ingresos;
        $caja_ingreso->contratos_codigo=$request->get('contratos_codigo');
        
        $caja_ingreso->tipo_movimiento = 'Ingresos Por Oro y Joyas';
        $caja_ingreso->monto = $request->get('total_pagado');
        $caja_ingreso->save();
		
		return $renovacion;
	}
	
	public function edit($id)
{

{
    
        // Consultar Contrato
        $oro = $this->oro->getContatoyDetallesContratooro($id);
        
        // Consultar Renovaciones
        $contratos_renovacionesoro = $this->contratos_renovacionesoro->getRenovacionesxContratooro($oro->codigo);
        
        // Fecha Actual
        $fecha_actual = Carbon::now();
        
        if ($contratos_renovacionesoro->count() == 0) {
            $fechas = [
                    'fecha_actual'  => $fecha_actual->format('Y-m-d'),
                    'fecha_inicio'  => $oro->fecha_inicio,
                    'fecha_mes'     => $oro->fecha_mes,
                    'fecha_final'   => $oro->fecha_final
            ];
            
            $fecha_inicio = Carbon::parse($fechas['fecha_inicio']);
            
        }else{
            
            $fechas = [
                    'fecha_actual'  => $fecha_actual->format('Y-m-d'),
                    'fecha_inicio'  => Carbon::parse($contratos_renovacionesoro->last()->fecha_renovacion)->format('Y-m-d'),
                    'fecha_mes'     => $contratos_renovacionesoro->last()->fecha_mes,
                    'fecha_final'   => $contratos_renovacionesoro->last()->fecha_final
            ];
            
            $fecha_inicio = Carbon::parse($fechas['fecha_inicio']);
        }
        
        $dias_transcurridos = $this->calcularDias($fecha_actual, $fecha_inicio);
        $total_interes = $this->calcularInteres($dias_transcurridos, $oro);
        $total_mora = $this->calcularMora($dias_transcurridos, $oro);
        
        return view ('detalles.renovacion_oro.index', compact('oro', 'contratos_renovacionesoro', 'fechas', 'dias_transcurridos', 'total_interes', 'total_mora'));
    }

}

public function calcularDias($fecha_mayor, $fecha_menor) 
    {
        if ($fecha_mayor > $fecha_menor) {
            $dias_transcurridos = $fecha_mayor->diffInDays($fecha_menor);
        }else $dias_transcurridos = 0;
        
        return $dias_transcurridos;
    }
    public function adicionarDias($fecha, $dias)
    {
        $fecha_con_dias_adicionales = $fecha->addDays($dias);
    
        return $fecha_con_dias_adicionales;
    }
    
    public function calcularInteres($dias, $detalles_contrato) 
    {
        $total_interes = 0;
        
        if ($dias <= 35) {
            $total_interes = $detalles_contrato->interes;
        }
        
        if ($dias > 35 && $dias <= 65) {
            $interes_diario = ($detalles_contrato->interes)/ 30;
            $total_interes = $interes_diario * $dias;
        }

        
        return $total_interes;
    }
    
    public function calcularMora($dias, $detalles_contrato) 
    {
        $total_mora = 0;
        
        if ($dias > 35 && $dias <= 60) {
            $total_mora = $detalles_contrato->interes * 0.30;
        }
    
        if ($dias > 60 && $dias <= 65) {
            $total_mora = $detalles_contrato->interes * 1;


        }
        
        return $total_mora;
    }
	public function vitrina($dias){
        
    }
}
