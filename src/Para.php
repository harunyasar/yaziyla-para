<?php

namespace harunyasar\YaziylaPara;

/**
 * Class Para
 * Türk Lirasını okunuşuna çevir
 * @package harunyasar/yaziyla-para
 * @see     https://github.com/harunyasar/yaziyla-para
 */
class Para
{

    private $digits = [null, 'bin', 'milyon', 'milyar', 'trilyon', 'katrilyon', 'kentilyon', 'seksilyon', 'septilyon'];
    private $one_digit = [null, 'bir', 'iki', 'üç', 'dört', 'beş', 'altı', 'yedi', 'sekiz', 'dokuz'];
    private $two_digits = [null, 'on', 'yirmi', 'otuz', 'kırk', 'elli', 'altmış', 'yetmiş', 'seksen', 'doksan'];
    private $word_speaker = [1 => 'one_digit_text', 2 => 'two_digit_text', 3 => 'three_digit_text'];
    public $text;

    /**
     * Para constructor
     * @param $number Kelimelere çevirilecek olan sayı
     */
    public function __construct($number)
    {
        $this->number = $number;
        $minus = $this->number < 0;

        if ($minus) {
            $this->number *= -1;
        }

        $price = explode('.', strval($this->number));

        $this->price = $price[0];
        $this->cents = (isset($price[1])) ? $price[1] : '';

        $this->convert_price();
        $this->convert_cents();

        $this->text = (($minus) ? 'eksi ' : '') . $this->text;
    }

    /**
     * Lira hesap et ve kelimelere çevir
     */
    private function convert_price()
    {
        $this->text = $this->convert_to_text($this->price);
        if ($this->text) {
            $this->text .= 'TL';
        }
    }

    /**
     * Kuruş tespit et ve kelimelere çevir
     */
    private function convert_cents()
    {
        if ($this->cents && intval($this->cents) > 0) {
            if (strlen($this->cents) == 1) {
                $this->cents = strval((intval($this->cents) * 10));
            }
            if (strlen($this->cents) > 2) {
                $this->cents = substr($this->cents, 0, 2);
            }
            if ($this->text) {
                $this->text .= ',';
            }
            $this->text .= $this->convert_to_text($this->cents) . 'kr.';
        }
    }

    /**
     * Tek basamaklı sayıları söyle
     * @param array $n Tek basamaklı sayı
     * @return mixed
     */
    private function one_digit_text($n)
    {
        return $this->one_digit[$n];
    }

    /**
     * İki basamaklı sayıları söyle
     * @param array $n İki basamaklı sayı
     * @return mixed
     */
    private function two_digit_text($n)
    {
        return $this->two_digits[$n[0]] . $this->one_digit_text($n[1]);
    }

    /**
     * Üç basamaklı sayıları söyle
     * @param array $n Üç basamaklı sayı
     * @return mixed
     */
    private function three_digit_text($n)
    {
        $one = $n[0] == 1 ? 'yüz' : $this->one_digit_text($n[0]);
        if ($n[0] != 1 || $n[0] != 0) {
            $one .= 'yüz';
        }
        array_shift($n);
        return $one . $this->two_digit_text($n);
    }

    /**
     * Sayıları kelimelere çevir
     * @param int $number Okunacak olan sayı
     * @return string
     */
    private function convert_to_text($number)
    {
        $number = intval($number);
        $text = '';
        $i = 0;
        while ($number) {
            list($number, $r) = array(intval($number / 1000), $number % 1000);
            $size = count(str_split(strval($r)));
            $arg = $size == 1 ? $r : array_map('intval', str_split(strval($r)));

            $new_text = ($r == 1 && $i == 1) ? '' : $this->{$this->word_speaker[$size]}($arg);

            if ($r != 0) {
                $new_text .= $this->digits[$i];
            }

            $text = $new_text . $text;

            $i += 1;
        }
        return $text;
    }

}