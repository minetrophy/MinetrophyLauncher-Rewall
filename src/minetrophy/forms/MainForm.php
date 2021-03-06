                                                                                                                   <?php
namespace minetrophy\forms;

use facade\Async;
use std, gui, framework, minetrophy;

use kosogroup\minecraft\launcher\core\TrophyLauncher;
use kosogroup\minecraft\launcher\core\TrophyVersion;

class MainForm extends AbstractForm
{

    private $_buttonPlayEvent = null;
    function __construct()
    {
        parent::__construct();
        
        TrophyVersion::DeployMeta("https://launchermeta.minetrophy.ru/");
        
        $this->fragmentAuth->content->buttonCabinet->enabled = false;
        $this->fragmentAuth->content->buttonCabinet->on('click', function()
        {
            if(!$this->_auth()) return;
            
            $cabinetForm = app()->showForm("CabinetForm");
            //$cabinetForm->
            
        });
        
        $launchOptions = array(
            'javaPath' => 'java',
            'launcherPath' =>   './minecraft',
            'version' => array(
                //'jsonDownload' => false,
                'number' =>     'legacy',
                'type' =>       'release'
            ),
            
            //'detached' => false,
            /*'memory' => array(
                'min' =>        '8G',
                'max' =>        '32G'
            )
            //*/
            
            
        );
        
        $launchOptions['eventEmiter'] = function ($target, $meta, $thread = null)
        {
            switch($target)
            {
                case "downloadUpdateAA": 
                    uiLater(function() use ($this, $meta)
                    {
                        $this->fragmentDownloader->content->labelUpdateInfo->text = 'Готово... (' . $meta['target'] . ' | ' . $meta['current'] . ' из ' . $meta['of'] . ')';
                        $this->fragmentDownloader->content->progressDownload->progress = $meta['current'] / ($meta['of']/100);
                    });
                    break;
                case "_download": break;
                case "_downloadBegin": 
                    uiLater(function() use ($this, $meta)
                    {
                        $this->toggleUpdateUI(true);
                        $this->fragmentDownloader->content->progressDownload->progress = -1;
                    });
                    break;
                case "interrupt":
                case "_downloadEnd":
                    uiLater(function() use ($this, $meta)
                    {
                        $this->toggleUpdateUI(false);
                        $this->fragmentAuth->content->hidePreloader();
                    });
                    break;
                case "_downloadCallback": 
                    uiLater(function() use ($this, $meta)
                    {
                       $this->fragmentDownloader->content->labelUpdateInfoFile->text = 'Загрузка... (' . $meta['name'] . ' | ' . $meta['progress'] . ' из ' . $meta['total'] . ')';
                       $this->fragmentDownloader->content->progressDownloadFile->progress = ($meta['progress'] / ($meta['total'] / 100));
                    });
                    break;
                case "_checkHash":
                    uiLater(function() use ($this, $meta)
                    {                           
                        $fi = explode('/', $meta['file']);
                        $this->fragmentDownloader->content->labelUpdateInfoFile->text = 'Проверка файлов... ( ' . array_pop($fi) . ' | ' . $meta['calculated_hash'] . ' of ' . $meta['hash'] . ')';
                        $this->fragmentDownloader->content->progressDownloadFile->progress = -1;
                    });
                    break;
    
                case "std::out":
                case "err::out": 
                    uiLater(function() use ($this, $meta)
                    {    
                        //тут вывод в консоль можно выполнить                       
                        //var_dump($meta);
                    });
                    break;                       
    
    
    
            }
        };
        
        $this->fragmentAuth->content->buttonPlay->on('click', function() use ($launchOptions)
        {
            $this->fragmentAuth->content->showPreloader();
            
            $auth = $this->_auth();
            if(!$auth) return;
            
            
            $launchOptions['auth'] = array(
                'username' =>   $auth['username'],
                'uuid' =>       $auth['uuid'],
                'token' =>      $auth['accessToken'],
                'type' =>       'trophy',
            );
            
            $launcher = (new TrophyLauncher())->launch($launchOptions);
            
            $this->fragmentDownloader->content->buttonCancel->on('click', function() use ($launcher)
            {
                $launcher->Stop();
            });
            
        });
        
        $this->on('close', function ()
        {
            App::shutdown();
            System::halt(0);
        });

    }
    
    private function toggleUpdateUI($state)
    {
        $this->fragmentDownloader->visible = $state;
    }
    
    private function _auth()
    {
        $username = $this->fragmentAuth->content->fieldUsername->text;
        $password = $this->fragmentAuth->content->fieldPassword->text;    
        $result = json_decode(file_get_contents("https://auth.minetrophy.ru/session/minecraft/auth/{$username}/{$password}"), true);
        if($result)
        {
            return $result;
        }
        else 
        {
                    $this->fragmentAuth->content->hidePreloader();
                    $this->toast("Неверное Имя пользователя или Пароль");
        }
        return false;
    }

}
