
<?php
echo ($this->type == 'block' && !$this->renderFormAfterSuccess || !Yii::app()->user->hasFlash($this->optionsForm['id'])) ? '<h2>'.$this->title.'</h2>' : '';

$reset = $this->optionsForm['reset'] ? 'setTimeout(function(){
                    $("#'.$this->optionsForm['id'].'").trigger("reset");
                    $success.html("");
                    $success.removeClass("alert-success");
                    $success.removeClass("alert-error");
                    $success.fadeOut(500);
                    $("#close-modal").click();
                  },4000);':'';

$form = $this->beginWidget("TbActiveForm",array(
  'id'=> $this->optionsForm['id'],
  'type'=>$this->optionsForm['type'],
  'enableClientValidation'=>true,
  'htmlOptions'=>$this->optionsForm['htmlOptions'],
  'clientOptions'=>$this->optionsForm['ajax'] ? array(
    'validateOnSubmit'=>$this->optionsForm['ajax'],
    'afterValidate'=>'js:function(form,data,hasError)
      {
        if(!hasError)
        {
          $.ajax(
          {
            "type":"POST",
            "url":"'.$this->optionsForm['action'].'",
            "data": form.serialize(),
            "success":function(data){
                  data = $.parseJSON(data);
                  var $success = $("#success",form);
                  $success.fadeIn(500);
                  if(data.result){
                    $success.addClass("alert-success");
                    $success.html("'.$this->successMessage.'");
                  }else{
                    $success.addClass("alert-error");
                    $success.html("'.$this->errorMessage.'");
                  }'
                  .$reset.
                  '
              },
          });
        }
      }'
    ):'',
));
if(!$this->renderFormAfterSuccess || !Yii::app()->user->hasFlash($this->optionsForm['id'])){
  if($this->optionsForm['ajax'])
  echo "<script>$('#".$this->optionsForm['id']."').attr('action','".$this->optionsForm['action']."');</script>";

  echo $this->optionsForm['prevText'];

  eval($body);

  echo $form->errorSummary($model);
  echo '<div class="row-fluid">';
  echo CHtml::hiddenField('mailView',$this->mailText);
  echo CHtml::hiddenField('template',$this->template);
  $this->widget('bootstrap.widgets.TbButton', [
                'context'=>'info',
                'label'=>'Отправить',
                'htmlOptions'=>array(
                  'class'=>'span6 offset3',
                )
            ]);
  echo '</div>';
}
echo Yii::app()->user->hasFlash($this->optionsForm['id']) ? '<div class="alert alert-success">'.Yii::app()->user->getFlash($this->optionsForm['id']).'</div>':'';
?>

<div id="success" class="alert"></div>

<?php 
echo $this->optionsForm['afterText'];
$this->endWidget();?>