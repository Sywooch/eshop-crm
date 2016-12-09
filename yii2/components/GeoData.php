<?php
/**
 * Gets user location data from IP
 * 
 * Определяет местоположение пользователя по ip. Получает данные о местоположении.
 * Есть возможность записывать эти данные в сессии/куки, для дальнейшего использования.
 * Изменяет временную зону приложения, для вывода времени в значении местоположения пользователя
 * 
 * Based on phpnt/yii2-sypexgeo
 * Used https://github.com/fiorix/freegeoip
 *  
 * @author: mindochin - https://github.com/mindochin
 * @version: 0.0.1
 * 
 * Date: 2016-12-09
 */
namespace app\components;

use yii\base\Object;
use yii\base\Exception;

class GeoData extends Object
{
    public $addToCookie         = true;
    public $addToSession        = true;
    public $setTimezoneApp      = true;
    public $setTimezoneSql      = true;

    public $cookieDuration      = 2592000;

	private $geodataSessionKey = '_geodata';
    private $geodataCookieName = '_geodata';
    
    private $data = false;
    
    /*private $timezoneSessionKey = '_timezone';
    private $timezoneCookieName = '_timezone';

    private $citySessionKey     = '_city';
    private $cityCookieName     = '_city';
    private $regionSessionKey   = '_region';
    private $regionCookieName   = '_region';
    private $countrySessionKey  = '_country';
    private $countryCookieName  = '_country';*/

    public function init()
    {
        parent::init();
                        
        if($this->data === false) {
			//throw new Exception(print_r($this->data, true));
			$this->data = $this->getGeoData();
			$this->setData();
		}
		if($this->data !==false) {
			//$data = ($this->data);
			if($data->ip != \Yii::$app->request->userIP) {
				$this->data = $this->getGeoData();
				$this->setData();
				if ($this->setTimezoneApp) {
		            $this->setTimeZone();
		        }
		        if ($this->setTimezoneSql) {
		            $this->setTimeZoneSql();
		        }
			}
		}
/*
        

        if ($this->addToCookie) {
            if (isset($geo->city['id'])) {
                $timezone = \Yii::$app->request->cookies->getValue($this->timezoneCookieName);
                if ($timezone === null) {
                    if ($this->cookieDuration) {
                        if ($this->setTimezoneApp) {
                            if (isset($geo->region['timezone'])
                                && $geo->region['timezone'] != ''
                                && \Yii::$app->formatter->timeZone != $geo->region['timezone']
                            ) {
                                $cookies = \Yii::$app->response->cookies;
                                $cookies->add(new \yii\web\Cookie([
                                    'name' => $this->timezoneCookieName,
                                    'value' => $geo->region['timezone'],
                                    'expire' => time() + (int) $this->cookieDuration,
                                ]));
                                \Yii::$app->formatter->timeZone = $geo->region['timezone'];
                            } elseif (isset($geo->country['timezone'])
                                && $geo->country['timezone'] != ''
                                && \Yii::$app->formatter->timeZone != $geo->country['timezone']
                            ) {
                                $cookies = \Yii::$app->response->cookies;
                                $cookies->add(new \yii\web\Cookie([
                                    'name' => $this->timezoneCookieName,
                                    'value' => $geo->country['timezone'],
                                    'expire' => time() + (int) $this->cookieDuration,
                                ]));
                                \Yii::$app->formatter->timeZone = $geo->country['timezone'];
                            }
                        }
                    }
                }

                $city = \Yii::$app->request->cookies->getValue($this->cityCookieName);
                if ($city === null) {
                    if ($this->cookieDuration) {
                        $cookies = \Yii::$app->response->cookies;
                        $cookies->add(new \yii\web\Cookie([
                            'name' => $this->cityCookieName,
                            'value' => $geo->city['id'],
                            'expire' => time() + (int) $this->cookieDuration,
                        ]));
                    }
                }

                $region = \Yii::$app->request->cookies->getValue($this->regionCookieName);
                if ($region === null) {
                    if ($this->cookieDuration) {
                        $cookies = \Yii::$app->response->cookies;
                        $cookies->add(new \yii\web\Cookie([
                            'name' => $this->regionCookieName,
                            'value' => $geo->region['id'],
                            'expire' => time() + (int) $this->cookieDuration,
                        ]));
                    }
                }

                $country = \Yii::$app->request->cookies->getValue($this->countryCookieName);
                if ($country === null) {
                    if ($this->cookieDuration) {
                        $cookies = \Yii::$app->response->cookies;
                        $cookies->add(new \yii\web\Cookie([
                            'name' => $this->countryCookieName,
                            'value' => $geo->country['id'],
                            'expire' => time() + (int) $this->cookieDuration
                        ]));
                    }
                }
            }
        } elseif ($this->addToSession && $geo->city['id']) {
            if (isset($data)) {
                $timezone = \Yii::$app->session->get($this->timezoneSessionKey);
                if ($timezone === null) {
                    \Yii::$app->session[$this->timezoneSessionKey] = $geo->region['timezone'];
                }

                $city = \Yii::$app->session->get($this->citySessionKey);
                if ($city === null) {
                    \Yii::$app->session[$this->citySessionKey] = $geo->city['id'];
                }

                $region = \Yii::$app->session->get($this->regionSessionKey);
                if ($region === null) {
                    \Yii::$app->session[$this->regionSessionKey] = $geo->region['id'];
                }

                $country = \Yii::$app->session->get($this->countrySessionKey);
                if ($country === null) {
                    \Yii::$app->session[$this->countrySessionKey] = $geo->country['id'];
                }
            }
        }*/
    }

