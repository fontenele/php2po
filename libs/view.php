<?php

class View {

    public function translate($string) {
        return gettext($string) ? gettext($string) : $string;
    }

}
