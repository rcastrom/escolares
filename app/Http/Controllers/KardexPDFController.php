<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\DB;

class CertificadoPDFController extends Controller
{
    private $fpdf;
    public function __construct(){

    }
    public function crearPDF(Request $request){
        $periodo=$request->get('periodo');
        $rfc=$request->get('rfc');
        $this->fpdf=new Fpdf();
        $this->fpdf->AddPage('P','Letter');
        $this->fpdf->SetFont('Arial','',9);
        $nombre=DB::table('personal')->where('rfc',$rfc)->first();
        $doc=trim($nombre->apellidos_empleado).' '.trim($nombre->nombre_empleado);
        $this->fpdf->Cell(30,5,$doc,0,0,'L');
        $this->fpdf->Output();
        exit();
    }
}
