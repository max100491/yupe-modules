<?php 
class FormTemplater {
	public static function renderName(){
		return 'echo $form->textFieldGroup($model,"name",$this->templateOptions["name"]);';
	}

	public static function renderPhone(){
		return 'echo $form->textFieldGroup($model,"phone",$this->templateOptions["phone"]);';
	}

	public static function renderPhoneMasked(){
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
  			<div class=\'row-fluid\'>".$form->textFieldGroup($model,"phone")."</div>
  		</noscript>";';
	}

	public static function renderEmail(){
		return 'echo $form->textFieldGroup($model,"email",$this->templateOptions["email"]);';
	}

	public static function renderText(){
		return 'echo $form->textAreaGroup($model,"text",$this->templateOptions["text"]);';
	}

	public static function renderService(){
		return 'echo $form->dropDownListGroup($model,"service", $this->optionsForm["serviceList"],$this->templateOptions["serviceList"]);';
	}

	public static function renderCaptcha(){
 		return 'echo "<div class=\'row-fluid\'>".$form->captchaRow($model,"verifyCode",array(
		    			"class"=>"span12",
		    			"captchaOptions"=>array(
		        			"buttonLabel"=>"<i class=\'icon-refresh\'></i>",
		        			//"captchaAction"=>$this->optionsForm["actionCaptcha"]
		        		)
		    		)
		  		)."</div>";';
	}

	public static function renderQaptcha(){
 		return 'echo "<div class=\'row-fliud\' style=\'position:relative\'><div class=\'QapTcha\'></div></div>";
		echo $form->hiddenField($model,"qaptcha");';
	}
	public static function renderButton(){
		return '$this->widget("bootstrap.widgets.TbButton", $this->templateOptions["button"]);';
	}
	public static function renderErrors(){
		return 'echo $form->errorSummary($model,$this->templateOptions["errors"]);';
	}
	public static function renderMessage(){
		return 'echo Chtml::tag("div",$this->templateOptions["message"])."</div>";';
	}
	

}
?>