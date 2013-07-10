<?php

/**
 * ownCloud
 *
 * @author Saša Tomić
 * @copyright 2012 Saša Tomić <tomic80@gmail.com>
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
require('vendor/nategood/httpful/bootstrap.php');
class OC_User_drupal extends OC_User_Backend {
  protected $drupal_rest_uri;
  private $drupal_rest_username;
  private $drupal_rest_password;
  private $drupal_rest_session;

	function __construct() {
		$this->drupal_rest_session = FALSE;
		$this->drupal_rest_uri = OC_Appconfig::getValue('user_drupal', 'drupal_rest_uri','');
		$this->drupal_rest_username = OC_Appconfig::getValue('user_drupal', 'drupal_rest_username','');
		$this->drupal_rest_password = OC_Appconfig::getValue('user_drupal', 'drupal_rest_password','');

    if($result = $this->mhmRestLogin($this->drupal_rest_username, $this->drupal_rest_password)) {
      $this->drupal_rest_session = (object) array(
        'session_name' => $result->session_name,
        'sessid' => $result->sessid,
      );
    } else {
      OC_Log::write('OC_User_drupal',
        'OC_User_drupal, Failed to login to rest server',
        OC_Log::ERROR);
      return FALSE;
    }
	}

  /**
   * Log into rest server with supplied creds.
   *
   * @param (string) The username to log in with.
   * @param (string) The user's password.
   * 
   * @return (object or FALSE) The body of the response.
   */
  private function mhmRestLogin($username, $password) {
    try {
      $response = \Httpful\Request::post($this->drupal_rest_uri . 'user/login.json')
        ->body(array(
          'username' => $username,
          'password' => $password,
        ))
        ->sendsJson()
        ->send();
      return $response->body;
    } catch(Exception $e) {
      OC_log::write('OC_User_drupal', 'OC_User_drupal, Failed to log in with supplied credentials. Error details: ' . $e->getMessage(), OC_Log::ERROR);
      return FALSE;
    }
  }

  private function sendMMHRest($endpoint, $method = 'get', $data = NULL) {
    if(!$this->drupal_rest_session) {
      return FALSE;
    }
    $uri = $this->drupal_rest_uri;
    try {
      switch($method) {
        case 'get':
        default:
          $uri .= $endpoint;
          if(!empty($data)) {
            $uri .= '?' . http_build_query($data, NULL, '&');
          }
          $response = \Httpful\Request::get($uri)->addHeader('Cookie', $this->drupal_rest_session->session_name . '=' . $this->drupal_rest_session->sessid)->sendsAndExpects('json')->send();
        break;
        case 'post':
          $response = \Httpful\Request::post($uri . $endpoint)->addHeader('Cookie', $this->drupal_rest_session->session_name . '=' . $this->drupal_rest_session->sessid)->body($data)->sendsAndExpects('json')->send();
        break;
      }
      return $response->body;
    } catch(Exception $e) {
      OC_log::write('OC_User_drupal', 'OC_User_drupal, REST request failed. Error details: ' . $e->getMessage(), OC_Log::ERROR);
      return FALSE;
    }
  }

	/**
	 * @brief Set email address
	 * @param $uid The username
	 */
	private function setEmail($uid) {
    if(!$this->drupal_rest_session) {
      return FALSE;
    }
    $params = array(
      'pagesize' => 1,
      'fields' => 'mail',
      'parameters' => array('name' => $uid, 'status' => 1),
    );
    if($response = $this->sendMMHRest('user', 'get', $params)) {
      OC_Preferences::setValue($uid, 'settings', 'email', $response[0]->mail);
    }
	}

	/**
	 * @brief Check if the password is correct
	 * @param $uid The username
	 * @param $password The password
	 * @returns true/false
	 */
	public function checkPassword($uid, $password){
    if(!$this->drupal_rest_session) {
      return FALSE;
    } 
    if($response = $this->mhmRestLogin($uid, $password)) {
      if(is_object($response) && !empty($response->user->name) && $response->user->name == $uid) {
        return $response->user->name;
      } else {
        return FALSE;
      }
    }

    return FALSE;
	}

	/**
	 * @brief Get a list of all users
	 * @returns array with all enabled uids
	 *
	 * Get a list of all users
	 */
	public function getUsers($search = '', $limit = null, $offset = null) {
		$users = array();
    $params = array(
      'page' => ($offset > 0) ? $offset / $limit : 0,
      'pagesize' => $limit,
      'fields' => 'name,mail',
      'parameters' => array('status' => 1),
      'sort' => array(
        array('name'),
      ),
    );
    if($response = $this->sendMMHRest('user', 'get', $params)) {
      foreach($response as $row) {
        $users[] = $row->name;
      }
    }
    return $users;
	}

	/**
	 * @brief check if a user exists
	 * @param string $uid the username
	 * @return boolean
	 */
	public function userExists($uid) {
    if(!$this->drupal_rest_session) {
      return FALSE;
    } 

    if($response = $this->sendMMHRest('user', 'get', array('parameters' => array('name' => $uid)))) {
      return count($response) > 0;
    }
    return FALSE;

		$q = 'SELECT name FROM '. $this->drupal_db_prefix .'users WHERE name = "'. $this->db->real_escape_string($uid) .'" AND status = 1';
		$result = $this->db->query($q);
		return $result->num_rows > 0;
	}
}
