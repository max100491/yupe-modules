<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'request')); 
?>
<div class="modal-header">
    <a class="close" id="close-modal" data-dismiss="modal"><i class="icon-remove"></i></a>   
    <h5><?php echo $this->title?></h5>
</div>
<div class="modal-body">

<?php $this->render('_form',array('model'=>$model,'body'=>$body));?>

</div>
<?php $this->endWidget(); ?>