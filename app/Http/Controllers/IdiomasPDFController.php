<?php

namespace App\Http\Controllers;

use App\Alumnos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IdiomasPDFController extends Controller
{
    private $fpdf;
    public function __construct(){

    }
    public function mes_espanol($mes)
    {
        switch ($mes)
        {
            case '01': return 'Enero'; break;
            case '02': return 'Febrero'; break;
            case '03': return 'Marzo'; break;
            case '04': return 'Abril'; break;
            case '05': return 'Mayo'; break;
            case '06': return 'Junio'; break;
            case '07': return 'Julio'; break;
            case '08': return 'Agosto'; break;
            case '09': return 'Septiembre'; break;
            case '10': return 'Octubre'; break;
            case '11': return 'Noviembre'; break;
            case '12': return 'Diciembre'; break;
        }
    }
    public function dia_espanol($dia)
    {

        switch($dia)
        {
            case 1: return 'Domingo'; break;
            case 2: return 'Lunes'; break;
            case 3: return 'Martes'; break;
            case 4: return 'Miercoles'; break;
            case 5: return 'Jueves'; break;
            case 6: return 'Viernes'; break;
            case 7: return 'Sabado'; break;

        }
    }
    public function fecha_espanol($fecha = NULL)
    {
        if(!$fecha)
        {
            return date("d")." de ".$this->mes_espanol(date("m"))." del ".date("Y");
        }
        else
        {
            return substr($fecha, 8, 2)." de ".$this->mes_espanol(substr($fecha, 5, 2))." de ".substr($fecha, 0, 4);
        }
    }

    public function mes($mes){
        switch($mes){
            case '01': $nmes="enero"; break;
            case '02': $nmes="febrero"; break;
            case '03': $nmes="marzo"; break;
            case '04': $nmes="abril"; break;
            case '05': $nmes="mayo"; break;
            case '06': $nmes="junio"; break;
            case '07': $nmes="julio"; break;
            case '08': $nmes="agosto"; break;
            case '09': $nmes="septiembre"; break;
            case '10': $nmes="octubre"; break;
            case '11': $nmes="noviembre"; break;
            case '12': $nmes="diciembre"; break;
        }
    return $nmes;
}
public function encabezado($pdf,$depto,$folio,$dia,$mes,$anio){
        //$pdf->Image("/var/www/html/escolares/public/img/aguila.jpg",0,0,'','','JPG');
        // Logo SEP
        //$pdf->Image("/var/www/html/escolares/public/img/educacion.jpg",25,10,77,22,'JPG');
        $pdf->Image("/var/www/html/escolares/public/img/escudo_2021.jpg",25,10,110,18,'JPG');
        // Logo TecNM
        //$pdf->Image("/var/www/html/escolares/public/img/tecnm.jpg",150,12,34,18,'JPG');
        //Leyenda
        $pdf->AddFont('MM','','Montserrat-Medium.php');
        $pdf->AddFont('MM','B','Montserrat-Bold.php');
        $pdf->SetFont('MM','B',9);
        $pdf->SetXY(140,30);

        $ndepto=DB::table('organigrama')->where('clave_area',$depto)->first();
        $pdf->Cell(50,6,utf8_decode("Instituto Tecnológico de Ensenada"),0,1,'L');
        $pdf->SetFont('MM','',8);
        $pdf->SetXY(140,34);
        $pdf->Cell(50,6,$ndepto->descripcion_area,0,1,'L');
        $pdf->SetFont('MM','B',8);
       // $pdf->Cell(200,5,utf8_decode("\"2020, Año de Leona Vicario, Benemérita Madre de la Patria \""),0,1,"C");
        $asunto=utf8_decode("LIBERACIÓN IDIOMA EXTRANJERO");
        $h 	= 4;
        $wt = 27;
        $wd = 36;
        $y 	= 48;//Original 48
        $xt = 140;
        $b  = $h+1.5;
        $xd = $xt + $wt;
        $pdf->SetXY($xt, $y);
        //1ra linea
        $pdf->Cell($wt,$h,"Ensenada, BC.,",0,0,"L");
        $pdf->SetXY($xd,$y);
        $pdf->SetTextColor(255,255,255);
        $fecha=$dia."/".$this->mes($mes)."/".$anio;
        $pdf->Cell($wd,$h,$fecha,0,1,"L",true);
        $pdf->SetTextColor(0,0,0);
        //2da linea
        $pdf->SetXY($xt,$y+$b);
        //$pdf->Cell($wt,$h,"Oficio",0,0,"L");
        //$pdf->SetXY($xd,$y+$b);
        //$pdf->SetTextColor(255,255,255);
        $pdf->Cell($wd,$h,$folio,0,0,"L");
        $pdf->SetTextColor(0,0,0);
        //4ta linea
        $pdf->SetXY($xt,$y+2*$b);
        $pdf->Cell($wt,$h,"Asunto",0,0,"L");
        $pdf->SetXY($xd,$y+2*$b);
        //$pdf->SetTextColor(255,255,255);
        $pdf->MultiCell($wd,$h,$asunto,0,"L");
        $pdf->SetTextColor(0,0,0);
        return $pdf;
    }
    public function fecha_completa($fecha = NULL)
    {
        if(!$fecha) {
            return "Ensenada, B.C., a ".$this->fecha_espanol($fecha);
        } else {
            return "Ensenada, B.C., a ".$this->fecha_espanol($fecha);
        }
    }
    public function nperiodo($periodo, $largo=false)
    {
        if(substr($periodo,4,1) == '1') { return (($largo)?"ENERO-JUNIO/":"ENE-JUN/").substr($periodo,0,4);}
        if(substr($periodo,4,1) == '2') { return "VERANO/".substr($periodo,0,4);}
        if(substr($periodo,4,1) == '3') { return (($largo)?"AGOSTO-DICIEMBRE/":"AGO-DIC/").substr($periodo,0,4);}
    }

    public function num_a_letra($num, $fem = true, $dec = true) {
//if (strlen($num) > 14) die("El número introducido es demasiado grande");
        $matuni[2]  = "dos";
        $matuni[3]  = "tres";
        $matuni[4]  = "cuatro";
        $matuni[5]  = "cinco";
        $matuni[6]  = "seis";
        $matuni[7]  = "siete";
        $matuni[8]  = "ocho";
        $matuni[9]  = "nueve";
        $matuni[10] = "diez";
        $matuni[11] = "once";
        $matuni[12] = "doce";
        $matuni[13] = "trece";
        $matuni[14] = "catorce";
        $matuni[15] = "quince";
        $matuni[16] = "dieciseis";
        $matuni[17] = "diecisiete";
        $matuni[18] = "dieciocho";
        $matuni[19] = "diecinueve";
        $matuni[20] = "veinte";
        $matunisub[2] = "dos";
        $matunisub[3] = "tres";
        $matunisub[4] = "cuatro";
        $matunisub[5] = "quin";
        $matunisub[6] = "seis";
        $matunisub[7] = "sete";
        $matunisub[8] = "ocho";
        $matunisub[9] = "nove";
        $matdec[2] = "veint";
        $matdec[3] = "treinta";
        $matdec[4] = "cuarenta";
        $matdec[5] = "cincuenta";
        $matdec[6] = "sesenta";
        $matdec[7] = "setenta";
        $matdec[8] = "ochenta";
        $matdec[9] = "noventa";
        $matsub[3]  = 'mill';
        $matsub[5]  = 'bill';
        $matsub[7]  = 'mill';
        $matsub[9]  = 'trill';
        $matsub[11] = 'mill';
        $matsub[13] = 'bill';
        $matsub[15] = 'mill';
        $matmil[4]  = 'millones';
        $matmil[6]  = 'billones';
        $matmil[7]  = 'de billones';
        $matmil[8]  = 'millones de billones';
        $matmil[10] = 'trillones';
        $matmil[11] = 'de trillones';
        $matmil[12] = 'millones de trillones';
        $matmil[13] = 'de trillones';
        $matmil[14] = 'billones de trillones';
        $matmil[15] = 'de billones de trillones';
        $matmil[16] = 'millones de billones de trillones';
        $num = trim((string)@$num);
        if ($num[0] == '-') {
            $neg = 'menos ';
            $num = substr($num, 1);
        }else
            $neg = '';
        while ($num[0] == '0') $num = substr($num, 1);
        if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
        $zeros = true;
        $punt = false;
        $ent = '';
        $fra = '';
        for ($c = 0; $c < strlen($num); $c++) {
            $n = $num[$c];
            if (! (strpos(".,'''", $n) === false)) {
                if ($punt) break;
                else{
                    $punt = true;
                    continue;
                }
            }elseif (! (strpos('0123456789', $n) === false)) {
                if ($punt) {
                    if ($n != '0') $zeros = false;
                    $fra .= $n;
                }else
                    $ent .= $n;
            }else
                break;
        }

        $ent = '     ' . $ent;

        if ($dec and $fra and ! $zeros) {
            $fin = ' punto'; //$fin = ' coma';
            for ($n = 0; $n < strlen($fra); $n++) {
                if (($s = $fra[$n]) == '0')
                    $fin .= ' cero';
                elseif ($s == '1')
                    $fin .= $fem ? ' una' : ' uno';
                else
                    $fin .= ' ' . $matuni[$s];
            }
        }else
            $fin = '';
        if ((int)$ent === 0) return 'cero ' . $fin;
        $tex = '';
        $sub = 0;
        $mils = 0;
        $neutro = false;

        while ( ($num = substr($ent, -3)) != '   ') {

            $ent = substr($ent, 0, -3);
            if (++$sub < 3 and $fem) {
                $matuni[1] = 'una';
                $subcent = 'as';
            }else{
                $matuni[1] = $neutro ? 'un' : 'uno';
                $subcent = 'os';
            }
            $t = '';
            $n2 = substr($num, 1);
            if ($n2 == '00') {
            }elseif ($n2 < 21)
                $t = ' ' . $matuni[(int)$n2];
            elseif ($n2 < 30) {
                $n3 = $num[2];
                if ($n3 != 0) $t = 'i' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
            }else{
                $n3 = $num[2];
                if ($n3 != 0) $t = ' y ' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
            }

            $n = $num[0];
            if ($n == 1) {
                $t = ' cien' . $t;
            }elseif ($n == 5){
                $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
            }elseif ($n != 0){
                $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
            }

            if($sub == 1) {
                $t=$t;
            }elseif (! isset($matsub[$sub])) {
                if ($num == 1) {
                    $t = ' mil';
                }elseif ($num > 1){
                    $t .= ' mil';
                }
            }elseif ($num == 1) {
                $t .= ' ' . $matsub[$sub] . 'ón';
            }elseif ($num > 1){
                $t .= ' ' . $matsub[$sub] . 'ones';
            }
            if ($num == '000') $mils ++;
            elseif ($mils != 0) {
                if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
                $mils = 0;
            }
            $neutro = true;
            $tex = $t . $tex;
        }
        $tex = $neg . substr($tex, 1) . $fin;
        //$tex = $neg . $tex . $fin;
        return $tex; //ucfirst($tex);
    }

    public function crearPDF(Request $request){
        $control=$request->get('control');
        $alumno=Alumnos::findOrfail($control);
        $fexpedicion=$request->get('fexpedicion');
        $datos_fecha=explode("-",$fexpedicion);
        $anio=$datos_fecha[0]; $mes=$datos_fecha[1]; $dia=$datos_fecha[2];
        $nombre_alumno = trim($alumno->nombre_alumno).' '.trim($alumno->apellido_paterno).' '.trim($alumno->apellido_materno);
        $ncarrera=DB::table('carreras')->where('carrera',$alumno->carrera)->where('reticula',$alumno->reticula)
            ->select('nombre_carrera','clave_oficial')->first();
        if($alumno->sexo == 'F')
        {
            $genero_a = "a";
            $prop_a 	= "la";
            $interesado = "de la";
        }
        else
        {
            $genero_a = "o";
            $prop_a		=	"el";
            $interesado = "del";
        }
        $rfc_jefe=DB::table('jefes')->where('clave_area','120600')->first();
        $jefatura=DB::table('personal')->where('rfc',$rfc_jefe->rfc)->first();
        if($jefatura->sexo_empleado == 'F')
        {
            $genero_j = "JEFA";
            $prop_j 	= "La";
            $gen_j 		= "Jefa";
        }
        else
        {
            $genero_j = "JEFE";
            $prop_j		= "El";
            $gen_j 		= "Jefe";
        }
        $idiomas=DB::table('idiomas_liberacion')->where('control',$control)->first();
        $idioma=DB::table('idiomas')->where('id',$idiomas->idioma)->first();
        $opcion=$idiomas->opcion;
        switch($opcion)
        {
            case 'X': $descrip_opcion = "Examen"; break;
            case 'A': $descrip_opcion = "Aprobación del curso"; break;
            case 'D': $descrip_opcion = "Diplomado"; break;
            case 'E': $descrip_opcion = "Institución Externa"; break;
        }
        $cuerpo=$prop_j." que suscribe, ".$gen_j." del Departamento de Servicios Escolares, hace constar ".
            "que ".$prop_a." alumn".$genero_a." $nombre_alumno con número de control ".
            $control." de la carrera de ".$ncarrera->nombre_carrera." con plan de estudios ".trim($ncarrera->clave_oficial).", ".
            "ACREDITÓ la lengua extranjera ".trim($idioma->idiomas)." por la opción ".$descrip_opcion."\n\n".
            "Se extiende la presente CONSTANCIA en la Ciudad y Puerto de Ensenada, a los $dia días del mes de ".$this->mes($mes)." del ".
            "año $anio, para los fines que al interesado convengan.";
        $jefe=$rfc_jefe->jefe_area."\n".$genero_j." DEL DEPARTAMENTO DE SERVICIOS ESCOLARES";
        $this->fpdf=new Fpdf('P','mm','Letter');
        $this->fpdf->AddPage();
        $this->fpdf->SetAutoPageBreak(0);
        $this->fpdf->AddFont('MM','','Montserrat-Medium.php');
        $this->fpdf->AddFont('MM','B','Montserrat-Bold.php');
        $this->fpdf->AddFont("Montserrat2",'','Montserrat-ExtraLight.php');
        $this->fpdf->AddFont("Montserrat2",'I','Montserrat-ExtraLightItalic.php');
        $this->fpdf->AddFont("Montserrat2",'B','Montserrat-Light.php');
        $this->fpdf->AddFont("Montserrat2",'BI','Montserrat-SemiBoldItalic.php');
        $depto="120600";
        $x = 15;
        $y = 80; //Original 120
        $w = 180;
        $h = 4;
        $yr=date("y");
        $oficio="EXPEDIENTE ".$control."/".$yr;
        $encabezado=$this->encabezado($this->fpdf,$depto,$oficio,$dia,$mes,$anio);
        $this->fpdf->SetXY($x, $y);
        $titulo_persona="A QUIEN CORRESPONDA:";
        $ancho = $h*4;
        $centra='L';
        $this->fpdf->SetFont('MM','B',10);
        $this->fpdf->Cell($w, $ancho, $titulo_persona, 0, 2, $centra);
        $this->fpdf->SetFont("MM",'',9);
        $this->fpdf->SetFont("MM",'',9);
        $this->fpdf->MultiCell($w, $h, utf8_decode($cuerpo), 0, 'J');
        $this->fpdf->SetX($x);
        $this->fpdf->Ln(3);
            $this->fpdf->SetX($x);
            $this->fpdf->SetFont("MM",'B',9);
            $this->fpdf->Cell($w,$h,"A T E N T A M E N T E",0,1,'L');
            $this->fpdf->SetFont("MM",'B',8);
            //Lema TecNM
            $this->fpdf->SetX($x);
            $lema2=strtoupper("Excelencia en Educación Tecnológica");
            $this->fpdf->Cell($w,$h,utf8_decode($lema2),0,1,'L');
            //Lema Tec
            //$this->fpdf->SetFont("Montserrat2",'I',7);
            $this->fpdf->SetX($x);
            $lema=strtoupper("Por la Tecnología de Hoy y del Futuro");
            $this->fpdf->Cell($w,$h,utf8_decode($lema),0,1,'L');
            //$pdf->AddFont("SoberanaSans_Bold",'','soberanasans_bold.php');
            $this->fpdf->SetFont("MM",'B',9);
            $jefe2 = $rfc_jefe->jefe_area;
            $jefe2g=$genero_j." DEL DEPARTAMENTO DE SERVICIOS ESCOLARES";
            $this->fpdf->SetX($x);
            $this->fpdf->Cell(80,9," ",0,1,'L');
            $this->fpdf->SetX($x);
            $this->fpdf->Cell($w,$h,$jefe2,0,1,'L');
            $this->fpdf->SetX($x);
            $this->fpdf->Cell($w,$h,$jefe2g,0,1,'L');
            //$pdf->MultiCell($w, $h, "ATENTAMENTE,\n".$CFG->lema."\n\n\n\n".$jefe, 0, 'J');


        $ypie = 262;
        $xpie = 10;
        $this->fpdf->SetLineWidth(0.1);
        $this->fpdf->SetDrawColor(128,0,0);
        $this->fpdf->Line($xpie+10,$ypie-6,190,$ypie-6);

        $this->fpdf->Image("/var/www/html/escolares/public/img/escudo.jpg", 20, $ypie, 15);
        $this->fpdf->Image("/var/www/html/escolares/public/img/calidad1.jpg", 35, $ypie, 15);
        $this->fpdf->Image("/var/www/html/escolares/public/img/reciclado1.jpg", 50, $ypie, 15);

        $w = 120;
        $h = 6;
        $xpie+=40+5;
        $this->fpdf->SetXY($xpie+5, $ypie);
        $this->fpdf->AddFont("Montserrat2",'','Montserrat-ExtraLight.php');
        $this->fpdf->AddFont("Montserrat2",'B','Montserrat-Light.php');
        $this->fpdf->SetFont("Montserrat2","",6);
        $this->fpdf->Cell($w, $h/3, "", 0, 2, 'C');
        $this->fpdf->Cell($w-15, $h/2, utf8_decode("Blvd Tecnológico # 150, Col. Ex Ejido Chapultepec, C.P. 22780, Ensenada B.C"), 0, 2, 'C');
        $this->fpdf->Cell($w-15, $h/2, "Tel(s). (646)177-5680 y 82 ", 0, 2, 'C');
        $this->fpdf->SetFont("Montserrat2",'B',6);
        $this->fpdf->Cell($w-15, $h/2, "E-mail: escolares@ite.edu.mx, Sitio Web https://www.ensenada.tecnm.mx", 0, 2, 'C');
        //$this->fpdf->Image("/var/www/html/escolares/public/img/calidad.jpg", 168, 263, 17,15);
        $this->fpdf->Image("/var/www/html/escolares/public/img/escudo_aguila2.jpg", 168, $ypie-5, 25,21);
        $this->fpdf->SetLineWidth(0.1);
        $this->fpdf->SetDrawColor(0);
        $this->fpdf->Output();
        exit();
    }
}
