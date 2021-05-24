<?php
App::uses('DatabaseSession', 'Model/Datasource/Session');
/**
 * @version   0.2
 * @author    gondoh@catchup.co.jp
 * @license   MIT License
 * @copyright 2021, catchup inc.
 */
/**
 * Redis Session Store for CakePHP 2
 *
 * @version   0.1
 * @author    Kjell Bublitz <m3nt0r.de@gmail.com>
 * @license   MIT License
 * @copyright 2011, Kjell Bublitz
 * @package   redis_session
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Redis Session Store Class
 */
class RedisSession extends DatabaseSession implements CakeSessionHandlerInterface {
	public static $configName = 'redis';
    private static $timeout;

    public function __construct() {
        parent::__construct();
        $timeout = Configure::read('Session.timeout');
        if (empty($timeout)) {
            $timeout = 60 * 24 * 90;
        }
        self::$timeout = $timeout;
    }

    /**
     * open
     * connect to Redis
     * authorize
     * select database
     */
    public function open() {
		// CacheEnginより利用するため実装不要
		return true;
    }

    /**
     * close
     * disconnect from Redis
     * @return type
     */
    public function close() {
		// CacheEnginより利用するため実装不要
		// RedisEngine::__destruct()
        return true;
    }

    /**
     * read
     * @param type $id
     * @return type
     * - Return whatever is stored in key
     */
    public function read($id) {
		$result = Cache::read($id, self::$configName);
		return $result ? $result : '';
    }

    /**
     * write
     * @param type $id
     * @param type $data
     * @return type
     * - SETEX data with timeout calculated in open()
     */
    public function write($id, $data) {
		return Cache::write($id, $data, self::$configName);
    }

    /**
     * destroy
     * @param type $id
     * @return type
     * - DEL the key from store
     */
    public function destroy($id) {
		return Cache::delete($id, self::$configName);
    }

    /**
     * gc
     * @param type $expires
     * @return type
     * not needed as SETEX automatically removes itself after timeout
     */
    public function gc($expires = null) {
		return Cache::gc(self::$configName, $expires);
    }
}

