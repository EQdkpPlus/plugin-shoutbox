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

$sb_timezones = array(
  '-12'     => '(GMT - 12:00 hours) Enewetak, Kwajalein',
  '-11'     => '(GMT - 11:00 hours) Midway Island, Samoa',
  '-10'     => '(GMT - 10:00 hours) Hawaii',
  '-9.5'    => '(GMT - 9:30 hours) French Polynesia',
  '-9'      => '(GMT - 9:00 hours) Alaska',
  '-8'      => '(GMT - 8:00 hours) Pacific Time (US &amp; Canada)',
  '-7'      => '(GMT - 7:00 hours) Mountain Time (US &amp; Canada)',
  '-6'      => '(GMT - 6:00 hours) Central Time (US &amp; Canada), Mexico City',
  '-5'      => '(GMT - 5:00 hours) Eastern Time (US &amp; Canada), Bogota, Lima',
  '-4'      => '(GMT - 4:00 hours) Atlantic Time (Canada), Caracas, La Paz',
  '-3.5'    => '(GMT - 3:30 hours) Newfoundland',
  '-3'      => '(GMT - 3:00 hours) Brazil, Buenos Aires, Falkland Is.',
  '-2'      => '(GMT - 2:00 hours) Mid-Atlantic, Ascention Is., St Helena',
  '-1'      => '(GMT - 1:00 hours) Azores, Cape Verde Islands',
  '0'       => '(GMT) Casablanca, Dublin, London, Lisbon, Monrovia',
  '1'       => '(GMT + 1:00 hours) Brussels, Copenhagen, Madrid, Paris',
  '2'       => '(GMT + 2:00 hours) Kaliningrad, South Africa',
  '3'       => '(GMT + 3:00 hours) Baghdad, Riyadh, Moscow, Nairobi',
  '3.5'     => '(GMT + 3:30 hours) Tehran',
  '4'       => '(GMT + 4:00 hours) Abu Dhabi, Baku, Muscat, Tbilisi',
  '4.5'     => '(GMT + 4:30 hours) Kabul',
  '5'       => '(GMT + 5:00 hours) Ekaterinburg, Karachi, Tashkent',
  '5.5'     => '(GMT + 5:30 hours) Bombay, Calcutta, Madras, New Delhi',
  '5.75'    => '(GMT + 5:45 hours) Kathmandu',
  '6'       => '(GMT + 6:00 hours) Almaty, Colombo, Dhaka',
  '6.5'     => '(GMT + 6:30 hours) Yangon, Naypyidaw, Bantam',
  '7'       => '(GMT + 7:00 hours) Bangkok, Hanoi, Jakarta',
  '8'       => '(GMT + 8:00 hours) Hong Kong, Perth, Singapore, Taipei',
  '8.75'    => '(GMT + 8:45 hours) Caiguna, Eucla',
  '9'       => '(GMT + 9:00 hours) Osaka, Sapporo, Seoul, Tokyo, Yakutsk',
  '9.5'     => '(GMT + 9:30 hours) Adelaide, Darwin',
  '10'      => '(GMT + 10:00 hours) Melbourne, Papua New Guinea, Sydney',
  '10.5'    => '(GMT + 10:30 hours) Lord Howe Island',
  '11'      => '(GMT + 11:00 hours) Magadan, New Caledonia, Solomon Is.',
  '11.5'    => '(GMT + 11:30 hours) Burnt Pine, Kingston',
  '12'      => '(GMT + 12:00 hours) Auckland, Fiji, Marshall Island',
  '12.75'   => '(GMT + 12:45 hours) Chatham Islands',
  '13'      => '(GMT + 13:00 hours) Kamchatka, Anadyr',
  '14'      => '(GMT + 14:00 hours) Kiritimati',
);

?>