    /* Установить timezone в formatter (для вывода) */
    public function setTimeZone() {        
        $tz = $this->data->time_zone;
        if ($tz !== null or !empty($tz)) {
            \Yii::$app->setTimeZone($tz);
        }
    }
    
    /* Установить timezone в db connection */
    public function setTimeZoneSql() {        
        $tz = $this->data->time_zone;
        if ($tz !== null or !empty($tz)) {
            \Yii::$app->db->on('afterOpen', function($event) {
     				$event->sender->createCommand("SET time_zone = '".$tz."'")->execute();
				});
        }
    }

    /* Обновить сессии и куки */
    public function setData()
    {
        $this->removeData();
        
        if($this->data === false) return false;
        
        if($this->addToCookie) {		
	        $cookies = \Yii::$app->response->cookies;
	        $cookies->add(new \yii\web\Cookie([
	            'name' => $this->geodataCookieName,
	            'value' => serialize($this->data),
	            'expire' => time() + (int) $this->cookieDuration,
	            'domain' => $_SERVER['SERVER_NAME'],
	        ]));
        }
        elseif($this->addToSession) {
        	\Yii::$app->session[$this->geodataSessionKey] = serialize($this->data);
        }
        //\Yii::$app->formatter->timeZone = $timezone;
    }

    /* Чистим сессиии и куки */
    public function removeData()
    {
        $cookies = \Yii::$app->response->cookies;

        \Yii::$app->session->remove($this->geodataSessionKey);
        $cookies->remove($this->geodataCookieName);
    }

	/* получим данные из кук или сессии */
	public function getData() {
		$data = \Yii::$app->request->cookies->getValue($this->geodataCookieName);
        if ($data === null) {
        	$data = \Yii::$app->session->get($this->geodataSessionKey);            
        }
        if ($data !== null) {
        	return unserialize($data);
        }      
                
        return false; 
		
	}
    public function getGeoData()
    {        
        return $this->getGeoIp();
    }

