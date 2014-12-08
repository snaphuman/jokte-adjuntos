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
    }

}

