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

class CallbackWidget extends yupe\widgets\YWidget{

	/*Parametrs*/
	private $assets;  //путь до папкси с ресурсами
	private $clientPos; //позиция подключения Js-скриптов
	private $htmlOptionsDefault = array( //настройки кнопки подъема модального окна по дефолту
          'data-toggle'=>'modal',
          'data-target'=>'#request',
          'class'=>''
        );
	private $body; //собираемое тело формы

	public $type = 'modal'; 
	public $cssFile = 'style.css'; 
	public $title = 'Заявка на обратный звонок';
	public $template = '{name}{email}{phone}{text}{captcha}';
	public $mailText = '_text';
	public $renderFormAfterSuccess = true;
	public $successMessage = 'Мы вскоре свяжемся с Вами!';
	public $errorMessage = 'Произашла ошибка отправки, попробуйте еще раз';
	public $optionsButton = array();
    public $optionsForm = array();   
    public $optionsTemplate = array();   
	
	public function init(){
		$this->setupVariable();

		$this->clientPos = Yii::app()->clientScript->coreScriptPosition;
		/*$this->assets = Yii::app()->assetManager->publish('application.modules.callback.views.assets', false, -1, YII_DEBUG);

		Yii::app()->clientScript->registerCssFile($this->assets.SiteHelper::transformPath('/css/').$this->cssFile);

        Yii::app()->clientScript->registerScriptFile($this->assets.SiteHelper::transformPath('/js/request.js'),$this->clientPos);

		Yii::app()->clientScript->registerCssFile($this->assets.SiteHelper::transformPath('/plugins/QapTcha/jquery/QapTcha.jquery.css'));
        Yii::app()->clientScript->registerScriptFile($this->assets.SiteHelper::transformPath('/plugins/QapTcha/jquery/QapTcha.jquery.js'),$this->clientPos);*/

		
		return parent::init();
	}

	public function setupVariable(){
		$this->optionsButton['context'] = isset($this->optionsButton['context'])? $this->optionsButton['context'] : 'link';
		$this->optionsButton['label'] = isset($this->optionsButton['label'])? $this->optionsButton['label'] : 'Оставить заявку';
		$this->optionsButton['url'] = isset($this->optionsButton['url'])? $this->optionsButton['url'] : CHtml::normalizeUrl(array('site/request'));
		$this->optionsButton['htmlOptions'] = isset($this->optionsButton['htmlOptions'])? $this->optionsButton['htmlOptions'] : $this->htmlOptionsDefault;
		
		$this->optionsForm['type'] = isset($this->optionsForm['type'])? $this->optionsForm['type'] : 'horizontal';
		$this->optionsForm['id'] = isset($this->optionsForm['id'])? $this->optionsForm['id'] : 'request-form';
		$this->optionsForm['action'] = isset($this->optionsForm['action'])? $this->optionsForm['action'] : CHtml::normalizeUrl(array($this->controller->getUniqueId().'/request'));;
		$this->optionsForm['actionCaptcha'] = isset($this->optionsForm['actionCaptcha'])? $this->optionsForm['actionCaptcha'] : CHtml::normalizeUrl(array($this->controller->getUniqueId().'/captcha'));
		$this->optionsForm['ajax'] = isset($this->optionsForm['ajax'])? $this->optionsForm['ajax'] : true;
		$this->optionsForm['reset'] = isset($this->optionsForm['reset'])? $this->optionsForm['reset'] : true;
		$this->optionsForm['serviceList'] = isset($this->optionsForm['serviceList'])? $this->optionsForm['serviceList'] : array();
		$this->optionsForm['prevText'] = isset($this->optionsForm['prevText'])? $this->optionsForm['prevText'] : '';
		$this->optionsForm['afterText'] = isset($this->optionsForm['afterText'])? $this->optionsForm['afterText'] : '';
		$this->optionsForm['htmlOptions'] = isset($this->optionsForm['htmlOptions'])? $this->optionsForm['htmlOptions'] : array();

	}

	public function createBody(){
		preg_replace_callback("/{(\w+)}/",array($this,'renderSection'),$this->template);
	}

