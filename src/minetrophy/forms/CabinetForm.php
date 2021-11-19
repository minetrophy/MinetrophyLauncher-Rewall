<?php
namespace minetrophy\forms;

use std, gui, framework, minetrophy;
use Minetrophy\Skin2D;

class CabinetForm extends AbstractForm
{
    function __construct()
    {
        parent::__construct();
        
        $buttonSkinChoice = new UXButton("...");
        $buttonSkinChoice->focusTraversable = false;        
        $fieldSkinPath = new UXTextField();
        $fieldSkinPath->promptText = "skin path";
        $fieldSkinPath->focusTraversable = false;
        $fieldSkinPath->editable = false;
        $fieldSkinPath->enabled = false;
        UXHBox::setHgrow($fieldSkinPath, 'ALWAYS');
        
        $hBoxSkinPath = new UXHBox([$buttonSkinChoice, $fieldSkinPath]);
        $hBoxSkinPath->spacing = 8;
        $this->vBoxContentPane->add($hBoxSkinPath);
        
        
        $buttonSkinPreview = new UXButton("PREWIEW");
        $buttonSkinPreview->focusTraversable = false;   
        UXHBox::setHgrow($buttonSkinPreview, 'ALWAYS');
        $buttonSkinPreview->maxWidth = 1000;
        $buttonSkinUpdate = new UXButton("UPDATE");
        $buttonSkinUpdate->focusTraversable = false;   
        UXHBox::setHgrow($buttonSkinUpdate, 'ALWAYS');
        $buttonSkinUpdate->maxWidth = 1000;
        
        $hBoxSkinButtons = new UXHBox([$buttonSkinPreview, $buttonSkinUpdate]);
        $hBoxSkinButtons->spacing = 8;
        $this->vBoxContentPane->add($hBoxSkinButtons);
        
        $this->on('click', function()
        {
            
            $this->imageSkinFront->image = UXImage::ofUrl("https://minetrophy.ru/skin2d/{$username}/front/get");
            $this->imageSkinBack->image = UXImage::ofUrl("https://minetrophy.ru/skin2d/{$username}/back/get");
        });
    }
}
