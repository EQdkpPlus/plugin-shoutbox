<?php
/*
 * Project:     EQdkp Shoutbox
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:        http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2009-08-05 11:41:15 +0200 (mié, 05 ago 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: Dallandros $
 * @copyright   2008 Aderyn
 * @link        http://eqdkp-plus.com
 * @package     shoutbox
 * @version     $Rev: 5478 $
 *
 * $Id: lang_main.php 5478 2009-08-05 09:41:15Z Dallandros $
 */

if (!defined('EQDKP_INC'))
{
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
  'shoutbox'                        => 'Shoutbox',
  'sb_shoutbox'                     => 'Shoutbox',

  // Description
  'sb_short_desc'                   => 'Shoutbox',
  'sb_long_desc'                    => 'Shoutbox es un plugin que permite intercambiar mensajes cortos entre usuarios.',

  // General
  'sb_plugin_not_installed'         => 'El plugin Shoutbox no está instalado',
  'sb_php_version'                  => "Shoutbox requiere la versión PHP %1\$s o superior. Tú servidor esta ejecutandose en PHP %2\$s",
  'sb_plus_version'                 => "Shoutbox requiere la versión de EQDKP-PLUS %1\$s o superior. Tienes instalado la versión %2\$s",

  // Menu
  'sb_manage'                       => 'Administrar',

  // Admin -> Settings
  'sb_date_format'                  => 'Y/m/d H:i',  // YYYY/MM/DD HH:mm
  'sb_time_format'                  => 'H:i',        // HH:mm
  'sb_adm_date'                     => 'Fecha',
  'sb_adm_name'                     => 'Nombre',
  'sb_adm_text'                     => 'Mensaje',
  'sb_adm_select_all'               => 'Seleccionar todos',
  'sb_adm_select_none'              => 'No seleccionar nada',

  // Configuration
  'sb_config_saved'                 => 'Los ajustes se han almacenado correctamente',
  'sb_header_general'               => 'Ajustes generales del Shoutbox',
  'sb_updatecheck'                  => 'Habilitar casilla de verificación para las nuevas versiones Plugin',
  'sb_timezone'                     => 'Zona horaria del servidor',
  'sb_dstcorrect'                   => '¿Ajustar automáticamente al horario de verano?',

  // Portal Modules
  'sb_output_count_limit'           => 'Limitar entradas del shoutbox.',
  'sb_show_date'                    => '¿Mostrar las fechas?',
  'sb_show_archive'                 => 'Mostrar Archivo?',
  'sb_input_box_below'              => '¿Caja de texto debajo de las entradas?',
  'sb_autoreload'                   => 'Cantidad de segundos a esperar para que recargue automaticamente el Shoutbox (Por defecto 0 = Off)',
  'sb_autoreload_help'              => 'Seleccionar 0 para desactivar la recarga automática',
  'sb_invisible_to_guests'          => '<u>Invisible</u> para los invitados?',
  'sb_no_character_assigned'        => 'Aún no hay usuarios conectados. Al menos un usuario tiene que estar conectado para poder enviar una entrada.',
  'sb_submit_text'                  => 'Envíar',
  'sb_save_wait'                    => 'Almacenando, por favor espere...',
  'sb_reload'                       => 'Recargar',
  'sb_no_entries'                   => 'No hay entradas',
  'sb_archive'                      => 'Archivo',
  'sb_shoutbox_archive'             => 'Archivo Shoutbox',
  'sb_footer'                       => "... %1\$d encontrado / %2\$d por páginas",

  // About/Credits
  'sb_about_header'                 => 'Acerca de Shoutbox',
  'sb_credits_part1'                => 'Shoutbox v',
  'sb_credits_part2'                => ' por Aderyn',
  'sb_copyright'                    => 'Copyright',
);

?>
