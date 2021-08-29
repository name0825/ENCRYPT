<?php
    /**
    * Class for RSA encryption.
    * Provides functions related to RSA encryption, decoding.

    * @author name0825
    * @source https://www.crocus.co.kr/1203
    */
    namespace ENCRYPT;

    class RSA {
        private $publicKey;
        private $privateKey;

        public function key_generator($max = 4096, $max_try = INF, $n_min = 0) {
            $n = 0;
            $p = 0;
            $q = 0;
            $prime = self::getPrimeList($max);

            $try = 0;
            while ($n <= $n_min) {
                $try++;
                if ($try > $max_try) return FALSE;
                $p = $prime[array_rand($prime)];
                $q = $prime[array_rand($prime)];
                if ($p == $q || ($p - 1) * ($q - 1) == 2) continue;
                $n = $p * $q;
            }

            $e = 0;
            $pi = ($p - 1) * ($q - 1);

            $try = 0;
            while (!$e){
                $try++;
                if ($try > $max_try) return FALSE;
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
            $try = 0;

            while (!$d) {
                $try++;
                if ($try > $max_try) return FALSE;
                $rand = rand(0, $pi * 2) % $pi + 1;
                if (($e * $rand) % $pi == 1) $d = $rand;
            }

            $this -> publicKey = array($e, $n);
            $this -> privateKey = array($d, $n);
            return TRUE;
        }

        public function setKey(int $n, int $pb, int $pv) {
            $this -> publicKey = array($pb, $n);
            $this -> privateKey = array($pv, $n);
        }

        public function getPublicKey() {
            return $this -> publicKey;
        }

        public function getPrivateKey() {
            return $this -> privateKey;
        }

        public function encrypt(string $str) {
            $buffer = Array();
            foreach (str_split($str) as $char) {
                $sum = 1;
                $ascii = ord($char);
                for ($i = 0; $i < $this -> publicKey[0]; $i++) {
                    $sum *= $ascii;
                    $sum %= $this -> publicKey[1];
                }
                $buffer[] = $sum;
            }
            return $buffer;
        }

        public function decrypt(array $buffer) {
            $str = '';
            foreach ($buffer as $num) {
                $sum = 1;
                for ($i = 0; $i < $this -> privateKey[0]; $i++) {
                    $sum *= $num;
                    $sum %= $this -> privateKey[1];
                }
                $str .= chr($sum);
            }
            return $str;
        }

        private static function getPrimeList($max) {
            $check = array(1, 1);
            $prime_numbers = array();

            for ($i = 2; $i <= $max; $i++) $check[$i] = 0;

            for ($i = 2; $i <= $max; $i++) {
                if ($check[$i] == 1) continue;
                for ($j = $i * 2; $j <= $max; $j += $i) $check[$j] = 1;
                $prime_numbers[] = $i;
            }

            return $prime_numbers;
        }
    }
?>