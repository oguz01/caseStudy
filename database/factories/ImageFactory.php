<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    /**
     * Çakışma olmucak şekilde rasgele bir sıralama için time ve rand fonksyonlarından yararlandım.
     * Resmin uniq olması için bir isim oluşturdum.
     * Uzak sunucu üzerinden 100 * 100 resimleri çektim.
     * Belirlediğim klasöre kopyaladım.
     * Veri tabanın kayıt ettim.
     */


    public function definition()
    {
        $rand = substr(time(),9,10).rand(0,9);
        $imageName= time().'_img_'.$rand.'.png';
        $remoteImage = 'https://placeimg.com/100/100/any?'.$rand.'.png';
        copy($remoteImage , 'public/images/'.$imageName);
        return [
          'path' => 'images/'.$imageName,
          'sort' =>  $rand
        ];
    }
}
