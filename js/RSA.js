/**
* Class for RSA encryption.
* Provides functions related to RSA encryption, decoding.

* @author name0825
* @source https://www.crocus.co.kr/1203
*/
class RSA {
    constructor() {
        this.n = null;
        this.publicKey = null;
        this.privateKey = null;
    }

    key_generate(max = 4096, max_try = Infinity, n_min = 0) {
        let t;
        let n = 0, p = 0, q = 0;
        let prime_numbers = this.get_prime_number(max);

        t = 0;
        while (n <= n_min) {
            if (t++ > max_try) return false;
            p = prime_numbers[Math.floor(Math.random() * prime_numbers.length)];
            q = prime_numbers[Math.floor(Math.random() * prime_numbers.length)];
            if (p == q || (p - 1) * (q - 1) == 2) continue;
            n = p * q;
        }

        t = 0
        let e = 0, pi = (p - 1) * (q - 1);
        while (!e) {
            if (t++ > max_try) return false;
            let rand = Math.floor(Math.random() * pi * 2) % pi;
            if (rand < pi && rand > 1) {
                e = rand;
                for (let cnt = 0, i = 1; i < e; i++) {
                    if (e % i == 0 && pi % i == 0)
                        cnt++;
                if (cnt >= 2) e = 0;
                }
            }
        }

        t = 0;
        let d = 0;
        while (!d) {
            if (t++ > max_try) {
                console.log(n, e, d);
                return false;
            }
            let rand = Math.floor(Math.random() * pi * 2) % pi + 1;
            if ((e * rand) % pi == 1) d = rand;
        }

        this.n = n;
        this.publicKey = e;
        this.privateKey = d;
        return true;
    }

    encrypt(str) {
        let buffer = [];
        for (let v of data) {
            let sum = 0;
            let ascii = v.charCodeAt(0);
            for (let i = 0; i < this.publicKey; i++) {
                sum *= ascii;
                sum %= this.n;
            }
            buffer.push(sum);
        }
        return buffer;
    }

    decrypt(buffer) {
        let str = "";
        for (let v of buffer) {
            let sum = 0;
            for (let i = 0; i < this.privateKey; i++) {
                sum *= v;
                sum %= this.n;
            }
            str += String.fromCharCode(sum);
        }
        return str;
    }

    get_public_key() {
        return [this.n, this.publicKey];
    }

    get_private_key() {
        return [this.n, this.privateKey];
    }

    set_public_key(n, e) {
        this.n = n;
        this.publicKey = e;
    }

    set_private_key(n, d) {
        this.n = n;
        this.privateKey = d;
    }

    get_prime_number(max) {
        let check = [1, 1];
        let prime_numbers = [];
        for (let i = 2; i <= max; i++) check.push(0);
        for (let i = 2; i <= max; i++) {
            if (check[i] == 1) continue;
            for (let j = i * 2; j <= max; j += i) check[j] = 1;
            prime_numbers.push(i);
        }
        return prime_numbers;
    }
}