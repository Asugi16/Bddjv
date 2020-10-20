<?php
    class Guerrier extends Personnage
    {
        public function recevoirDegats($degats)
        {
            if ($this -> degats() >= 0 && $this -> degats() <= 25){
                $this -> setAtout(4);
            } elseif ($this -> degats() > 25 && $this -> degats() <= 50){
                $this -> setAtout(3);
            } elseif ($this -> degats() > 50 && $this -> degats() <= 75){
                $this -> setAtout(2);
            } elseif ($this -> degats() > 75 && $this -> degats() <= 90){
                $this -> setAtout(1);
            } else {
                $this -> setAtout(0);
            }
             
            $this -> setDegats( $this -> degats() + $degats - $this -> atout());
             
            if ($this -> degats >= 100){
                return self::PERSONNAGE_TUE;
            }
             
            return self::PERSONNAGE_FRAPPE;
             
        }
    }
?>