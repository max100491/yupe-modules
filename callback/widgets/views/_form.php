
<?php
//echo ($this->type == 'block' && !$this->renderFormAfterSuccess || !Yii::app()->user->hasFlash($this->formOptions['id'])) ? '<h2>'.$this->title.'</h2>' : '';

$reset = $this->formOptions['reset'] 
                  ? 
                  'setTimeout(function(){
                    var $success = $("#'.$this->templateOptions['message']['id'].'",form);
                    $("#'.$this->formOptions['id'].'").trigger("reset");
                    $success.removeClass("alert-success alert-error show").html("");
                    $("#close-modal").click();
                    $("button",form).removeAttr("disabled");
                  },4000);'
                  :
                  '';
$form = $this->beginWidget('TbActiveForm',array(
  'id'=> $this->formOptions['id'],
  'type'=>$this->formOptions['type'],
  'enableClientValidation'=>$this->formOptions['enableClientValidation'],
  'htmlOptions'=>$this->formOptions['htmlOptions'],
  'clientOptions'=>$this->formOptions['ajax'] ? 
  [
    'validateOnSubmit'=>$this->formOptions['ajax'],
    'afterValidate'=>'js:function(form,data,hasError)
      {
        if(!hasError)
        {
          $.ajax(
          {
            "type":"POST",
            "url":"'.$this->formOptions['action'].'",
            "data": form.serialize(),
            "beforeSend":function(){
                $("button",form).attr("disabled","disabled");
            },
            "success":function(data){
                data = $.parseJSON(data);
                var $success = $("#'.$this->templateOptions['message']['id'].'",form);
                if(data.result){
                  $success.addClass("alert-success show");
                  $success.html("'.$this->successMessage.'");
                }else{
                  $success.addClass("alert-error show");
                  $success.html("'.$this->errorMessage.'");
                }
            },
            "complete":function(){
              '.$reset.'
            },
          });
        }
      }'
    ]:
    '',
));
if(!$this->renderFormAfterSuccess || !Yii::app()->user->hasFlash($this->formOptions['id'])){
  if($this->formOptions['ajax'])
    echo "<script>$('#".$this->formOptions['id']."').attr('action','".$this->formOptions['action']."');</script>";

  echo $this->formOptions['prevText'];

  eval($body);

  echo CHtml::hiddenField('mail',json_encode([$this->mailOptions]));
  echo CHtml::hiddenField('template',$this->template);
  echo CHtml::hiddenField('messages',json_encode([
      'id'=>$this->formOptions['id'],
      'success'=>$this->successMessage,
      'error'=>$this->successMessage
      ])
  );
}
echo Yii::app()->user->hasFlash($this->formOptions['id']) ? '<div class="alert alert-success">'.Yii::app()->user->getFlash($this->formOptions['id']).'</div>':'';
echo $this->formOptions['afterText'];
$this->endWidget();?>