    public function getGeoDataIp($ip)
    {
        return $this->getGeoIp($ip);
    }
/*
    public function getCity()
    {
        $city = \Yii::$app->session->get($this->citySessionKey);
        if ($city === null) {
            $city = \Yii::$app->request->cookies->getValue($this->cityCookieName);
            if ($city === null) {
                $geo = new Sypexgeo();
                $geo->get();
                if (isset($geo->city['id'])) {
                    $city = $geo->city['id'];
                }
            }
        }
        return $city;
    }

    public function getRegion()
    {
        $region = \Yii::$app->session->get($this->regionSessionKey);
        if ($region === null) {
            $region = \Yii::$app->request->cookies->getValue($this->regionCookieName);
            if ($region === null) {
                $geo = new Sypexgeo();
                $geo->get();
                if (isset($geo->city['id'])) {
                    $region = $geo->region['id'];
                }
            }
        }
        return $region;
    }

    public function getCountry()
    {
        $country = \Yii::$app->session->get($this->countrySessionKey);
        if ($country === null) {
            $country = \Yii::$app->request->cookies->getValue($this->countryCookieName);
            if ($country === null) {
                $geo = new Sypexgeo();
                $geo->get();
                if (isset($geo->city['id'])) {
                    $country = $geo->country['id'];
                }
            }
        }
        return $country;
    }
    */
    /* 
	 * -------------------------------------------------------
	 * getGeoIP.freegeoip.net
	 * -------------------------------------------------------
	 * @Version: 1.0.0
	 * @Author:  FireDart
	 * @Link:    http://www.firedartstudios.com/
	 * @GitHub:  https://github.com/FireDart/snippets/PHP/GeoIP/
	 * @License: The MIT License (MIT)
	 * 
	 * Used to get geo information from a selected ip using the 
	 * freegeoip.net service, up to 10,000 queries an hour.
	 * 
	 * -------------------------------------------------------
	 * Requirements
	 * -------------------------------------------------------
	 * PHP 5.3.0+
	 * 
	 * -------------------------------------------------------
	 * Usage
	 * -------------------------------------------------------
	 * Basic / Detect IP
	 * getGeoIP();
	 * 
	 * Input IP to check
	 * getGeoIP("aaa.bbb.ccc.ddd", false);
	 * 
	 */
	 
	/* 
	 * getGeoIP
	 * 
	 * Returns GEO info about an IP address from 
	 * FreeGeoIP.net, allows 10,000 queries per hour.
	 * 
	 * @param str     $ip        IP to check leave blank to get REMOTE_ADDR
	 * @param boolean $jsonArray Return JSON as array?
	 * @return (obj|booealn) If info can be return use obj, otherwise report false.
	 */
	private function getGeoIP($ip = null, $jsonArray = false) {
		try {
			// If no IP is provided use the current users IP
			if($ip == null) {
				$ip   = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
			}
			// If the IP is equal to 127.0.0.1 (IPv4) or ::1 (IPv6) then cancel, won't work on localhost
			if($ip == "127.0.0.1" || $ip == "::1") {
				throw new Exception('You are on a local server, this script won\'t work right.');
				//\yii::error('You are on a local server, this script won\'t work right.');
				//return false;
			}
			// Make sure IP provided is valid
			if(!filter_var($ip, FILTER_VALIDATE_IP)) {
				throw new Exception('Invalid IP address "' . $ip . '".');
				//\yii::error('Invalid IP address "' . $ip . '".');
				//return false;
			}
			if(!is_bool($jsonArray)) {
				throw new Exception('The second parameter must be a boolean - true (return array) or false (return JSON object); default is false.');
				//\yii::error('The second parameter must be a boolean - true (return array) or false (return JSON object); default is false.');
				//return false;
			}
			// Fetch JSON data with the IP provided
			$url  = "http://freegeoip.net/json/" . $ip;
			// Return the contents, supress errors because we will check in a bit
			$json = @file_get_contents($url);
			// Did we manage to get data?
			if($json === false) {
				return false;
			}
			// Decode JSON
			$json = json_decode($json, $jsonArray);
			// If an error happens we can assume the JSON is bad or invalid IP
			if($json === null) {
				// Return false
				return false;
			} else {
				// Otherwise return JSON data
				return $json;
			}
		} catch(Exception $e) {
			\yii::error($e->getMessage());
			return false;			
		}
	}
}