<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2008 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev$
 *
 * $Id$
 */

if (!defined('EQDKP_INC'))
{
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  'shoutbox'                        => 'Shoutbox',
  'sb_shoutbox'                     => 'Shoutbox',

  // Portal
  'shoutbox_name'                   => 'Shoutbox',
  'shoutbox_desc'                   => 'Shoutbox es un plugin que permite intercambiar mensajes cortos entre usuarios.',

  // Description
  'sb_short_desc'                   => 'Shoutbox',
  'sb_long_desc'                    => 'Shoutbox es un plugin que permite intercambiar mensajes cortos entre usuarios.',

  // General
  'sb_plugin_not_installed'         => 'El plugin Shoutbox no está instalado',
  'sb_php_version'                  => "Shoutbox requiere la versión PHP %1\$s o superior. Tú servidor esta ejecutandose en PHP %2\$s",
  'sb_plus_version'                 => "Shoutbox requiere la versión de EQDKP-PLUS %1\$s o superior. Tienes instalado la versión %2\$s",

  // Menu
  'sb_manage_archive'               => 'Administrar Archivo',

  // Archive
  'sb_written_by'                   => 'written by',
  'sb_written_at'                   => 'at',

  // Admin
  'sb_delete_success'               => 'Successfully deleted entries',
  'sb_settings_info'                => 'Further Shoutbox settings could be found within the <a href="'.$eqdkp_root_path.'admin/manage_portal.php'.$SID.'">Portalmodule settings</a>',

  // Configuration
  'sb_config_saved'                 => 'Los ajustes se han almacenado correctamente',
  'sb_header_general'               => 'Ajustes generales del Shoutbox',

  // Portal Modules
  'sb_output_count_limit'           => 'Limitar entradas del shoutbox.',
  'sb_show_date'                    => '¿Mostrar las fechas?',
  'sb_show_archive'                 => 'Mostrar Archivo?',
  'sb_input_box_location'           => 'Location of input box',
  'sb_location_top'                 => 'Above entries',
  'sb_location_bottom'              => 'Below entries',
  'sb_autoreload'                   => 'Cantidad de segundos a esperar para que recargue automaticamente el Shoutbox (Por defecto 0 = Off)',
  'sb_autoreload_help'              => 'Seleccionar 0 para desactivar la recarga automática',
  'sb_no_character_assigned'        => 'Aún no hay usuarios conectados. Al menos un usuario tiene que estar conectado para poder enviar una entrada.',
  'sb_submit_text'                  => 'Envíar',
  'sb_save_wait'                    => 'Almacenando, por favor espere...',
  'sb_reload'                       => 'Recargar',
  'sb_no_entries'                   => 'No hay entradas',
  'sb_archive'                      => 'Archivo',
  'sb_shoutbox_archive'             => 'Archivo Shoutbox',

  // Exchange
  'sb_missing_member_id'            => 'Missing Member ID to insert',
  'sb_missing_text'                 => 'Missing text to insert',

  // About/Credits
  'sb_about_header'                 => 'Acerca de Shoutbox',
  'sb_credits_part1'                => 'Shoutbox v',
  'sb_credits_part2'                => ' por Aderyn',
  'sb_copyright'                    => 'Copyright',
);

?>
