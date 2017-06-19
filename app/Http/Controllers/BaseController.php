<?php

namespace App\Http\Controllers;

use App\Models\Web\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class BaseController extends Controller
{
    /**
     * @var Language
     */
    protected $language;


    /**
     * Return current language
     *
     * @return Language|null
     */
    public function getLanguage(){
        if($this->language) return $this->language;

        $languageId = Session::get('language', null);

        if($languageId){
            $this->language = Language::enabled()->where('id', $languageId)->first();
        }

        if(!$languageId || !$this->language){
            $this->language = Language::enabled()->orderBy('default', 'desc')->first();
        }

        return $this->language;
    }

}
