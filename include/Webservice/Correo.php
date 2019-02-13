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
function cbwsCorreo($where, $fields, $servicecategory, $codigopromocion, $adw, $user) {
    
    global $adb;
    $record = null;
    
    $query = 'SELECT id FROM vtiger_account WHERE '.$where.'';
    $result = $adb->pquery($query, array());
    if ($adb->num_rows($result) == 0) {
        $record = vtws_create('Accounts', $fields, $user);
    } else {
        $account_id = $adb->query_result($result, 0, "id");
        $account = vtws_retrieve($accountid, $user);
        unset($account['assigned_user_id']);
        $record = vtws_update($account, $user);
    }

    if ($record) {
        $query = "select id from services where cf_759 > '".date('Y-m-d')."' AND servicecategory='".$servicecategory."' order by cf_759 ASC LIMIT 1";
        $queryResult = vtws_query($query, $user);

        if (!empty($queryResult)) {
            $serviceid = $queryResult[0]['id'];
        } else {
            $srvfields = array(
                'servicename' => 'TEMPORAL WEB: '.$servicecategory.' -- '.date('d-m-Y',strtotime("+1 day")),
                'servicecategory' => $servicecategory,
                'cf_759' => date('Y-m-d',strtotime("+1 day")),
                'description' => 'Curso creado porque no se ha encontrado un curso de "'.$servicecategory.'" en fechas siguientes a '.date('d-m-Y'),
            );
            $srv = vtws_create('Services' ,$srvfields, $user);
            $serviceid = $srv['id'];
        }
        if (!empty($serviceid)) {
            $query = "select count(*) from potentials where related_to='".$record['id']."' AND cf_638 = '".date('Y-m-d')."' AND serviceid='".$serviceid."'";
            $queryResult = vtws_query($query, $user);
            $count = $queryResult[0]['count'];
            if ($count == 0) {
               $codigoprom = (!empty($codigopromocion) ? $codigopromocion : '');
               $adw = (!empty($adw) ? $adw : '');
               $potfields = array(
                    'potentialname' => $record['accountname'].' -- '.$servicecategory,
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
                    return array("ok"=>true);
                } else {
                    return array("error"=>"Error al enviar la información. POT");
                }
            } else {
                return array("error"=>"Ya está registrado en este curso, en breve le enviaremos la información.");
            }
        } else {
            return array("error"=>"Error al enviar la información.");
        }
    } else {
        return array("error"=>"Error al enviar la información. No disponemos de su teléfono ni correo.");
    }
}