	protected function renderSection($matches){
		$method='render'.$matches[1];
		
		if(method_exists($this,$method))
			$this->body .= $this->$method();
		else
			return $matches[0];
	}

	public function renderName(){
		return 'echo "$form->textFieldGroup($model,"name",array("wrapperHtmlOptions"=>"span2"))."</div>";';
	}

	public function renderPhone(){
		return 'echo "<div class=\'row-fluid\'>".$form->textFieldGroup($model,"phone",array("wrapperHtmlOptions"=>"span2"))."</div>";';
	}

	public function renderPhoneMasked(){
		return 
		'echo "
		<div class=\'hide-non-js\'>
			<div class=\'row-fluid\'>".$form->labelEx($model,"phoneMasked")."</div>
				<div class=\'row-fluid\'>";
					$this->widget("CMaskedTextField", array(
					          "model" => $model,
					          "attribute" => "phoneMasked",
					          "mask" => "8(999)999-99-99",
					          "htmlOptions"=>array(
					            "class"=>"span12",
					          )
					));
			echo "</div>
			<div class=\'row-fluid\'>".$form->error($model,"phoneMasked")."</div>
		</div>
		<script>$(\'.hide-non-js\').fadeIn();</script>
		<noscript>
  			<div class=\'row-fluid\'>".$form->textFieldGroup($model,"phone",array("class"=>"span12"))."</div>
  		</noscript>";';
	}

	public function renderEmail(){
		return 'echo "<div class=\'row-fluid\'>".$form->textFieldGroup($model,"email", array("class"=>"span2"))."</div>";';
	}

	public function renderText(){
		return 'echo "<div class=\'row-fluid\'>".$form->textAreaGroup($model,"text", array("class"=>"span2"))."</div>";';
	}

	public function renderService(){
		return 'echo "<div class=\'row-fluid\'>".$form->dropDownListGroup($model,"service", $this->optionsForm["serviceList"], array("class"=>"span12"))."</div>";';
	}

	public function renderCaptcha(){
 		return 'echo "<div class=\'row-fluid\'>".$form->captchaRow($model,"verifyCode",array(
		    			"class"=>"span12",
		    			"captchaOptions"=>array(
		        			"buttonLabel"=>"<i class=\'icon-refresh\'></i>",
		        			//"captchaAction"=>$this->optionsForm["actionCaptcha"]
		        		)
		    		)
		  		)."</div>";';
	}

	public function renderQaptcha(){
 		return 'echo "<div class=\'row-fliud\' style=\'position:relative\'><div class=\'QapTcha\'></div></div>";
		echo $form->hiddenField($model,"qaptcha");';
	}

	/*Write your formField there
		public function render$NameTemplate$(){
	 		return '@php';
		}
	*/

	public function send($model,$view = '_text'){
	       		$txt_message = $this->render('mail/'.$view,array('model'=>$model),true,false);
	       		$headers = 'From:'.Yii::app()->params['adminEmail']."\r\n".
							    'Content-type: text/html;'.
							    'charset=utf-8'."\r\n".
							    'X-Mailer: PHP/' . phpversion();
	       		$result = mail(
	                	Yii::app()->params['emailString'],
	                	'Оповещение от DesignClub',
	                	$txt_message,
	                	$headers
	                );
	       		return $result;
	}

	public function validate($model){

		if(isset($_POST['ajax'])){
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if(isset($_POST['CallbackModel'])){
			$model->attributes=$_POST['CallbackModel'];
			$valid = $model->validate();
			if($valid){
				$result = $this->send($model, $_POST['mailView']);
				if(Yii::app()->request->isAjaxRequest){
					$data["result"] = $result;
					echo json_encode($data);
				}else{
					if($result){
						$model->unsetAttributes();
						Yii::app()->user->setFlash($this->optionsForm['id'],$this->successMessage);
					}
				}
			}
		}
	}

	public function run(){
		$model = new CallbackModel;
		$temp = isset($_POST['template'])?$_POST['template']:$this->template;
		$model->setRules($temp); 
		$this->validate($model);
		if(!Yii::app()->request->isAjaxRequest){
			$this->createBody();
			$this->render("request",array('model'=>$model,'body'=>$this->body));
		}
	}
}
?>