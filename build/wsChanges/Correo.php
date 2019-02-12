<?php
/*********************************************************************************
 * Copyright 2012-2018 JPL TSolucio, S.L.  --  This file is a part of coreBOS
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
 ********************************************************************************/

$operationInfo = array(
	'name'    => 'Correo',
	'include' => 'include/Webservices/Correo.php',
	'handler' => 'cbwsCorreo',
	'prelogin'=> 0,
	'type'    => 'GET',
	'parameters' => array(
		array('name' => 'mail','type' => 'string'),
		array('name' => 'telefono','type' => 'string'),
		array('name' => 'facebook','type' => 'string'),
		array('name' => 'fbedad','type' => 'string'),
		array('name' => 'desde','type' => 'string'),
		array('name' => 'urlkubus','type' => 'string'),
		array('name' => 'delagacion','type' => 'string'),
		array('name' => 'edad','type' => 'string'),
		array('name' => 'curso','type' => 'string'),
		array('name' => 'comentarios','type' => 'string'),
		array('name' => 'nombre','type' => 'string'),
		array('name' => 'apellido','type' => 'string'),
		array('name' => 'ga_clientid','type' => 'string'),
		array('name' => 'keyword','type' => 'string'),
		array('name' => 'codigoPromocion','type' => 'string'),
		array('name' => 'adw','type' => 'string'),
	)
);
