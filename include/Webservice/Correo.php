<?php
/*************************************************************************************************
 * Copyright 2018 JPL TSolucio, S.L.  --  This file is a part of vtiger CRM.
 * You can copy, adapt and distribute the work under the "Attribution-NonCommercial-ShareAlike"
 * Vizsage Public License (the "License"). You may not use this file except in compliance with the
 * License. Roughly speaking, non-commercial users may share and modify this code, but must give credit
 * and share improvements. However, for proper details please read the full License, available at
 * http://vizsage.com/license/Vizsage-License-BY-NC-SA.html and the handy reference for understanding
 * the full license at http://vizsage.com/license/Vizsage-Deed-BY-NC-SA.html. Unless required by
 * applicable law or agreed to in writing, any software distributed under the License is distributed
 * on an  "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License terms of Creative Commons Attribution-NonCommercial-ShareAlike 3.0 (the License).
*************************************************************************************************
*  Author       : JPL TSolucio, S. L.
*************************************************************************************************/

include_once 'vtlib/Vtiger/Module.php';
require_once 'include/Webservices/Query.php';
require_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Retrieve.php';
require_once 'include/Webservices/Update.php';

/*
 * Correo
 * mail: User email
 * telefono: User phone
 */
function cbwsCorreo($email, $telefono, $facebook, $fbedad, $desde, $urlkubus, $delagacion, 
    $edad, $curso, $comentarios, $nombre, $apellido, $ga_clientid, $keyword, $codigoPromocion, 
    $adw, $user) {
    global $adb, $log;
    
    $module = 'Accounts';

    // Proceso Curso
    $curso_array = array(
        "Preparatoria en un solo examen" => 'Preparatoria en un solo examen',
        "Curso de ingreso a la UNAM" => 'C. ingreso a la Unam',
        "Curso de ingreso a la UAM" => 'C. ingreso a la UAM',
        "Curso de ingreso al IPN" => 'C. ingreso al IPN',
        "Cursos de Ingles" => 'Curso de Ingles',
        "Curso Comipems" => 'Curso Comipems',
        "Secundaria Abierta" => 'Secundaria Abierta',
        "Preparatoria Abierta" => 'Preparatoria en un solo examen',
        "Regularización de materias" => 'Regularizacion de materias',
        "COLBACH" => 'Colbach',   
        "KUBUS ONLINE CENEVAL" => 'Kubus Online Ceneval',   
        "Prepa Menores de Edad" => 'Prepa Menores de Edad', 
    );

    // Proceso Medio - Tipo de contacto - Como te enteraste
    if (isset($facebook) && $facebook==1) {
        $Medio = 'Facebook';
        $d1 = new DateTime();
        list($month,$day,$year) = explode('/', $fbedad);
        $d2 = new DateTime($year.'/'.$month.'/'.$day);
        $diff = $d2->diff($d1);
        $edad = $diff->y;
    } else {
        $Medio = 'Internet';
    }
    
    $TipoContacto = 'Principal';
    $ComoEnteraste = 'Google';
    
    // Precios y horarios - ok
    if (strpos($urlkubus,'/kubus-educacion-precios-horarios-y-fechas-de-nuestros-planteles')>0) 
    $TipoContacto = 'MasInformacion';
    
    // Preguntas Frecuentes - ok
    if (strpos($urlkubus,'preguntas-frecuentes')>0) 
    $TipoContacto = 'PreguntasFrecuentes';
    
    // Servicio para empresas - ok  
    if (strpos($urlkubus,'capacitacion-para-empleados')>0) 
    $TipoContacto = 'Corporativos';
    
    // Preparatoria abierta - ok
    if (strpos($urlkubus,'prepa-abierta')>0) 
    $TipoContacto = 'PrincipalABIERTA';
    
    // Regularizacion de materias - ok
    if (strpos($urlkubus,'regularizacion-de-materias')>0) 
    $TipoContacto = 'PrincipalMATERIAS';
    
    // Secundaria - ok
    if (strpos($urlkubus,'secundaria-en-un-examen')>0) 
    $TipoContacto = 'PrincipalSECUNDARIA';
    
    // Curso UAM - ok
    if (strpos($urlkubus,'curso-para-la-uam')>0) 
    $TipoContacto = 'PrincipalUAM';   
    
    // Curso COMIPEMS - ok
    if (strpos($urlkubus,'curso-comipems')>0) 
    $TipoContacto = 'PrincipalCOMIPEMS';    
    
    // Curso UNAM - ok
    if (strpos($urlkubus,'curso-de-ingreso-a-la-unam')>0) 
    $TipoContacto = 'PrincipalUNAM';      
    
    // Curso COLBACH - ok
    if (strpos($urlkubus,'bachillerato-en-un-solo-examen-colbach')>0) 
    $TipoContacto = 'PrincipalCOLBACH';

    // Curso CENEVAL - ok
    if (strpos($urlkubus,'prepa-en-un-solo-examen-ceneval')>0) 
    $TipoContacto = 'PrincipalCENEVAL';
    
    // Garantia Kubus MKT - ok
    if (strpos($urlkubus,'metodo-de-estudio-garantia-kubus')>0) 
    $TipoContacto = 'Garantia';
    
    // 5 pasos COMIPEMS MKT - ok
    if (strpos($urlkubus,'curso-de-ingreso-comipems')>0) 
    $TipoContacto = '5pasosCOMIPEMS';  	
   
    // Formulario CENEVAL MKT - ok
    if (strpos($urlkubus,'formulario-matematicas-examen-ceneval')>0) 
    $TipoContacto = 'FormularioCENEVAL';  	
    
    // Formulario UNAM MKT - ok
    if (strpos($urlkubus,'formulario-matematicas-examen-unam')>0)
    $TipoContacto = 'FormularioUNAM';
    
    // Formulario COLBACH MKT - ok
    if (strpos($urlkubus,'formulario-matematicas-examen-colbach')>0) 
    $TipoContacto = 'FormularioCOLBACH';
    
    // Examen Simulacion COLBACH MKT - ok
    if (strpos($urlkubus,'prepa-en-un-examen-de-simulaci')>0) 
    $TipoContacto = 'ExamenSimulacionCOLBACH';
    
    // Examen Simulacion CENEVAL MKT - ok
    if (strpos($urlkubus,'b3n-ceneval')>0) 
    $TipoContacto = 'ExamenSimulacionCENEVAL';
    
    // Examen Simulacion CENEVAL MKT - ok
    if (strpos($urlkubus,'B3n-unam')>0) 
    $TipoContacto = 'ExamenSimulacionUNAM';

    // Proceso Desde
    list($void,$url) = explode('|',$desde);
    $desde = 'Search';

    // URLS añadidas en el TCK 0004566: Ajuste de URLs y valores del CRM
    if($url == 'http://kubus.com.mx/') $desde = 'Página principal';
    if($url == 'https://kubus.com.mx/') $desde = 'Página principal';
    if (strpos($url,'/planteles.php')>0) $desde = 'Página principal';
    if (strpos($url,'/preparatoria-en-examen-ceneval.php')>0) $desde = 'Página principal';
    if (strpos($url,'/curso-examen-unam.php')>0) $desde = 'Página principal';
    if (strpos($url,'/cursos-de-preparacion-comipems.php')>0) $desde = 'Página principal';
    if (strpos($url,'/cursos-de-ingles.php')>0) $desde = 'Página principal';
    if (strpos($url,'/secundaria-en-un-examen.php')>0) $desde = 'Página principal';
    if (strpos($url,'/regularizacion-de-materias.php')>0) $desde = 'Página principal';
    if (strpos($url,'/cursos-a-empresas.php')>0) $desde = 'Página principal';
    if (strpos($url,'/preparatoria-abierta.php')>0) $desde = 'Página principal';
    if (strpos($url,'/Quienes-somos.php')>0) $desde = 'Página principal';
    if (strpos($url,'/Mision-y-valores.php')>0) $desde = 'Página principal';
    if (strpos($url,'/Conozca-nuestro-equipo.php')>0) $desde = 'Página principal';
    if (strpos($url,'/Quieres-ser-profesor.php')>0) $desde = 'Página principal';
    if (strpos($url,'/oportunidades-de-empleo.php')>0) $desde = 'Página principal';
    if (strpos($url,'/Preguntas-frecuentes.php')>0) $desde = 'Página principal';
    if (strpos($url,'/Terminos-y-condiciones.php')>0) $desde = 'Página principal';
    if (strpos($url,'/Aviso-de-privacidad.php')>0) $desde = 'Página principal';
    if (strpos($url,'/Mapa-del-sitio.php')>0) $desde = 'Página principal';
    if (strpos($url,'/landing/ceneval/preparatoria-en-un-examen/alt.html')>0) $desde = 'Display';
    if (strpos($url,'/landing/colbach/preparatoria-en-un-examen/alt.html')>0) $desde = 'Display';
    if (strpos($url,'/landing/comipems/curso-examen-comipems/alt.html')>0) $desde = 'Display';
    if (strpos($url,'/landing/unam/curso-examen-unam/alt.html')>0) $desde = 'Display';

    // Proceso Delegacion - Estado
    $dlg_array = array(
        'Álvaro Obregón' => array('Alvaro Obregon','Distrito Federal'),
		'Alvaro Obregón' => array('Alvaro Obregon','Distrito Federal'),
        'Azcapotzalco' => array('Azcapotzalco','Distrito Federal'),
        'Benito Juárez' => array('Benito Juarez','Distrito Federal'),
        'Cuajimalpa de Morelos' => array('Cuajimalpa de Morelos','Distrito Federal'),
        'Coyoacán' => array('Coyoacan','Distrito Federal'),
        'Cuauhtémoc' => array('Cuauhtemoc','Distrito Federal'),
        'Gustavo A. Madero' => array('Gustavo A. Madero','Distrito Federal'),
        'Iztacalco' => array('Iztacalco','Distrito Federal'),
        'Iztapalapa' => array('Iztapalapa','Distrito Federal'),
        'Magdalena Contreras' => array('Magdalena Contreras','Distrito Federal'),
        'Magadalena Contreras' => array('Magdalena Contreras','Distrito Federal'),
        'Miguel Hidalgo' => array('Miguel Hidalgo','Distrito Federal'),
        'Milpa Alta' => array('Milpa Alta','Distrito Federal'),
        'Tláhuac' => array('Tlahuac','Distrito Federal'),
        'Tlalpan' => array('Tlalpan','Distrito Federal'),
        'Venustiano Carranza' => array('Venustiano Carranza','Distrito Federal'),
        'Xochimilco' => array('Xochimilco','Distrito Federal'),
        'Acambay' => array('Acambay','Estado de Mexico'),
        'Acolman' => array('Acolman','Estado de Mexico'),
        'Aculco' => array('','Aculco','Estado de Mexico'),
        'Almoloya De Alquisiras' => array('','Almoloya de Alquisiras','Estado de Mexico'),
        'Almoloya De Juarez' => array('Almoloya de Juarez','Estado de Mexico'),
        'Almoloya Del Rio' => array('Almoloya del Rio','Estado de Mexico'),
        'Amanalco De Becerra' => array('Amanalco de Becerra','Estado de Mexico'),
        'Amatepec' => array('Amatepec','Estado de Mexico'),
        'Apaxco' => array('Apaxco','Estado de Mexico'),
        'Atenco' => array('Atenco','Estado de Mexico'),
        'Atizapán' => array('Atizapan','Estado de Mexico'),
        'Atizapán De Zaragoza' => array('Atizapan de Zaragoza','Estado de Mexico'),
        'Atlacomulco' => array('Atlacomulco','Estado de Mexico'),
        'Atlautla' => array('Atlautla','Estado de Mexico'),
        'Axapusco' => array('Axapusco','Estado de Mexico'),
        'Ayapango' => array('Ayapango','Estado de Mexico'),
        'Calimaya' => array('Calimaya','Estado de Mexico'),
        'Capulhuac' => array('Capulhuac','Estado de Mexico'),
        'Chalco' => array('Chalco','Estado de Mexico'),
        'Chapa De Mota' => array('Chapa de Mota','Estado de Mexico'),
        'Chapultepec' => array('Chapultepec','Estado de Mexico'),
        'Chiautla' => array('Chiautla','Estado de Mexico'),
        'Chicoloapan' => array('Chicoloapan','Estado de Mexico'),
        'Chiconcuac' => array('Chiconcuac','Estado de Mexico'),
        'Chimalhuacán' => array('Chimalhuacan','Estado de Mexico'),
        'Coacalco De Berriozabal' => array('Coacalco De Berriozabal','Estado de Mexico'),
        'Coatepec Harinas' => array('Coatepec Harinas','Estado de Mexico'),
        'Cocotitlán' => array('Cocotitlan','Estado de Mexico'),
        'Coyotepec' => array('Coyotepec','Estado de Mexico'),
        'Cuautitlán' => array('Cuautitlan','Estado de Mexico'),
        'Cuautitlán Izcalli' => array('Cuautitlan Izcalli','Estado de Mexico'),
        'Donato Guerra' => array('Donato Guerra','Estado de Mexico'),
        'Ecatepec' => array('Ecatepec','Estado de Mexico'),
        'Ecatzingo' => array('Ecatzingo','Estado de Mexico'),
        'El Oro' => array('El Oro','Estado de Mexico'),
        'Huehuetoca' => array('Huehuetoca','Estado de Mexico'),
        'Hueypoxtla' => array('Hueypoxtla','Estado de Mexico'),
        'Huixquilucan' => array('Huixquilucan','Estado de Mexico'),
        'Isidro Fabela' => array('Isidro Fabela','Estado de Mexico'),
        'Ixtapaluca' => array('Ixtapaluca','Estado de Mexico'),
        'Ixtapan De La Sal' => array('Ixtapan De la Sal','Estado de Mexico'),
        'Ixtapan Del Oro' => array('Ixtapan Del Oro','Estado de Mexico'),
        'Ixtlahuaca' => array('Ixtlahuaca','Estado de Mexico'),
        'Jaltenco' => array('Jaltenco','Estado de Mexico'),
        'Jilotepec' => array('Jilotepec','Estado de Mexico'),
        'Jilotzingo' => array('Jilotzingo','Estado de Mexico'),
        'Jiquipulco' => array('Jiquipulco','Estado de Mexico'),
        'Jocotitlan' => array('Jocotitlan','Estado de Mexico'),
        'Joquicingo' => array('Joquicingo','Estado de Mexico'),
        'Juchitepec' => array('Juchitepec','Estado de Mexico'),
        'La Paz' => array('La Paz','Estado de Mexico'),
        'Lerma' => array('Lerma','Estado de Mexico'),
        'Luvianos' => array('Luvianos','Estado de Mexico'),
        'Malinalco' => array('Malinalco','Estado de Mexico'),
        'Melchor Ocampo' => array('Melchor Ocampo','Estado de Mexico'),
        'Metepec','Metepec' => array('Estado de Mexico'),
        'Mexicaltzingo' => array('Mexicaltzingo','Estado de Mexico'),
        'Morelos' => array('Morelos','Estado de Mexico'),
        'Naucalpan De Juarez' => array('Naucalpan de Juarez','Estado de Mexico'),
        'Nextlalpan' => array('Nextlalpan','Estado de Mexico'),
        'Nezahualcoyotl' => array('Nezahualcoyotl','Estado de Mexico'),
        'Nicolas Romero' => array('Nicolas Romero','Estado de Mexico'),
        'Nopaltepec' => array('Nopaltepec','Estado de Mexico'),
        'Ocoyoacac' => array('Ocoyoacac','Estado de Mexico'),
        'Ocuilan' => array('Ocuilan','Estado de Mexico'),
        'Otumba' => array('Otumba','Estado de Mexico'),
        'Otzoloapan' => array('Otzoloapan','Estado de Mexico'),
        'Otzolotepec' => array('Otzolotepec','Estado de Mexico'),
        'Ozumba' => array('Ozumba','Estado de Mexico'),
        'Papalotla' => array('Papalotla','Estado de Mexico'),
        'Polotitlan' => array('Polotitlan','Estado de Mexico'),
        'Rayon' => array('Rayon','Estado de Mexico'),
        'San Antonio La Isla' => array('San Antonio La Isla','Estado de Mexico'),
        'San Felipe Del Progreso' => array('San Felipe Del Progreso','Estado de Mexico'),
        'San Jose Del Rincon' => array('San Jose Del Rincon','Estado de Mexico'),
        'San Martin De Las Piramides' => array('San Martin De Las Piramides','Estado de Mexico'),
        'San Mateo Atenco' => array('San Mateo Atenco','Estado de Mexico'),
        'San Simon De Guerrero' => array('San Simon De Guerrero','Estado de Mexico'),
        'Santo Tomas' => array('Santo Tomas','Estado de Mexico'),
        'Soyaniquilpan De Juarez' => array('Soyaniquilpan De Juare','Estado de Mexico'),
        'Sultepec' => array('Sultepec','Estado de Mexico'),
        'Tecamac' => array('Tecamac','Estado de Mexico'),
        'Tejupilco' => array('Tejupilco','Estado de Mexico'),
        'Temamatla' => array('Temamatla','Estado de Mexico'),
        'Temascalapa' => array('Temascalapa','Estado de Mexico'),
        'Temascalcingo' => array('Temascalcingo','Estado de Mexico'),
        'Temascaltepec' => array('Temascaltepec','Estado de Mexico'),
        'Temoaya' => array('Temoaya','Estado de Mexico'),
        'Tenancingo' => array('Tenancingo','Estado de Mexico'),
        'Tenango Del Aire' => array('Tenango Del Aire','Estado de Mexico'),
        'Tenango Del Valle' => array('Tenango Del Valle','Estado de Mexico'),
        'Teoloyucan' => array('Teoloyucan','Estado de Mexico'),
        'Teotihuacan' => array('Teotihuacan','Estado de Mexico'),
        'Tepetlaoxtoc' => array('Tepetlaoxtoc','Estado de Mexico'),
        'Tepetlixpa' => array('Tepetlixpa','Estado de Mexico'),
        'Tepotzotlan' => array('Tepotzotlan','Estado de Mexico'),
        'Tequixquiac' => array('Tequixquiac','Estado de Mexico'),
        'Texcaltitlan' => array('Texcaltitlan','Estado de Mexico'),
        'Texcalyacac' => array('Texcalyacac','Estado de Mexico'),
        'Texcoco' => array('Texcoco','Estado de Mexico'),
        'Tezoyuca' => array('Tezoyuca','Estado de Mexico'),
        'Tianguistenco' => array('Tianguistenco','Estado de Mexico'),
        'Timilpan' => array('Timilpan','Estado de Mexico'),
        'Tlalmanalco' => array('Tlalmanalco','Estado de Mexico'),
        'Tlalnepantla De Baz' => array('Tlalnepantla De Baz','Estado de Mexico'),
        'Tlatlaya' => array('Tlatlaya','Estado de Mexico'),
        'Toluca' => array('Toluca','Estado de Mexico'),
        'Tonanitla' => array('Tonanitla','Estado de Mexico'),
        'Tonatico' => array('Tonatico','Estado de Mexico'),
        'Tultepec' => array('Tultepec','Estado de Mexico'),
        'Tultitlan' => array('Tultitlan','Estado de Mexico'),
        'Valle De Bravo' => array('Valle De Bravo','Estado de Mexico'),
        'Valle De Chalco Solidaridad' => array('Valle de Chalco','Estado de Mexico'),
        'Villa De Allende' => array('Villa De Allende','Estado de Mexico'),
        'Villa Del Carbon' => array('Villa Del Carbon','Estado de Mexico'),
        'Villa Guerrero' => array('Villa Guerrero','Estado de Mexico'),
        'Villa Victoria' => array('Villa Victoria','Estado de Mexico'),
        'Xalatlaco' => array('Xalatlaco','Estado de Mexico'),
        'Xonocatlan' => array('Xonocatlan','Estado de Mexico'),
        'Zacazonapan' => array('Zacazonapan','Estado de Mexico'),
        'Zacualpan' => array('Zacualpan','Estado de Mexico'),
        'Zinacantepec' => array('Zinacantepec','Estado de Mexico'),
        'Zumpahuacan' => array('Zumpahuacan','Estado de Mexico'),
        'Zumpango' => array('Zumpango','Estado de Mexico')
    );

    $estados_array = array(
        'Aguascalientes' => 'Aguascalientes',
        'Baja California' => 'Baja California Norte',
        'Baja California Sur' => 'Baja California Sur',
        'Campeche' => 'Campeche',
        'Chiapas' => 'Chiapas',
        'Chihuahua' => 'Chihuahua',
        'Coahuila' => 'Coahuila',
        'Colima' => 'Colima',
        'Durango' => 'Durango',
        'Guanjuato' => 'Guanjuato',
        'Guerrero' => 'Guerrero',
        'Hidalgo' => 'Hidalgo',
        'Jalisco' => 'Jalisco',
        'Michoacán' => 'Michoacan',
        'Morelos' => 'Morelos',
        'Nayarit' => 'Nayarit',
        'Nuevo León' => 'Nuevo Leon',
        'Oaxaca' => 'Oaxaca',
        'Puebla' => 'Puebla',
        'Querétaro' => 'Queretaro',
        'Quintana Roo' => 'Quintana Roo',
        'San Luis Potosí' => 'San Luis Potosi',
        'Sinaloa' => 'Sinaloa',
        'Sonora' => 'Sonora',
        'Tabasco' => 'Tabasco',
        'Tamaulipas' => 'Tamaulipas',
        'Tlaxcala' => 'Tlaxcala',
        'Veracruz' => 'Veracruz',
        'Yucatán' => 'Yucatan',
        'Zacatecas' => 'Zacatecas'
    );

    if (array_key_exists($delagacion, $estados_array)) {
        $municipio = 'Foraneo';
        $estado = $estados_array[$delagacion];
    } elseif ($delagacion == 'Otro') {
        $municipio = 'Otro';
        $estado = 'Estado de Mexico';
    } else {
        $municipio = $dlg_array[$delagacion][0];
        $estado = $dlg_array[$delagacion][1];
    }

    $nocontactar = '0';
    if ($edad <= 14 and $curso_array[$edad]=='Preparatoria en un solo examen') $nocontactar = '1';
    if ($edad >= 15 and $edad <= 17 and $curso_array[$curso]=='Preparatoria en un solo examen') $curso = 'Prepa Menores de Edad';
    if ($edad >= 15 and $edad <= 16 and $curso_array[$curso]=='Colbach') $curso = 'Prepa Menores de Edad';
    
    // Campos cuenta
    $fields = array(
        'accountname' => $nombre.' '.$apellido,
        'cf_1029' => 'Particular',
        'email1' => $mail,
        'kubusdlg' => (empty($municipio) ? 'Foraneo' : $municipio),
        'kubusestado' => $estado,
        'emailoptout' => $nocontactar,
        'cf_641' => $edad,
        'cf_1382' => $url.($comentarios != 'Comentarios' ? "\n".$comentarios : '' ),
        'cf_637' => date('Y-m-d'),
        'cf_623' => 'Activo',
        'cf_856' => $Medio, //medio
		'ga_clientid' => $ga_clientid,
		'keyword' => $keyword,
        'siccode' => 'XAXX010101000',
        'cf_1775' => $TipoContacto, //tipo de contacto
        'cf_1774' => 'OUT',
		'cf_1804' => $ComoEnteraste //¿Como te enteraste
    );

    $data_to_search = true;

    // Proceso telefono
    $tmp_tlf = '';
    $telefono = trim($telefono);
    if (!empty($telefono) && $telefono != 'Telefono o celular*') {
        $tmp_tlf = $telefono;
        $tlf = substr($tmp_tlf,-8);
        $prefijo = substr(substr($tmp_tlf,0,strlen($tmp_tlf)-8),-2);
        if ($prefijo == '55' || strlen($tmp_tlf) >= 10) {
            $fields['cf_1099'] = $tmp_tlf;
        } else {
            $fields['phone'] = $tmp_tlf;
        }
    }

    if (!empty($tmp_tlf)) {
        if (!empty($mail)) {
          $where = "email1='".$mail."' OR cf_1099='".$tmp_tlf."' OR phone='".$tmp_tlf."'";
        } else {
          $where = "cf_1099='".$tmp_tlf."' OR phone='".$tmp_tlf."'";
        }
    } else {
        if (!empty($mail)) {
            $where = "email1='".$mail."'";
        } else {
            $data_to_search = false;
        }
    }

    if ($data_to_search) {
        $query = "select id from accounts where ".$where.";";
        $queryResult = vtws_query($query, $user);
        if (empty($queryResult)) {
          $record = vtws_create($module, $fields, $user);
        } else {
          $accountid = $queryResult[0]['id'];
          $account = vtws_retrieve($accountid, $user);
          unset($account['assigned_user_id']);
          $record = vtws_update($account, $user);
        }

    }

    if ($record) {
        $query = "select id from services where cf_759 > '".date('Y-m-d')."' AND servicecategory='".$curso_array[$curso]."' order by cf_759 ASC LIMIT 1";
        $queryResult = vtws_query($query, $user);

        if (!empty($queryResult)) {
            $serviceid = $queryResult[0]['id'];
        } else {
            $srvfields = array(
                'servicename' => 'TEMPORAL WEB: '.$curso_array[$curso].' -- '.date('d-m-Y',strtotime("+1 day")),
                'servicecategory' => $curso_array[$curso],
                'cf_759' => date('Y-m-d',strtotime("+1 day")),
                'description' => 'Curso creado porque no se ha encontrado un curso de "'.$curso_array[$curso].'" en fechas siguientes a '.date('d-m-Y'),
            );
            $srv = vtws_create('Services' ,$srvfields, $user);
            $serviceid = $srv['id'];
        }
        if (!empty($serviceid)) {
            $query = "select count(*) from potentials where related_to='".$record['id']."' AND cf_638 = '".date('Y-m-d')."' AND serviceid='".$serviceid."'";
            $queryResult = vtws_query($query, $user);
            $count = $queryResult[0]['count'];
            if ($count == 0) {
               $codigoprom = (!empty($codigoPromocion) ? $codigoPromocion : '');
               $adw = (!empty($adw) ? $adw : '');
               $potfields = array(
                    'potentialname' => $record['accountname'].' -- '.$curso_array[$curso],
                    'related_to' => $record['id'],
                    'serviceid' => $serviceid,
                    'cf_638' => date('Y-m-d'),
                    'sales_stage' => 'Prospecting',
                    'codigopromocion' => $codigoprom,
                    'google_trace' => $adw,
                    'cf_1776' => ''
                );
                $pot = vtws_create('Potentials', $potfields, $user);
                if ($pot) {
                    return json_encode(array("ok"=>true));
                } else {
                    return json_encode(array("error"=>"Error al enviar la información. POT"));
                }
            } else {
                return json_encode(array("error"=>"Ya está registrado en este curso, en breve le enviaremos la información."));
            }
        } else {
            return json_encode(array("error"=>"Error al enviar la información."));
        }
    } else {
        if ($data_to_search) {
            return json_encode(array("error"=>"Error al enviar la información. ACC"));
        } else {
            return json_encode(array("error"=>"Error al enviar la información. No disponemos de su teléfono ni correo."));
        }
    }

    // Envio de correo original
    $to  = 'info@kubus.com.mx';
    $subject = 'Contacto en Kubus';
    $message = '
    <html>
    <head>
    <title>Contacto en Kubus Educación</title>
    </head>
    <body>
    <h3>Han solicitado información</h3>
    <p><strong>Nombre</strong> '.ucwords(str_replace(array('á','Á','é','É','í','Í','ó','Ó','ú','Ú','ñ','Ñ'),array('a','A','e','E','i','I','o','O','u','U','n','N'),strtolower($nombre))).' '.ucwords(str_replace(array('á','Á','é','É','í','Í','ó','Ó','ú','Ú','ñ','Ñ'),array('a','A','e','E','i','I','o','O','u','U','n','N'),strtolower($apellido))).'</p>
    <p><strong>Correo</strong> <a href="mailto:'.strtolower($mail).'">'.strtolower($mail).'</a></p>
    <p><strong>Edad</strong> '.$edad.'</p>
    <p><strong>Telefono</strong> '.$telefono.'</p>
    <p><strong>Delegación</strong> '.$delagacion.'</p>
    <p><strong>Curso</strong> '.$curso.'</p>
    <p><strong>Comentarios</strong>:<br/> '.str_replace(array('\n',"\n",'\r\n','\\n'), "<br/>", $comentarios).'</p>
    <p><small>URL kubus '.$urlkubus.'</p>
    </body>
    </html>
    ';

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= 'From: Kubus Educacion <info@kubus.com.mx>' . "\r\n";
    $headers .= 'Bcc: carlosc@designo.mx,dgluiss@designo.mx,canela.carlos@gmail.com' . "\r\n";
    mail($to, $subject, $message, $headers);
}
