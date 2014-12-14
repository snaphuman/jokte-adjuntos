<?php
/**
 * @version 0.1.0
 * @package Jokte.Plugin
 * @copyright CopyLeft Comparte Igual. Comunidad Juuntos Latinoamérica.
 * @license GNU/GPL v3.0
 */

defined('_JEXEC') or die('Acceso directo a este archivo restringido');

jimport('joomla.plugin.plugin');

class plgSystemJokte_Adjuntos extends JPlugin {

    function __construct(&$subject, $config) {

        parent::__construct($subject, $config);

        // Realiza comprobaciones adicionales en la inicialización
        // del plugin para verificar si la operación se efectua desde
        // el backend y si el usuario actual tiene permisos de edición

        if(!JFactory::getApplication()->isAdmin()) return;

        $user = JFactory::getUser();
        if(!$user->authorise('core.edit', 'com_content')) return;

    }

    function onBeforeRender () {

        $app = JFactory::getApplication();
        $jinput = $app->input;

        // obtiene los parámetros del request e inicializa valores predeterminados para
        // el contexto de edición de un artículo
        $reqParams = $jinput->getArray(array('view' => '','layout' => ''));

        // termina la ejecución del plugin si los parametros recibidos no cumplen
        // con las condiciones preestablecidas para modificar el contexto de edición de
        // artículos
        if ($reqParams["view"] != "article" || $reqParams["layout"] != "edit") return;

        // Obtiene id del artículo
        $id = $jinput->get('id', null, null);

        // obtiene el buffer del documento que será renderizado
        $doc = JFactory::getDocument();
        $buffer = mb_convert_encoding($doc->getBuffer('component'),'html-entities','utf-8');

        // inicializa la manipulación del DOM para el buffer del documento
        $dom = new DomDocument;
        $dom->validateOnParse = true;
        $dom->loadHTML($buffer);

        // obtiene los datos de los adjuntos
        $data = self::getAttachmentsData($id);

        // selecciona elemento del DOM  que contendrá los registros de los archivos adjuntos
        $contenedor = $dom->getElementById("adjuntos");

        // realiza la construcción de la tabla con el listado de adjuntos

        $adjuntosList = $dom->createElement("form");
        $adjuntosList->setAttribute("id","list-adjuntos");

        $tabla = $dom->createElement("table");
        $tbody = $dom->createElement("tbody");

        $c = 0;
        foreach($data as $item){

            $row = $dom->createElement("tr");

            $check = $dom->createElement("td");
            $checkBox = $dom->createElement("input");
            $checkBox->setAttribute("type", "checkbox");

            $file = $dom->createElement("td");
            $file->nodeValue = $item->nombre_archivo;

            $fileType = $dom->createElement("td");
            $img = $dom->createElement("img");
            $img->setAttribute("src", "/media/adjuntos/caneca.png");

            $fileType->appendChild($img);
            $check->appendChild($checkBox);
            $row->appendChild($check);
            $row->appendChild($file);
            $row->appendChild($fileType);

            $tbody->appendChild($row);
            $c++;
        }

        $tabla->appendChild($tbody);
        $adjuntosList->appendChild($tabla);
        $contenedor->appendChild($adjuntosList);

        // aplica los cambios realizados al DOM en un nuevo buffer para actualizar la presentación
        // del la vista del componente en el contexto indicado
        $newBuffer = $dom->saveHTML();
        $doc->setBuffer($newBuffer,'component');
    }

    private function getAttachmentsData ($id) {

        // carga el subcontrolador para ejecutar la tarea mostrar
        JLoader::register('ContentControllerAdjuntos', JPATH_ADMINISTRATOR . '/components/com_content/controllers/adjuntos.json.php');

        $data = new ContentControllerAdjuntos;
        $adjuntos = $data->mostrar($id);

        return $adjuntos;
    }
}
