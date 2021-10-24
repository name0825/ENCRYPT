<?php
    /**
    * Class for RSA encryption.
    * Provides functions related to RSA encryption, decoding.

    * @author name0825
    * @source https://www.crocus.co.kr/1203
    */
    namespace ENCRYPT;

    class RSA {
        private $n;
        private $pb_key;
        private $pv_key;

        private $max;
        private $max_strength = [256, 512, 768, 1024, 1536, 2048, 4096];

        public function __construct(int $strength = 1) {
            if ($strength < 0 || $strength > count($this -> max_strength)) $strength = 1;
            $this -> max = $this -> max_strength[$strength];
        }

        public function key_generator(int $n_min = 128, int $n_max = 300000, int $max = 0) {
            if ($max == 0) $max = $this -> max;

            $n = 0;
            $p = 0;
            $q = 0;
            $prime = self::get_prime_number($max);

            while ($n <= $n_min || $n >= $n_max) {
                $p = $prime[array_rand($prime)];
                $q = $prime[array_rand($prime)];
                if ($p == $q || ($p - 1) * ($q - 1) == 2) continue;
                $n = $p * $q;
            }

            $e = 0;
            $pi = ($p - 1) * ($q - 1);

            while (!$e){
                $randVal = rand(0, $pi * 2) % $pi;
                if (1 < $randVal && $randVal < $pi) {
                    $e = $randVal;
                    $cnt = 0;
                    for ($i = 1; $i < $e; $i++)
                        if ($e % $i == 0 && $pi % $i == 0)
                            $cnt++;
                    if ($cnt >= 2) $e = 0;
                }
            }

            $d = 0;

            while (!$d) {
                $rand = rand(0, $pi * 2) % $pi + 1;
                if (($e * $rand) % $pi == 1) $d = $rand;
            }

            $this -> pb_key = $e;
            $this -> pv_key = $d;
            $this -> n = $n;
            return TRUE;
        }

        public function get_pb_key() {
            return $this -> pb_key;
        }

        public function get_pv_key() {
            return $this -> pv_key;
        }

        public function get_n() {
            return $this -> n;
        }

        public function set_pb_key($key) {
            $this -> pb_key = $key;
        }

        public function set_pv_key($key) {
            $this -> pv_key = $key;
        }

        public function set_n($n) {
            $this -> n = $n;
        }

        public function encrypt(string $str) {
            if ($this -> pb_key == null) return FALSE;

            $buffer = Array();
            $len = strlen($str);

            for ($i = 0; $i < $len; $i++) {
                $sum = 1;
                $v = ord($str[$i]);
                for ($j = 0; $j < $this -> pb_key; $j++) {
                    $sum *= $v;
                    $sum %= $this -> n;
                }
                $buffer[] = $sum;
            }

            return $buffer;
        }

        public function decrypt(array $buffer) {
            if ($this -> pv_key == null) return FALSE;

            $str = "";
            $len = count($buffer);

            for ($i = 0; $i < $len; $i++) {
                $sum = 1;
                $v = $buffer[$i];
                for ($j = 0; $j < $this -> pv_key; $j++) {
                    $sum *= $v;
                    $sum %= $this -> n;
                }
                $str .= chr($sum);
            }

            return $str;
        }

        private static function get_prime_number($max) {
            $res = Array();
            $arr = Array(0);

            for ($i = 0; $i <= $max; $i++) $arr[$i] = 0;

            for ($i = 2; $i <= $max; $i++) {
                if ($arr[$i]) continue;
                for ($j = $i * 2; $j <= $max; $j += $i) $arr[$j] = 1;
                $res[] = $i;
            }

            return $res;
        }
    }
?>