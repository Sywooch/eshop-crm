<?php
/* @var $this yii\web\View */
use yii\bootstrap\ActiveForm;
use app\models\Senders;
?>
<h1>Выгрузить заказы для отправки</h1>
<?if(!empty($errors)) {
	print_r($errors);
}?>
<?php $form = ActiveForm::begin(['layout' => 'inline', 'fieldConfig'=>['labelOptions'=>['class'=>'']]])
//$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'class'=>'form-inline']]) ?>

    <?= $form->field($model, 'sender')->dropdownList(Senders::find()->select(['name', 'id'])->indexBy('id')->column());?>

    <button type="submit" class="btn btn-primary">Отобрать</button>

<?php ActiveForm::end() ?>

<p></p>

<?if(!empty($results)) ?>
