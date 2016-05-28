<?php

namespace harunyasar\YaziylaPara;

class Para
{

    private $digits = [null, 'bin', 'milyon', 'milyar', 'trilyon', 'katrilyon', 'kentilyon', 'seksilyon', 'septilyon'];
    private $one_digit = [null, 'bir', 'iki', 'üç', 'dört', 'beş', 'altı', 'yedi', 'sekiz', 'dokuz'];
    private $two_digits = [null, 'on', 'yirmi', 'otuz', 'kırk', 'elli', 'altmış', 'yetmiş', 'seksen', 'doksan'];
    public $text;

    public function __construct($number)
    {
        $this->number = $number;
        $minus = $this->number < 0;

        if($minus) {
            $this->number *= -1;
        }

        $price = explode('.', strval($this->number));

        $this->price = $price[0];
        $this->cents = (isset($price[1])) ? $price[1] : '';

        $this->convert_price();
        $this->convert_cents();

        $this->text = (($minus) ? 'eksi ' : '') . $this->text;
    }

    private function convert_price()
    {
        $this->text = $this->convert_to_text($this->price);
        if($this->text) {
            $this->text .= 'TL';
        }
    }

    private function convert_cents()
    {
        if($this->cents && intval($this->cents) > 0) {
            if(strlen($this->cents) == 1) {
                $this->cents = strval((intval($this->cents) * 10));
            }
            if(strlen($this->cents) > 2) {
                $this->cents = substr($this->cents, 0, 2);
            }
            if($this->text) {
                $this->text .= ',';
            }
            $this->text .= $this->convert_to_text($this->cents) . 'kr.';
        }
    }

    private function say_1_digit_text($n)
    {
        return $this->one_digit[$n];
    }

    private function say_2_digit_text($n)
    {
        return $this->two_digits[$n[0]] . $this->say_1_digit_text($n[1]);
    }

    private function say_3_digit_text($n)
    {
        $one = $n[0] == 1 ? 'yüz' : $this->say_1_digit_text($n[0]);
        if ($n[0] != 1 || $n[0] != 0) {
            $one .= 'yüz';
        }
        array_shift($n);
        return $one . $this->say_2_digit_text($n);
    }

    private function convert_to_text($number)
    {
        $number = intval($number);
        $text = '';
        $i = 0;
        $new_text = '';
        while($number) {
            list($number, $r) = array(intval($number / 1000), $number % 1000);
            $size = count(str_split(strval($r)));
            $arg = $size == 1 ? $r : array_map('intval', str_split(strval($r)));

            if($size == 1) {
                $new_text = ($r == 1 && $i == 1) ? '' : $this->say_1_digit_text($arg);
            }

            if($size == 2) {
                $new_text = ($r == 1 && $i == 1) ? '' : $this->say_2_digit_text($arg);
            }

            if($size == 3) {
                $new_text = ($r == 1 && $i == 1) ? '' : $this->say_3_digit_text($arg);
            }

            if($r != 0) {
                $new_text .= $this->digits[$i];
            }

            $text = $new_text . $text;

            $i += 1;
        }
        return $text;
    }

}