<?php
/**
 * Виждет для вывода сконструированной формы
 *
 * @author WebGears team
 * @link http://none.shit
 * @copyright 2015-2013 BlackTag && WebGears team
 * @package yupe.modules.callback.install
 * @license  BSD
 *
 **/
Yii::import('application.modules.callback.models.CallbackModel');
Yii::import('application.modules.callback.components.SiteHelper');
Yii::import('application.modules.callback.components.FormTemplater');

class CallbackWidget extends yupe\widgets\YWidget{

	public $view = '_construct';
	private $assets;  //путь до папкси с ресурсами
	private $clientPos; //позиция подключения Js-скриптов
	private $htmlOptionsDefault = [ //настройки кнопки подъема модального окна по дефолту
          'data-toggle'=>'modal',
          'data-target'=>'#request',
          'class'=>''
        ];
	private $buttonOptionsDefault=[
                'context'=>'info',
                'label'=>'Отправить',
            ];
    private $messageOptionsDefault=[
                'id'=>'success',
                'class'=>'alert',
            ];
	private $body; //собираемое тело формы

	public $type = 'modal'; 
	public $cssFile = 'style.css'; 
	public $title = 'Заявка на обратный звонок';
	public $template = '{name}{email}{phone}{text}{captcha}';
	public $renderFormAfterSuccess = true;
	public $successMessage = 'Мы вскоре свяжемся с Вами!';
	public $errorMessage = 'Произашла ошибка отправки, попробуйте еще раз';
	public $buttonOptions = [];
    public $formOptions = [];
    public $templateOptions = [];      
    public $mailOptions = [];      
	
	public function setupVariable(){
		$this->buttonOptions['context'] = isset($this->buttonOptions['context'])? $this->buttonOptions['context'] : 'link';
		$this->buttonOptions['label'] = isset($this->buttonOptions['label'])? $this->buttonOptions['label'] : 'Оставить заявку';
		$this->buttonOptions['url'] = isset($this->buttonOptions['url'])? $this->buttonOptions['url'] : CHtml::normalizeUrl(['/callback/send']);
		$this->buttonOptions['htmlOptions'] = isset($this->buttonOptions['htmlOptions'])? $this->buttonOptions['htmlOptions'] : $this->htmlOptionsDefault;
		
		$this->formOptions['type'] = isset($this->formOptions['type'])? $this->formOptions['type'] : 'horizontal';
		$this->formOptions['id'] = isset($this->formOptions['id'])? $this->formOptions['id'] : 'request-form';
		$this->formOptions['action'] = isset($this->formOptions['action'])? $this->formOptions['action'] :  CHtml::normalizeUrl(['/callback/send']);
		$this->formOptions['actionCaptcha'] = isset($this->formOptions['actionCaptcha'])? $this->formOptions['actionCaptcha'] : CHtml::normalizeUrl(['/callback/captcha']);
		$this->formOptions['ajax'] = isset($this->formOptions['ajax'])? $this->formOptions['ajax'] : true;
		$this->formOptions['reset'] = isset($this->formOptions['reset'])? $this->formOptions['reset'] : true;
		$this->formOptions['serviceList'] = isset($this->formOptions['serviceList'])? $this->formOptions['serviceList'] : [];
		$this->formOptions['prevText'] = isset($this->formOptions['prevText'])? $this->formOptions['prevText'] : '';
		$this->formOptions['afterText'] = isset($this->formOptions['afterText'])? $this->formOptions['afterText'] : '';
		$this->formOptions['enableClientValidation'] = isset($this->formOptions['enableClientValidation'])? $this->formOptions['enableClientValidation'] : true;
		$this->formOptions['htmlOptions'] = isset($this->formOptions['htmlOptions'])? $this->formOptions['htmlOptions'] : [];

		$this->templateOptions["button"] = isset($this->templateOptions["button"])? $this->templateOptions["button"] : $this->buttonOptionsDefault;
		$this->templateOptions["message"] = isset($this->templateOptions["message"])? $this->templateOptions["message"] : $this->messageOptionsDefault;
		
		$this->mailOptions["view"] = isset($this->mailOptions["view"])? $this->mailOptions["view"] : '_text';
		$this->mailOptions["from"] = isset($this->mailOptions["from"])? $this->mailOptions["from"] : Yii::app()->getModule('callback')->getAddress(1);
		$this->mailOptions["to"] = isset($this->mailOptions["to"])? $this->mailOptions["to"] : Yii::app()->getModule('callback')->emailsRecipients;
		$this->mailOptions["title"] = isset($this->mailOptions["title"])? $this->mailOptions["title"] : 'Оповещение об обратном звонке';
	}

	public function createBody(){
		preg_replace_callback("/{(\w+)}/",array($this,'renderSection'),$this->template);
	}

	protected function renderSection($matches){
		$method='render'.$matches[1];
		if(method_exists(FormTemplater,$method))
			$this->body .= FormTemplater::$method();
		else
			throw new CException(Yii::t('zii', 'Нет такого атрибута. Это тебе не шахтерский ребус, не надо тут угадывать. Загляни в исходный код, или допиши свое.'));;
	}

	public function run(){
		$this->setupVariable();
		$model = new CallbackModel;
		//$temp = isset($_POST['template'])?$_POST['template']:$this->template;
		$model->setRules($this->template); 
		//$this->validate($model);
		// if(!Yii::app()->request->isAjaxRequest){
		$this->createBody();
		$this->render($this->view,['model'=>$model,'body'=>$this->body]);
		// }
		
	}
}