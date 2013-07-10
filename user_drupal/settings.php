<?php

/**
 * ownCloud - user_redmine
 *
 * @author Patrik Karisch
 * @copyright 2012 Patrik Karisch <patrik.karisch@abimus.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
$params = array(
  'drupal_rest_uri',
  'drupal_rest_username',
  'drupal_rest_password',
);

if ($_POST) {
	foreach($params as $param){
		if(isset($_POST[$param])){
			OC_Appconfig::setValue('user_drupal', $param, $_POST[$param]);
		}
	}
}

// fill template
$tmpl = new OC_Template( 'user_drupal', 'settings');
$tmpl->assign( 'drupal_rest_uri', OC_Appconfig::getValue('user_drupal', 'drupal_rest_uri', ''));
$tmpl->assign( 'drupal_rest_username', OC_Appconfig::getValue('user_drupal', 'drupal_rest_username', ''));
$tmpl->assign( 'drupal_rest_password', OC_Appconfig::getValue('user_drupal', 'drupal_rest_password', ''));

return $tmpl->fetchPage();
